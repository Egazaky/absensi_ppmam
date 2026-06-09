<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekapan Qiyamullail</title>
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
        <h2>Rekapan Qiyamullail</h2>
        <p>Periode: {{ $startOfPeriod->isoFormat('D MMMM Y') }} - {{ $endOfPeriod->isoFormat('D MMMM Y') }}</p>
        <p>Tanggal referensi: {{ \Carbon\Carbon::parse($selectedDate)->isoFormat('D MMMM Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Santri</th>
                @foreach ($weekDates as $wd)
                    <th>{{ $wd['day_name'] }} {{ $wd['day_number'] }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @php $counter = 1; @endphp
            @forelse ($attendanceData as $data)
                <tr>
                    <td>{{ $counter++ }}</td>
                    <td class="text-left">{{ $data['name'] }}</td>
                    @foreach ($weekDates as $wd)
                        <td>{{ ($data['days'][$wd['date']] ?? false) ? 'Hadir' : 'Tidak' }}</td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ 2 + count($weekDates) }}">Tidak ada data santri</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
