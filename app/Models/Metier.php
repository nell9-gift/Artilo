<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Metier extends Model
{
    protected $fillable = ['nom', 'slug', 'icone', 'description', 'actif'];

    public function artisans()    { return $this->hasMany(Artisan::class); }
    public function prestations() { return $this->hasMany(Prestation::class); }
    public function salaires()    { return $this->hasMany(SalaireJournalier::class); }
}
