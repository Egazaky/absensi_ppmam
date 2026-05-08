<?php

namespace App\Http\Controllers\Web;

use App\Helpers\LogActivity;
use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Santri;
use Carbon\Carbon;
use Illuminate\Http\Request;

class QiyamullailController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display qiyam recap from Saturday to Thursday for each santri.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function report(Request $request)
    {
        $selectedDate = $request->date ?? date('Y-m-d');
        $date = Carbon::parse($selectedDate);

        // Start from Saturday of the week that contains the selected date
        $startOfPeriod = $date->copy()->startOfWeek(Carbon::SATURDAY);
        // Saturday to Thursday => 6 days (Sat + 5 = Thu)
        $endOfPeriod = $startOfPeriod->copy()->addDays(5);

        // Generate dates array: Sabtu -> Kamis
        $weekDates = [];
        $dayNames = ['Sabtu', 'Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis'];
        $current = $startOfPeriod->copy();
        for ($i = 0; $i < 6; $i++) {
            $weekDates[] = [
                'date' => $current->format('Y-m-d'),
                'day_name' => $dayNames[$i],
                'day_number' => $current->format('d'),
            ];
            $current->addDay();
        }

        $query = Santri::query();

        // Santri hanya bisa lihat datanya sendiri
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

        $startDate = $startOfPeriod->format('Y-m-d');
        $endDate = $endOfPeriod->format('Y-m-d');

        $attendances = Attendance::where('status', true)
            ->where('session', 'Qiyamullail')
            ->whereDate('date', '>=', $startDate)
            ->whereDate('date', '<=', $endDate)
            ->get()
            ->keyBy(function ($item) {
                return $item->santri_id . '_' . $item->date->format('Y-m-d');
            });

        $attendanceData = [];
        foreach ($santris as $santri) {
            $attendanceData[$santri->id] = [
                'name' => $santri->name,
                'days' => []
            ];

            foreach ($weekDates as $wd) {
                $key = $santri->id . '_' . $wd['date'];
                $present = $attendances->has($key);
                $attendanceData[$santri->id]['days'][$wd['date']] = $present;
            }
        }

        return view('qiyamullail.report', compact('weekDates', 'attendanceData', 'startOfPeriod', 'endOfPeriod', 'selectedDate'));
    }

    public function index(Request $request)
    {
        $date = $request->date ?? date('Y-m-d');
        $query = Santri::query();

        // Jika user bukan SuperAdmin, jangan tampilkan santri dengan user role SuperAdmin
        if (auth()->user()->role != 'SuperAdmin') {
            $query->whereDoesntHave('user', function ($q) {
                $q->where('role', 'SuperAdmin');
            });
        }

        $santris = $query->orderBy('name')->get();

        // Ambil daftar kehadiran untuk sesi Qiyamullail pada tanggal ini
        $attended = Attendance::whereDate('date', $date)
            ->where('session', 'Qiyamullail')
            ->where('status', true)
            ->pluck('santri_id')
            ->toArray();

        return view('qiyamullail.index', compact('santris', 'attended', 'date'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'santri_id' => 'required|uuid|exists:santris,id',
            'date' => 'required|date',
        ]);

        $santriId = $request->santri_id;
        $date = Carbon::parse($request->date)->format('Y-m-d');

        // Simpan atau update attendance untuk sesi Qiyamullail
        Attendance::updateOrCreate(
            [
                'date' => $date,
                'santri_id' => $santriId,
                'session' => 'Qiyamullail'
            ],
            ['status' => true]
        );

        LogActivity::addToLog('Absensi Qiyamullail disimpan untuk santri ' . $santriId . ' tanggal ' . $date);

        return redirect()->route('qiyam.index', ['date' => $date])->with('alert', 'Absensi tersimpan.');
    }
}
