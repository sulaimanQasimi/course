<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EnrollmentResource\Pages;
use App\Models\Enrollment;
use App\Models\Course;
use App\Models\Student;
use App\Rules\UniqueEnrollment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\CheckboxList;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\CreateAction;

class EnrollmentResource extends Resource
{
    protected static ?string $model = Enrollment::class;

    // protected static ?string $navigationIcon = 'heroicon-o-user-plus';

    // protected static ?string $navigationGroup = 'Student Management';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Enrollment Information')
                    ->description('Each student can only be enrolled once per course. If a student is already enrolled, please select a different course or student.')
                    ->components([
                        Select::make('course_id')
                            ->label('Course')
                            ->relationship('course', 'title')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->rules([
                                function () {
                                    return function (string $attribute, $value, \Closure $fail) {
                                        $studentId = request()->input('data.student_id');
                                        $enrollmentId = request()->route('record'); // For editing
                                        $rule = new UniqueEnrollment($studentId, $enrollmentId);
                                        $rule->validate($attribute, $value, $fail);
                                    };
                                }
                            ])
                            ->afterStateUpdated(function ($state, $get, $set) {
                                if ($state) {
                                    $course = \App\Models\Course::find($state);
                                    if ($course) {
                                        $set('course_fee', $course->fee);
                                    }
                                }
                            }),
                        Select::make('student_id')
                            ->label('Student')
                            ->relationship('student', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->rules([
                                function () {
                                    return function (string $attribute, $value, \Closure $fail) {
                                        $courseId = request()->input('data.course_id');
                                        $enrollmentId = request()->route('record'); // For editing
                                        $rule = new UniqueEnrollment($value, $enrollmentId);
                                        $rule->validate($attribute, $courseId, $fail);
                                    };
                                }
                            ]),
                        Select::make('status')
                            ->options([
                                'enrolled' => 'Enrolled',
                                'pending' => 'Pending',
                                'waitlisted' => 'Waitlisted',
                                'cancelled' => 'Cancelled',
                                'completed' => 'Completed',
                            ])
                            ->required()
                            ->default('pending'),
                    ])
                    ->columns(2),

                Section::make('Payment Information')
                    ->components([
                        TextInput::make('course_fee')
                            ->label('Course Fee')
                            ->numeric()
                            ->prefix('$')
                            ->disabled()
                            ->dehydrated(false)
                            ->afterStateUpdated(function ($state, $get, $set) {
                                // This will be populated by the course selection
                            }),
                        TextInput::make('amount_paid')
                            ->label('Amount Paid')
                            ->numeric()
                            ->prefix('$')
                            ->default(0)
                            ->helperText('Amount already paid by the student'),
                        TextInput::make('discount_code')
                            ->label('Discount Code')
                            ->maxLength(255)
                            ->helperText('Enter discount code to apply (optional)')
                            ->live()
                            ->suffixAction(
                                \Filament\Actions\Action::make('view_codes')
                                    ->label('View Available Codes')
                                    ->icon('heroicon-o-eye')
                                    ->modalHeading('Available Discount Codes')
                                    ->modalContent(function () {
                                        $codes = \App\Models\DiscountCode::available()->get();
                                        if ($codes->isEmpty()) {
                                            return new \Illuminate\Support\HtmlString('<p>No discount codes are currently available.</p>');
                                        }
                                        
                                        $html = '<div class="space-y-2">';
                                        foreach ($codes as $code) {
                                            $html .= '<div class="border p-3 rounded">';
                                            $html .= '<strong>' . $code->code . '</strong> - ' . $code->name . '<br>';
                                            $html .= '<small class="text-gray-600">' . $code->formatted_value . ' off';
                                            if ($code->minimum_amount) {
                                                $html .= ' (min. $' . number_format((float) $code->minimum_amount, 2) . ')';
                                            }
                                            $html .= '</small>';
                                            $html .= '</div>';
                                        }
                                        $html .= '</div>';
                                        
                                        return new \Illuminate\Support\HtmlString($html);
                                    })
                                    ->modalSubmitAction(false)
                                    ->modalCancelActionLabel('Close')
                            )
                            ->rules([
                                function () {
                                    return function (string $attribute, $value, \Closure $fail) {
                                        if (empty($value)) {
                                            return; // Optional field
                                        }
                                        
                                        $discountCode = \App\Models\DiscountCode::where('code', $value)->first();
                                        
                                        if (!$discountCode) {
                                            $fail('The discount code "' . $value . '" does not exist.');
                                            return;
                                        }
                                        
                                        if (!$discountCode->is_active) {
                                            $fail('The discount code "' . $value . '" is not active.');
                                            return;
                                        }
                                        
                                        if (!$discountCode->is_available) {
                                            $fail('The discount code "' . $value . '" is no longer available.');
                                            return;
                                        }
                                        
                                        // Check if code has expired
                                        if ($discountCode->is_expired) {
                                            $fail('The discount code "' . $value . '" has expired.');
                                            return;
                                        }
                                        
                                        // Check if code hasn't started yet
                                        if ($discountCode->is_not_started) {
                                            $fail('The discount code "' . $value . '" is not yet active.');
                                            return;
                                        }
                                    };
                                }
                            ])
                            ->afterStateUpdated(function ($state, $get, $set) {
                                if ($state) {
                                    $discountCode = \App\Models\DiscountCode::where('code', $state)
                                        ->where('is_active', true)
                                        ->first();
                                    
                                    if ($discountCode) {
                                        $courseFee = $get('course_fee') ?: 0;
                                        $discountAmount = $discountCode->calculateDiscount($courseFee);
                                        $set('discount_amount', $discountAmount);
                                        
                                        if ($discountAmount > 0) {
                                            $set('amount_paid', $courseFee - $discountAmount);
                                        }
                                    } else {
                                        $set('discount_amount', 0);
                                    }
                                } else {
                                    $set('discount_amount', 0);
                                    $set('amount_paid', $get('course_fee') ?: 0);
                                }
                            }),
                        TextInput::make('discount_amount')
                            ->label('Discount Amount')
                            ->numeric()
                            ->prefix('$')
                            ->disabled()
                            ->dehydrated(false)
                            ->helperText('Automatically calculated based on discount code'),
                    ])
                    ->columns(2),

                Section::make('Dates')
                    ->components([
                        DateTimePicker::make('enrolled_at')
                            ->native(false),
                        DateTimePicker::make('cancelled_at')
                            ->native(false),
                        Textarea::make('cancellation_reason')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('student.name')
                    ->label('Student')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('course.title')
                    ->label('Course')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('course.code')
                    ->label('Course Code')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'enrolled' => 'success',
                        'pending' => 'warning',
                        'waitlisted' => 'info',
                        'cancelled' => 'danger',
                        'completed' => 'success',
                    }),
                Tables\Columns\TextColumn::make('course.fee')
                    ->label('Course Fee')
                    ->money('USD')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('amount_paid')
                    ->label('Amount Paid')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('remaining_amount')
                    ->label('Remaining')
                    ->money('USD')
                    ->sortable()
                    ->toggleable()
                    ->color(fn ($state) => $state > 0 ? 'warning' : 'success'),
                Tables\Columns\TextColumn::make('discount_code')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('enrolled_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('cancelled_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('course')
                    ->relationship('course', 'title'),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'enrolled' => 'Enrolled',
                        'pending' => 'Pending',
                        'waitlisted' => 'Waitlisted',
                        'cancelled' => 'Cancelled',
                        'completed' => 'Completed',
                    ]),
                Tables\Filters\Filter::make('enrolled')
                    ->query(fn (Builder $query): Builder => $query->where('status', 'enrolled'))
                    ->label('Enrolled'),
                Tables\Filters\Filter::make('pending')
                    ->query(fn (Builder $query): Builder => $query->where('status', 'pending'))
                    ->label('Pending'),
                Tables\Filters\Filter::make('waitlisted')
                    ->query(fn (Builder $query): Builder => $query->where('status', 'waitlisted'))
                    ->label('Waitlisted'),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEnrollments::route('/'),
            'create' => Pages\CreateEnrollment::route('/create'),
            'view' => Pages\ViewEnrollment::route('/{record}'),
            'edit' => Pages\EditEnrollment::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getFormData($record = null): array
    {
        $data = parent::getFormData($record);
        
        // If editing an existing enrollment, populate the course fee
        if ($record && $record->course) {
            $data['course_fee'] = $record->course->fee;
        }
        
        return $data;
    }
}
