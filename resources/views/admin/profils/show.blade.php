<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Détail du profil - {{ $artisan->user->name }}</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f4f4f4; }
        .container { max-width: 800px; margin: auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .info { margin-bottom: 15px; }
        .info strong { display: inline-block; width: 180px; }
        .photos { display: flex; flex-wrap: wrap; gap: 15px; margin-top: 15px; }
        .photos img { width: 150px; height: 150px; object-fit: cover; border-radius: 8px; border: 1px solid #ddd; }
        .btn-retour { background: #6c757d; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; display: inline-block; margin-top: 20px; }
        .btn-retour:hover { background: #5a6268; }
        h3 { margin-top: 25px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>{{ $artisan->user->name }}</h1>

        <div class="info"><strong>Métier :</strong> {{ $artisan->profession }}</div>
        <div class="info"><strong>Années d'expérience :</strong> {{ $artisan->years_experience ?? 0 }}</div>
        <div class="info"><strong>Adresse :</strong> {{ $artisan->address ?? 'Non renseignée' }}</div>
        <div class="info"><strong>Note moyenne :</strong> {{ $artisan->average_rating ?? 'Pas encore noté' }}</div>
         @if($artisan->latitude && $artisan->longitude)
            <h3>Localisation sur la carte</h3>
            <div id="map" style="height: 300px; border-radius: 8px; margin-bottom: 20px;"></div>
        @else
            <div class="info"><strong>Localisation :</strong> Non renseignée par l'artisan</div>
        @endif
        <div class="info"><strong>Description :</strong> {{ $artisan->description ?? 'Aucune description' }}</div>

        <h3>Disponibilité et zone d'intervention</h3>
        <div class="info">
            <strong>Horaires de disponibilité :</strong>
            @if($artisan->availability_start_time && $artisan->availability_end_time)
                {{ $artisan->availability_start_time->format('H:i') }} - {{ $artisan->availability_end_time->format('H:i') }}
            @else
                Non renseignés
            @endif
        </div>
        <div class="info">
            <strong>Rayon d'intervention :</strong>
            {{ $artisan->max_distance_km ? $artisan->max_distance_km . ' km' : 'Non renseigné' }}
        </div>

        <h3>Coordonnées de paiement</h3>
        <div class="info">
            <strong>Mobile Money :</strong>
            @if($artisan->mobile_money_number)
                {{ $artisan->mobile_money_number }}
                @if($artisan->mobile_money_operator)
                    ({{ $artisan->mobile_money_operator === 'moov' ? 'Moov Money' : 'Yas Money' }})
                @endif
            @else
                Non renseigné
            @endif
        </div>

        <h3>Photos de réalisations</h3>
        <div class="photos">
            @if(!empty($artisan->photos))
                @foreach($artisan->photos as $photo)
                    <img src="{{ asset('storage/' . $photo) }}" alt="Photo">
                @endforeach
            @else
                <p>Aucune photo téléchargée.</p>
            @endif
        </div>

        <a href="{{ route('admin.profils.index') }}" class="btn-retour">← Retour à la liste</a>
    </div>
      @if($artisan->latitude && $artisan->longitude)
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <script>
            const map = L.map('map').setView([{{ $artisan->latitude }}, {{ $artisan->longitude }}], 15);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            L.marker([{{ $artisan->latitude }}, {{ $artisan->longitude }}])
                .addTo(map)
                .bindPopup("{{ $artisan->user->name }}")
                .openPopup();
        </script>
    @endif
</body>
</html>