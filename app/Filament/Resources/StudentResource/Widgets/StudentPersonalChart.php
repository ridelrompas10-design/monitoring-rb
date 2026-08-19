<?php

namespace App\Filament\Resources\StudentResource\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Model;
use App\Models\PhysicalRecord;
use Carbon\Carbon;
use Filament\Support\RawJs;

class StudentPersonalChart extends ChartWidget
{
    protected static ?string $heading = 'Progres Fisik Siswa (Skala 1-10)';
    protected int | string | array $columnSpan = 'full';

    public ?Model $record = null;

    // Default ke 30 Hari Terakhir agar data harian langsung membentuk garis melengkung indah
    public ?string $filter = '30_days'; 

    protected function getFilters(): ?array
    {
        return [
            '30_days' => '30 Hari Terakhir (Harian)',
            'q3'      => 'Kuartal 3 (Jul - Sep)',
            'all'     => 'Satu Tahun Penuh',
        ];
    }

    private function calculateLariPoint($val) {
        if (!$val) return null;
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
        if (!$val) return null;
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

    private function getIndicatorText($point) {
        if ($point === null) return 'Belum ada latihan';
        if ($point <= 3) return 'Belum Baik ⚠️';
        if ($point <= 6) return 'Baik 👍';
        if ($point <= 8) return 'Sangat Baik 🔥';
        return 'Pertahankan! 🏆';
    }

    protected function getData(): array
    {
        $activeFilter = $this->filter;
        
        $labels = [];
        $lariData = []; $pushData = []; $sitData = [];
        $lariStatuses = []; $pushStatuses = []; $sitStatuses = [];

        if ($activeFilter === '30_days') {
            // MODE HARIAN: Ambil 30 hari terakhir agar tes tanggal 10, 11, 12, 13, 14, 15, 16 Ags membentuk garis terhubung
            $startDate = now()->subDays(29)->startOfDay();
            $endDate = now()->endOfDay();

            $records = PhysicalRecord::where('student_id', $this->record->id)
                ->whereBetween('record_date', [$startDate, $endDate])
                ->get();

            $currentDate = $startDate->copy();
            while ($currentDate <= $endDate) {
                $dateStr = $currentDate->format('Y-m-d');
                $labels[] = $currentDate->translatedFormat('d M'); // Contoh: "10 Aug"

                $rec = $records->firstWhere('record_date', $dateStr);

                if ($rec) {
                    $lPt = $this->calculateLariPoint($rec->run_12_min_dist);
                    $pPt = $this->calculateRepsPoint($rec->push_up_reps);
                    $sPt = $this->calculateRepsPoint($rec->sit_up_reps);

                    $lariData[] = $lPt;
                    $pushData[] = $pPt;
                    $sitData[] = $sPt;

                    $lariStatuses[] = $this->getIndicatorText($lPt);
                    $pushStatuses[] = $this->getIndicatorText($pPt);
                    $sitStatuses[] = $this->getIndicatorText($sPt);
                } else {
                    $lariData[] = null;
                    $pushData[] = null;
                    $sitData[] = null;

                    $lariStatuses[] = 'Tidak ada tes';
                    $pushStatuses[] = 'Tidak ada tes';
                    $sitStatuses[] = 'Tidak ada tes';
                }

                $currentDate->addDay();
            }
        } else {
            // MODE MINGGUAN (Untuk Kuartal/Tahunan)
            $mStart = $activeFilter === 'q3' ? 7 : 1;
            $mEnd   = $activeFilter === 'q3' ? 9 : 12;

            $year = now()->year;
            $startDate = Carbon::create($year, $mStart, 1)->startOfWeek();
            $endDate = Carbon::create($year, $mEnd, 1)->endOfMonth()->endOfWeek();

            $records = PhysicalRecord::where('student_id', $this->record->id)
                ->whereBetween('record_date', [$startDate, $endDate])
                ->get();

            $currentDate = $startDate->copy();
            while ($currentDate <= $endDate) {
                $weekStart = $currentDate->copy();
                $weekEnd = $currentDate->copy()->endOfWeek();

                $labels[] = $weekEnd->translatedFormat('M') . " M" . $weekEnd->weekOfMonth;

                $rec = $records->whereBetween('record_date', [$weekStart, $weekEnd])->last();

                if ($rec) {
                    $lPt = $this->calculateLariPoint($rec->run_12_min_dist);
                    $pPt = $this->calculateRepsPoint($rec->push_up_reps);
                    $sPt = $this->calculateRepsPoint($rec->sit_up_reps);

                    $lariData[] = $lPt;
                    $pushData[] = $pPt;
                    $sitData[] = $sPt;

                    $lariStatuses[] = $this->getIndicatorText($lPt);
                    $pushStatuses[] = $this->getIndicatorText($pPt);
                    $sitStatuses[] = $this->getIndicatorText($sPt);
                } else {
                    $lariData[] = null; $pushData[] = null; $sitData[] = null;
                    $lariStatuses[] = 'Tidak ada tes';
                    $pushStatuses[] = 'Tidak ada tes';
                    $sitStatuses[] = 'Tidak ada tes';
                }

                $currentDate->addWeek();
            }
        }

        return [
            'datasets' => [
                [
                    'label' => 'Lari 12 Menit',
                    'data' => $lariData,
                    'customStatuses' => $lariStatuses,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'transparent',
                    'tension' => 0.3,
                    'pointRadius' => 5, // Titik diperbesar agar jelas dilihat Coach
                    'pointHoverRadius' => 8,
                    'spanGaps' => true, // KUNCI UTAMA: Menghubungkan garis antar tanggal tes walau ada hari libur
                ],
                [
                    'label' => 'Push Up',
                    'data' => $pushData,
                    'customStatuses' => $pushStatuses,
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'transparent',
                    'tension' => 0.3,
                    'pointRadius' => 5,
                    'pointHoverRadius' => 8,
                    'spanGaps' => true,
                ],
                [
                    'label' => 'Sit Up',
                    'data' => $sitData,
                    'customStatuses' => $sitStatuses,
                    'borderColor' => '#f59e0b',
                    'backgroundColor' => 'transparent',
                    'tension' => 0.3,
                    'pointRadius' => 5,
                    'pointHoverRadius' => 8,
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

    protected function getOptions(): RawJs
    {
        return RawJs::make(<<<'JS'
        {
            scales: {
                y: {
                    min: 0,
                    max: 10,
                    ticks: {
                        stepSize: 2 
                    },
                    grid: {
                        color: 'rgba(255, 255, 255, 0.08)' // Garis pandu horisontal agar mudah trace nilai Y
                    }
                },
                x: {
                    ticks: {
                        maxRotation: 45,
                        minRotation: 45
                    },
                    grid: {
                        color: 'rgba(255, 255, 255, 0.03)'
                    }
                }
            },
            plugins: {
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        afterLabel: function(context) {
                            let status = context.dataset.customStatuses[context.dataIndex];
                            return 'Status: ' + status;
                        }
                    }
                }
            }
        }
        JS);
    }
}