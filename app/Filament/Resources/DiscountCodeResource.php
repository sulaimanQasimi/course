<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DiscountCodeResource\Pages;
use App\Models\DiscountCode;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DiscountCodeResource extends Resource
{
    protected static ?string $model = DiscountCode::class;

    // protected static ?string $navigationIcon = 'heroicon-o-ticket';

    // protected static ?string $navigationGroup = 'Financial Management';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\Section::make('Discount Information')
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText('Unique discount code (e.g., SAVE20)'),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                        Forms\Components\Select::make('type')
                            ->options([
                                'percentage' => 'Percentage',
                                'fixed' => 'Fixed Amount',
                            ])
                            ->required()
                            ->default('percentage')
                            ->live(),
                        Forms\Components\TextInput::make('value')
                            ->numeric()
                            ->required()
                            ->prefix(fn (Forms\Get $get): string => $get('type') === 'percentage' ? '' : '$')
                            ->suffix(fn (Forms\Get $get): string => $get('type') === 'percentage' ? '%' : ''),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Usage Limits')
                    ->schema([
                        Forms\Components\TextInput::make('minimum_amount')
                            ->numeric()
                            ->prefix('$')
                            ->helperText('Minimum order amount to use this code'),
                        Forms\Components\TextInput::make('usage_limit')
                            ->numeric()
                            ->helperText('Total usage limit (leave empty for unlimited)'),
                        Forms\Components\TextInput::make('usage_limit_per_user')
                            ->numeric()
                            ->default(1)
                            ->helperText('Usage limit per user'),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Validity Period')
                    ->schema([
                        Forms\Components\DatePicker::make('starts_at')
                            ->native(false)
                            ->helperText('Start date (leave empty for immediate availability)'),
                        Forms\Components\DatePicker::make('expires_at')
                            ->native(false)
                            ->helperText('Expiry date (leave empty for no expiry)'),
                        Forms\Components\Toggle::make('is_active')
                            ->default(true)
                            ->helperText('Whether this discount code is active'),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Applicable Courses')
                    ->schema([
                        Forms\Components\Textarea::make('applicable_courses')
                            ->helperText('JSON array of course IDs (leave empty for all courses)')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'percentage' => 'blue',
                        'fixed' => 'green',
                    }),
                Tables\Columns\TextColumn::make('value')
                    ->formatStateUsing(fn (DiscountCode $record): string => $record->formatted_value)
                    ->sortable(),
                Tables\Columns\TextColumn::make('usage_count')
                    ->sortable()
                    ->label('Used'),
                Tables\Columns\TextColumn::make('usage_limit')
                    ->sortable()
                    ->label('Limit')
                    ->formatStateUsing(fn ($state): string => $state ?: '∞'),
                Tables\Columns\TextColumn::make('starts_at')
                    ->date()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('expires_at')
                    ->date()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'percentage' => 'Percentage',
                        'fixed' => 'Fixed Amount',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status'),
                Tables\Filters\Filter::make('valid')
                    ->query(fn (Builder $query): Builder => $query->valid())
                    ->label('Valid'),
                Tables\Filters\Filter::make('available')
                    ->query(fn (Builder $query): Builder => $query->available())
                    ->label('Available'),
                Tables\Filters\Filter::make('expired')
                    ->query(fn (Builder $query): Builder => $query->where('expires_at', '<', now()))
                    ->label('Expired'),
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
            'index' => Pages\ListDiscountCodes::route('/'),
            'create' => Pages\CreateDiscountCode::route('/create'),
            'view' => Pages\ViewDiscountCode::route('/{record}'),
            'edit' => Pages\EditDiscountCode::route('/{record}/edit'),
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
