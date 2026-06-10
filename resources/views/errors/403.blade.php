<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
  <script>
      (function() {
          var t = localStorage.getItem('theme') || 'dark';
          document.documentElement.setAttribute('data-theme', t);
      })();
  </script>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>403 &mdash; Akses Ditolak</title>

  <!-- Favicon -->
  <link rel="favicon icon" href="{{ asset('assets/img/ppm_am.png') }}" type="image/x-icon">
  
  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- General CSS Files -->
  <link rel="stylesheet" href="{{ asset('assets/modules/bootstrap/css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/modules/fontawesome/css/all.min.css') }}">

  <!-- Template CSS -->
  <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/components.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/ponpes-style.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/dark-light-theme.css') }}">
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

            <div class="card login-card text-center">
              <div class="card-body">
                <div class="text-danger mb-4" style="font-size: 64px; font-weight: 800; line-height: 1;">
                  403
                </div>
                <h5 class="font-weight-bold mb-3" style="color: var(--text-main); font-size: 20px;">Akses Ditolak</h5>
                <p class="text-muted mb-4" style="font-size: 14px; line-height: 1.6;">
                  {{ $exception->getMessage() ?: 'Anda tidak memiliki hak akses untuk halaman ini.' }}
                </p>
                
                <div class="form-group">
                  <a href="{{ url()->previous() }}" class="btn btn-primary btn-lg btn-block mb-3" style="height: 46px !important; font-size: 15px !important; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                  </a>
                  <a href="{{ route('home') }}" class="btn btn-secondary btn-lg btn-block" style="height: 46px !important; font-size: 15px !important; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-home mr-2"></i> Halaman Utama
                  </a>
                </div>
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
  <script src="{{ asset('assets/js/stisla.js') }}"></script>

  <!-- Template JS File -->
  <script src="{{ asset('assets/js/scripts.js') }}"></script>
  <script src="{{ asset('assets/js/custom.js') }}"></script>

  <button id="theme-toggle-btn" type="button" data-tooltip="Ganti Tema" aria-label="Toggle dark/light theme">
      <span class="theme-icon"><i class="fas fa-moon"></i></span>
  </button>
  <script>
  (function() {
      var btn = document.getElementById('theme-toggle-btn');
      if (!btn) return;
      var icon = btn.querySelector('.theme-icon');
      function getTheme() { return localStorage.getItem('theme') || 'dark'; }
      function updateIcon(theme) {
          icon.innerHTML = theme === 'light' ? '<i class="fas fa-sun"></i>' : '<i class="fas fa-moon"></i>';
          btn.setAttribute('data-tooltip', theme === 'light' ? 'Mode Gelap' : 'Mode Terang');
      }
      updateIcon(getTheme());
      btn.addEventListener('click', function() {
          var next = getTheme() === 'dark' ? 'light' : 'dark';
          document.documentElement.classList.add('theme-transition');
          document.documentElement.setAttribute('data-theme', next);
          localStorage.setItem('theme', next);
          icon.classList.remove('spin-in'); void icon.offsetWidth; icon.classList.add('spin-in');
          updateIcon(next);
          setTimeout(function() { document.documentElement.classList.remove('theme-transition'); }, 400);
      });
  })();
  </script>
</body>
</html>
