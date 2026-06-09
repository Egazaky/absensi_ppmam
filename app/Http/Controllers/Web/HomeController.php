<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Santri;
use App\Models\Schedule;
use App\Models\User;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @param  App\Models\Santri  $santri
     * @param  App\Models\User  $user
     * @param  App\Models\Schedule  $schedule
     * @param  App\Models\Attendance  $attendance
     * @return Illuminate\Contracts\Support\Renderable
     */
    public function index(Santri $santri, User $user, Schedule $schedule, Attendance $attendance)
    {
        $santri = $santri->count();
        $users = $user->where('role', '!=', 'SuperAdmin')->count();
        $schedules = $schedule->count();
        $todayAttendances = $attendance->whereDate('date', date('Y-m-d'))
            ->where('status', true)
            ->count();

        return view('home', compact(
            'santri',
            'users',
            'schedules',
            'todayAttendances'
        ));
    }
}
