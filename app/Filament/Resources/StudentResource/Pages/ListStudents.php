<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Resources\StudentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms\Components\FileUpload;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;
use App\Imports\ExcelStudentsImport;

class ListStudents extends ListRecords
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('import_excel')
                ->label('Import Excel')
                ->color('success')
                ->icon('heroicon-o-document-arrow-up')
                ->form([
                    FileUpload::make('file')
                        ->label('Upload File Excel (.xlsx)')
                        ->disk('local') // Menyimpan file sementara dengan aman
                        ->directory('imports')
                        ->acceptedFileTypes([
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 
                            'application/vnd.ms-excel'
                        ])
                        ->required(),
                ])
                ->action(function (array $data) {
                $filePath = Storage::disk('local')->path($data['file']);
                
                // GUNAKAN NAMA CLASS YANG BARU DI SINI:
                Excel::import(new ExcelStudentsImport, $filePath);
                
                Storage::disk('local')->delete($data['file']);

                Notification::make()
                    ->title('Data siswa berhasil di-import!')
                    ->success()
                    ->send();
            }),
                
            Actions\CreateAction::make(),
        ];
    }
}