<footer class="main-footer">
    <div class="footer-left">
        Copyright &copy; {{ date('Y') }}
        <div class="bullet"></div> Pondok Pesantren Al-Musawwa | Made by <a href="https://github.com/zakyyega" target="_blank">Ega Zaky Janitra</a>
    </div>
    <div class="footer-right">
        Laravel v{{ Illuminate\Foundation\Application::VERSION }} (PHP v{{ PHP_VERSION }})
    </div>
</footer>
</div>
</div>

@yield('modal')

<!-- General JS Scripts -->
<script src="{{ asset('assets/modules/jquery.min.js') }}"></script>
{{-- <script src="{{ asset('assets/modules/popper.js') }}"></script> --}}
{{-- <script src="{{ asset('assets/modules/tooltip.js') }}"></script> --}}
<script src="{{ asset('assets/modules/bootstrap/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/modules/nicescroll/jquery.nicescroll.min.js') }}"></script>
<script src="{{ asset('assets/modules/moment.min.js') }}"></script>
<script src="{{ asset('assets/js/stisla.js') }}"></script>
<script src="{{ asset('assets/js/digital-sign.js') }}"></script>
<script src="{{ asset('assets/modules/select2/dist/js/select2.full.min.js') }}"></script>
<script src="{{ asset('assets/modules/datatables/datatables.min.js') }}"></script>
<script src="{{ asset('assets/modules/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets/modules/datatables/Select-1.2.4/js/dataTables.select.min.js') }}"></script>
<script src="{{ asset('assets/modules/jquery-ui/jquery-ui.min.js') }}"></script>

<!-- JS Libraies -->
<script src="{{ asset('assets/js/page/modules-datatables.js') }}"></script>

<!-- Page Specific JS File -->
@yield('script')

<script>
$('.alert').alert()
</script>

<!-- Theme Toggle Button -->
<button id="theme-toggle-btn" type="button" data-tooltip="Ganti Tema" aria-label="Toggle dark/light theme">
    <span class="theme-icon"><i class="fas fa-moon"></i></span>
</button>

<!-- Theme Toggle Script -->
<script>
(function() {
    var btn = document.getElementById('theme-toggle-btn');
    if (!btn) return;
    var icon = btn.querySelector('.theme-icon');

    function getTheme() {
        return localStorage.getItem('theme') || 'dark';
    }

    function updateIcon(theme) {
        if (theme === 'light') {
            icon.innerHTML = '<i class="fas fa-sun"></i>';
            btn.setAttribute('data-tooltip', 'Mode Gelap');
        } else {
            icon.innerHTML = '<i class="fas fa-moon"></i>';
            btn.setAttribute('data-tooltip', 'Mode Terang');
        }
    }

    // Init icon
    updateIcon(getTheme());

    btn.addEventListener('click', function() {
        var current = getTheme();
        var next = current === 'dark' ? 'light' : 'dark';

        // Add transition class
        document.documentElement.classList.add('theme-transition');
        document.documentElement.setAttribute('data-theme', next);
        localStorage.setItem('theme', next);

        // Animate icon
        icon.classList.remove('spin-in');
        void icon.offsetWidth; // force reflow
        icon.classList.add('spin-in');
        updateIcon(next);

        // Remove transition class after animation
        setTimeout(function() {
            document.documentElement.classList.remove('theme-transition');
        }, 400);
    });
})();
</script>

<!-- Template JS File -->
<script src="{{ asset('assets/js/scripts.js') }}"></script>
<script src="{{ asset('assets/js/custom.js') }}"></script>
</body>

</html>
