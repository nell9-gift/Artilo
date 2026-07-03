<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validation des Artisans</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f4f4f4; }
        .container { max-width: 1200px; margin: auto; background: white; padding: 20px; border-radius: 8px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: left; }
        th { background: #333; color: white; }
        .btn-valid { background: green; color: white; border: none; padding: 5px 15px; border-radius: 4px; cursor: pointer; }
        .btn-refuse { background: red; color: white; border: none; padding: 5px 15px; border-radius: 4px; cursor: pointer; }
        .input-refus { padding: 5px; border: 1px solid #ddd; border-radius: 4px; }
        .alert-success { background: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>👷 Artisans en attente de validation</h1>

        {{-- Affichage du message de succès (flash) --}}
        @if(session('success'))
            <div class="alert-success">
                {{ session('success') }}
            </div>
        @endif

        {{-- Vérification si la liste est vide --}}
        @if($artisans->isEmpty())
            <p style="color: #888; font-size: 18px;">✅ Aucun artisan en attente de validation pour le moment.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Téléphone</th>
                        <th>Métier</th>
                        <th>Zone</th>
                        <th>Document</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($artisans as $artisan)
                        <tr>
                            <td>{{ $artisan->user->name }}</td>
                            <td>{{ $artisan->user->email }}</td>
                            <td>{{ $artisan->user->telephone ?? 'Non renseigné' }}</td>
                            <td>{{ $artisan->profession }}</td>
                            <td>{{ $artisan->intervention_area }}</td>
                            <td>
                                {{-- Lien pour voir le document (si tu as la route) --}}
                                <a href="{{ route('admin.artisans.document', $artisan) }}" target="_blank">📄 Voir</a>
                            </td>
                            <td style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                                
                                {{-- 🔵 BOUTON VALIDER (simple formulaire POST) --}}
                                <form action="{{ route('admin.artisans.valider', $artisan) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn-valid">✅ Valider</button>
                                </form>

                                {{-- 🔴 FORMULAIRE REFUSER (avec champ motif) --}}
                                <form action="{{ route('admin.artisans.refuser', $artisan) }}" method="POST" style="display: flex; gap: 5px; align-items: center;">
                                    @csrf
                                    <input type="text" name="refusal_reason" class="input-refus" placeholder="Motif du refus" required>
                                    <button type="submit" class="btn-refuse">❌ Refuser</button>
                                </form>

                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Liens de pagination --}}
            <div style="margin-top: 20px;">
                {{ $artisans->links() }}
            </div>
        @endif
    </div>
</body>
</html>