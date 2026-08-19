<?php

namespace App\Filament\Resources\StudentResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Model;
use App\Models\PhysicalRecord;

class StudentPhysicalStats extends BaseWidget
{
    // Menerima data siswa yang sedang dibuka
    public ?Model $record = null;

    // Membagi tampilan menjadi 2 kolom (2 kotak di atas, 2 di bawah)
    protected function getColumns(): int
    {
        return 2;
    }

    protected function getStats(): array
    {
        // Ambil riwayat tes siswa ini, urutkan dari terlama ke terbaru
        $records = PhysicalRecord::where('student_id', $this->record->id)
            ->orderBy('record_date', 'asc')
            ->get();

        $latest = $records->last();
        $previous = $records->count() > 1 ? $records[$records->count() - 2] : null;

        // Jika siswa belum punya data sama sekali
        if (!$latest) {
            return [
                Stat::make('Lari 12 menit', '- meter')->description('Belum ada data'),
                Stat::make('Push up', '- kali')->description('Belum ada data'),
                Stat::make('Sit up', '- kali')->description('Belum ada data'),
                Stat::make('Pull up', '- kali')->description('Belum ada data'),
            ];
        }

        // Fungsi kecil untuk menghitung tren naik/turun secara otomatis
        $getTrend = function ($current, $prev, $suffix) {
            $diff = $prev !== null ? ($current - $prev) : 0;
            $color = $diff >= 0 ? 'success' : 'danger'; // Hijau jika naik/tetap, Merah jika turun
            $icon = $diff >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down';
            $sign = $diff > 0 ? '+' : '';
            
            return [
                'desc' => $diff !== 0 ? "{$sign}{$diff} {$suffix}" : 'Stabil',
                'color' => $color,
                'icon' => $diff !== 0 ? $icon : 'heroicon-m-minus'
            ];
        };

        // Hitung tren masing-masing latihan
        $lariInfo = $getTrend($latest->run_12_min_dist, $previous?->run_12_min_dist, 'm');
        $pushInfo = $getTrend($latest->push_up_reps, $previous?->push_up_reps, 'kali');
        $sitInfo  = $getTrend($latest->sit_up_reps, $previous?->sit_up_reps, 'kali');
        $pullInfo = $getTrend($latest->pull_up_reps, $previous?->pull_up_reps, 'kali');

        // Kembalikan 4 kotak estetik sesuai desain klien
        return [
            Stat::make('Lari 12 menit', $latest->run_12_min_dist . ' meter')
                ->description($lariInfo['desc'])
                ->descriptionIcon($lariInfo['icon'])
                ->color($lariInfo['color'])
                ->chart($records->pluck('run_12_min_dist')->toArray()), // Gambar grafik garis

            Stat::make('Push up', $latest->push_up_reps . ' kali')
                ->description($pushInfo['desc'])
                ->descriptionIcon($pushInfo['icon'])
                ->color($pushInfo['color'])
                ->chart($records->pluck('push_up_reps')->toArray()),

            Stat::make('Sit up', $latest->sit_up_reps . ' kali')
                ->description($sitInfo['desc'])
                ->descriptionIcon($sitInfo['icon'])
                ->color($sitInfo['color'])
                ->chart($records->pluck('sit_up_reps')->toArray()),

            Stat::make('Pull up', $latest->pull_up_reps . ' kali')
                ->description($pullInfo['desc'])
                ->descriptionIcon($pullInfo['icon'])
                ->color($pullInfo['color'])
                ->chart($records->pluck('pull_up_reps')->toArray()),
        ];
    }
}