<x-app-layout>
    <div class="admin-artisans-page">

        <div class="page-header">
            <h1>Candidatures artisans en attente</h1>
            <p class="subtitle">{{ $artisans->total() }} candidature(s) à traiter</p>
        </div>

        @if (session('success'))
            <div class="alert-success">{{ session('success') }}</div>
        @endif

        @if ($artisans->isEmpty())
            <div class="empty-state">
                <p>Aucune candidature en attente pour le moment.</p>
            </div>
        @else
            <div class="artisan-list">
                @foreach ($artisans as $artisan)
                    <div class="artisan-card">

                        <div class="artisan-info">
                            <h3>{{ $artisan->user->name }}</h3>
                            <p class="meta">
                                <strong>Métier :</strong> {{ ucfirst($artisan->profession) }} ·
                                <strong>Zone :</strong> {{ $artisan->intervention_area }}
                            </p>
                            <p class="meta">
                                <strong>Email :</strong> {{ $artisan->user->email }} ·
                                <strong>Tél :</strong> {{ $artisan->user->telephone }}
                            </p>
                            <p class="meta">
                                <strong>Inscrit le :</strong> {{ $artisan->created_at->format('d/m/Y à H:i') }}
                            </p>

                            @if ($artisan->identity_document)
                                <a href="{{ route('admin.artisans.document', $artisan) }}" target="_blank" class="doc-link">
                                    Voir la pièce d'identité
                                </a>
                            @endif
                        </div>

                        <div class="artisan-actions">
                            <form method="POST" action="{{ route('admin.artisans.valider', $artisan) }}">
                                @csrf
                                <button type="submit" class="btn-valider">Valider</button>
                            </form>

                            {{-- Fallback inline refusal form (accessible sans JS) --}}
                            <form method="POST" action="{{ route('admin.artisans.refuser', $artisan) }}" class="inline-refuse-form">
                                @csrf
                                <input type="text" name="refusal_reason" placeholder="Motif du refus (court)" class="refuse-input" required>
                                <button type="submit" class="btn-refuser">Refuser</button>
                            </form>

                            <button type="button" class="btn-refuser" onclick="document.getElementById('refuse-modal-{{ $artisan->id }}').classList.add('is-open')">
                                Refuser (modifier)
                            </button>
                        </div>

                        <!-- Modale de refus (motif obligatoire) -->
                        <div class="refuse-modal" id="refuse-modal-{{ $artisan->id }}">
                            <div class="refuse-modal-content">
                                <h4>Motif du refus</h4>
                                <form method="POST" action="{{ route('admin.artisans.refuser', $artisan) }}">
                                    @csrf
                                    <textarea name="refusal_reason" rows="4" placeholder="Expliquez pourquoi cette candidature est refusée..." required></textarea>
                                    <div class="modal-actions">
                                        <button type="button" class="btn-cancel" onclick="document.getElementById('refuse-modal-{{ $artisan->id }}').classList.remove('is-open')">Annuler</button>
                                        <button type="submit" class="btn-refuser">Confirmer le refus</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>
                @endforeach
            </div>

            <div class="pagination-wrap">
                {{ $artisans->links() }}
            </div>
        @endif

    </div>
</x-app-layout>