<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GroupSchduleResource\Pages;
use App\Filament\Resources\GroupSchduleResource\RelationManagers;
use App\Models\GroupSchdule;
use App\Models\Group;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GroupSchduleResource extends Resource
{
    protected static ?string $model = GroupSchdule::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('group_id')
                    ->required()
                    ->label('Group')
                    ->options(
                        Group::with('trainingCourse')
                            ->latest()
                            ->limit(5)
                            ->get()
                            ->mapWithKeys(
                                fn($group) => [$group->id => $group->trainingCourse?->name . ' - ' . $group->name]
                            )
                            ->toArray()
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
                Forms\Components\Select::make('day')
                    ->required()
                    ->options([
                        'Saturday' => 'Saturday',
                        'Sunday' => 'Sunday',
                        'Monday' => 'Monday',
                        'Tuesday' => 'Tuesday',
                        'Wednesday' => 'Wednesday',
                        'Thursday' => 'Thursday',
                        'Friday' => 'Friday',
                    ]),
                Forms\Components\TimePicker::make('start_time')
                    ->required()
                    ->seconds(false)
                    ->beforeOrEqual('end_time'),
                Forms\Components\TimePicker::make('end_time')
                    ->required()
                    ->seconds(false)
                    ->afterOrEqual('start_time'),
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
                Tables\Columns\TextColumn::make('group.branch.name')
                    ->default('Online')
                    ->sortable(),
                Tables\Columns\TextColumn::make('day')
                    ->searchable(),
                Tables\Columns\TextColumn::make('start_time')
                    ->time(format: 'h:i A')
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_time')
                    ->time(format: 'h:i A')
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
            'index' => Pages\ManageGroupSchdules::route('/'),
        ];
    }
}
