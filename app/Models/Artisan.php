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
        'identity_document_type',
        'identity_document_number',
        'identity_photo_recto',
        'identity_photo_verso',
        'identity_selfie',
        'identity_expiration_date',
        'main_profession',
        'sub_specialties',
        'diplomas',
        'certifications',
        'company_name',
        'company_registration_number',
        'intervention_region',
        'intervention_prefecture',
        'intervention_city',
        'languages',
    ];

    // Automatic conversion of the schedules and photos fields to PHP arrays
    protected $casts = [
        'schedules' => 'array',
        'photos' => 'array',
        'sub_specialties' => 'array',
        'diplomas' => 'array',
        'certifications' => 'array',
        'languages' => 'array',
        'availability_start_time' => 'datetime:H:i',
        'availability_end_time' => 'datetime:H:i',
        'identity_expiration_date' => 'date',
        'max_distance_km' => 'integer',
    ];

    // Relation: an artisan belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
