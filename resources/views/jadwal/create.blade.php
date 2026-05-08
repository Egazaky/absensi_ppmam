@extends('layouts.home')
@section('title_page','Tambah Jadwal Pengajian')
@section('content')

    <form action="{{ route('jadwal.store') }}" method="post">
        @csrf
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="title">Judul Jadwal</label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" name="title" value="{{ old('title') }}" required>

                        @error('title')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="teacher">Pengajar</label>
                        <input type="text" class="form-control @error('teacher') is-invalid @enderror" name="teacher" value="{{ old('teacher') }}" required>

                        @error('teacher')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="session">Sesi</label>
                        <select class="form-control @error('session') is-invalid @enderror" name="session" required>
                            <option value="">Pilih Sesi</option>
                            <option value="isya" {{ old('session') == 'isya' ? 'selected' : '' }}>Isya</option>
                            <option value="subuh" {{ old('session') == 'subuh' ? 'selected' : '' }}>Subuh</option>
                        </select>

                        @error('session')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="date">Tanggal</label>
                        <input type="date" class="form-control @error('date') is-invalid @enderror" name="date" value="{{ old('date') }}" required>

                        @error('date')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="time">Waktu</label>
                        <input type="time" class="form-control @error('time') is-invalid @enderror" name="time" value="{{ old('time') }}" required>

                        @error('time')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="description">Deskripsi</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" name="description" rows="4">{{ old('description') }}</textarea>

                        @error('description')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="{{ route('jadwal.index') }}" class="btn btn-secondary">Kembali</a>
                </div>
            </div>
        </div>
    </form>

@endsection
