<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil Artisan</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; padding: 20px; }
        .container { max-width: 700px; margin: auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        label { font-weight: bold; display: block; margin-top: 20px; }
        input[type="text"], input[type="number"], input[type="time"], textarea, select { width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        textarea { resize: vertical; }
        input[type="file"] { margin-top: 5px; }
        button { background: #28a745; color: white; border: none; padding: 12px 25px; margin-top: 25px; border-radius: 4px; cursor: pointer; font-size: 16px; }
        button:hover { background: #218838; }
        .success { background: #d4edda; color: #155724; padding: 12px; border-radius: 4px; margin-bottom: 20px; }
        .error { background: #f8d7da; color: #721c24; padding: 12px; border-radius: 4px; margin-bottom: 20px; }
        .photos-preview { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 10px; }
        .photos-preview img { width: 80px; height: 80px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd; }
        small { color: #666; display: block; margin-top: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Modifier mon profil</h1>

        @if(session('success'))
            <div class="success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="error">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('artisan.profil.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- 1. Description -->
            <label for="description">Description (présentation, parcours, spécialités) :</label>
            <textarea name="description" id="description" rows="5" placeholder="Parlez de vous...">{{ old('description', $artisan->description) }}</textarea>

            <!-- 2. Années d'expérience -->
            <label for="years_experience">Années d'expérience :</label>
            <input type="number" name="years_experience" id="years_experience" min="0" max="50" value="{{ old('years_experience', $artisan->years_experience) }}">

            <!-- 3. Adresse (pour le matching géographique) -->
            <label for="address">Adresse précise :</label>
            <input type="text" name="address" id="address" value="{{ old('address', $artisan->address) }}" placeholder="Ex: 123 Rue de la République, Lomé">
            <small>Cette adresse servira à vous géolocaliser pour proposer vos services aux clients proches.</small>

            <!-- 3bis. Géolocalisation automatique (latitude/longitude) -->
            <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude', $artisan->latitude) }}">
            <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', $artisan->longitude) }}">

            <button type="button" id="detect-location">📍 Utiliser ma position actuelle</button>
            <p id="location-status">
                @if($artisan->latitude && $artisan->longitude)
                    Position déjà enregistrée.
                @endif
            </p>

            <!-- 5. Rayon d'intervention maximal -->
            <label for="max_distance_km">Rayon d'intervention maximal (en km) :</label>
            <input type="number" name="max_distance_km" id="max_distance_km" min="1" max="100" value="{{ old('max_distance_km', $artisan->max_distance_km) }}" placeholder="Ex: 10">
            <small>Distance maximale que vous êtes prêt à parcourir pour une mission.</small>

            <!-- 6. Horaires de disponibilité -->
            <label for="availability_start_time">Heure de début de disponibilité :</label>
            <input type="time" name="availability_start_time" id="availability_start_time" value="{{ old('availability_start_time', $artisan->availability_start_time) }}">

            <label for="availability_end_time">Heure de fin de disponibilité :</label>
            <input type="time" name="availability_end_time" id="availability_end_time" value="{{ old('availability_end_time', $artisan->availability_end_time) }}">
            <small>Ces horaires seront utilisés pour savoir quand vous êtes disponible pour recevoir des missions.</small>

            <!-- 7. Coordonnées de paiement Mobile Money -->
            <label for="mobile_money_number">Numéro Mobile Money (pour être payé) :</label>
            <input type="text" name="mobile_money_number" id="mobile_money_number" value="{{ old('mobile_money_number', $artisan->mobile_money_number) }}" placeholder="Ex: 90 12 34 56">

            <label for="mobile_money_operator">Opérateur :</label>
            <select name="mobile_money_operator" id="mobile_money_operator">
                <option value="">-- Choisir --</option>
                <option value="moov" {{ old('mobile_money_operator', $artisan->mobile_money_operator) == 'moov' ? 'selected' : '' }}>Moov Money</option>
                <option value="yas" {{ old('mobile_money_operator', $artisan->mobile_money_operator) == 'yas' ? 'selected' : '' }}>Yas Money</option>
            </select>

            <!-- 4. Photos (upload multiple) -->
            <label for="photos">Photos de vos réalisations (plusieurs possibles) :</label>
            <input type="file" name="photos[]" id="photos" accept="image/*" multiple>

            @if(!empty($artisan->photos))
                <div class="photos-preview">
                    @foreach($artisan->photos as $photo)
                        <img src="{{ asset('storage/' . $photo) }}" alt="Photo artisan">
                    @endforeach
                </div>
                <small>Photos actuellement en ligne.</small>
            @endif

            <button type="submit">💾 Mettre à jour le profil</button>
        </form>
    </div>

    <script>
        document.getElementById('detect-location').addEventListener('click', function () {
            const status = document.getElementById('location-status');

            if (!navigator.geolocation) {
                status.textContent = "La géolocalisation n'est pas supportée par votre navigateur.";
                return;
            }

            status.textContent = "Détection en cours...";

            navigator.geolocation.getCurrentPosition(
                function (position) {
                    document.getElementById('latitude').value = position.coords.latitude;
                    document.getElementById('longitude').value = position.coords.longitude;

                    status.textContent = "Position détectée avec succès ✅";
                },
                function () {
                    status.textContent = "Impossible de récupérer votre position. Vérifiez que la localisation est activée.";
                }
            );
        });
    </script>
</body>
</html>