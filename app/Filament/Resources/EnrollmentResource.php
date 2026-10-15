<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EnrollmentResource\Pages;
use App\Filament\Resources\EnrollmentResource\RelationManagers;
use App\Enums\PaymentStatus;
use App\Models\Enrollment;
use App\Models\Group;
use App\Models\Student;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EnrollmentResource extends Resource
{
    protected static ?string $model = Enrollment::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-plus';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('student_id')
                    ->required()
                    ->label('Student')
                    ->options(
                        Student::limit(10)
                        ->pluck('name','id')
                        ->toArray()
                    )
                    ->searchable()
                    ->getSearchResultsUsing(fn($search): array => Student::where('name','like','%'.$search.'%')->pluck('name','id')->toArray())
                    ->getOptionLabelUsing(fn($value):?string =>  Student::find($value)?->name),
                Forms\Components\Select::make('group_id')
                    ->required()
                    ->options(
                        Group::with('trainingCourse:id,name,start_date')
                        ->wherehas('trainingCourse', fn($query) => $query->where('start_date','>=', now()->subdays(10)->startOfDay()))
                        ->latest()
                        ->limit(10)
                        ->get(['name','id','training_course_id'])
                        ->mapWithKeys(fn($group) :array => [$group->id => $group->trainingCourse->name . ' - ' . $group->name])->toArray()
                    )
                    ->searchable()
                    ->getSearchResultsUsing(
                        fn($search): array => 
                        Group::with('trainingCourse:id,name')
                        ->where('name','like',"%{$search}%")
                        ->orWhereHas('trainingCourse',
                         fn($query) => $query->where('name','like',"%{$search}%")
                         )
                        ->limit(10)
                        ->get(['id','name','training_course_id'])
                        ->mapWithKeys(fn($group): array => [$group->id => $group->trainingCourse->name . ' - ' . $group->name])
                        ->toArray())
                        ->getOptionLabelUsing(fn($value): ?string => ($group = Group::with('trainingCourse')->find($value))? $group->trainingCourse->name . ' - ' . $group->name : null),
                Forms\Components\Select::make('payment_status')
                    ->options(PaymentStatus::class)
                    ->required()
                    ->default(PaymentStatus::PENDING),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('student.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('group.trainingCourse.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('group.trainingCourse.price')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('group.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_status')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_paid')
                    ->label("Total Paid")
                    ->money('EGP')
                    ->getStateUsing(function(Enrollment $record): float{
                        return (float) $record->paymentDetails()
                            ->where('status', PaymentStatus::APPROVED)
                            ->sum('amount');
                    }),
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
            'index' => Pages\ManageEnrollments::route('/'),
        ];
    }
}
