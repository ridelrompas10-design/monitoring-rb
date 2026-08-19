<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Resources\StudentResource;
use App\Models\Student;
use App\Models\PhysicalRecord;
use Filament\Resources\Pages\Page;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Columns\Layout\View;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class InputProgres extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = StudentResource::class;

    protected static string $view = 'filament.resources.student-resource.pages.input-progres';
    
    protected static ?string $title = 'Input Progres Fisik';
    
    // Menambahkan ikon khusus di menu bar
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    public function table(Table $table): Table
    {
        return $table
            // Mengambil semua data siswa
            ->query(Student::query()->with('physicalRecords'))
            // Mengubah tampilan tabel menjadi Grid (Kartu)
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
            ])
            // Menyembunyikan header tabel bawaan
            ->defaultPaginationPageOption(12)
            ->columns([
                // Memanggil file Blade kustom untuk desain kartunya
                View::make('filament.components.student-card'),
            ])
            ->actions([
                // Aksi Modal Pop-up saat kartu diklik
                Action::make('inputData')
                    ->label('Input Hasil')
                    ->modalHeading(fn (Student $record) => $record->name)
                    ->modalDescription(fn (Student $record) => $record->registration_number ?? 'Siswa Aktif')
                    ->modalSubmitActionLabel('Simpan hasil hari ini')
                    ->form([
                        DatePicker::make('record_date')
                            ->label('Tanggal tes')
                            ->default(now())
                            ->required(),
                        
                        TextInput::make('run_12_min_dist')
                            ->label('Lari 12 menit (meter)')
                            ->numeric()
                            ->suffix('meter')
                            ->required(),
                            
                        TextInput::make('push_up_reps')
                            ->label('Push up (repetisi)')
                            ->numeric()
                            ->suffix('rep')
                            ->required(),
                            
                        Textarea::make('notes')
                            ->label('Catatan coach (opsional)')
                            ->placeholder('Contoh: napas masih terengah di menit ke-8...')
                            ->rows(3),
                    ])
                    ->action(function (array $data, Student $record): void {
                        // Logika menyimpan data ke database
                        PhysicalRecord::updateOrCreate(
                            [
                                'student_id' => $record->id,
                                'record_date' => $data['record_date'],
                            ],
                            [
                                'run_12_min_dist' => $data['run_12_min_dist'],
                                'push_up_reps' => $data['push_up_reps'],
                                'notes' => $data['notes'] ?? null,
                            ]
                        );
                    })
                    // Membuat seluruh area kartu bisa diklik
                    ->extraAttributes(['class' => 'w-full']),
            ]);
    }
}