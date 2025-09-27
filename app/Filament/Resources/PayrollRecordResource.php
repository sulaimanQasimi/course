<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PayrollRecordResource\Pages;
use App\Models\PayrollRecord;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PayrollRecordResource extends Resource
{
    protected static ?string $model = PayrollRecord::class;

    // protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    // protected static ?string $navigationGroup = 'Financial Management';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\Section::make('Payroll Information')
                    ->schema([
                        Forms\Components\Select::make('teacher_id')
                            ->label('Teacher')
                            ->relationship('teacher', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\DatePicker::make('period_start')
                            ->required()
                            ->native(false),
                        Forms\Components\DatePicker::make('period_end')
                            ->required()
                            ->native(false)
                            ->after('period_start'),
                        Forms\Components\Select::make('status')
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

                Forms\Components\Section::make('Financial Details')
                    ->schema([
                        Forms\Components\TextInput::make('gross_amount')
                            ->numeric()
                            ->prefix('$')
                            ->required()
                            ->default(0),
                        Forms\Components\TextInput::make('deductions')
                            ->numeric()
                            ->prefix('$')
                            ->default(0),
                        Forms\Components\TextInput::make('net_amount')
                            ->numeric()
                            ->prefix('$')
                            ->required()
                            ->default(0),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Payment Information')
                    ->schema([
                        Forms\Components\TextInput::make('payment_method')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('payment_reference')
                            ->maxLength(255),
                        Forms\Components\DateTimePicker::make('paid_at')
                            ->native(false),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Additional Information')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                        Forms\Components\DateTimePicker::make('generated_at')
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
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
