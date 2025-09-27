<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PayrollRecordResource\Pages;
use App\Models\PayrollRecord;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\CheckboxList;
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

class PayrollRecordResource extends Resource
{
    protected static ?string $model = PayrollRecord::class;

    // protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    // protected static ?string $navigationGroup = 'Financial Management';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Payroll Information')
                    ->components([
                        Select::make('teacher_id')
                            ->label('Teacher')
                            ->relationship('teacher', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        DatePicker::make('period_start')
                            ->required()
                            ->native(false),
                        DatePicker::make('period_end')
                            ->required()
                            ->native(false)
                            ->after('period_start'),
                        Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'generated' => 'Generated',
                                'paid' => 'Paid',
                                'cancelled' => 'Cancelled',
                            ])
                            ->required()
                            ->default('draft'),
                    ])
                    ->columns(2),

                Section::make('Financial Details')
                    ->components([
                        TextInput::make('gross_amount')
                            ->numeric()
                            ->prefix('$')
                            ->required()
                            ->default(0),
                        TextInput::make('deductions')
                            ->numeric()
                            ->prefix('$')
                            ->default(0),
                        TextInput::make('net_amount')
                            ->numeric()
                            ->prefix('$')
                            ->required()
                            ->default(0),
                    ])
                    ->columns(3),

                Section::make('Payment Information')
                    ->components([
                        TextInput::make('payment_method')
                            ->maxLength(255),
                        TextInput::make('payment_reference')
                            ->maxLength(255),
                        DateTimePicker::make('paid_at')
                            ->native(false),
                    ])
                    ->columns(3),

                Section::make('Additional Information')
                    ->components([
                        Textarea::make('notes')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                        DateTimePicker::make('generated_at')
                            ->native(false)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('teacher.name')
                    ->label('Teacher')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('period_start')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('period_end')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('gross_amount')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('deductions')
                    ->money('USD')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('net_amount')
                    ->money('USD')
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'generated' => 'warning',
                        'paid' => 'success',
                        'cancelled' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('payment_method')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('generated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('paid_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('teacher')
                    ->relationship('teacher', 'name'),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'generated' => 'Generated',
                        'paid' => 'Paid',
                        'cancelled' => 'Cancelled',
                    ]),
                Tables\Filters\Filter::make('draft')
                    ->query(fn (Builder $query): Builder => $query->where('status', 'draft'))
                    ->label('Draft'),
                Tables\Filters\Filter::make('generated')
                    ->query(fn (Builder $query): Builder => $query->where('status', 'generated'))
                    ->label('Generated'),
                Tables\Filters\Filter::make('paid')
                    ->query(fn (Builder $query): Builder => $query->where('status', 'paid'))
                    ->label('Paid'),
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
            'index' => Pages\ListPayrollRecords::route('/'),
            'create' => Pages\CreatePayrollRecord::route('/create'),
            'view' => Pages\ViewPayrollRecord::route('/{record}'),
            'edit' => Pages\EditPayrollRecord::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
