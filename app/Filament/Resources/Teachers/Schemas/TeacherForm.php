<?php

namespace App\Filament\Resources\Teachers\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Grid;

class TeacherForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Personal Information')
                    ->description('Basic teacher information and contact details')
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
                        TextInput::make('employee_id')
                            ->label('Employee ID')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50)
                            ->helperText('Unique identifier for the teacher'),
                    ])
                    ->columns(1),

                Section::make('Professional Information')
                    ->description('Teacher employment and academic details')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                DatePicker::make('hire_date')
                                    ->label('Hire Date')
                                    ->required()
                                    ->native(false)
                                    ->displayFormat('d/m/Y')
                                    ->default(now())
                                    ->columnSpan(1),
                                Select::make('status')
                                    ->label('Employment Status')
                                    ->options([
                                        'active' => 'Active',
                                        'inactive' => 'Inactive',
                                        'on_leave' => 'On Leave',
                                        'terminated' => 'Terminated',
                                    ])
                                    ->required()
                                    ->default('active')
                                    ->columnSpan(1),
                            ]),
                        Grid::make(2)
                            ->schema([
                                TextInput::make('department')
                                    ->label('Department')
                                    ->maxLength(100)
                                    ->placeholder('e.g., Computer Science, Mathematics')
                                    ->columnSpan(1),
                                TextInput::make('specialization')
                                    ->label('Specialization')
                                    ->maxLength(100)
                                    ->placeholder('e.g., Web Development, Data Science')
                                    ->columnSpan(1),
                            ]),
                        Textarea::make('bio')
                            ->label('Biography')
                            ->rows(4)
                            ->maxLength(1000)
                            ->placeholder('Brief biography and teaching philosophy...')
                            ->columnSpanFull(),
                    ])
                    ->columns(1),

                Section::make('Compensation')
                    ->description('Teacher salary and payment information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('hourly_rate')
                                    ->label('Hourly Rate')
                                    ->numeric()
                                    ->prefix('$')
                                    ->step(0.01)
                                    ->columnSpan(1),
                                TextInput::make('salary')
                                    ->label('Annual Salary')
                                    ->numeric()
                                    ->prefix('$')
                                    ->step(0.01)
                                    ->columnSpan(1),
                            ]),
                    ])
                    ->columns(1),

                Section::make('Address Information')
                    ->description('Teacher address and location details')
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

                Section::make('Qualifications')
                    ->description('Teacher qualifications and certifications')
                    ->schema([
                        Repeater::make('qualifications')
                            ->label('Qualifications & Certifications')
                            ->schema([
                                TextInput::make('qualification')
                                    ->label('Qualification')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('e.g., PhD Computer Science, MSc Mathematics'),
                            ])
                            ->addActionLabel('Add Qualification')
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['qualification'] ?? null)
                            ->columnSpanFull(),
                    ])
                    ->columns(1),

                Section::make('Profile & Media')
                    ->description('Teacher profile image and additional media')
                    ->schema([
                        FileUpload::make('profile_image')
                            ->label('Profile Image')
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '1:1',
                            ])
                            ->maxSize(2048)
                            ->directory('teacher-profiles')
                            ->columnSpanFull(),
                    ])
                    ->columns(1),

                Section::make('Additional Information')
                    ->description('Optional additional teacher data')
                    ->schema([
                        Textarea::make('notes')
                            ->label('Notes')
                            ->rows(4)
                            ->maxLength(1000)
                            ->placeholder('Additional notes about the teacher...')
                            ->columnSpanFull(),
                        Textarea::make('metadata')
                            ->label('Additional Data (JSON)')
                            ->rows(3)
                            ->maxLength(2000)
                            ->placeholder('Enter JSON data for additional teacher information')
                            ->helperText('Optional: Additional structured data about the teacher')
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}
