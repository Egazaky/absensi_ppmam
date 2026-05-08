@extends('layouts.home')
@section('title_page','Scan QR Kehadiran')
@section('content')

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
            <select id="attendance-session" class="form-control">
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
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="history-body">
                                <tr><td colspan="3" class="text-muted text-center">Belum ada scan</td></tr>
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
    </form>

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

        function onScanSuccess(decodedText, decodedResult) {
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
                // Add to history
                addHistoryEntry(data.name, 'Hadir');

                // Now submit form via AJAX to toggle attendance WITHOUT page refresh
                inputSantri.value = decodedText;
                inputDate.value = attendanceDate.value;
                inputSession.value = attendanceSession.value;

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
                            <td><span class="badge badge-success">Hadir</span></td>
                        `;
                        historyBody.appendChild(row);
                    });
                }
            } catch (err) {
                console.error('Error loading history from storage:', err);
            }
        }

        // Add entry to history table (unique per santri)
        function addHistoryEntry(santriName, status) {
            const historyBody = document.getElementById('history-body');
            const now = new Date();
            const timeStr = now.toLocaleTimeString('id-ID');

            // Remove existing row if santri already exists
            const existingRows = historyBody.querySelectorAll('tr');
            existingRows.forEach(row => {
                const nameCell = row.querySelector('td:nth-child(2)');
                if (nameCell && nameCell.textContent === santriName) {
                    row.remove();
                }
            });

            // Clear empty message if needed
            const emptyRow = historyBody.querySelector('tr:has(td.text-muted)');
            if (emptyRow) {
                emptyRow.remove();
            }

            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${timeStr}</td>
                <td>${santriName}</td>
                <td><span class="badge badge-success">Hadir</span></td>
            `;
            historyBody.insertBefore(row, historyBody.firstChild);

            // Save to localStorage
            saveHistoryToStorage(santriName, timeStr);

            // Keep only last 20 entries visible
            while (historyBody.rows.length > 20) {
                historyBody.deleteRow(historyBody.rows.length - 1);
            }
        }

        // Save history to localStorage (unique per santri name)
        function saveHistoryToStorage(santriName, timeStr) {
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

            // Remove if this santri already exists
            entries = entries.filter(entry => entry.name !== santriName);

            // Add new entry at the beginning
            entries.unshift({
                time: timeStr,
                name: santriName
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
                historyBody.innerHTML = '<tr><td colspan="3" class="text-muted text-center">Belum ada scan</td></tr>';
            }
        }

        // Setup clear button
        const clearBtn = document.getElementById('clear-history-btn');
        if (clearBtn) {
            clearBtn.addEventListener('click', clearHistory);
        }

        // Check if document is already loaded
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', checkAndInitialize);
        } else {
            checkAndInitialize();
        }
    </script>

@endsection
