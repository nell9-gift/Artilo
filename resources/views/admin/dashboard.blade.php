<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tableau de bord admin') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <div class="px-6 py-5 bg-gray-50 border-b border-gray-200">
                    <p class="mt-1 text-sm text-gray-600">Bienvenue, administrateur. Gérez ici les candidatures et accédez aux validations.</p>
                </div>
                            <a href="{{ route('admin.profils.index') }}" class="nav-link">
                👷 Profils des Artisans Partenaires
            </a>
                <div class="p-6 space-y-6">
                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="rounded-xl border border-gray-200 p-5 bg-white shadow-sm">
                            <h2 class="text-lg font-semibold text-gray-900">Candidatures en attente</h2>
                            <p class="mt-2 text-3xl font-bold text-indigo-600">{{ $pendingCount }}</p>
                            <p class="mt-2 text-sm text-gray-500">Artisans en attente de validation.</p>
                        </div>
                        <div class="rounded-xl border border-gray-200 p-5 bg-white shadow-sm">
                            <h2 class="text-lg font-semibold text-gray-900">Actions rapides</h2>
                            <p class="mt-2 text-sm text-gray-500">Consultez la liste des artisans candidats et validez ou refusez les demandes.</p>
                            <a href="{{ route('admin.artisans.index') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">Voir les candidatures</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
