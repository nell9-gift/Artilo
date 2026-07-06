<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tableau de bord Artisan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">
                        Bienvenue, {{ Auth::user()->name }}
                    </h3>
                    <a href="{{ route('artisan.profil.edit') }}" class="nav-link">
                        ✏️ Modifier mon profil
                    </a>
                    @if ($artisan)
                        <p><strong>Métier :</strong> {{ ucfirst($artisan->profession) }}</p>
                        <p><strong>Zone d'intervention :</strong> {{ $artisan->intervention_area }}</p>
                        <p><strong>Statut :</strong>
                            <span class="text-green-600 font-medium">Validé ✅</span>
                        </p>
                    @else
                        <p class="text-red-500">Profil artisan introuvable.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>