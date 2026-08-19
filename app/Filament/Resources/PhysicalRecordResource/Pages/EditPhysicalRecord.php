<?php

namespace App\Filament\Resources\PhysicalRecordResource\Pages;

use App\Filament\Resources\PhysicalRecordResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPhysicalRecord extends EditRecord
{
    protected static string $resource = PhysicalRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
