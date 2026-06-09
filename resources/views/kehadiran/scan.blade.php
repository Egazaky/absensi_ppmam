@extends('layouts.home')
@section('title_page','Scan QR Kehadiran')
@section('content')

    <style>
        .scan-alert-popup {
            position: fixed;
            right: 24px;
            top: 88px;
            z-index: 20010;
            width: min(420px, calc(100vw - 32px));
            background: var(--bg-elevated);
            border-radius: 14px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4);
            border-left: 5px solid var(--warning);
            display: none;
            overflow: hidden;
            border: 1px solid var(--border-color);
        }

        .scan-alert-popup.show {
            display: block;
        }

        .scan-alert-popup .scan-alert-header {
            background: rgba(245, 158, 11, 0.15);
            color: var(--warning);
            padding: 14px 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-weight: 700;
        }

        .scan-alert-popup .scan-alert-body {
            padding: 16px 18px 18px;
            color: var(--text-main);
        }

        .scan-alert-popup .scan-alert-close {
            border: 0;
            background: transparent;
            color: var(--warning);
            cursor: pointer;
            font-size: 18px;
            line-height: 1;
            padding: 0;
            opacity: 0.7;
        }

        .scan-alert-popup .scan-alert-close:hover {
            opacity: 1;
        }

        .scan-alert-popup .scan-alert-action {
            text-align: right;
            margin-top: 14px;
        }

        #history-table.table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(79, 140, 255, 0.03);
        }
    </style>

    <div class="mb-3">
        <a href="{{ route('kehadiran.report') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-md-6">
            <label for="date">Tanggal</label>
            <input type="date" id="attendance-date" class="form-control" value="{{ date('Y-m-d') }}">
        </div>
        <div class="col-md-6">
            <label for="session">Sesi</label>
            <select id="attendance-session" class="form-control select2">
                <option value="Subuh">Subuh</option>
                <option value="Isya">Isya</option>
            </select>
        </div>
    </div>

    <div class="mt-3">
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="form-inline mb-3 justify-content-center">
                            <label class="mr-2">Kamera:</label>
                            <select id="camera-select" class="form-control mr-2"></select>
                            <button id="btn-start" class="btn btn-primary mr-1">Mulai</button>
                            <button id="btn-stop" class="btn btn-secondary">Stop</button>
                        </div>

                        <div style="display:flex; justify-content:center;">
                            <div id="reader" style="width:100%; max-height:520px; aspect-ratio:1; background:#000; border-radius:5px;"></div>
                        </div>

                        <div class="mt-3">
                            <p>Hasil scan: <span id="scan-result">-</span></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Riwayat Scan</h5>
                    </div>
                    <div class="card-body" style="max-height:500px; overflow-y:auto;">
                        <div class="mb-2 text-right">
                            <button id="clear-history-btn" class="btn btn-sm btn-secondary">
                                <i class="fas fa-trash"></i> Bersihkan
                            </button>
                        </div>
                        <table class="table table-sm table-striped" id="history-table">
                            <thead>
                                <tr>
                                    <th>Waktu</th>
                                    <th>Nama Santri</th>
                                    <th>Sesi</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="history-body">
                                <tr><td colspan="4" class="text-muted text-center">Belum ada scan</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form id="toggle-form" method="POST" action="{{ route('kehadiran.toggle') }}" style="display:none;">
        @csrf
        <input type="hidden" name="santri_id" id="form-santri-id" value="">
        <input type="hidden" name="date" id="form-date" value="">
        <input type="hidden" name="session" id="form-session" value="">
        <input type="hidden" name="source" value="scan">
    </form>

    <div class="scan-alert-popup" id="scan-alert-popup" role="alert" aria-live="assertive">
        <div class="scan-alert-header">
            <span><i class="fas fa-exclamation-circle mr-2"></i>Sudah Absen</span>
            <button type="button" class="scan-alert-close" id="scan-alert-close" aria-label="Tutup">&times;</button>
        </div>
        <div class="scan-alert-body">
            <p class="mb-1" id="scan-alert-message">Santri sudah absen.</p>
            <small class="text-muted" id="scan-alert-detail"></small>
            <div class="scan-alert-action">
                <button type="button" class="btn btn-warning" id="scan-alert-ok">Mengerti</button>
            </div>
        </div>
    </div>

    <script src="/js/html5-qrcode.min.js"></script>
    <script>
        const resultEl = document.getElementById('scan-result');
        const form = document.getElementById('toggle-form');
        const inputSantri = document.getElementById('form-santri-id');
        const inputDate = document.getElementById('form-date');
        const inputSession = document.getElementById('form-session');

        const attendanceDate = document.getElementById('attendance-date');
        const attendanceSession = document.getElementById('attendance-session');

        const cameraSelect = document.getElementById('camera-select');
        const btnStart = document.getElementById('btn-start');
        const btnStop = document.getElementById('btn-stop');

        let html5QrCode = null;
        let currentCameraId = null;
        let lastScanKey = '';
        let lastScanAt = 0;
        let scanAlertOpen = false;
        let scanAlertTimer = null;

        function onScanSuccess(decodedText, decodedResult) {
            if (scanAlertOpen) {
                return;
            }

            const session = attendanceSession.value;
            const date = attendanceDate.value;
            const scanKey = `${decodedText}-${date}-${session}`;
            const now = Date.now();

            if (scanKey === lastScanKey && (now - lastScanAt) < 2500) {
                return;
            }

            lastScanKey = scanKey;
            lastScanAt = now;

            // decodedText expected to be santri id (uuid)
            resultEl.textContent = decodedText;

            // Fetch santri name and add to history before submitting form
            fetch(`/api/santri/${decodedText}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) throw new Error('Santri tidak ditemukan');
                return response.json();
            })
            .then(data => {
                // Now submit form via AJAX to toggle attendance WITHOUT page refresh
                inputSantri.value = decodedText;
                inputDate.value = date;
                inputSession.value = session;

                // Submit via AJAX instead of form.submit()
                const formData = new FormData(form);
                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(response => {
                    if (response.duplicate) {
                        addHistoryEntry(data.name, response.session || session, 'Sudah Absen', true);
                        showAlreadyAttendedPopup(data.name, response.session || session, date);
                        resultEl.textContent = `${data.name} sudah absen ${response.session || session}`;
                        return;
                    }

                    addHistoryEntry(data.name, response.session || session, 'Hadir', false);
                    resultEl.textContent = `${data.name} masuk absen ${response.session || session}`;
                })
                .catch(err => {
                    console.error('Error submitting attendance:', err);
                    resultEl.textContent = 'Error: ' + err.message;
                });
            })
            .catch(err => {
                console.error('Error:', err);
                resultEl.textContent = 'Error: ' + err.message;
            });
        }

        function onScanFailure(error) {
            // no-op for now
        }

        async function populateCameras() {
            cameraSelect.innerHTML = '';
            cameraSelect.disabled = true;
            btnStart.disabled = true;
            resultEl.textContent = 'Meminta izin kamera...';

            // First, request permission to show camera permission prompt in browser
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ video: true });
                // immediately stop stream; we only needed permission to enumerate devices
                stream.getTracks().forEach(t => t.stop());
            } catch (err) {
                console.error('Camera permission denied or no camera:', err);
                cameraSelect.innerHTML = '<option value="">Izin kamera diperlukan</option>';
                cameraSelect.disabled = false;
                btnStart.disabled = true;
                resultEl.textContent = 'Izinkan akses kamera: klik ikon 🔒 pada address bar → Setting → Izin → Allow. Lalu refresh halaman.';
                return;
            }

            // After permission granted, use Html5Qrcode to enumerate cameras
            try {
                console.log('Calling Html5Qrcode.getCameras()...');
                const cameras = await Html5Qrcode.getCameras();
                console.log('Cameras found:', cameras);
                cameraSelect.innerHTML = '';
                if (cameras && cameras.length > 0) {
                    cameras.forEach((cam, idx) => {
                        const opt = document.createElement('option');
                        opt.value = cam.id;
                        opt.textContent = cam.label || ('Kamera ' + (idx+1));
                        cameraSelect.appendChild(opt);
                    });
                    cameraSelect.disabled = false;
                    btnStart.disabled = false;
                    currentCameraId = cameras[0].id;
                    resultEl.textContent = '-';
                } else {
                    cameraSelect.innerHTML = '<option value="">Tidak ada kamera terdeteksi</option>';
                    cameraSelect.disabled = false;
                    btnStart.disabled = true;
                    resultEl.textContent = 'Tidak ada kamera terdeteksi.';
                }
            } catch (err) {
                console.error('getCameras failed', err);
                cameraSelect.innerHTML = '<option value="">Gagal mengambil daftar kamera</option>';
                cameraSelect.disabled = false;
                btnStart.disabled = true;
                resultEl.textContent = 'Gagal mengambil daftar kamera: ' + err.message;
            }
        }

        btnStart.addEventListener('click', function(e) {
            e.preventDefault();

            // Validasi: pastikan ada kamera terpilih
            const camId = cameraSelect.value;
            if (!camId) {
                resultEl.textContent = 'Pilih kamera terlebih dahulu.';
                return;
            }

            if (html5QrCode) {
                html5QrCode.stop().catch(()=>{});
                html5QrCode.clear();
                html5QrCode = null;
            }

            const readerId = 'reader';
            html5QrCode = new Html5Qrcode(readerId);

            console.log('Starting camera:', camId);
            html5QrCode.start(
                camId,
                { fps: 10, qrbox: {width: 250, height: 250} },
                onScanSuccess,
                onScanFailure
            ).catch(err => {
                console.error('Start error', err);
                resultEl.textContent = 'Gagal memulai kamera: ' + err;
            });
        });

        btnStop.addEventListener('click', function(e) {
            e.preventDefault();
            if (html5QrCode) {
                html5QrCode.stop().then(() => {
                    html5QrCode.clear();
                    html5QrCode = null;
                    resultEl.textContent = '-';
                }).catch(err => {
                    console.error('Stop error', err);
                });
            }
        });

        // Wait for Html5Qrcode library to be ready
        function checkAndInitialize() {
            if (typeof Html5Qrcode !== 'undefined') {
                console.log('Html5Qrcode ready, calling populateCameras');
                populateCameras();
                loadHistoryFromStorage();
            } else {
                console.log('Waiting for Html5Qrcode...');
                setTimeout(checkAndInitialize, 100);
            }
        }

        // Load history from localStorage
        function loadHistoryFromStorage() {
            const historyBody = document.getElementById('history-body');
            const stored = localStorage.getItem('scanHistory');

            if (!stored) {
                return; // No stored history
            }

            try {
                const entries = JSON.parse(stored);
                if (entries.length > 0) {
                    historyBody.innerHTML = ''; // Clear empty message
                    entries.forEach(entry => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${entry.time}</td>
                            <td>${entry.name}</td>
                            <td><span class="badge badge-info">${entry.session || '-'}</span></td>
                            <td>${statusBadge(entry.status || 'Hadir', entry.duplicate || false)}</td>
                        `;
                        historyBody.appendChild(row);
                    });
                }
            } catch (err) {
                console.error('Error loading history from storage:', err);
            }
        }

        function statusBadge(status, duplicate) {
            const className = duplicate ? 'badge-warning' : 'badge-success';
            const icon = duplicate ? 'fa-exclamation-circle' : 'fa-check';
            return `<span class="badge ${className}"><i class="fas ${icon} mr-1"></i>${status}</span>`;
        }

        function showAlreadyAttendedPopup(santriName, session, date) {
            scanAlertOpen = true;

            document.getElementById('scan-alert-message').textContent = `${santriName} sudah absen pada sesi ${session}.`;
            document.getElementById('scan-alert-detail').textContent = `Tanggal: ${date}. Data absen tidak diubah.`;

            document.getElementById('scan-alert-popup').classList.add('show');

            if (scanAlertTimer) {
                clearTimeout(scanAlertTimer);
            }

            scanAlertTimer = setTimeout(hideAlreadyAttendedPopup, 4500);
        }

        function hideAlreadyAttendedPopup() {
            document.getElementById('scan-alert-popup').classList.remove('show');
            scanAlertOpen = false;
            lastScanKey = '';
            lastScanAt = 0;
        }

        // Add entry to history table
        function addHistoryEntry(santriName, session, status, duplicate) {
            const historyBody = document.getElementById('history-body');
            const now = new Date();
            const timeStr = now.toLocaleTimeString('id-ID');

            // Clear empty message if needed
            const emptyRow = historyBody.querySelector('tr:has(td.text-muted)');
            if (emptyRow) {
                emptyRow.remove();
            }

            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${timeStr}</td>
                <td>${santriName}</td>
                <td><span class="badge badge-info">${session}</span></td>
                <td>${statusBadge(status, duplicate)}</td>
            `;
            historyBody.insertBefore(row, historyBody.firstChild);

            // Save to localStorage
            saveHistoryToStorage(santriName, session, status, duplicate, timeStr);

            // Keep only last 20 entries visible
            while (historyBody.rows.length > 20) {
                historyBody.deleteRow(historyBody.rows.length - 1);
            }
        }

        // Save history to localStorage
        function saveHistoryToStorage(santriName, session, status, duplicate, timeStr) {
            const stored = localStorage.getItem('scanHistory');
            let entries = [];

            if (stored) {
                try {
                    entries = JSON.parse(stored);
                } catch (err) {
                    console.error('Error parsing stored history:', err);
                    entries = [];
                }
            }

            // Add new entry at the beginning
            entries.unshift({
                time: timeStr,
                name: santriName,
                session: session,
                status: status,
                duplicate: duplicate
            });

            // Keep only last 20 entries in storage
            entries = entries.slice(0, 20);

            localStorage.setItem('scanHistory', JSON.stringify(entries));
        }

        // Clear history
        function clearHistory() {
            if (confirm('Yakin ingin menghapus semua riwayat scan?')) {
                localStorage.removeItem('scanHistory');
                const historyBody = document.getElementById('history-body');
                historyBody.innerHTML = '<tr><td colspan="4" class="text-muted text-center">Belum ada scan</td></tr>';
            }
        }

        // Setup clear button
        const clearBtn = document.getElementById('clear-history-btn');
        if (clearBtn) {
            clearBtn.addEventListener('click', clearHistory);
        }

        document.getElementById('scan-alert-close').addEventListener('click', hideAlreadyAttendedPopup);
        document.getElementById('scan-alert-ok').addEventListener('click', hideAlreadyAttendedPopup);

        // Check if document is already loaded
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', checkAndInitialize);
        } else {
            checkAndInitialize();
        }
    </script>

@endsection
