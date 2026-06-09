@extends('layouts.home')
@section('title_page','Rekapan Qiyamullail')
@section('content')

    <div class="row mb-4">
        <div class="col-md-6">
            <label for="date">Pilih Tanggal (untuk menentukan Sabtu dalam periode)</label>
            <input type="date" name="date" id="date" class="form-control" value="{{ $selectedDate }}" onchange="window.location.href='{{ route('qiyam.report') }}?date=' + this.value">
        </div>
        <div class="col-md-6">
            <label>Periode</label>
            <div class="form-control">
                <strong>{{ $startOfPeriod->isoFormat('D MMMM Y') }}</strong> -
                <strong>{{ $endOfPeriod->isoFormat('D MMMM Y') }}</strong>
            </div>
            <div class="mt-2 text-right">
                <a href="{{ route('qiyam.export.pdf', ['date' => $selectedDate]) }}" class="btn btn-primary mr-2">
                    <i class="fas fa-file-pdf"></i> Cetak PDF
                </a>
                <a href="{{ route('qiyam.export.excel', ['date' => $selectedDate]) }}" class="btn btn-success">
                    <i class="fas fa-file-excel"></i> Export Excel
                </a>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover table-bordered">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th>Nama Santri</th>
                    @foreach ($weekDates as $wd)
                        <th class="text-center">
                            {{ $wd['day_name'] }}<br>
                            <small>{{ $wd['day_number'] }}</small>
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @php $i = 1; @endphp
                @forelse($attendanceData as $santriId => $data)
                <tr>
                    <td>{{ $i++ }}</td>
                    <td>{{ $data['name'] }}</td>
                    @foreach ($weekDates as $wd)
                        @php $present = $data['days'][$wd['date']] ?? false; @endphp
                        <td align="center">
                            @if ($present)
                                <span class="badge badge-success"><i class="fas fa-check"></i></span>
                            @else
                                <span class="badge badge-secondary"><i class="fas fa-times"></i></span>
                            @endif
                        </td>
                    @endforeach
                </tr>
                @empty
                <tr>
                    <td colspan="{{ 2 + count($weekDates) }}" align="center">Tidak ada data santri.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

@endsection
