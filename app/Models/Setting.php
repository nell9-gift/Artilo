<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model {
    protected $fillable = ['key', 'value', 'type'];

    // Méthode helper pour lire une valeur facilement
    public static function get(string $key, string $default = ''): string {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }
}