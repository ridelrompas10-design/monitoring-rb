<?php

namespace App\Filament\Widgets;

use App\Models\Student;
use App\Filament\Resources\StudentResource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Columns\TextColumn;

class DashboardStudents extends BaseWidget
{
    protected static ?int $sort = 3; // Di bawah grafik utama
    
    protected int | string | array $columnSpan = [
        'default' => 'full',
        'md' => 1,
    ]; // lebar penuh 

    public function table(Table $table): Table
    {
        return $table
            ->query(
                // Mengambil 5 siswa terbaru
                Student::query()->latest()->limit(5)
            )
            ->columns([
                TextColumn::make('registration_number')
                    ->label('No. Daftar'),
                    
                TextColumn::make('name')
                    ->label('Nama Lengkap'),
                    
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Aktif' => 'success',
                        'Lulus' => 'info',
                        'Keluar' => 'danger',
                        default => 'secondary',
                    }),
            ])
            ->paginated(false) // Hilangkan pagination karena cuma 5
            ->heading('5 Siswa Terdaftar Terbaru')
            ->description('Untuk melihat seluruh siswa dan menambah data, silakan ke menu Data Siswa di sidebar.')
            ->actions([
                Tables\Actions\Action::make('Lihat Rekapan')
                    ->icon('heroicon-m-eye')
                    // Ini akan mengarahkan ke halaman View khusus siswa tersebut
                    ->url(fn (Student $record): string => StudentResource::getUrl('view', ['record' => $record]))
            ]);
    }
}