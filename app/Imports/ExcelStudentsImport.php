<?php

namespace App\Imports;

use App\Models\Student;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ExcelStudentsImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Lewati baris yang namanya kosong
            if (!isset($row['nama']) || trim($row['nama']) === '') {
                continue;
            }

            // PERBAIKAN 1: Tambahkan 'lp' untuk mengantisipasi hilangnya garis miring (L/P)
            $genderInput = $row['lp'] ?? $row['l_p'] ?? $row['jenis_kelamin'] ?? 'L';
            $genderCode = strtoupper(substr(trim($genderInput), 0, 1)); 
            $gender = ($genderCode === 'P') ? 'P' : 'L';

            // PERBAIKAN 2: Gunakan updateOrCreate agar data tidak menjadi ganda (double)
            Student::updateOrCreate(
                // Sistem akan mencari siswa berdasarkan No. Daftar dan Nama ini
                [
                    'registration_number' => $row['no_daftar'] ?? '-',
                    'name'                => $row['nama'],
                ],
                // Jika ketemu, sistem hanya akan memperbarui data di bawah ini:
                [
                    'gender'              => $gender,
                    'status'              => 'Aktif',
                    'is_watchlist'        => false,
                ]
            );
        }
    }
}