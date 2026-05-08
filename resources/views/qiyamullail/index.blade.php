@extends('layouts.home')
@section('title_page','Absensi Qiyamullail')
@section('content')

    <div class="row mb-4">
        <div class="col-md-6">
            <label for="date">Tanggal</label>
            <input type="date" name="date" id="date" class="form-control" value="{{ $date }}" onchange="window.location.href='{{ route('qiyam.index') }}?date=' + this.value">
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover table-bordered">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th>Nama Santri</th>
                    <th width="15%">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @php $i = 1; @endphp
                @foreach($santris as $santri)
                <tr>
                    <td>{{ $i++ }}</td>
                    <td>{{ $santri->name }}</td>
                    <td>
                        @php
                            $isAttended = in_array($santri->id, $attended);
                            $user = auth()->user();
                            $canClick = true;
                            if (strtolower($user->role) === 'santri' && $user->santri_id !== $santri->id) {
                                $canClick = false;
                            }
                        @endphp

                        @if ($isAttended)
                            <span class="badge badge-success">Sudah Hadir</span>
                        @else
                            <form method="POST" action="{{ route('qiyam.store') }}" style="display:inline">
                                @csrf
                                <input type="hidden" name="santri_id" value="{{ $santri->id }}">
                                <input type="hidden" name="date" value="{{ $date }}">
                                <button class="btn btn-primary btn-sm" type="submit" @if(! $canClick) disabled title="Hanya bisa absen untuk akun sendiri" @endif>Hadir</button>
                            </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

@endsection
