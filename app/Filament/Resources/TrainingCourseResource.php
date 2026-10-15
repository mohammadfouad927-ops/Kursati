<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TrainingCourseResource\Pages;
use App\Models\Training_Course;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Resources\TrainingCourseResource\RelationManagers;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TrainingCourseResource extends Resource
{
    protected static ?string $model = Training_Course::class;
    
    protected static ?string $navigationLabel  = 'Training Management';
    
    protected static ?string $modelLabel = 'Training Course';

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Training Course Information')
                ->description('Enter Basic Information about the Training Course')
                ->schema([
                    Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                        Forms\Components\TextInput::make('price')
                        ->required()
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(999999.99),
                    ])->columns(2),
                    Forms\Components\RichEditor::make('description')
                    ->required()
                ])->collapsible(true),
                Forms\Components\Section::make('Training Course Dates')
                ->description('The start and end dates of the training course')
                ->schema([
                    Forms\Components\DatePicker::make('start_date')
                    ->required()
                    ->before('end_date'),
                    Forms\Components\DatePicker::make('end_date')
                    ->required()
                    ->after('start_date')
                ])->columns(2)->collapsible(true),
                Forms\Components\Toggle::make('is_active')
                ->label('Is Active?')
                ->required()
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('start_date')->date()->sortable(),
                Tables\Columns\TextColumn::make('end_date')->date()->sortable(),
                Tables\Columns\TextColumn::make('price')->money('egp', true)->sortable(),
                // Tables\Columns\TextColumn::make('subjects_count')->counts('subjects')->label('Subjects Count'),
                Tables\Columns\TextColumn::make('subjects.name')
                    ->listWithLineBreaks()
                    ->limitList(2)
                    ->expandableLimitedList()
                    ->badge()
                    ->label('Course Titles'),
                Tables\Columns\TextColumn::make('description')->limit(50)->wrap(),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
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
            RelationManagers\SubjectsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTrainingCourses::route('/'),
            'create' => Pages\CreateTrainingCourse::route('/create'),
            'view' => Pages\ViewTrainingCourse::route('/{record}'),
            'edit' => Pages\EditTrainingCourse::route('/{record}/edit'),
        ];
    }
}
