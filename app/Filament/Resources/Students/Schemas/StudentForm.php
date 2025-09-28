<?php

namespace App\Filament\Resources\Students\Schemas;

use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;

class StudentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Personal Information')
                    ->description('Basic student information and contact details')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label('Full Name')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpan(1),
                                TextInput::make('email')
                                    ->label('Email Address')
                                    ->email()
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255)
                                    ->columnSpan(1),
                            ]),
                        Grid::make(2)
                            ->schema([
                                TextInput::make('phone')
                                    ->label('Phone Number')
                                    ->tel()
                                    ->maxLength(20)
                                    ->columnSpan(1),
                                DatePicker::make('date_of_birth')
                                    ->label('Date of Birth')
                                    ->native(false)
                                    ->displayFormat('d/m/Y')
                                    ->columnSpan(1),
                            ]),
                        TextInput::make('student_id_number')
                            ->label('Student ID Number')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50)
                            ->helperText('Unique identifier for the student'),
                    ])
                    ->columns(1),

                Section::make('Address Information')
                    ->description('Student address and location details')
                    ->schema([
                        Textarea::make('address')
                            ->label('Street Address')
                            ->rows(3)
                            ->maxLength(500)
                            ->columnSpanFull(),
                        Grid::make(3)
                            ->schema([
                                TextInput::make('city')
                                    ->label('City')
                                    ->maxLength(100)
                                    ->columnSpan(1),
                                TextInput::make('state')
                                    ->label('State/Province')
                                    ->maxLength(100)
                                    ->columnSpan(1),
                                TextInput::make('zip_code')
                                    ->label('ZIP/Postal Code')
                                    ->maxLength(20)
                                    ->columnSpan(1),
                            ]),
                        TextInput::make('country')
                            ->label('Country')
                            ->maxLength(100)
                            ->default('United States'),
                    ])
                    ->columns(1),

                Section::make('Emergency Contact')
                    ->description('Emergency contact information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('emergency_contact_name')
                                    ->label('Emergency Contact Name')
                                    ->maxLength(255)
                                    ->columnSpan(1),
                                TextInput::make('emergency_contact_phone')
                                    ->label('Emergency Contact Phone')
                                    ->tel()
                                    ->maxLength(20)
                                    ->columnSpan(1),
                            ]),
                        TextInput::make('emergency_contact_relationship')
                            ->label('Relationship to Student')
                            ->maxLength(100)
                            ->placeholder('e.g., Parent, Guardian, Spouse')
                            ->columnSpanFull(),
                    ])
                    ->columns(1),

                Section::make('Academic Information')
                    ->description('Student enrollment and academic status')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                DatePicker::make('enrollment_date')
                                    ->label('Enrollment Date')
                                    ->required()
                                    ->native(false)
                                    ->displayFormat('d/m/Y')
                                    ->default(now())
                                    ->columnSpan(1),
                                Select::make('status')
                                    ->label('Student Status')
                                    ->options([
                                        'active' => 'Active',
                                        'inactive' => 'Inactive',
                                        'suspended' => 'Suspended',
                                        'graduated' => 'Graduated',
                                    ])
                                    ->required()
                                    ->default('active')
                                    ->columnSpan(1),
                            ]),
                        Textarea::make('notes')
                            ->label('Notes')
                            ->rows(4)
                            ->maxLength(1000)
                            ->placeholder('Additional notes about the student...')
                            ->columnSpanFull(),
                    ])
                    ->columns(1),

                Section::make('Additional Information')
                    ->description('Optional additional student data')
                    ->schema([
                        Textarea::make('metadata')
                            ->label('Additional Data (JSON)')
                            ->rows(3)
                            ->maxLength(2000)
                            ->placeholder('Enter JSON data for additional student information')
                            ->helperText('Optional: Additional structured data about the student')
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}
