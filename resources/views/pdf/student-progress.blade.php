<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Progres Fisik</title>
    <style>
        @page { margin: 28px 32px; }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            color: #1f2937;
        }

        /* ===== HEADER ===== */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4px;
        }
        .header-table td { vertical-align: middle; }
        .brand-name {
            font-size: 11px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0;
        }
        .doc-title {
            font-size: 20px;
            font-weight: bold;
            color: #111827;
            margin: 2px 0 0 0;
        }
        .header-line {
            border-bottom: 3px solid #ea580c;
            margin-bottom: 18px;
        }

        /* ===== INFO SISWA ===== */
        .info-box {
            width: 100%;
            background-color: #fff7ed;
            border-left: 4px solid #ea580c;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .info-box td {
            padding: 10px 14px;
            font-size: 12px;
        }
        .info-label {
            color: #6b7280;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0 0 2px 0;
        }
        .info-value {
            font-weight: bold;
            font-size: 13px;
            color: #111827;
            margin: 0;
        }

        /* ===== TABEL DATA ===== */
        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }
        table.data thead th {
            background-color: #dc2626;
            color: #ffffff;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            padding: 9px 6px;
            text-align: center;
            border: 1px solid #dc2626;
        }
        table.data tbody td {
            padding: 8px 6px;
            text-align: center;
            font-size: 12px;
            border: 1px solid #e5e7eb;
        }
        table.data tbody tr:nth-child(even) {
            background-color: #fff7ed;
        }
        table.data tbody td.tanggal {
            font-weight: bold;
            color: #ea580c;
            text-align: left;
            padding-left: 12px;
        }
        table.data tbody td.empty-row {
            padding: 22px 0;
            color: #9ca3af;
            font-style: italic;
        }

        /* ===== FOOTER / TTD ===== */
        .footer-note {
            margin-top: 18px;
            font-size: 10px;
            color: #9ca3af;
        }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td>
                <p class="brand-name">PT. Rumah Binlat Indonesia</p>
                <p class="doc-title">Laporan Progres Fisik Casis</p>
            </td>
        </tr>
    </table>
    <div class="header-line"></div>

    <table class="info-box">
        <tr>
            <td style="width: 40%;">
                <p class="info-label">Nama Siswa</p>
                <p class="info-value">{{ $student->name }}</p>
            </td>
            <td style="width: 30%;">
                <p class="info-label">No. Daftar</p>
                <p class="info-value">{{ $student->registration_number }}</p>
            </td>
            <td style="width: 30%;">
                <p class="info-label">Periode Laporan</p>
                <p class="info-value">
                    {{ \Carbon\Carbon::parse($start_date)->format('d M Y') }}
                    &ndash;
                    {{ \Carbon\Carbon::parse($end_date)->format('d M Y') }}
                </p>
            </td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th style="text-align: left; padding-left: 12px;">Tanggal Tes</th>
                @if(in_array('run_12_min_dist', $metrics)) <th>Lari 12 Menit (M)</th> @endif
                @if(in_array('push_up_reps', $metrics)) <th>Push Up</th> @endif
                @if(in_array('sit_up_reps', $metrics)) <th>Sit Up</th> @endif
                @if(in_array('pull_up_reps', $metrics)) <th>Pull/Chin Up</th> @endif
            </tr>
        </thead>
        <tbody>
            @forelse($records as $row)
            <tr>
                <td class="tanggal">{{ \Carbon\Carbon::parse($row->record_date)->format('d M Y') }}</td>
                @if(in_array('run_12_min_dist', $metrics)) <td>{{ $row->run_12_min_dist ?? '-' }}</td> @endif
                @if(in_array('push_up_reps', $metrics)) <td>{{ $row->push_up_reps ?? '-' }}</td> @endif
                @if(in_array('sit_up_reps', $metrics)) <td>{{ $row->sit_up_reps ?? '-' }}</td> @endif
                @if(in_array('pull_up_reps', $metrics)) <td>{{ $row->pull_up_reps ?? '-' }}</td> @endif
            </tr>
            @empty
            <tr>
                <td class="empty-row" colspan="{{ count($metrics) + 1 }}">
                    Tidak ada data latihan pada rentang tanggal ini.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <p class="footer-note">
        Dicetak pada {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WIB &mdash; PT. Rumah Binlat Indonesia
    </p>

</body>
</html>