<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $schedules = Schedule::with('creator')->orderBy('date', 'desc')->get();

        return view('jadwal.index', compact('schedules'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (Auth::user()->role == 'Santri') {
            abort(403, 'Unauthorized');
        }

        return view('jadwal.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (Auth::user()->role == 'Santri') {
            abort(403, 'Unauthorized');
        }
        $request->validate([
            'title' => 'required|string|max:255',
            'teacher' => 'required|string|max:255',
            'description' => 'nullable|string',
            'session' => 'required|in:isya,subuh',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
        ]);

        $time1 = \Carbon\Carbon::parse($request->time)->format('H:i');
        $time2 = \Carbon\Carbon::parse($request->time)->format('H:i:s');

        $conflict = Schedule::whereDate('date', $request->date)
            ->whereIn('time', [$time1, $time2])
            ->where('session', $request->session)
            ->exists();

        if ($conflict) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'date' => 'Jadwal pada tanggal, waktu, dan sesi tersebut sudah tersedia atau bentrok.',
            ]);
        }

        Schedule::create([
            'title' => $request->title,
            'teacher' => $request->teacher,
            'description' => $request->description,
            'session' => $request->session,
            'date' => $request->date,
            'time' => $request->time,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('jadwal.index')->with('success', 'Jadwal berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $schedule = Schedule::with('creator')->findOrFail($id);

        return view('jadwal.show', compact('schedule'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        if (Auth::user()->role == 'Santri') {
            abort(403, 'Unauthorized');
        }
        $schedule = Schedule::findOrFail($id);

        return view('jadwal.edit', compact('schedule'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        if (Auth::user()->role == 'Santri') {
            abort(403, 'Unauthorized');
        }

        $schedule = Schedule::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'teacher' => 'required|string|max:255',
            'description' => 'nullable|string',
            'session' => 'required|in:isya,subuh',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
        ]);

        $time1 = \Carbon\Carbon::parse($request->time)->format('H:i');
        $time2 = \Carbon\Carbon::parse($request->time)->format('H:i:s');

        $conflict = Schedule::whereDate('date', $request->date)
            ->whereIn('time', [$time1, $time2])
            ->where('session', $request->session)
            ->where('id', '!=', $schedule->id)
            ->exists();

        if ($conflict) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'date' => 'Jadwal pada tanggal, waktu, dan sesi tersebut sudah tersedia atau bentrok.',
            ]);
        }

        $schedule->update([
            'title' => $request->title,
            'teacher' => $request->teacher,
            'description' => $request->description,
            'session' => $request->session,
            'date' => $request->date,
            'time' => $request->time,
        ]);

        return redirect()->route('jadwal.index')->with('success', 'Jadwal berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if (Auth::user()->role == 'Santri') {
            abort(403, 'Unauthorized');
        }
        $schedule = Schedule::findOrFail($id);
        $schedule->delete();

        return redirect()->route('jadwal.index')->with('success', 'Jadwal berhasil dihapus.');
    }
}
