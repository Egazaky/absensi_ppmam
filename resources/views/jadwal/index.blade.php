@extends('layouts.home')
@section('title_page','Jadwal Pengajian')
@section('content')

    @if (Session::has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ Session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        @if(Auth::user()->role != 'Santri')
        <div class="col-md-8">
            <a href="{{ route('jadwal.create') }}" class="btn btn-primary">Tambah Jadwal</a><br><br>
        </div>
        @endif
        <div class="col-md-4 mb-3">
            <form action="#" class="flex-sm">
                <div class="input-group">
                    <input type="text" name="keyword" class="form-control" placeholder="Search" value="{{ Request::get('keyword') }}">
                    <div class="input-group-append">
                        <button class="btn btn-primary mr-2 rounded-right" type="submit"><i class="fas fa-search"></i></button>
                        <button onclick="window.location.href='{{ route('jadwal.index') }}'" type="button" class="btn btn-md btn-secondary rounded"><i class="fas fa-sync-alt"></i></button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover table-bordered">
            <thead>
                <tr align="center">
                    <th width="5%">No</th>
                    <th>Judul</th>
                    <th>Pengajar</th>
                    <th>Sesi</th>
                    <th>Tanggal</th>
                    <th>Waktu</th>
                    <th>Dibuat Oleh</th>
                    <th width="13%">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($schedules as $index => $schedule)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><a href="{{ route('jadwal.show', $schedule->id) }}" class="text-primary">{{ $schedule->title }}</a></td>
                        <td>{{ $schedule->teacher }}</td>
                        <td>{{ ucfirst($schedule->session) }}</td>
                        <td>{{ \Carbon\Carbon::parse($schedule->date)->format('d M Y') }}</td>
                        <td>{{ $schedule->time }}</td>
                        <td>{{ $schedule->creator->email ?? 'N/A' }}</td>
                        <td align="center">
                            @if(Auth::user()->role != 'Santri')
                            <a href="{{ route('jadwal.edit', $schedule->id) }}" class="btn btn-sm btn-warning">Edit</a>
                            <form action="{{ route('jadwal.destroy', $schedule->id) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus jadwal ini?')">Hapus</button>
                            </form>
                            @else
                            -
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" align="center">Tidak ada jadwal pengajian.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

@endsection
