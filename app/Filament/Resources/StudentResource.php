<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentResource\Pages;
use App\Filament\Resources\StudentResource\RelationManagers;
use App\Models\Student;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-o-identification';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Student Information')
                ->description('Enter Basic Information about the Stuedent')
                ->schema([
                    Forms\Components\Group::make([
                        Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                            
                        Forms\Components\TextInput::make('id_card_number')
                        ->label('ID Card Number')
                        ->required()
                        ->unique(Student::class, 'id_card_number', ignoreRecord: true)
                        ->length(14),
                        
                    ])->columns(2),
                    Forms\Components\Group::make([
                        Forms\Components\DatePicker::make('date_of_birth')
                        ->required(),
                        Forms\Components\Select::make('gender')
                            ->required()
                            ->options([
                                'male' => 'Male',
                                'female' => 'Female',
                            ]),
                    ])->columns(2),
                    Forms\Components\FileUpload::make('profile_picture')
                    ->image()
                    ->maxSize(2024)
                    ->disk('public')
                    ->directory('profilePictures')
                    ->required(),
                ])->collapsible(true),

                Forms\Components\Section::make('Contact Information')
                ->description('Enter Contact Information about the Students')
                ->schema([
                    Forms\Components\Group::make([
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->unique(Student::class, 'email', ignoreRecord: true)
                            ->required(),
                        Forms\Components\DateTimePicker::make('email_verified_at'),
                    ])->columns(2),
                    Forms\Components\Group::make([
                        Forms\Components\TextInput::make('address')
                        ->required()
                        ->maxLength(255),
                        
                        Forms\Components\TextInput::make('city')
                        ->required()
                        ->maxLength(255),
                        
                        Forms\Components\TextInput::make('phone_number')
                        ->tel()
                        ->required()
                        ->maxLength(11),

                    ])->columns(2),
                ])->collapsible(true),

                Forms\Components\Section::make('Education Information')
                ->description('Enter Education Information about the Students')
                ->schema([

                    Forms\Components\TextInput::make('school')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\Select::make('grade')
                        ->label('Highest Qualification')
                        ->required()
                        ->options([
                            'primary' => 'Primary',
                            'preparatory' => 'Preparatory',
                            'high_school_diploma' => 'High School Diploma',
                            'bachelors' => 'Bachelors',
                            'masters' => 'Masters',
                            'doctors' => 'Doctors',
                        ]),
                ])->columns(2)->collapsible(true),
                
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('gender')
                    ->searchable(),
                Tables\Columns\TextColumn::make('id_card_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone_number')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('profile_picture')
                    ->searchable()
                    ->disk('public'),
                Tables\Columns\TextColumn::make('city')
                    ->searchable(),
                Tables\Columns\TextColumn::make('grade')
                    ->label('Highest Qualification')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'view' => Pages\ViewStudent::route('/{record}'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
        ];
    }
}
