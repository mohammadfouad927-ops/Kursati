<?php

namespace App\Filament\Resources\ProgramCourseResource\Pages;

use App\Filament\Resources\ProgramCourseResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageProgramCourses extends ManageRecords
{
    protected static string $resource = ProgramCourseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
