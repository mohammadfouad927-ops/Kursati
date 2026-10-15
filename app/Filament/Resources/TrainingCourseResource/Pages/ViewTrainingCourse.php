<?php

namespace App\Filament\Resources\TrainingCourseResource\Pages;

use App\Filament\Resources\TrainingCourseResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTrainingCourse extends ViewRecord
{
    protected static string $resource = TrainingCourseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
