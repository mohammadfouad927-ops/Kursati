<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProgramCourseResource\Pages;
use App\Filament\Resources\ProgramCourseResource\RelationManagers;
use App\Models\ProgramCourse;
use App\Models\Subject;
use App\Models\Training_Course;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProgramCourseResource extends Resource
{
    protected static ?string $model = ProgramCourse::class;

    protected static ?string $navigationIcon = 'heroicon-o-queue-list';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('subject_id')
                    ->required()
                    ->options(
                        fn() => Subject::latest()
                            ->limit(10)
                            ->pluck('name','id')
                            ->toArray()
                    )
                    ->searchable()
                    ->getSearchResultsUsing(fn($search) => Subject::where('name','like',"%{$search}%")->pluck('name','id')->toArray())
                    ->getOptionLabelsUsing(fn($value) => Subject::find($value)->name),
                Forms\Components\Select::make('training_course_id')
                    ->required()
                    ->options(
                        fn() => Training_Course::latest()
                            ->limit(10)
                            ->pluck('name','id')
                            ->toArray()
                    )
                    ->searchable()
                    ->getSearchResultsUsing(fn($search) => Training_Course::where('name','like',"%{$search}%")->pluck('name','id')->toArray())
                    ->getOptionLabelsUsing(fn($value) => Training_Course::find($value)->name),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('trainingCourse.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('subject.name')
                    ->sortable(),
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
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageProgramCourses::route('/'),
        ];
    }
}
