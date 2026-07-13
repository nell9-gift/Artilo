<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // ── COULEURS ──────────────────────────────
            ['key' => 'color_primary',          'value' => '#7C3AED',   'type' => 'color'],
            ['key' => 'color_primary_dark',     'value' => '#6D28D9',   'type' => 'color'],
            ['key' => 'color_primary_900',      'value' => '#2E1065',   'type' => 'color'],
            ['key' => 'color_primary_800',      'value' => '#4C1D95',   'type' => 'color'],
            ['key' => 'color_primary_light',    'value' => '#A78BFA',   'type' => 'color'],
            ['key' => 'color_secondary',        'value' => '#D97706',   'type' => 'color'],
            ['key' => 'color_secondary_light',  'value' => '#F59E0B',   'type' => 'color'],
            ['key' => 'color_bg',               'value' => '#FFFFFF',   'type' => 'color'],
            ['key' => 'color_bg_2',             'value' => '#FAF8FF',   'type' => 'color'],
            ['key' => 'color_text',             'value' => '#2E1065',   'type' => 'color'],
            ['key' => 'color_muted',            'value' => '#6B5B95',   'type' => 'color'],

            // ── IMAGES (logo, favicon, héros, métiers, carte) ──
            ['key' => 'logo',                    'value' => 'images/logo.jpeg',            'type' => 'image'],
            ['key' => 'favicon',                 'value' => 'images/favicon.png',                'type' => 'image'],
            ['key' => 'hero_image_1',            'value' => 'images/artisan_plombier.png',       'type' => 'image'],
            ['key' => 'hero_image_2',            'value' => 'images/artisan_electricien.png',    'type' => 'image'],
            ['key' => 'hero_image_3',            'value' => 'images/artisan_btp.png',            'type' => 'image'],
            ['key' => 'hero_image_4',            'value' => 'images/artisan_charpenterie.png',   'type' => 'image'],
            ['key' => 'togo_map_image',          'value' => 'images/togo-map-artilo.png',        'type' => 'image'],
            ['key' => 'trade_image_plomberie',   'value' => 'images/plomberie.jfif',             'type' => 'image'],
            ['key' => 'trade_image_electricite', 'value' => 'images/electricité.jfif',           'type' => 'image'],
            ['key' => 'trade_image_maconnerie',  'value' => 'images/maçonnerie.jfif',            'type' => 'image'],
            ['key' => 'trade_image_menuiserie',  'value' => 'images/menuiserie.jfif',            'type' => 'image'],

            // ── NOUVELLES IMAGES (bâtiment, construction, outils, etc.) ──
            ['key' => 'image_batiment',          'value' => 'images/batiment.jfif',              'type' => 'image'],
            ['key' => 'image_building',          'value' => 'images/Building.jfif',              'type' => 'image'],
            ['key' => 'image_construction',      'value' => 'images/construction.jfif',          'type' => 'image'],
            ['key' => 'image_outils',            'value' => 'images/outils.jfif',                'type' => 'image'],
            ['key' => 'image_plan',              'value' => 'images/plan.jfif',                  'type' => 'image'],
            ['key' => 'image_usine',             'value' => 'images/usine.jfif',                 'type' => 'image'],
            ['key' => 'image_maison',            'value' => 'images/maison.jfif',                'type' => 'image'],

            // ── TEXTES généraux ──────────────────────
            ['key' => 'site_name',      'value' => 'Artilo',                     'type' => 'text'],
            ['key' => 'contact_phone',  'value' => '+228 92 89 37 97',           'type' => 'text'],
            ['key' => 'contact_email',  'value' => 'pacomedzah720@gmail.com',    'type' => 'text'],
            ['key' => 'contact_city',   'value' => 'Lomé, Togo',                 'type' => 'text'],

            // ═══════════════════════════════════════════════
            // ── PAGE AUTHENTIFICATION (login) ─────────────
            // ═══════════════════════════════════════════════

            // Textes du formulaire
            ['key' => 'auth_title',                'value' => 'Bon retour',                                'type' => 'text'],
            ['key' => 'auth_subtitle',             'value' => 'Connectez-vous pour accéder à votre espace', 'type' => 'text'],
            ['key' => 'auth_email_label',          'value' => 'Email',                                    'type' => 'text'],
            ['key' => 'auth_email_placeholder',    'value' => 'Entrez votre email',                       'type' => 'text'],
            ['key' => 'auth_password_label',       'value' => 'Mot de passe',                             'type' => 'text'],
            ['key' => 'auth_password_placeholder', 'value' => '••••••••',                                 'type' => 'text'],
            ['key' => 'auth_remember_text',        'value' => 'Se souvenir de moi',                       'type' => 'text'],
            ['key' => 'auth_forgot_text',          'value' => 'Mot de passe oublié ?',                    'type' => 'text'],
            ['key' => 'auth_login_button',         'value' => 'Se connecter',                             'type' => 'text'],
            ['key' => 'auth_or_text',              'value' => 'ou',                                       'type' => 'text'],
            ['key' => 'auth_google_button',        'value' => 'Continuer avec Google',                    'type' => 'text'],
            ['key' => 'auth_register_question',    'value' => 'Pas encore de compte ?',                   'type' => 'text'],
            ['key' => 'auth_register_link_text',   'value' => 'Inscrivez-vous ici',                       'type' => 'text'],

            // Images de fond (locales, dossier public/images/) ──
            ['key' => 'auth_background_image_1',   'value' => 'images/artisan_btp.png',           'type' => 'image'],
            ['key' => 'auth_background_image_2',   'value' => 'images/artisan_charpenterie.png',  'type' => 'image'],
            ['key' => 'auth_background_image_3',   'value' => 'images/artisan_electricien.png',   'type' => 'image'],
        ];

        foreach ($settings as $s) {
            Setting::updateOrCreate(['key' => $s['key']], $s);
        }
    }
}
