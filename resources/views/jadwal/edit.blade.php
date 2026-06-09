@extends('layouts.home')
@section('title_page','Edit Jadwal Pengajian')
@section('content')

    <form action="{{ route('jadwal.update', $schedule->id) }}" method="post">
        @csrf
        @method('PUT')
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="title">Judul Jadwal</label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" name="title" value="{{ old('title', $schedule->title) }}" required>

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
                        <input type="text" class="form-control @error('teacher') is-invalid @enderror" name="teacher" value="{{ old('teacher', $schedule->teacher) }}" required>

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
                        <select class="form-control select2 @error('session') is-invalid @enderror" name="session" required>
                            <option value="">Pilih Sesi</option>
                            <option value="isya" {{ old('session', $schedule->session) == 'isya' ? 'selected' : '' }}>Isya</option>
                            <option value="subuh" {{ old('session', $schedule->session) == 'subuh' ? 'selected' : '' }}>Subuh</option>
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
                        <input type="date" class="form-control @error('date') is-invalid @enderror" name="date" value="{{ old('date', $schedule->date) }}" required>

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
                        <input type="time" class="form-control @error('time') is-invalid @enderror" name="time" value="{{ old('time', $schedule->time) }}" required>

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
                        <textarea class="form-control @error('description') is-invalid @enderror" name="description" rows="4">{{ old('description', $schedule->description) }}</textarea>

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
                    <button type="submit" class="btn btn-primary">Perbarui</button>
                    <a href="{{ route('jadwal.index') }}" class="btn btn-secondary">Kembali</a>
                </div>
            </div>
        </div>
    </form>

@endsection
