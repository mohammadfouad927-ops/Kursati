<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TrainingCourseAssiginmentResource\Pages;
use App\Filament\Resources\TrainingCourseAssiginmentResource\RelationManagers;
use App\Models\TrainingCourseAssiginment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\Group;
use App\Models\Instructor;
use App\Models\ProgramCourse;
use Filament\Forms\Get;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TrainingCourseAssiginmentResource extends Resource
{
    protected static ?string $model = TrainingCourseAssiginment::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('group_id')
                    ->required()
                    ->label('Group')
                    ->reactive()
                    ->afterStateUpdated(fn (Forms\Set $set) => $set('program_course_id',null))
                    ->options(
                        Group::with('trainingCourse')
                            ->latest()
                            ->limit(10)
                            ->get()
                            ->mapWithKeys(
                                fn($group) => [$group->id => $group->trainingCourse?->name . ' - ' . $group->name]
                            )
                    )
                    ->searchable()
                    ->getSearchResultsUsing(fn(string $search): array => Group::with('trainingCourse')
                                                                                ->where('name','like','%'.$search.'%')
                                                                                ->orWhereHas('trainingCourse', function ($query) use ($search){
                                                                                    $query->where('name','like','%'.$search.'%');
                                                                                })
                                                                                ->limit(10)
                                                                                ->get()
                                                                                ->mapWithKeys(
                                                                                    fn($group) => [$group->id => $group->trainingCourse?->name . ' - ' . $group->name]
                                                                                )
                                                                                ->toArray())
                    ->getOptionLabelUsing(fn($value): ?string => ($group = Group::with('trainingCourse')->find($value))? $group->trainingCourse->name . ' - ' . $group->name : null),
                Forms\Components\Select::make('instructor_id')
                    ->required()
                    ->label('Instructor')
                    ->disabled(fn (Get $get): bool => !$get('program_course_id'))
                    ->options(
                        function(Get $get): array {
                            $programCourseId = $get('program_course_id');

                            if(!$programCourseId) {
                                return [];
                            }
                            $programCourse = ProgramCourse::find($programCourseId);

                            if(!$programCourse) {
                                return [];
                            }

                            return Instructor::with('subjects')
                                ->latest()
                                ->whereHas('subjects', function ($query) use ($programCourse){
                                    $query->where('subjects.id', $programCourse->subject_id);
                                })
                                ->limit(10)
                                ->pluck('name', 'id')->toArray();
                        }
                    )
                    ->searchable(),
                Forms\Components\Select::make('program_course_id')
                    ->required()
                    ->label('Program Course')
                    ->reactive()
                    ->afterStateUpdated(fn (Forms\Set $set) => $set('instructor_id', null))
                    ->disabled(fn (Get $get): bool => !$get('group_id'))
                    ->options( function(Get $get): array {

                        $groupId = $get('group_id');
                        if(!$groupId) {
                            return [];
                        }

                        $group = Group::with('trainingCourse')->find($groupId);

                        if(!$group) {
                            return [];
                        }
                        return ProgramCourse::latest()
                            ->doesntHave('trainingCourseAssiginments')
                            ->where('training_course_id', $group->training_course_id)
                            ->limit(10)
                            ->get()
                            ->mapWithKeys(
                            fn($programCourse) => [$programCourse->id => $programCourse->subject->name]
                            )->toArray();
                        })
                    ->searchable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('group.trainingCourse.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('group.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('programCourse.subject.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('instructor.name')
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
            'index' => Pages\ManageTrainingCourseAssiginments::route('/'),
        ];
    }
}
