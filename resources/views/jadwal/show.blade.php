@extends('layouts.home')
@section('title_page','Detail Jadwal Pengajian')
@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Detail Jadwal Pengajian</h4>
                    <div class="card-header-action">
                        <a href="{{ route('jadwal.index') }}" class="btn btn-secondary">Kembali</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong>Judul Materi:</strong></label>
                                <p>{{ $schedule->title }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong>Pengajar:</strong></label>
                                <p>{{ $schedule->teacher ?? 'Belum ditentukan' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong>Sesi:</strong></label>
                                <p>{{ ucfirst($schedule->session) }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong>Tanggal:</strong></label>
                                <p>{{ \Carbon\Carbon::parse($schedule->date)->format('d M Y') }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong>Waktu:</strong></label>
                                <p>{{ $schedule->time }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label><strong>Deskripsi:</strong></label>
                                <p>{{ $schedule->description ?: 'Tidak ada deskripsi' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong>Dibuat Oleh:</strong></label>
                                <p>{{ $schedule->creator->email ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong>Dibuat Pada:</strong></label>
                                <p>{{ $schedule->created_at->format('d M Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                    @if(Auth::user()->role != 'Santri')
                    <div class="row">
                        <div class="col-md-12">
                            <a href="{{ route('jadwal.edit', $schedule->id) }}" class="btn btn-warning">Edit Jadwal</a>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection
