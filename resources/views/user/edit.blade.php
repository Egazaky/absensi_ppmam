@extends('layouts.home')
@section('title_page','Edit Data Pengguna')
@section('content')

    <form action="{{ route('pengguna.update', $user->id) }}" method="post">
        @csrf
        @method('PATCH')
        <div class="container">
            <div class="row">
                <div class="col-sm">
                    <div class="form-group">
                        <label for="santri_id">Nama Santri</label>
                        <select class="form-control select2 @error('santri_id') is-invalid @enderror" name="santri_id" required @if(Auth::user()->role == 'Santri') disabled @endif>
                            <option selected disabled>Pilih Santri</option>
                            @foreach ($data as $santri)
                                <option value="{{ $santri->id }}"
                                    @if ($santri->id == $user->santri_id)
                                        selected
                                    @endif>{{ $santri->name }}</option>
                            @endforeach
                        </select>

                        @error('santri_id')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-sm">
                    <div class="form-group">
                        <label for="email">{{ __('E-Mail Address') }}</label>
                        <div class="input-group">
                            <input id="email" type="text" class="form-control @error('email') is-invalid @enderror"
                                   name="email"
                                   value="{{ old('email', substr($user->email, 0, strpos($user->email, '@'))) }}"
                                   required autocomplete="email"
                                   @if(Auth::user()->role == 'Santri') placeholder="email prefix" @endif>
                            <div class="input-group-append">
                                <span class="input-group-text" style="background-color: #e9ecef;">@ppm.am</span>
                            </div>
                        </div>

                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-sm">
                    <div class="form-group">
                        <label for="role">Role</label>
                        <select class="form-control select2 @error('role') is-invalid @enderror" name="role" required @if(Auth::user()->role == 'Santri') disabled @endif>
                            <option selected disabled>Pilih Role</option>
                            @if (Auth::user()->role == 'SuperAdmin')
                                <option value="SuperAdmin" @if ($user->role == 'SuperAdmin') selected @endif>SuperAdmin</option>
                            @endif
                            <option value="Administrator" @if ($user->role == 'Administrator') selected @endif>Administrator</option>
                            <option value="Pengurus" @if ($user->role == 'Pengurus') selected @endif @if (auth()->user()->role == 'Administrator' && auth()->user() == $user) disabled @endif>Pengurus</option>
                            <option value="Santri" @if ($user->role == 'Santri') selected @endif @if (auth()->user() == $user) disabled @endif>Santri</option>
                        </select>

                        @error('role')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm">
                    <div class="form-group">
                        <label for="password">{{ __('Password') }}</label>
                        <div class="input-group" id="show_hide_password">
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <a href="javascript:void(0)"><i class="fa fa-eye-slash" aria-hidden="true"></i></a>
                                </div>
                            </div>

                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="col-sm">
                    <div class="form-group">
                        <label for="password_confirmation">{{ __('Confirm Password') }}</label>
                        <div class="input-group" id="show_hide_password">
                            <input id="password_confirmation" type="password" class="form-control @error('password') is-invalid @enderror" name="password_confirmation" required autocomplete="new-password">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <a href="javascript:void(0)"><i class="fa fa-eye-slash" aria-hidden="true"></i></a>
                                </div>
                            </div>

                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm">
                    <div class="form-group">
                        <button class="btn btn-primary">Update</button>
                        <a href="{{ route('pengguna.index') }}" class="btn btn-secondary">Kembali</a>
                    </div>
                </div>
            </div>
        </div>
    </form>

@endsection

@section('script')
    <script>
        $(document).ready(function() {
            $("#show_hide_password a").on('click', function(event) {
                event.preventDefault();
                if($('#show_hide_password input').attr("type") == "text"){
                    $('#show_hide_password input').attr('type', 'password');
                    $('#show_hide_password i').addClass( "fa-eye-slash" );
                    $('#show_hide_password i').removeClass( "fa-eye" );
                }else if($('#show_hide_password input').attr("type") == "password"){
                    $('#show_hide_password input').attr('type', 'text');
                    $('#show_hide_password i').removeClass( "fa-eye-slash" );
                    $('#show_hide_password i').addClass( "fa-eye" );
                }
            });
        });
    </script>
@endsection
