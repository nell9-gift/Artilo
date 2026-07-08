<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profils des Artisans Partenaires</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f4f4f4; }
        .container { max-width: 1200px; margin: auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        th { background: #333; color: white; }
        .miniature { width: 60px; height: 60px; object-fit: cover; border-radius: 4px; }
        .btn { background: #007bff; color: white; padding: 5px 15px; text-decoration: none; border-radius: 4px; display: inline-block; }
        .btn:hover { background: #0056b3; }
        .pagination { margin-top: 20px; }
        .empty { color: #888; font-size: 18px; }
        .badge-dispo { background: #28a745; color: white; padding: 2px 8px; border-radius: 4px; font-size: 13px; }
        .badge-indispo { background: #6c757d; color: white; padding: 2px 8px; border-radius: 4px; font-size: 13px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>👷 Profils des Artisans Partenaires</h1>

        @if($artisans->isEmpty())
            <p class="empty">Aucun artisan n'a encore rempli son profil.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Métier</th>
                        <th>Expérience</th>
                        <th>Photos</th>
                        <th>Adresse</th>
                        <th>Disponibilité</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($artisans as $artisan)
                        <tr>
                            <td>{{ $artisan->user->name }}</td>
                            <td>{{ $artisan->profession }}</td>
                            <td>{{ $artisan->years_experience ?? 0 }} ans</td>
                            <td>
                                @if(!empty($artisan->photos))
                                    <img src="{{ asset('storage/' . $artisan->photos[0]) }}" class="miniature">
                                @else
                                    Aucune
                                @endif
                            </td>
                            <td>{{ $artisan->address ?? 'Non renseignée' }}</td>
                            <td>
                                @if($artisan->availability_start_time && $artisan->availability_end_time)
                                    <span class="badge-dispo">
                                        {{ $artisan->availability_start_time->format('H:i') }} - {{ $artisan->availability_end_time->format('H:i') }}
                                    </span>
                                @else
                                    <span class="badge-indispo">Non renseignée</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.profils.show', $artisan->id) }}" class="btn">👁️ Voir</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="pagination">
                {{ $artisans->links() }}
            </div>
        @endif
    </div>
</body>
</html>