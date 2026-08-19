<?php

namespace App\Filament\Widgets;

use App\Models\Student;
use App\Models\Training;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStats extends BaseWidget
{
    protected static ?int $sort = 1; // Paling atas

    protected function getStats(): array
    {
        return [
            Stat::make('Total Siswa', Student::count())
                ->description('Jumlah seluruh casis')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),
                
            Stat::make('Siswa Perlu Perhatian', Student::where('is_watchlist', true)->count())
                ->description('Lihat di menu Data Siswa')
                ->descriptionIcon('heroicon-m-exclamation-circle')
                ->color('danger'),
                
            Stat::make('Program Latihan', Training::count())
                ->description('Bela diri, Renang, dll')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('info'),
        ];
    }
}