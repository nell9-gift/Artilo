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
        input[type="text"], input[type="number"], textarea { width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
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
        <h1>📝 Modifier mon profil</h1>

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
</body>
</html>