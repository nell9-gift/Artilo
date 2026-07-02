<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold mb-6">✅ Validation des Artisans</h1>

                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($artisans->isEmpty())
                        <p class="text-gray-500">🎉 Aucun artisan en attente de validation.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nom</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Métier</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Zone</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pièce d'identité</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($artisans as $artisan)
                                    <tr>
                                        <td class="px-6 py-4">{{ $artisan->user->name }}</td>
                                        <td class="px-6 py-4">{{ $artisan->user->email }}</td>
                                        <td class="px-6 py-4">{{ $artisan->metier }}</td>
                                        <td class="px-6 py-4">{{ $artisan->zone_intervention }}</td>
                                        <td class="px-6 py-4">
                                            <a href="#" class="text-blue-500 underline">Voir le fichier</a>
                                            {{-- TODO: Ajouter un lien de téléchargement plus tard --}}
                                        </td>
                                        <td class="px-6 py-4 space-x-2">
                                            {{-- Bouton Valider --}}
                                            <form action="{{ route('admin.artisans.valider', $artisan) }}" method="POST" class="inline-block">
                                                @csrf
                                                <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-3 rounded text-sm">
                                                    ✅ Valider
                                                </button>
                                            </form>

                                            {{-- Bouton Refuser --}}
                                            <form action="{{ route('admin.artisans.refuser', $artisan) }}" method="POST" class="inline-block" onsubmit="return confirm('Êtes-vous sûr de vouloir refuser cet artisan ?');">
                                                @csrf
                                                <input type="text" name="motif_refus" placeholder="Motif du refus" class="border rounded px-2 py-1 text-sm" required>
                                                <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-sm">
                                                     Refuser
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        {{ $artisans->links() }}
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>