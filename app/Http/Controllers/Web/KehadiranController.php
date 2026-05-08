<?php

namespace App\Http\Controllers\Web;

use App\Helpers\LogActivity;
use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Santri;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class KehadiranController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        // Hanya Administrator, SuperAdmin dan Pengurus yang bisa input/edit kehadiran
        $this->middleware(function ($request, $next) {
            if (auth()->user()->role != 'SuperAdmin' &&
                ($request->routeIs('kehadiran.index') || $request->routeIs('kehadiran.store') || $request->routeIs('kehadiran.toggle'))) {
                abort(403, 'Anda tidak memiliki hak akses untuk halaman ini.');
            }
            return $next($request);
        })->except(['report', 'scan', 'qrcode']);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $selectedDate = $request->date ?? date('Y-m-d');
        $date = Carbon::parse($selectedDate);

        // Minggu sampai Sabtu (sesuai rekapan)
        $startOfWeek = $date->copy()->startOfWeek(Carbon::SUNDAY);
        $endOfWeek = $date->copy()->endOfWeek(Carbon::SATURDAY);

        // Generate week dates array
        $weekDates = [];
        $currentDate = $startOfWeek->copy();
        $dayNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        for ($i = 0; $i < 7; $i++) {
            $weekDates[] = [
                'date' => $currentDate->format('Y-m-d'),
                'day_name' => $dayNames[$i],
                'day_number' => $currentDate->format('d'),
            ];
            $currentDate->addDay();
        }

        $santris = Santri::orderBy('name')->get();
        $query = Santri::query();

        // Jika user bukan SuperAdmin, jangan tampilkan santri dengan user role SuperAdmin
        if (auth()->user()->role != 'SuperAdmin') {
            $query->whereDoesntHave('user', function ($q) {
                $q->where('role', 'SuperAdmin');
            });
        }

        $santris = $query->orderBy('name')->get();

        // Ambil kehadiran selama minggu tersebut
        $startDate = $startOfWeek->format('Y-m-d');
        $endDate = $endOfWeek->format('Y-m-d');

        $attendances = Attendance::where('status', true)
            ->whereDate('date', '>=', $startDate)
            ->whereDate('date', '<=', $endDate)
            ->select('id', 'santri_id', DB::raw("DATE_FORMAT(date, '%Y-%m-%d') as date_str"), 'session')
            ->get()
            ->keyBy(function ($item) {
                return $item->santri_id . '_' . $item->date_str . '_' . $item->session;
            });

        // Struktur data untuk view
        $attendanceData = [];
        foreach ($santris as $santri) {
            $attendanceData[$santri->id] = [
                'name' => $santri->name,
                'days' => []
            ];

            foreach ($weekDates as $wd) {
                $dateStr = $wd['date'];
                $subuhKey = $santri->id . '_' . $dateStr . '_Subuh';
                $isyaKey = $santri->id . '_' . $dateStr . '_Isya';
                $attendanceData[$santri->id]['days'][$dateStr] = [
                    'subuh' => $attendances->has($subuhKey),
                    'isya' => $attendances->has($isyaKey),
                ];
            }
        }

        return view('kehadiran.index', compact('weekDates', 'attendanceData', 'startOfWeek', 'endOfWeek', 'selectedDate'));
    }

    /**
     * Toggle attendance for a single santri/date/session.
     */
    public function toggle(Request $request)
    {
        try {
            $this->validate($request, [
                'santri_id' => 'required|uuid|exists:santris,id',
                'date' => 'required|date',
                'session' => 'required|in:Subuh,Isya'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson() || $request->isXmlHttpRequest()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal: ' . implode(', ', array_map(fn($v) => implode(', ', $v), $e->errors()))
                ], 422);
            }
            throw $e;
        }

        $santriId = $request->santri_id;
        $date = $request->date;
        $session = $request->session;

        $attendance = Attendance::whereDate('date', $date)
            ->where('santri_id', $santriId)
            ->where('session', $session)
            ->first();

        if ($attendance && $attendance->status) {
            // hapus atau set status false
            $attendance->delete();
            $action = 'batal hadir';
        } else {
            Attendance::updateOrCreate(
                [
                    'date' => $date,
                    'santri_id' => $santriId,
                    'session' => $session
                ],
                ['status' => true]
            );
            $action = 'hadir';
        }

        LogActivity::addToLog("Kehadiran {$action} - santri {$santriId} tanggal {$date} sesi {$session}");
        // additional log
        Log::info('Kehadiran toggle', ['santri' => $santriId, 'date' => $date, 'session' => $session, 'action' => $action]);

        // Clear ALL cache untuk memastikan data fresh di semua halaman
        Cache::flush();

        // Return JSON for AJAX requests, redirect for form submissions
        if ($request->expectsJson() || $request->isXmlHttpRequest()) {
            return response()->json([
                'success' => true,
                'message' => 'Perubahan kehadiran tersimpan.',
                'action' => $action
            ]);
        }

        return redirect()->back()->with('alert', 'Perubahan kehadiran tersimpan.');
    }

    /**
     * Debug helper: return attendances for a specific date (SuperAdmin only)
     */
    public function debugDate($date)
    {
        if (auth()->user()->role != 'SuperAdmin') {
            abort(403);
        }

        $attendances = Attendance::whereDate('date', $date)
            ->where('status', true)
            ->select('santri_id', DB::raw("DATE_FORMAT(date, '%Y-%m-%d') as date_str"), 'session')
            ->get();

        return response()->json([
            'date' => $date,
            'count' => $attendances->count(),
            'data' => $attendances,
        ]);
    }

    /**
     * Store attendance data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'date' => 'required|date',
            'subuh_ids' => 'array',
            'subuh_ids.*' => 'array',
            'subuh_ids.*.*' => 'uuid|exists:santris,id',
            'isya_ids' => 'array',
            'isya_ids.*' => 'array',
            'isya_ids.*.*' => 'uuid|exists:santris,id',
        ]);

        $date = $request->date;

        // Log raw request data to debug
        Log::info('Store request raw data', [
            'date' => $date,
            'all_input' => $request->all(),
            'subuh_ids_raw' => $request->input('subuh_ids'),
            'isya_ids_raw' => $request->input('isya_ids'),
        ]);

        // only take the arrays for the selected date
        $subuhIds = $request->input("subuh_ids.{$date}", []);
        $isyaIds = $request->input("isya_ids.{$date}", []);

        Log::info('Extracted IDs', ['date' => $date, 'subuh' => $subuhIds, 'isya' => $isyaIds]);

        // Deduplicate submitted IDs to guard against duplicate checkbox values
        $subuhIds = array_values(array_unique($subuhIds));
        $isyaIds = array_values(array_unique($isyaIds));

        try {
            DB::transaction(function () use ($date, $subuhIds, $isyaIds) {
                // Hapus semua kehadiran untuk tanggal ini terlebih dahulu menggunakan whereDate
                Attendance::whereDate('date', $date)->delete();

                // Simpan kehadiran sesi Subuh untuk santri yang checkbox-nya dicentang
                foreach ($subuhIds as $santriId) {
                    Attendance::updateOrCreate(
                        ['date' => $date, 'santri_id' => $santriId, 'session' => 'Subuh'],
                        ['status' => true]
                    );
                }

                // Simpan kehadiran sesi Isya untuk santri yang checkbox-nya dicentang
                foreach ($isyaIds as $santriId) {
                    Attendance::updateOrCreate(
                        ['date' => $date, 'santri_id' => $santriId, 'session' => 'Isya'],
                        ['status' => true]
                    );
                }
            });
        } catch (\Exception $e) {
            // Log error and return with message
            Log::error('Error saving attendances', ['date' => $date, 'error' => $e->getMessage(), 'subuh' => $subuhIds, 'isya' => $isyaIds]);
            return redirect()->back()->with('alert', 'Terjadi kesalahan saat menyimpan kehadiran. Silakan coba lagi.');
        }

        LogActivity::addToLog('Simpan Daftar Kehadiran tanggal ' . $date);
        // Log and clear cache so Rekapan reads fresh data
        Log::info('Simpan Daftar Kehadiran', ['date' => $date, 'subuh' => $subuhIds, 'isya' => $isyaIds]);
        Cache::flush();
        return redirect()->route('kehadiran.index', ['date' => $date])
            ->with('alert', 'Daftar kehadiran berhasil disimpan.');
    }

    /**
     * Display attendance report for a week (Sunday to Saturday).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function report(Request $request)
    {
        // Jika ada parameter date, gunakan tanggal tersebut, jika tidak gunakan hari ini
        $selectedDate = $request->date ?? date('Y-m-d');
        $date = Carbon::parse($selectedDate);

        // Hitung awal minggu (Minggu) - Carbon menggunakan 0 untuk Minggu
        $startOfWeek = $date->copy()->startOfWeek(Carbon::SUNDAY);
        $endOfWeek = $date->copy()->endOfWeek(Carbon::SATURDAY);

        // Generate array tanggal dari Minggu sampai Sabtu
        $weekDates = [];
        $currentDate = $startOfWeek->copy();
        $dayNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        for ($i = 0; $i < 7; $i++) {
            $weekDates[] = [
                'date' => $currentDate->format('Y-m-d'),
                'day_name' => $dayNames[$i],
                'day_number' => $currentDate->format('d'),
            ];
            $currentDate->addDay();
        }

        // Ambil semua santri (saring SuperAdmin untuk user non-SuperAdmin)
        // Santri hanya bisa lihat datanya sendiri
        $query = Santri::query();
        if (auth()->user()->role == 'Santri') {
            // Santri hanya lihat santri nya sendiri
            $query->whereHas('user', function ($q) {
                $q->where('id', auth()->id());
            });
        } else if (auth()->user()->role != 'SuperAdmin') {
            // Admin dan Pengurus tidak lihat SuperAdmin
            $query->whereDoesntHave('user', function ($q) {
                $q->where('role', 'SuperAdmin');
            });
        }
        $santris = $query->orderBy('name')->get();

        // Ambil semua kehadiran dalam rentang minggu tersebut
        // Gunakan whereDate untuk memastikan perbandingan tanggal yang akurat
        $startDate = $startOfWeek->format('Y-m-d');
        $endDate = $endOfWeek->format('Y-m-d');

        $attendances = Attendance::where('status', true)
            ->whereDate('date', '>=', $startDate)
            ->whereDate('date', '<=', $endDate)
            ->select('id', 'santri_id', DB::raw("DATE_FORMAT(date, '%Y-%m-%d') as date_str"), 'session')
            ->get()
            ->keyBy(function ($item) {
                return $item->santri_id . '_' . $item->date_str . '_' . $item->session;
            });

        // Buat struktur data untuk view
        $attendanceData = [];
        foreach ($santris as $santri) {
            $attendanceData[$santri->id] = [
                'name' => $santri->name,
                'days' => []
            ];

            foreach ($weekDates as $weekDate) {
                $dateStr = $weekDate['date'];

                // Cek kehadiran dengan key yang sudah dibuat
                $subuhKey = $santri->id . '_' . $dateStr . '_Subuh';
                $isyaKey = $santri->id . '_' . $dateStr . '_Isya';

                $subuhHadir = $attendances->has($subuhKey);
                $isyaHadir = $attendances->has($isyaKey);

                $attendanceData[$santri->id]['days'][$dateStr] = [
                    'subuh' => $subuhHadir,
                    'isya' => $isyaHadir,
                ];
            }
        }

        return view('kehadiran.report', compact('weekDates', 'attendanceData', 'startOfWeek', 'endOfWeek', 'selectedDate'));
    }

    /**
     * Export rekapan kehadiran sebagai PDF.
     */
    public function exportPdf(Request $request)
    {
        $selectedDate = $request->date ?? date('Y-m-d');
        $data = $this->buildAttendanceReportData($selectedDate);

        $pdf = Pdf::loadView('kehadiran.report_pdf', $data)
            ->setPaper('a4', 'landscape');

        $filename = 'rekapan-kehadiran-' . $data['startOfWeek']->format('Ymd') . '-' . $data['endOfWeek']->format('Ymd') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Export rekapan kehadiran sebagai file Excel.
     */
    public function exportExcel(Request $request)
    {
        $selectedDate = $request->date ?? date('Y-m-d');
        $data = $this->buildAttendanceReportData($selectedDate);
        $html = view('kehadiran.report_excel', $data)->render();
        $filename = 'rekapan-kehadiran-' . $data['startOfWeek']->format('Ymd') . '-' . $data['endOfWeek']->format('Ymd') . '.xls';

        return response($html, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Build attendance report data for PDF/Excel and HTML views.
     */
    private function buildAttendanceReportData(string $selectedDate): array
    {
        $date = Carbon::parse($selectedDate);

        $startOfWeek = $date->copy()->startOfWeek(Carbon::SUNDAY);
        $endOfWeek = $date->copy()->endOfWeek(Carbon::SATURDAY);

        $weekDates = [];
        $currentDate = $startOfWeek->copy();
        $dayNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        for ($i = 0; $i < 7; $i++) {
            $weekDates[] = [
                'date' => $currentDate->format('Y-m-d'),
                'day_name' => $dayNames[$i],
                'day_number' => $currentDate->format('d'),
            ];
            $currentDate->addDay();
        }

        $query = Santri::query();
        if (auth()->user()->role == 'Santri') {
            $query->whereHas('user', function ($q) {
                $q->where('id', auth()->id());
            });
        } elseif (auth()->user()->role != 'SuperAdmin') {
            $query->whereDoesntHave('user', function ($q) {
                $q->where('role', 'SuperAdmin');
            });
        }

        $santris = $query->orderBy('name')->get();

        $startDate = $startOfWeek->format('Y-m-d');
        $endDate = $endOfWeek->format('Y-m-d');

        $attendances = Attendance::where('status', true)
            ->whereDate('date', '>=', $startDate)
            ->whereDate('date', '<=', $endDate)
            ->select('id', 'santri_id', DB::raw("DATE_FORMAT(date, '%Y-%m-%d') as date_str"), 'session')
            ->get()
            ->keyBy(function ($item) {
                return $item->santri_id . '_' . $item->date_str . '_' . $item->session;
            });

        $attendanceData = [];
        foreach ($santris as $santri) {
            $attendanceData[$santri->id] = [
                'name' => $santri->name,
                'days' => []
            ];

            foreach ($weekDates as $weekDate) {
                $dateStr = $weekDate['date'];
                $subuhKey = $santri->id . '_' . $dateStr . '_Subuh';
                $isyaKey = $santri->id . '_' . $dateStr . '_Isya';

                $attendanceData[$santri->id]['days'][$dateStr] = [
                    'subuh' => $attendances->has($subuhKey),
                    'isya' => $attendances->has($isyaKey),
                ];
            }
        }

        return compact('weekDates', 'attendanceData', 'startOfWeek', 'endOfWeek', 'selectedDate');
    }

    /**
     * Show QR scanner page for admin/pengurus to scan santri QR.
     */
    public function scan()
    {
        return view('kehadiran.scan');
    }

    /**
     * Show QR code for a santri so they can present it to be scanned.
     */
    public function qrcode($id)
    {
        $santri = Santri::findOrFail($id);

        // Santri hanya bisa lihat QR code mereka sendiri
        if (auth()->user()->role == 'Santri') {
            $userSantri = auth()->user()->santris;
            if (!$userSantri || $userSantri->id !== $santri->id) {
                abort(403, 'Anda tidak memiliki hak akses untuk melihat QR code santri lain.');
            }
        }

        return view('kehadiran.qrcode', compact('santri'));
    }
}
