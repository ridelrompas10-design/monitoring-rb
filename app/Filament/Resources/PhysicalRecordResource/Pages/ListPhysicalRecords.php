<?php

namespace App\Filament\Resources\PhysicalRecordResource\Pages;

use App\Filament\Resources\PhysicalRecordResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPhysicalRecords extends ListRecords
{
    protected static string $resource = PhysicalRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
