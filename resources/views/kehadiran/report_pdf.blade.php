<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekapan Kehadiran</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; }
        .header p { margin: 2px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table, th, td { border: 1px solid #444; }
        th, td { padding: 6px 8px; text-align: center; }
        th { background-color: #f2f2f2; }
        .text-left { text-align: left; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Rekapan Kehadiran</h2>
        <p>Periode: {{ $startOfWeek->isoFormat('D MMMM Y') }} - {{ $endOfWeek->isoFormat('D MMMM Y') }}</p>
        <p>Tanggal referensi: {{ \Carbon\Carbon::parse($selectedDate)->isoFormat('D MMMM Y') }}</p>
    </div>

    @php
        $totalSessionCols = 0;
        foreach ($weekDates as $weekDate) {
            $hasSubuh = !($weekDate['day_name'] === 'Minggu');
            $hasIsya = !($weekDate['day_name'] === 'Sabtu');
            $totalSessionCols += ($hasSubuh ? 1 : 0) + ($hasIsya ? 1 : 0);
        }
    @endphp
    <table>
        <thead>
            <tr>
                <th rowspan="2">No</th>
                <th rowspan="2">Nama Santri</th>
                @foreach ($weekDates as $weekDate)
                    @php
                        $hasSubuh = !($weekDate['day_name'] === 'Minggu');
                        $hasIsya = !($weekDate['day_name'] === 'Sabtu');
                        $colspan = ($hasSubuh ? 1 : 0) + ($hasIsya ? 1 : 0);
                    @endphp
                    <th colspan="{{ $colspan }}">{{ $weekDate['day_name'] }} {{ $weekDate['day_number'] }}</th>
                @endforeach
            </tr>
            <tr>
                @foreach ($weekDates as $weekDate)
                    @php
                        $hasSubuh = !($weekDate['day_name'] === 'Minggu');
                        $hasIsya = !($weekDate['day_name'] === 'Sabtu');
                    @endphp
                    @if ($hasSubuh)
                        <th>Subuh</th>
                    @endif
                    @if ($hasIsya)
                        <th>Isya</th>
                    @endif
                @endforeach
            </tr>
        </thead>
        <tbody>
            @php $counter = 1; @endphp
            @forelse ($attendanceData as $data)
                <tr>
                    <td>{{ $counter++ }}</td>
                    <td class="text-left">{{ $data['name'] }}</td>
                    @foreach ($weekDates as $weekDate)
                        @php
                            $dayData = $data['days'][$weekDate['date']] ?? ['subuh' => false, 'isya' => false];
                            $hasSubuh = !($weekDate['day_name'] === 'Minggu');
                            $hasIsya = !($weekDate['day_name'] === 'Sabtu');
                        @endphp
                        @if ($hasSubuh)
                            <td>{{ $dayData['subuh'] ? 'Hadir' : 'Tidak' }}</td>
                        @endif
                        @if ($hasIsya)
                            <td>{{ $dayData['isya'] ? 'Hadir' : 'Tidak' }}</td>
                        @endif
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ 2 + $totalSessionCols }}">Tidak ada data santri</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
