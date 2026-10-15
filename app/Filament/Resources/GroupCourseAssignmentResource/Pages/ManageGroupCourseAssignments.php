<?php

namespace App\Filament\Resources\GroupCourseAssignmentResource\Pages;

use App\Filament\Resources\GroupCourseAssignmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageGroupCourseAssignments extends ManageRecords
{
    protected static string $resource = GroupCourseAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
