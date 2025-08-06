<main class="lg:ml-64 pt-16 min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="p-6">
        @if ($project)
            <div class="bg-white dark:bg-gray-900 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 sm:px-20 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-6">Détails du Projet : <span class="text-blue-600">{{ $project->title }}</span></h1>
                    <p class="text-gray-600 dark:text-gray-300 mb-8">{{ $project->short_title ? '('.$project->short_title.')' : '' }} Code: {{ $project->project_code }}</p>

                    <div class="space-y-8">

                        {{-- Section 1: Informations Générales --}}
                        <div class="bg-gray-100 dark:bg-gray-800 p-6 rounded-lg shadow">
                            <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                                <i class="fas fa-info-circle mr-3 text-blue-600"></i> Informations Générales
                            </h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-gray-700 dark:text-gray-300">
                                <div>
                                    <p><strong class="font-medium">Statut :</strong> <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{
                                        $project->status === 'active' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200' :
                                        ($project->status === 'draft' ? 'bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-200' :
                                        ($project->status === 'completed' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' :
                                        ($project->status === 'on_hold' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' :
                                        'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200')))
                                    }}">
                                        {{ ucfirst($project->status) }}
                                    </span></p>
                                    <p><strong class="font-medium">Date de Début :</strong> {{ $project->start_date?->format('d/m/Y') }}</p>
                                    <p><strong class="font-medium">Date de Fin :</strong> {{ $project->end_date?->format('d/m/Y') }}</p>
                                </div>
                                <div>
                                    <p><strong class="font-medium">Créé le :</strong> {{ $project->created_at?->format('d/m/Y H:i') }}</p>
                                    <p><strong class="font-medium">Dernière mise à jour :</strong> {{ $project->updated_at?->format('d/m/Y H:i') }}</p>
                                    @if($project->creator)
                                        <p><strong class="font-medium">Créé par :</strong> {{ $project->creator->name }}</p>
                                    @endif
                                    @if($project->updater)
                                        <p><strong class="font-medium">Mis à jour par :</strong> {{ $project->updater->name }}</p>
                                    @endif
                                </div>
                            </div>
                            @if($project->description)
                                <div class="mt-4 ">
                                    <strong class="font-medium dark:text-gray-100">Description Générale :</strong>
                                    <p class="mt-1 p-3 bg-gray-50 dark:bg-gray-900 rounded-md text-sm dark:text-gray-400">{{ $project->description }}</p>
                                </div>
                            @endif
                        </div>

                        {{-- Section 2: Contexte pour l'IA --}}
                        @if($project->context)
                            <div class="bg-gray-100 dark:bg-gray-800 p-6 rounded-lg shadow">
                                <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                                    <i class="fas fa-brain mr-3 text-blue-600"></i> Contexte IA
                                </h2>
                                <p class="text-gray-700 dark:text-gray-300">
                                    <strong class="font-medium">Description de Base pour l'IA :</strong>
                                    <p class="mt-1 p-3 bg-gray-50 dark:bg-gray-900 rounded-md text-sm dark:text-gray-300">{{ $project->context->base_description }}</p>
                                </p>
                            </div>
                        @endif

                        {{-- Section 3: Analyse de l'Environnement --}}
                        @if($project->environmentAnalysis)
                            <div class="bg-gray-100 dark:bg-gray-800 p-6 rounded-lg shadow">
                                <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                                    <i class="fas fa-globe mr-3 text-blue-600"></i> Analyse de l'Environnement (PESTEL/SWOT)
                                </h2>
                                <p class="text-gray-700 dark:text-gray-300">
                                    <p class="mt-1 p-3 bg-gray-50 dark:bg-gray-900 rounded-md text-sm">{{ $project->environmentAnalysis->analysis_text }}</p>
                                </p>
                            </div>
                        @endif

                        {{-- Section 4: Parties Prenantes --}}
                        @if($project->stakeholders->isNotEmpty())
                            <div class="bg-gray-100 dark:bg-gray-800 p-6 rounded-lg shadow dark:text-gray-300">
                                <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                                    <i class="fas fa-users mr-3 text-blue-600"></i> Parties Prenantes
                                </h2>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    @foreach($project->stakeholders as $stakeholder)
                                        <div class="bg-white dark:bg-gray-900 p-4 rounded-md shadow-sm border border-gray-200 dark:border-gray-700">
                                            <p><strong class="font-medium">Nom :</strong> {{ $stakeholder->name }}</p>
                                            <p><strong class="font-medium">Rôle :</strong> {{ $stakeholder->role }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Section 5: Problématique Cible --}}
                        @if($project->problemAnalysis)
                            <div class="bg-gray-100 dark:bg-gray-800 p-6 rounded-lg shadow">
                                <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                                    <i class="fas fa-exclamation-triangle mr-3 text-blue-600"></i> Problématique Cible
                                </h2>
                                <p class="text-gray-700 dark:text-gray-300">
                                    <p class="mt-1 p-3 bg-gray-50 dark:bg-gray-900 rounded-md text-sm">{{ $project->problemAnalysis->problem_description }}</p>
                                </p>
                            </div>
                        @endif

                        {{-- Section 6: Stratégie --}}
                        @if($project->strategy)
                            <div class="bg-gray-100 dark:bg-gray-800 p-6 rounded-lg shadow">
                                <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                                    <i class="fas fa-lightbulb mr-3 text-blue-600"></i> Stratégie & Approche
                                </h2>
                                <p class="text-gray-700 dark:text-gray-300">
                                    <p class="mt-1 p-3 bg-gray-50 dark:bg-gray-900 rounded-md text-sm">{{ $project->strategy->strategy_description }}</p>
                                </p>
                            </div>
                        @endif

                        {{-- Section 7: But Général, Objectifs Spécifiques, Résultats et Activités --}}
                        @if($project->goal)
                            <div class="bg-gray-100 dark:bg-gray-800 p-6 rounded-lg shadow">
                                <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                                    <i class="fas fa-bullseye mr-3 text-blue-600"></i> Buts, Objectifs, Résultats & Activités
                                </h2>

                                <div class="mb-6">
                                    <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-2">But Général</h3>
                                    <p class="mt-1 p-3 bg-gray-50 dark:bg-gray-900 rounded-md text-sm text-gray-700 dark:text-gray-300">{{ $project->goal->description }}</p>
                                </div>

                                @if($project->goal->objectives->isNotEmpty())
                                    <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-3">Objectifs Spécifiques</h3>
                                    <div class="space-y-4">
                                        @foreach($project->goal->objectives as $objective)
                                            <div class="bg-white  dark:bg-gray-900 p-4 rounded-md shadow-sm border border-gray-200 dark:border-gray-700">
                                                <p><strong class="font-medium">Description :</strong> {{ $objective->description }}</p>

                                                @if($objective->results->isNotEmpty())
                                                    <h4 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mt-4 mb-2">Résultats Attendus</h4>
                                                    <ul class="list-disc list-inside space-y-2 text-gray-600 dark:text-gray-400">
                                                        @foreach($objective->results as $result)
                                                            <li>
                                                                <strong class="font-medium">{{ $result->description }}</strong>
                                                                <p class="text-sm italic">Indicateurs : {{ $result->indicators }}</p>
                                                                @if($result->activities->isNotEmpty())
                                                                    <h5 class="text-base font-semibold text-gray-600 dark:text-gray-400 mt-2 mb-1">Activités</h5>
                                                                    <ul class="list-disc list-inside ml-4 space-y-1 text-gray-500 dark:text-gray-500">
                                                                        @foreach($result->activities as $activity)
                                                                            <li>{{ $activity->description }} (Responsable: {{ $activity->responsibleUser->name ?? 'N/A' }})</li>
                                                                        @endforeach
                                                                    </ul>
                                                                @endif
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endif

                        {{-- Section 8: Gestion des Incertitudes (Risques) --}}
                        @if($project->risks->isNotEmpty())
                            <div class="bg-gray-100 dark:bg-gray-800 p-6 rounded-lg shadow">
                                <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                                    <i class="fas fa-shield-alt mr-3 text-blue-600"></i> Gestion des Incertitudes (Risques)
                                </h2>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 dark:text-gray-300">
                                    @foreach($project->risks as $risk)
                                        <div class="bg-white dark:bg-gray-900 p-4 rounded-md shadow-sm border border-gray-200 dark:border-gray-700">
                                            <p><strong class="font-medium">Description :</strong> {{ $risk->description }}</p>
                                            <p><strong class="font-medium">Impact :</strong> {{ $risk->impact }}</p>
                                            <p><strong class="font-medium">Probabilité :</strong> {{ $risk->probability }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Section 9: Documents Associés --}}
                        @if($project->documents->isNotEmpty())
                            <div class="bg-gray-100 dark:bg-gray-800 p-6 rounded-lg shadow">
                                <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                                    <i class="fas fa-file-alt mr-3 text-blue-600"></i> Documents Associés
                                </h2>
                                <ul class="list-disc list-inside space-y-2 text-gray-700 dark:text-gray-300">
                                    @foreach($project->documents as $document)
                                        <li>
                                            <a href="{{ asset('storage/' . $document->file_path) }}" target="_blank" class="text-blue-600 hover:underline">
                                                {{ $document->file_name }} ({{ strtoupper($document->file_mime_type) }})
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                                <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">
                                    Veuillez noter que pour accéder aux documents, le stockage symbolique (`php artisan storage:link`) doit être configuré.
                                </p>
                            </div>
                        @endif

                        <div class="mt-8 flex justify-end">
                            <a href="{{ route('project.list') }}" class="px-6 py-3 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-100 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors font-semibold">
                                Retour à la liste des projets
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="text-center p-8 bg-white dark:bg-gray-900 rounded-lg shadow">
                <p class="text-xl text-gray-700 dark:text-gray-300">Projet non trouvé.</p>
            </div>
        @endif
    </div>
</main>