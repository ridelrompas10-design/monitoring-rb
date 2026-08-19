<?php

namespace App\Filament\Pages;

use App\Models\Student;
use App\Models\PhysicalRecord;
use Filament\Pages\Page;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Columns\Layout\View;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;

class RekamFisikHarian extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'Rekam Fisik';
    
    protected static string $view = 'filament.pages.rekam-fisik-harian';
    protected static ?string $title = 'Input Progres Fisik';

    public function table(Table $table): Table
    {
        return $table
            ->query(Student::query()->with('physicalRecords'))
            ->contentGrid([
                'md' => 2,
                'xl' => 3, 
            ])
            ->defaultPaginationPageOption(12)
            ->columns([
                Stack::make([
                    // Fitur Pencarian Tersembunyi (Nama & Nomor Pendaftaran)
                    TextColumn::make('name')
                        ->searchable()
                        ->extraAttributes(['class' => 'hidden']),
                        
                    TextColumn::make('registration_number')
                        ->searchable()
                        ->extraAttributes(['class' => 'hidden']),

                    // Pemanggilan Tampilan Kartu Estetik
                    View::make('filament.components.student-card'),
                ])
            ])
            ->actions([
                Action::make('inputData')
                    ->label('Input Hasil')
                    ->modalHeading(fn (Student $record) => $record->name)
                    ->modalDescription('Catat hasil tes fisik hari ini secara lengkap.')
                    ->modalSubmitActionLabel('Simpan hasil hari ini')
                    ->form([
                        DatePicker::make('record_date')
                            ->label('Tanggal tes')
                            ->default(now())
                            ->required(),
                            
                        Section::make('Standar Samapta')
                            ->description('Penilaian fisik utama')
                            ->schema([
                                Grid::make(2)->schema([
                                    TextInput::make('run_12_min_dist')
                                        ->label('Lari 12 menit')
                                        ->numeric()
                                        ->suffix('meter')
                                        ->required(),
                                        
                                    TextInput::make('push_up_reps')
                                        ->label('Push up')
                                        ->numeric()
                                        ->suffix('rep')
                                        ->required(),
                                        
                                    TextInput::make('sit_up_reps')
                                        ->label('Sit up')
                                        ->numeric()
                                        ->suffix('rep')
                                        ->default(0), // Set 0 jika tidak dites
                                        
                                    TextInput::make('pull_up_reps')
                                        ->label('Pull up / Chining')
                                        ->numeric()
                                        ->suffix('rep')
                                        ->default(0), // Set 0 jika tidak dites
                                ]),
                            ]),

                        // Fitur Latihan Ekstra Dinamis (Tak Terbatas)
                        Repeater::make('extra_exercises')
                            ->label('Latihan Tambahan')
                            ->addActionLabel('+ Tambah Latihan Baru')
                            ->schema([
                                TextInput::make('nama_latihan')
                                    ->placeholder('Contoh: Shuttle Run')
                                    ->required(),
                                TextInput::make('hasil')
                                    ->placeholder('Contoh: 16 detik / 50 meter')
                                    ->required(),
                            ])
                            ->columns(2)
                            ->collapsible()
                            ->defaultItems(0), // Tidak muncul jika tidak ditekan
                    ])
                    ->action(function (array $data, Student $record): void {
                        PhysicalRecord::updateOrCreate(
                            [
                                'student_id' => $record->id,
                                'record_date' => $data['record_date'],
                            ],
                            [
                                'run_12_min_dist' => $data['run_12_min_dist'],
                                'push_up_reps' => $data['push_up_reps'],
                                'sit_up_reps' => $data['sit_up_reps'],
                                'pull_up_reps' => $data['pull_up_reps'],
                                'extra_exercises' => $data['extra_exercises'] ?? null,
                            ]
                        );
                    })
                    // Membuat seluruh area kartu bisa di-klik
                    ->extraAttributes(['class' => 'w-full']),
            ]);
    }
}