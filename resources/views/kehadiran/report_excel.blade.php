<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Rekapan Kehadiran</title>
    <style>
        .table-export { border-collapse: collapse; width: 100%; }
        .table-export th, .table-export td { border: 1px solid #444; padding: 5px; }
        .table-export th { background-color: #f0f0f0; }
        .text-left { text-align: left; }
    </style>
</head>
<body>
    <h2>Rekapan Kehadiran</h2>
    <p>Periode: {{ $startOfWeek->isoFormat('D MMMM Y') }} - {{ $endOfWeek->isoFormat('D MMMM Y') }}</p>
    @php
        $totalSessionCols = 0;
        foreach ($weekDates as $weekDate) {
            $hasSubuh = !($weekDate['day_name'] === 'Minggu');
            $hasIsya = !($weekDate['day_name'] === 'Sabtu');
            $totalSessionCols += ($hasSubuh ? 1 : 0) + ($hasIsya ? 1 : 0);
        }
    @endphp
    <table class="table-export">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Santri</th>
                @foreach ($weekDates as $weekDate)
                    @php
                        $hasSubuh = !($weekDate['day_name'] === 'Minggu');
                        $hasIsya = !($weekDate['day_name'] === 'Sabtu');
                    @endphp
                    @if ($hasSubuh)
                        <th>{{ $weekDate['day_name'] }} {{ $weekDate['day_number'] }} - Subuh</th>
                    @endif
                    @if ($hasIsya)
                        <th>{{ $weekDate['day_name'] }} {{ $weekDate['day_number'] }} - Isya</th>
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
