<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;

class CheckQiyamTime
{
    /**
     * Handle an incoming request.
     * Allow access for users who are NOT role 'Santri' at any time.
     * For users with role 'Santri', allow access only between 01:30 and 03:30.
     *
     * @param  Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = auth()->user();

        if (! $user) {
            abort(403, 'Unauthorized');
        }

        // jika bukan Santri, boleh akses kapan saja
        if (strtolower($user->role) !== 'santri') {
            return $next($request);
        }

        // Cek waktu sekarang server (gunakan Carbon)
        $now = Carbon::now();

        // Definisikan batas waktu: 01:30 -> 03:30
        $start = Carbon::createFromTime(1, 30, 0, $now->tzName);
        $end = Carbon::createFromTime(3, 30, 0, $now->tzName);

        if ($now->between($start, $end)) {
            return $next($request);
        }

        abort(403, 'Absensi Qiyamullail hanya dapat diakses antara pukul 01:30 sampai 03:30 oleh Santri.');
    }
}
