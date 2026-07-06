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
    'years_experience',   // ← modifié
    'photos',
    'average_rating',     // ← modifié
    'address',            // ← modifié
    'latitude',
    'longitude',
];

    // Automatic conversion of the schedules and photos fields to PHP arrays
    protected $casts = [
        'schedules' => 'array',
        'photos' => 'array',
    ];

    // Relation: an artisan belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
