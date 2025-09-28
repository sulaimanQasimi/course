<?php

namespace App\Filament\Resources\Teachers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class TeachersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('profile_image')
                    ->label('Photo')
                    ->circular()
                    ->defaultImageUrl(url('/images/default-avatar.png')),
                TextColumn::make('employee_id')
                    ->label('Employee ID')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable(),
                TextColumn::make('name')
                    ->label('Teacher Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->toggleable(),
                TextColumn::make('department')
                    ->label('Department')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray'),
                TextColumn::make('specialization')
                    ->label('Specialization')
                    ->searchable()
                    ->toggleable()
                    ->badge()
                    ->color('blue'),
                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'success' => 'active',
                        'warning' => 'inactive',
                        'info' => 'on_leave',
                        'danger' => 'terminated',
                    ])
                    ->icons([
                        'heroicon-o-check-circle' => 'active',
                        'heroicon-o-pause-circle' => 'inactive',
                        'heroicon-o-clock' => 'on_leave',
                        'heroicon-o-x-circle' => 'terminated',
                    ]),
                TextColumn::make('hire_date')
                    ->label('Hired')
                    ->date('M j, Y')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('hourly_rate')
                    ->label('Hourly Rate')
                    ->money('USD')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('salary')
                    ->label('Salary')
                    ->money('USD')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M j, Y g:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'on_leave' => 'On Leave',
                        'terminated' => 'Terminated',
                    ]),
                SelectFilter::make('department')
                    ->options(function () {
                        return \App\Models\Teacher::distinct()
                            ->pluck('department')
                            ->filter()
                            ->mapWithKeys(fn ($dept) => [$dept => $dept]);
                    }),
                Filter::make('active')
                    ->query(fn (Builder $query): Builder => $query->where('status', 'active'))
                    ->label('Active Teachers'),
                Filter::make('recent_hires')
                    ->query(fn (Builder $query): Builder => $query->where('hire_date', '>=', now()->subDays(90)))
                    ->label('Recent Hires (90 days)'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
