<!--
  PROJECT: Pesantren CMS
  AUTHOR: Muhammad Iqbal (dibaliqaja)
  GITHUB: https://github.com/dibaliqaja/pesantren-cms
  TWITTER: https://twitter.com/dibaliqaja
  FACEBOOK: https://facebook.com/dibaliqaja
  LINKEDIN: https://linkedin.com/in/dibaliqaja
  EMAIL: dibaliqaja@gmail.com
-->

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>Login &mdash; Sistem Manajemen Pondok Pesantren</title>
  <!-- Favicon -->
  <link rel="favicon icon" href="{{ asset('assets/img/ppm_am.png') }}" type="image/x-icon">
  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- General CSS Files -->
  <link rel="stylesheet" href="{{ asset('assets/modules/bootstrap/css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/modules/fontawesome/css/all.min.css') }}">
  <!-- CSS Libraries -->
  <link rel="stylesheet" href="{{ asset('assets/modules/bootstrap-social/bootstrap-social.css') }}">
  <!-- Template CSS -->
  <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/components.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/ponpes-style.css') }}">
</head>

<body class="login-body">
  <!-- Dynamic blurred background blobs -->
  <div class="login-bg-blob login-bg-blob-1"></div>
  <div class="login-bg-blob login-bg-blob-2"></div>

  <div id="app" class="login-container">
    <section class="section">
      <div class="container mt-5">
        <div class="row">
          <div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">
            <div class="login-brand">
              <img src="{{ asset('assets/img/ppm_am.png') }}" alt="logo" width="100">
            </div>

            <div class="card login-card">
              @if (session('alert'))
                <div class="alert alert-danger m-2" role="alert">
                  <div class="text-center">{{ session('alert') }}</div>
                </div>
              @endif

              <div class="card-body">
                <div class="text-center mb-4">
                  <h5 class="font-weight-bold" style="color: var(--text-main); font-size: 18px;">Sistem Manajemen</h5>
                  <p class="text-muted" style="font-size: 13px; margin-bottom: 0;">Pondok Pesantren Al-Musawwa</p>
                </div>
                <form method="POST" action="{{ route('login') }}" class="needs-validation" novalidate="">
                @csrf
                   <div class="form-group">
                        <label for="email" class="control-label">{{ __('E-Mail Address') }}</label>
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" placeholder="Enter your email" required autocomplete="email" autofocus>

                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                  <div class="form-group">
                    <div class="d-block">
                    	<label for="password" class="control-label">Password</label>
                    </div>
                    <div class="input-group show-hide-password-group">
                      <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="Enter your password" required autocomplete="current-password">
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

                  <div class="form-group mt-4">
                    <button type="submit" class="btn btn-primary btn-lg btn-block" tabindex="4" style="height: 46px !important; font-size: 15px !important;">
                      Login
                    </button>
                  </div>
                </form>

              </div>
            </div>
            <div class="simple-footer" style="color: var(--text-muted); font-size: 12px; font-weight: 500;">
              Copyright &copy; {{ date('Y') }}
              <div class="bullet"></div> Pondok Pesantren Al-Musawwa
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>

  <!-- General JS Scripts -->
  <script src="{{ asset('assets/modules/jquery.min.js') }}"></script>
  <script src="{{ asset('assets/modules/tooltip.js') }}"></script>
  <script src="{{ asset('assets/modules/bootstrap/js/bootstrap.min.js') }}"></script>
  <script src="{{ asset('assets/modules/nicescroll/jquery.nicescroll.min.js') }}"></script>
  <script src="{{ asset('assets/js/digital-sign.js') }}"></script>
  <script src="{{ asset('assets/js/stisla.js') }}"></script>

  <!-- Page Specific JS File -->
  <script>
    $(document).ready(function() {
        $(".show-hide-password-group a").on('click', function(event) {
            event.preventDefault();
            var $group = $(this).closest('.show-hide-password-group');
            var $input = $group.find('input');
            var $icon = $group.find('i');
            if($input.attr("type") == "text"){
                $input.attr('type', 'password');
                $icon.addClass("fa-eye-slash");
                $icon.removeClass("fa-eye");
            }else if($input.attr("type") == "password"){
                $input.attr('type', 'text');
                $icon.removeClass("fa-eye-slash");
                $icon.addClass("fa-eye");
            }
        });
    });
  </script>

  <!-- Template JS File -->
  <script src="{{ asset('assets/js/scripts.js') }}"></script>
  <script src="{{ asset('assets/js/custom.js') }}"></script>
</body>
</html>
