<x-app-layout>
    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- En-tête -->
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold">📋 Suivi de mission</h2>
                        <span class="px-3 py-1 text-sm font-semibold rounded-full 
                            {{ in_array($mission->statut, ['payee']) ? 'bg-green-100 text-green-800' : '' }}
                            {{ $mission->statut === 'en_attente' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $mission->statut === 'affectee' ? 'bg-blue-100 text-blue-800' : '' }}
                            {{ $mission->statut === 'annulee' ? 'bg-red-100 text-red-800' : '' }}
                            {{ $mission->statut === 'en_cours' ? 'bg-purple-100 text-purple-800' : '' }}
                            {{ $mission->statut === 'acceptee' ? 'bg-indigo-100 text-indigo-800' : '' }}
                            {{ in_array($mission->statut, ['devis_en_attente_validation', 'devis_valide', 'devis_envoye']) ? 'bg-teal-100 text-teal-800' : '' }}
                            ">
                            {{ ucfirst(str_replace('_', ' ', $mission->statut)) }}
                        </span>
                    </div>

                    <!-- Informations -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <span class="font-semibold">🔧 Métier :</span>
                            <span>{{ $mission->metier_requis }}</span>
                        </div>
                        <div>
                            <span class="font-semibold">📅 Créée le :</span>
                            <span>{{ $mission->created_at->format('d/m/Y à H:i') }}</span>
                        </div>
                        @if($mission->date_souhaitee)
                        <div>
                            <span class="font-semibold">📆 Date souhaitée :</span>
                            <span>{{ $mission->date_souhaitee->format('d/m/Y') }}</span>
                        </div>
                        @endif
                        <div>
                            <span class="font-semibold">📍 Adresse :</span>
                            <span>{{ $mission->adresse }}</span>
                        </div>
                    </div>

                    <div class="mb-6">
                        <span class="font-semibold">📝 Description :</span>
                        <p class="mt-1 text-gray-700 p-3 bg-gray-50 rounded">{{ $mission->description }}</p>
                    </div>

                    <!-- Photos -->
                    @if($mission->photos && count($mission->photos) > 0)
                    <div class="mb-6">
                        <span class="font-semibold">🖼️ Photos :</span>
                        <div class="flex flex-wrap gap-2 mt-2">
                            @foreach($mission->photos as $photo)
                                <img src="{{ asset('storage/' . $photo) }}" 
                                     alt="Photo" 
                                     class="w-24 h-24 object-cover rounded cursor-pointer hover:opacity-75"
                                     onclick="window.open(this.src)">
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Statut -->
                    <div class="mt-6 p-4 rounded-lg 
                        {{ $mission->statut === 'annulee' ? 'bg-red-50 border border-red-200' : '' }}
                        {{ $mission->statut === 'payee' ? 'bg-green-50 border border-green-200' : '' }}
                        {{ $mission->statut === 'en_attente' ? 'bg-yellow-50 border border-yellow-200' : '' }}
                        {{ $mission->statut === 'affectee' ? 'bg-blue-50 border border-blue-200' : '' }}
                        ">

                        @switch($mission->statut)
                            @case('en_attente')
                                <p class="text-yellow-600">⏳ Recherche d'un artisan disponible...</p>
                                <div class="mt-2 animate-pulse text-sm text-gray-500">
                                    Un artisan sera affecté automatiquement dans quelques instants.
                                </div>
                                <div class="mt-4 flex gap-2">
                                    <form action="{{ route('particulier.mission.annuler', $mission) }}" 
                                          method="POST" 
                                          onsubmit="return confirm('Êtes-vous sûr de vouloir annuler cette demande ?')">
                                        @csrf
                                        <button type="submit" 
                                                class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 text-sm">
                                            Annuler la demande
                                        </button>
                                    </form>
                                </div>
                                @break

                            @case('affectee')
                                <p class="text-blue-600">🔔 Un artisan a été contacté !</p>
                                <p class="text-sm text-gray-600 mt-1">
                                    Il a <strong>20 minutes</strong> pour accepter la mission.
                                </p>
                                @if($mission->expire_le)
                                    <div class="mt-2 text-2xl font-bold text-orange-500" id="minuteur"></div>
                                @endif
                                @break

                            @case('acceptee')
                                <p class="text-indigo-600">✅ L'artisan a accepté la mission !</p>
                                <p class="text-sm text-gray-600 mt-1">
                                    Il prépare son devis et vous contactera bientôt.
                                </p>
                                @if($mission->artisan)
                                    <p class="mt-2 text-sm">
                                        Artisan : <strong>{{ $mission->artisan->user->name }}</strong>
                                    </p>
                                @endif
                                @break

                            @case('diagnostic_effectue')
                                <p class="text-indigo-600">🔍 Diagnostic réalisé par l'artisan.</p>
                                <p class="text-sm text-gray-600 mt-1">
                                    Un devis va bientôt vous être proposé.
                                </p>
                                @break

                            @case('devis_en_attente_validation')
                                <p class="text-teal-600">📄 Un devis a été soumis et est en cours de validation par notre équipe.</p>
                                @break

                            @case('devis_valide')
                                <p class="text-emerald-600">✔️ Le devis a été validé.</p>
                                <p class="text-sm text-gray-600 mt-1">Il va vous être transmis très prochainement.</p>
                                @break

                            @case('devis_envoye')
                                <p class="text-teal-600">📨 Un devis vous a été envoyé.</p>
                                <p class="text-sm text-gray-600 mt-1">Consultez-le et confirmez pour lancer les travaux.</p>
                                @break

                            @case('devis_refuse')
                                <p class="text-red-600">❌ Le devis a été refusé.</p>
                                <p class="text-sm text-gray-600 mt-1">Contactez-nous pour discuter des prochaines étapes.</p>
                                @break

                            @case('acompte_regle')
                                <p class="text-emerald-600">💰 Acompte réglé.</p>
                                <p class="text-sm text-gray-600 mt-1">Les travaux vont pouvoir démarrer.</p>
                                @break

                            @case('en_cours')
                                <p class="text-purple-600">🛠️ Travaux en cours.</p>
                                @break

                            @case('terminee_prestataire')
                                <p class="text-blue-600">🏁 L'artisan a signalé la fin des travaux.</p>
                                <p class="text-sm text-gray-600 mt-1">Merci de confirmer que tout est conforme.</p>
                                @break

                            @case('validee_client')
                                <p class="text-emerald-600">👍 Travaux validés.</p>
                                <p class="text-sm text-gray-600 mt-1">Il ne reste plus qu'à régler le solde.</p>
                                @break

                            @case('solde_regle')
                                <p class="text-emerald-600">✅ Solde réglé.</p>
                                @break

                            @case('payee')
                                <p class="text-green-600">🎉 Mission terminée et payée. Merci de votre confiance !</p>
                                @break

                            @case('annulee')
                                <p class="text-red-600">🚫 Cette demande a été annulée.</p>
                                @break

                            @default
                                <p class="text-gray-600">Statut : {{ ucfirst(str_replace('_', ' ', $mission->statut)) }}</p>
                        @endswitch

                    </div>

                    <!-- Retour -->
                    <div class="mt-6">
                        <a href="{{ route('particulier.dashboard') }}" class="text-gray-600 hover:text-gray-900">
                            ← Retour au tableau de bord
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($mission->statut === 'affectee' && $mission->expire_le)
    <script>
        // ⏱️ Minuteur d'expiration de l'offre envoyée à l'artisan
        (function() {
            const expireLe = new Date("{{ $mission->expire_le->toIso8601String() }}").getTime();
            const el = document.getElementById('minuteur');
            if (!el) return;

            const interval = setInterval(function() {
                const now = new Date().getTime();
                const distance = expireLe - now;

                if (distance <= 0) {
                    clearInterval(interval);
                    el.textContent = 'Expiré';
                    return;
                }

                const minutes = Math.floor(distance / 60000);
                const seconds = Math.floor((distance % 60000) / 1000);
                el.textContent = minutes + 'min ' + seconds + 's';
            }, 1000);
        })();
    </script>
    @endif
</x-app-layout>