<?php

namespace App\Filament\Resources\TrainingCourseAssiginmentResource\Pages;

use App\Filament\Resources\TrainingCourseAssiginmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageTrainingCourseAssiginments extends ManageRecords
{
    protected static string $resource = TrainingCourseAssiginmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
