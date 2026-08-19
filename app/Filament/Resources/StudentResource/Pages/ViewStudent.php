<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Resources\StudentResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\CheckboxList;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\PhysicalRecord;
use Carbon\Carbon;

class ViewStudent extends ViewRecord
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Resources\StudentResource\Widgets\StudentPhysicalStats::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('download_pdf')
                ->label('Download PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->form([
                    // Baris 1: Pilihan Waktu Awal
                    Grid::make(2)->schema([
                        Select::make('start_month')
                            ->label('Dari Bulan')
                            ->options([
                                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
                            ])
                            ->default(now()->month) // Otomatis bulan saat ini
                            ->required(),
                            
                        Select::make('start_week')
                            ->label('Minggu Ke-')
                            ->options([
                                1 => 'Minggu 1', 2 => 'Minggu 2', 3 => 'Minggu 3', 
                                4 => 'Minggu 4', 5 => 'Minggu 5'
                            ])
                            ->default(1) // Default ke minggu 1
                            ->required(),
                    ]),

                    // Baris 2: Pilihan Waktu Akhir
                    Grid::make(2)->schema([
                        Select::make('end_month')
                            ->label('Sampai Bulan')
                            ->options([
                                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
                            ])
                            ->default(now()->month)
                            ->required(),
                            
                        Select::make('end_week')
                            ->label('Minggu Ke-')
                            ->options([
                                1 => 'Minggu 1', 2 => 'Minggu 2', 3 => 'Minggu 3', 
                                4 => 'Minggu 4', 5 => 'Minggu 5'
                            ])
                            ->default(4)
                            ->required(),
                    ]),
                        
                    // Pilihan Metrik (Tetap Dipertahankan)
                    CheckboxList::make('metrics')
                        ->label('Pilih Metrik yang Ingin Ditampilkan')
                        ->options([
                            'run_12_min_dist' => 'Lari 12 Menit (Meter)',
                            'push_up_reps' => 'Push Up',
                            'sit_up_reps' => 'Sit Up',
                            'pull_up_reps' => 'Pull/Chin Up',
                        ])
                        ->bulkToggleable()
                        ->required(),
                ])
                ->action(function (array $data, $record) {
                    $year = now()->year;

                    // Menerjemahkan Bulan & Minggu Awal menjadi Tanggal Aktual
                    $startDate = Carbon::create($year, $data['start_month'], 1)
                        ->startOfWeek()
                        ->addWeeks($data['start_week'] - 1)
                        ->startOfDay();

                    // Menerjemahkan Bulan & Minggu Akhir menjadi Tanggal Aktual
                    $endDate = Carbon::create($year, $data['end_month'], 1)
                        ->startOfWeek()
                        ->addWeeks($data['end_week'] - 1)
                        ->endOfWeek() // Ambil hari minggu pada minggu tersebut
                        ->endOfDay();

                    // Ambil data dari database sesuai rentang tanggal yang sudah dihitung
                    $records = PhysicalRecord::where('student_id', $record->id)
                        ->whereBetween('record_date', [$startDate, $endDate])
                        ->orderBy('record_date')
                        ->get();

                    // Kirim data ke file PDF Blade
                    $pdf = Pdf::loadView('pdf.student-progress', [
                        'student' => $record,
                        'records' => $records,
                        'metrics' => $data['metrics'],
                        'start_date' => $startDate->format('Y-m-d'), // Ubah kembali ke string agar tidak error di Blade
                        'end_date' => $endDate->format('Y-m-d'),
                    ]);

                    return response()->streamDownload(
                        fn () => print($pdf->output()), 
                        "Laporan-Fisik-{$record->name}.pdf"
                    );
                }),
                
            Actions\EditAction::make(),
        ];
    }
}