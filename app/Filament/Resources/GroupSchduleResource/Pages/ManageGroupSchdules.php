<?php

namespace App\Filament\Resources\GroupSchduleResource\Pages;

use App\Filament\Resources\GroupSchduleResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageGroupSchdules extends ManageRecords
{
    protected static string $resource = GroupSchduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
