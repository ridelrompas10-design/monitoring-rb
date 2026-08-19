<?php

namespace App\Filament\Widgets;

use App\Models\PhysicalRecord;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;
use Filament\Support\RawJs; 

class StudentProgressChart extends ChartWidget
{
    protected static ?string $heading = 'Progres Fisik (Skala 1-10)'; 
    
    protected static ?int $sort = 2; 

    protected int | string | array $columnSpan = [
        'default' => 'full',
        'md' => 1, 
    ];

    public ?string $filter = 'q3'; 

    protected function getFilters(): ?array
    {
        return [
            'all' => 'Satu Tahun Penuh',
            'h1'  => 'Semester 1 (Jan - Jun)',
            'h2'  => 'Semester 2 (Jul - Des)',
            'q1'  => 'Kuartal 1 (Jan - Mar)',
            'q2'  => 'Kuartal 2 (Apr - Jun)',
            'q3'  => 'Kuartal 3 (Jul - Sep)',
            'q4'  => 'Kuartal 4 (Okt - Des)',
        ];
    }

    // --- FUNGSI KONVERSI NILAI RATA-RATA KE POIN (0-10) ---
    private function calculateLariPoint($val) {
        if (!$val) return 0;
        if ($val < 1600) return 1;
        if ($val < 1800) return 2;
        if ($val < 2000) return 3; 
        if ($val < 2200) return 4;
        if ($val < 2400) return 5;
        if ($val < 2600) return 6; 
        if ($val < 2800) return 7;
        if ($val < 3000) return 8; 
        if ($val < 3200) return 9;
        return 10;                 
    }

    private function calculateRepsPoint($val) {
        if (!$val) return 0;
        if ($val < 10) return 1;
        if ($val < 15) return 2;
        if ($val < 20) return 3; 
        if ($val < 25) return 4;
        if ($val < 30) return 5;
        if ($val < 35) return 6; 
        if ($val < 40) return 7;
        if ($val < 45) return 8; 
        if ($val < 50) return 9;
        return 10;               
    }

    protected function getData(): array
    {
        $activeFilter = $this->filter;
        $mStart = 1; $mEnd = 12;

        switch ($activeFilter) {
            case 'h1': $mStart = 1; $mEnd = 6; break;
            case 'h2': $mStart = 7; $mEnd = 12; break;
            case 'q1': $mStart = 1; $mEnd = 3; break;
            case 'q2': $mStart = 4; $mEnd = 6; break;
            case 'q3': $mStart = 7; $mEnd = 9; break;
            case 'q4': $mStart = 10; $mEnd = 12; break;
        }

        $year = now()->year;
        $startDate = Carbon::create($year, $mStart, 1)->startOfWeek();
        $endDate = Carbon::create($year, $mEnd, 1)->endOfMonth()->endOfWeek();

        $labels = [];
        $lariData = [];
        $pushUpData = [];

        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            $weekStart = $currentDate->copy();
            $weekEnd = $currentDate->copy()->endOfWeek();

            $monthName = $weekEnd->translatedFormat('M'); 
            $weekOfMonth = ceil($weekEnd->day / 7);
            $labels[] = "{$monthName} - M{$weekOfMonth}";

            // Tarik rata-rata dari database
            $weeklyRecord = PhysicalRecord::whereBetween('record_date', [
                $weekStart->format('Y-m-d 00:00:00'),
                $weekEnd->format('Y-m-d 23:59:59')
            ])->selectRaw('AVG(run_12_min_dist) as avg_lari, AVG(push_up_reps) as avg_push')->first();

            $hasData = PhysicalRecord::whereBetween('record_date', [
                $weekStart->format('Y-m-d 00:00:00'),
                $weekEnd->format('Y-m-d 23:59:59')
            ])->exists();

            // Konversi nilai asli menjadi poin 0-10
            if ($hasData) {
                $lariData[] = $this->calculateLariPoint($weeklyRecord->avg_lari);
                $pushUpData[] = $this->calculateRepsPoint($weeklyRecord->avg_push);
            } else {
                // Biarkan kosong (null) agar grafik putus secara elegan jika tidak ada jadwal latihan
                $lariData[] = null;
                $pushUpData[] = null;
            }

            $currentDate->addWeek();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Skala Lari 12 Menit',
                    'data' => $lariData,
                    'borderColor' => '#10b981', 
                    'backgroundColor' => 'transparent', 
                    'tension' => 0.4, 
                    'spanGaps' => true, // Menghubungkan titik jika ada 1 minggu yang bolong
                ],
                [
                    'label' => 'Skala Push Up',
                    'data' => $pushUpData,
                    'borderColor' => '#3b82f6', 
                    'backgroundColor' => 'transparent', 
                    'tension' => 0.4,
                    'spanGaps' => true,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    // UI/UX FIX: Grafik dikunci di angka 0 - 10 dengan tampilan paling bersih
    protected function getOptions(): RawJs
    {
        return RawJs::make(<<<'JS'
        {
            scales: {
                y: {
                    min: 0,
                    max: 10,
                    ticks: {
                        stepSize: 2 // Menampilkan angka kelipatan 2 (0, 2, 4, 6, 8, 10) agar sumbu Y lega
                    },
                    grid: {
                        color: 'rgba(255, 255, 255, 0.05)' // Garis belakang sangat tipis agar tidak mengganggu
                    }
                },
                x: {
                    ticks: {
                        maxRotation: 45, // Tulisan miring di HP
                        minRotation: 45
                    },
                    grid: {
                        display: false // Menghilangkan garis vertikal di belakang agar terlihat bersih
                    }
                }
            },
            plugins: {
                tooltip: {
                    mode: 'index',
                    intersect: false
                }
            }
        }
        JS);
    }
}