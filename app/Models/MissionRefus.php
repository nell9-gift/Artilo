<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MissionRefus extends Model
{
    protected $table = 'mission_refus';

    protected $fillable = [
        'mission_id',
        'artisan_id',
        'motif',
    ];

    public function mission()
    {
        return $this->belongsTo(Mission::class);
    }

    public function artisan()
    {
        return $this->belongsTo(Artisan::class);
    }
}