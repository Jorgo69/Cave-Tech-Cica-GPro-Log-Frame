<x-app-layout>
    <main class="lg:ml-64 pt-16 min-h-screen bg-gray-50 dark:bg-gray-900">
        <div class="p-6">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">
                Tableau de Bord @if($isAdmin) (Admin) @else (Mon Espace) @endif
            </h1>

            {{-- Statistiques générales --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300">Total Projets</h2>
                    <p class="text-4xl font-bold text-blue-600 dark:text-blue-400 mt-2">{{ $totalProjects }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300">Projets en Cours</h2>
                    <p class="text-4xl font-bold text-yellow-600 dark:text-yellow-400 mt-2">{{ $projectsInProgress }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300">Projets Terminés</h2>
                    <p class="text-4xl font-bold text-emerald-600 dark:text-emerald-400 mt-2">{{ $projectsCompleted }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300">Activités en Cours</h2>
                    <p class="text-4xl font-bold text-orange-500 dark:text-orange-400 mt-2">{{ $activitiesInProgress }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Mises à jour récentes --}}
                <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                        Mises à jour récentes
                    </h2>
                    @forelse($recentProgressUpdates as $update)
                        <div class="flex items-start mb-4 p-4 rounded-lg bg-gray-50 dark:bg-gray-700/50 border border-gray-100 dark:border-gray-700">
                            <div class="flex-shrink-0 mr-4">
                                {{-- Placeholder pour une icône --}}
                                <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center text-blue-600 dark:text-blue-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-grow">
                                <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">{{ $update->project->title ?? 'N/A' }}</p>
                                <p class="text-xs text-gray-600 dark:text-gray-400">
                                    {{ $update->status_update }} - <span class="font-medium">Par : {{ $update->updatedByUser->name ?? 'N/A' }}</span>
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    Mise à jour le {{ $update->date }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 dark:text-gray-400">Aucune mise à jour de progression récente.</p>
                    @endforelse
                </div>

                {{-- Projets récents --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                        Projets Récents
                    </h2>
                    @forelse($recentProjects as $project)
                        <div class="mb-4 pb-4 border-b border-gray-100 dark:border-gray-700">
                            <h3 class="font-semibold text-blue-600 dark:text-blue-400">{{ $project->title }}</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Créé le {{ $project->created_at->format('d/m/Y') }} par {{ $project->creator->name ?? 'N/A' }}</p>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200 mt-2">
                                Statut : {{ $project->status }}
                            </span>
                        </div>
                    @empty
                        <p class="text-gray-500 dark:text-gray-400">Aucun projet récent trouvé.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </main>
</x-app-layout>