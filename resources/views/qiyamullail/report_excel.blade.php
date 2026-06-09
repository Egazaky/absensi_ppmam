<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Rekapan Qiyamullail</title>
    <style>
        .table-export { border-collapse: collapse; width: 100%; }
        .table-export th, .table-export td { border: 1px solid #444; padding: 5px; }
        .table-export th { background-color: #f0f0f0; }
        .text-left { text-align: left; }
    </style>
</head>
<body>
    <h2>Rekapan Qiyamullail</h2>
    <p>Periode: {{ $startOfPeriod->isoFormat('D MMMM Y') }} - {{ $endOfPeriod->isoFormat('D MMMM Y') }}</p>

    <table class="table-export">
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
