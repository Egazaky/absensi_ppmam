<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Santri extends Model
{
    use HasFactory, Uuids;

    public $incrementing = false;

    protected $guarded = [];

    protected static function booted()
    {
        static::creating(function ($santri) {
            if (empty($santri->nis)) {
                $year = $santri->entry_year ?? date('Y');
                
                $maxNis = static::where('entry_year', $year)
                    ->where('nis', 'like', $year . '%')
                    ->max('nis');
                
                if ($maxNis) {
                    $seq = intval(substr($maxNis, 4)) + 1;
                } else {
                    $seq = 1;
                }
                
                $santri->nis = $year . str_pad($seq, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}
