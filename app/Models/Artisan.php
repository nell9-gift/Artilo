<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Model representing an artisan
class Artisan extends Model
{
    // Fields allowed to be filled when creating or updating
   protected $fillable = [
    'user_id',
    'profession',
    'intervention_area',
    'identity_document',
    'status',
    'refusal_reason',
    'schedules',
    'description',
    'years_experience',
    'photos',
    'average_rating',
    'address',
    'latitude',
    'longitude',
    'mobile_money_number',
    'mobile_money_operator',
    'availability_start_time',
    'availability_end_time',
    'max_distance_km',
];

    // Automatic conversion of the schedules and photos fields to PHP arrays
    protected $casts = [
        'schedules' => 'array',
        'photos' => 'array',
        'availability_start_time' => 'datetime:H:i',
        'availability_end_time' => 'datetime:H:i',
        'max_distance_km' => 'integer',
    ];

    // Relation: an artisan belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
