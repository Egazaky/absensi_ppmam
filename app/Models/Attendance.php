<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory, Uuids;

    public $incrementing = false;

    protected $guarded = [];

    protected $casts = [
        'status' => 'boolean',
        'date' => 'date:Y-m-d',
    ];

    protected $fillable = [
        'date',
        'santri_id',
        'session',
        'status',
    ];

    public function santri()
    {
        return $this->belongsTo(Santri::class, 'santri_id');
    }
}
