@extends('layouts.home')
@section('title_page','Daftar Kehadiran')
@section('content')

    <style>
        /* Larger checkboxes for kehadiran matrix */
        .large-checkbox {
            transform: scale(1.6);
            -webkit-transform: scale(1.6);
            margin: 6px;
            /* keep alignment with table cells */
            vertical-align: middle;
        }
    </style>

    @if (Session::has('alert'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ Session('alert') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div id="ajaxAlert" style="display: none;"></div>

    <div class="row mb-4">
        <div class="col-md-6">
            <label for="date">Pilih Tanggal dalam Minggu</label>
            <input type="date" name="date" id="date" class="form-control" value="{{ $selectedDate }}" onchange="window.location.href='{{ route('kehadiran.index') }}?date=' + this.value">
        </div>
        <div class="col-md-6">
            <label>Periode</label>
            <div class="form-control">
                <strong>{{ $startOfWeek->isoFormat('D MMMM Y') }}</strong> -
                <strong>{{ $endOfWeek->isoFormat('D MMMM Y') }}</strong>
            </div>
            <div class="mt-2 text-right">
                {{-- Scan moved to Rekapan Kehadiran; button hidden here --}}
            </div>
        </div>
    </div>

    <div class="table-responsive">
        @if(auth()->user()->role == 'SuperAdmin')
            <form method="POST" action="{{ route('kehadiran.store') }}">
                @csrf
                <input type="hidden" name="date" value="{{ $selectedDate }}">
        @endif
        <table class="table table-hover table-bordered">
            <thead>
                @php
                    // sama seperti rekapan: sembunyikan Subuh pada Minggu dan Isya pada Sabtu
                @endphp
                <tr align="center">
                    <th rowspan="2" width="5%" style="vertical-align: middle;">No</th>
                    <th rowspan="2" style="vertical-align: middle;">Nama Santri</th>
                    @foreach ($weekDates as $weekDate)
                        @php
                            $hasSubuh = !($weekDate['day_name'] === 'Minggu');
                            $hasIsya = !($weekDate['day_name'] === 'Sabtu');
                            $colspan = ($hasSubuh ? 1 : 0) + ($hasIsya ? 1 : 0);
                        @endphp
                        <th colspan="{{ $colspan }}" class="text-center">
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
                            <th class="text-center" style="background-color: #e3f2fd;">
                                <i class="fas fa-sun"></i> Subuh
                            </th>
                        @endif
                        @if ($hasIsya)
                            <th class="text-center" style="background-color: #fff3e0;">
                                <i class="fas fa-moon"></i> Isya
                            </th>
                        @endif
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @php $counter = 1; @endphp
                @forelse ($attendanceData as $santriId => $data)
                    <tr>
                        <td align="center">{{ $counter++ }}</td>
                        <td>{{ $data['name'] }}</td>
                        @foreach ($weekDates as $weekDate)
                            @php
                                $day = $weekDate['date'];
                                $hasSubuh = !($weekDate['day_name'] === 'Minggu');
                                $hasIsya = !($weekDate['day_name'] === 'Sabtu');
                                $subuhHadir = $data['days'][$day]['subuh'] ?? false;
                                $isyaHadir = $data['days'][$day]['isya'] ?? false;
                            @endphp
                            @if ($hasSubuh)
                                <td align="center" style="background-color: #f5f5f5;">
                                    @if(auth()->user()->role == 'SuperAdmin')
                                        <input type="checkbox" class="large-checkbox" name="subuh_ids[{{ $day }}][]" value="{{ $santriId }}" {{ $subuhHadir ? 'checked' : '' }}>
                                    @else
                                        @if($subuhHadir)
                                            <span class="badge badge-success">&#10003;</span>
                                        @else
                                            <span class="text-muted">&#10007;</span>
                                        @endif
                                    @endif
                                </td>
                            @endif
                            @if ($hasIsya)
                                <td align="center" style="background-color: #fafafa;">
                                    @if(auth()->user()->role == 'SuperAdmin')
                                        <input type="checkbox" class="large-checkbox" name="isya_ids[{{ $day }}][]" value="{{ $santriId }}" {{ $isyaHadir ? 'checked' : '' }}>
                                    @else
                                        @if($isyaHadir)
                                            <span class="badge badge-success">&#10003;</span>
                                        @else
                                            <span class="text-muted">&#10007;</span>
                                        @endif
                                    @endif
                                </td>
                            @endif
                        @endforeach
                    </tr>
                @empty
                    @php
                        $totalSessionCols = 0;
                        foreach ($weekDates as $wd) {
                            $totalSessionCols += (!($wd['day_name'] === 'Minggu') ? 1 : 0) + (!($wd['day_name'] === 'Sabtu') ? 1 : 0);
                        }
                    @endphp
                    <tr>
                        <td colspan="{{ 2 + $totalSessionCols }}" align="center">Tidak ada data santri.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if(auth()->user()->role == 'SuperAdmin')
                <div class="mt-2 text-right">
                    <button type="submit" class="btn btn-primary">Simpan Kehadiran</button>
                </div>
            </form>
        @endif
    </div>

@endsection

