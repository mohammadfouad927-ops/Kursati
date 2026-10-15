<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GroupCourseAssignmentResource\Pages;
use App\Models\GroupCourseAssignment;
use App\Models\Group;
use App\Models\Subject;
use App\Models\Instructor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GroupCourseAssignmentResource extends Resource
{
    protected static ?string $model = GroupCourseAssignment::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('group_id')
                    ->required()
                    ->label('Group')
                    ->searchable()
                    ->options(fn() :array => Group::with('trainingCourse')
                                            ->latest()
                                            ->limit(5)
                                            ->get()
                                            ->mapWithKeys(fn($group) => [$group->id => $group->trainingCourse?->name . ' - ' . $group->name])->toArray()
                            )
                    ->getSearchResultsUsing(fn(string $search): array => Group::query()
                                                                        ->with('trainingCourse')
                                                                        ->where('name', 'like', "%{$search}%")
                                                                        ->limit(10)
                                                                        ->get() // if you delete this line, it will not work because you need to get the collection of groups to map them to an array
                                                                        ->mapWithKeys(fn($group) => [$group->id => $group->trainingCourse?->name . ' - ' . $group->name])->toArray())
                    ->getOptionLabelUsing(fn($value): ?string => ($group = Group::with('trainingCourse')->find($value))? "{$group->trainingCourse?->name} - {$group->name}" : null),
                    Forms\Components\Select::make('subject_id')
                    ->label('Subject')
                    ->required()
                    ->searchable()
                    ->relationship(
                        name: 'subject',
                        titleAttribute: 'name'
                    ),
                    Forms\Components\Select::make('instructor_id')
                    ->required()
                    ->label('Instructor')
                    ->relationship(
                            name: 'Instructor',    // Relationship method name on your Model
                            titleAttribute: 'name' // Column to display
                        )
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
                Tables\Columns\TextColumn::make('subject.name')
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
            'index' => Pages\ManageGroupCourseAssignments::route('/'),
        ];
    }
}
