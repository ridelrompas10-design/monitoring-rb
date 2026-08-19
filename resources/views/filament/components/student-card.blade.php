@php
    // Logika mengambil data hari ini dan sebelumnya
    $today = \Carbon\Carbon::today()->format('Y-m-d');
    $records = $getRecord()->physicalRecords->sortByDesc('record_date');
    
    $todayRecord = $records->firstWhere('record_date', '>=', $today);
    $lastRecord = $records->where('record_date', '<', $today)->first();
    
    $isFilledToday = $todayRecord ? true : false;
@endphp

<div class="relative flex flex-col p-4 rounded-xl bg-gray-900 border border-gray-800 hover:border-primary-500 transition-colors shadow-sm w-full cursor-pointer overflow-hidden group">
    
    {{-- Garis aksen di atas --}}
    <div class="absolute top-0 left-0 w-full flex space-x-1 px-4">
        @for($i=0; $i<6; $i++)
            <div class="h-1 flex-1 {{ $isFilledToday ? 'bg-green-500' : 'bg-yellow-600' }} rounded-b-sm"></div>
        @endfor
    </div>

    {{-- Info Siswa --}}
    <div class="mt-4 mb-4">
        <h3 class="text-lg font-bold text-white">{{ $getRecord()->name }}</h3>
        <p class="text-xs text-gray-500">{{ $getRecord()->registration_number ?? 'Siswa Aktif' }}</p>
    </div>

    {{-- Badge Status --}}
    <div class="mb-4 inline-flex items-center space-x-2 px-2.5 py-1 rounded-full text-xs font-medium {{ $isFilledToday ? 'bg-green-900/50 text-green-400' : 'bg-yellow-900/50 text-yellow-500' }} w-max">
        <div class="w-1.5 h-1.5 rounded-full {{ $isFilledToday ? 'bg-green-500' : 'bg-yellow-500' }}"></div>
        <span>{{ $isFilledToday ? 'Sudah diisi hari ini' : 'Belum diisi hari ini' }}</span>
    </div>

    {{-- Kotak Nilai --}}
    <div class="flex space-x-2">
        {{-- Kotak Lari --}}
        <div class="flex-1 bg-gray-950 p-3 rounded-lg border border-gray-800">
            <p class="text-[10px] uppercase text-gray-500 font-semibold tracking-wider">Lari 12 Menit</p>
            <div class="flex items-baseline space-x-1 mt-1">
                <span class="text-2xl font-bold text-white">{{ $todayRecord ? $todayRecord->run_12_min_dist : ($lastRecord ? $lastRecord->run_12_min_dist : '-') }}</span>
                <span class="text-xs text-gray-500">m</span>
            </div>
            @if($todayRecord && $lastRecord)
                @php $diff = $todayRecord->run_12_min_dist - $lastRecord->run_12_min_dist; @endphp
                <p class="text-xs mt-1 {{ $diff >= 0 ? 'text-green-500' : 'text-red-500' }}">
                    {{ $diff > 0 ? '+' : '' }}{{ $diff }}
                </p>
            @else
                <p class="text-xs mt-1 text-gray-600">-</p>
            @endif
        </div>

        {{-- Kotak Push Up --}}
        <div class="flex-1 bg-gray-950 p-3 rounded-lg border border-gray-800">
            <p class="text-[10px] uppercase text-gray-500 font-semibold tracking-wider">Push Up</p>
            <div class="flex items-baseline space-x-1 mt-1">
                <span class="text-2xl font-bold text-white">{{ $todayRecord ? $todayRecord->push_up_reps : ($lastRecord ? $lastRecord->push_up_reps : '-') }}</span>
                <span class="text-xs text-gray-500">rep</span>
            </div>
            @if($todayRecord && $lastRecord)
                @php $diff = $todayRecord->push_up_reps - $lastRecord->push_up_reps; @endphp
                <p class="text-xs mt-1 {{ $diff >= 0 ? 'text-green-500' : 'text-red-500' }}">
                    {{ $diff > 0 ? '+' : '' }}{{ $diff }}
                </p>
            @else
                <p class="text-xs mt-1 text-gray-600">-</p>
            @endif
        </div>
    </div>
</div>