@extends('layouts.home')
@section('title_page','Rekapan Kehadiran')
@section('content')

    <div class="row mb-4">
        <div class="col-md-6">
            <label for="date">Pilih Tanggal dalam Minggu</label>
            <input type="date" name="date" id="date" class="form-control" value="{{ $selectedDate }}" onchange="window.location.href='{{ route('kehadiran.report') }}?date=' + this.value">
        </div>
        <div class="col-md-6">
            <label>Periode</label>
            <div class="form-control">
                <strong>{{ $startOfWeek->isoFormat('D MMMM Y') }}</strong> -
                <strong>{{ $endOfWeek->isoFormat('D MMMM Y') }}</strong>
            </div>
            <div class="mt-2 text-right">
                <a href="{{ route('kehadiran.export.pdf', ['date' => $selectedDate]) }}" class="btn btn-primary mr-2">
                    <i class="fas fa-file-pdf"></i> Cetak PDF
                </a>
                <a href="{{ route('kehadiran.export.excel', ['date' => $selectedDate]) }}" class="btn btn-success mr-2">
                    <i class="fas fa-file-excel"></i> Export Excel
                </a>
                @if(auth()->user()->role != 'Santri')
                    <a href="{{ route('kehadiran.scan') }}" class="btn btn-info">
                        <i class="fas fa-qrcode"></i> Scan QR
                    </a>
                @endif
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover table-bordered">
            <thead>
                @php
                    // Hitung total kolom sesi untuk colspan pesan ketika tidak ada data
                    $totalSessionCols = 0;
                    foreach ($weekDates as $wd) {
                        $hasSubuh = !($wd['day_name'] === 'Minggu'); // hilangkan Subuh pada Minggu
                        $hasIsya = !($wd['day_name'] === 'Sabtu');  // hilangkan Isya pada Sabtu
                        $totalSessionCols += ($hasSubuh ? 1 : 0) + ($hasIsya ? 1 : 0);
                    }
                @endphp

                <tr align="center">
                    <th rowspan="2" width="5%" style="vertical-align: middle;">No</th>
                    <th rowspan="2" style="vertical-align: middle;">Nama Santri</th>
                    @foreach ($weekDates as $weekDate)
                        @php
                            $hasSubuh = !($weekDate['day_name'] === 'Minggu'); // tidak tampilkan Subuh Minggu
                            $hasIsya = !($weekDate['day_name'] === 'Sabtu');  // tidak tampilkan Isya Sabtu
                            $colspan = ($hasSubuh ? 1 : 0) + ($hasIsya ? 1 : 0);
                        @endphp
                        <th colspan="{{ $colspan }}" class="text-center" style="min-width: 150px;">
                            {{ $weekDate['day_name'] }}<br>
                            <small>{{ $weekDate['day_number'] }}</small>
                        </th>
                    @endforeach
                </tr>
                <tr align="center">
                    @foreach ($weekDates as $weekDate)
                        @php
                            $hasSubuh = !($weekDate['day_name'] === 'Minggu');
                            $hasIsya = !($weekDate['day_name'] === 'Sabtu');
                        @endphp
                        @if ($hasSubuh)
                            <th class="text-center" style="background-color: rgba(79, 140, 255, 0.12); color: var(--primary);">
                                <i class="fas fa-sun"></i> Subuh
                            </th>
                        @endif
                        @if ($hasIsya)
                            <th class="text-center" style="background-color: rgba(124, 58, 237, 0.12); color: #a78bfa;">
                                <i class="fas fa-moon"></i> Isya
                            </th>
                        @endif
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @php
                    $counter = 1;
                @endphp
                @forelse ($attendanceData as $santriId => $data)
                    <tr>
                        <td align="center">{{ $counter++ }}</td>
                        <td>{{ $data['name'] }}</td>
                        @foreach ($weekDates as $weekDate)
                            @php
                                $dayData = $data['days'][$weekDate['date']] ?? ['subuh' => false, 'isya' => false];
                                $subuhHadir = $dayData['subuh'] ?? false;
                                $isyaHadir = $dayData['isya'] ?? false;
                                $hasSubuh = !($weekDate['day_name'] === 'Minggu');
                                $hasIsya = !($weekDate['day_name'] === 'Sabtu');
                            @endphp
                            @if ($hasSubuh)
                                <td align="center" style="background-color: rgba(79, 140, 255, 0.04);">
                                    @if ($subuhHadir)
                                        <span class="badge badge-success" title="Subuh: Hadir">
                                            <i class="fas fa-check"></i>
                                        </span>
                                    @else
                                        <span class="badge badge-secondary" title="Subuh: Tidak Hadir">
                                            <i class="fas fa-times"></i>
                                        </span>
                                    @endif
                                </td>
                            @endif
                            @if ($hasIsya)
                                <td align="center" style="background-color: rgba(124, 58, 237, 0.04);">
                                    @if ($isyaHadir)
                                        <span class="badge badge-success" title="Isya: Hadir">
                                            <i class="fas fa-check"></i>
                                        </span>
                                    @else
                                        <span class="badge badge-secondary" title="Isya: Tidak Hadir">
                                            <i class="fas fa-times"></i>
                                        </span>
                                    @endif
                                </td>
                            @endif
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ 2 + $totalSessionCols }}" align="center">Tidak ada data santri.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        <div class="alert alert-info">
            <strong>Keterangan:</strong>
            <ul class="mb-0">
                <li><span class="badge badge-success"><i class="fas fa-check"></i></span> = Hadir</li>
                <li><span class="badge badge-secondary"><i class="fas fa-times"></i></span> = Tidak hadir</li>
            </ul>
        </div>
    </div>

@endsection

