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
                            @if($project->projectType)
                                <div class="mt-4">
                                    <p><strong class="font-medium dark:text-gray-100">Type de Projet :</strong> {{ $project->projectType->name }}</p>
                                </div>
                            @endif
                            @if($project->description)
                                <div class="mt-4">
                                    <strong class="font-medium dark:text-gray-100">Description Générale :</strong>
                                    <p class="mt-1 p-3 bg-gray-50 dark:bg-gray-900 rounded-md text-sm dark:text-gray-400">{{ $project->description }}</p>
                                </div>
                            @endif
                        </div>

                        {{-- Section 2: Contexte du projet --}}
                        @if($project->projectContext)
                            <div class="bg-gray-100 dark:bg-gray-800 p-6 rounded-lg shadow">
                                <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                                    <i class="fas fa-file-alt mr-3 text-blue-600"></i> Contexte du Projet
                                </h2>
                                <p class="text-gray-700 dark:text-gray-300">
                                    <strong class="font-medium">Description :</strong>
                                    <p class="mt-1 p-3 bg-gray-50 dark:bg-gray-900 rounded-md text-sm dark:text-gray-300">{{ $project->projectContext->context_description }}</p>
                                </p>
                            </div>
                        @endif

                        {{-- Section 3: Champs Dynamiques (traitement des délimiteurs) --}}
                        @if($dynamicFormFields)
                            @foreach($dynamicFormFields as $section => $fields)
                                <div class="bg-gray-100 dark:bg-gray-800 p-6 rounded-lg shadow">
                                    <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                                        <i class="fas fa-cogs mr-3 text-blue-600"></i> {{ ucfirst($section) }}
                                    </h2>
                                    <div class="space-y-4 text-gray-700 dark:text-gray-300">
                                        @foreach($fields as $fieldDef)
                                            @php
                                                $targetField = $fieldDef['target_project_field'];
                                                $value = null;
                                                // Logique pour extraire la valeur délimitée
                                                if (isset($project->$targetField)) {
                                                    $pattern = '/' . preg_quote($fieldDef['delimiter_start'], '/') . '(.*?)' . preg_quote($fieldDef['delimiter_end'], '/') . '/s';
                                                    if (preg_match($pattern, $project->$targetField, $matches)) {
                                                        $value = $matches[1];
                                                    }
                                                }
                                            @endphp
                                            @if($value)
                                                <div>
                                                    <strong class="font-medium">{{ $fieldDef['question_text'] }} :</strong>
                                                    <p class="mt-1 p-3 bg-gray-50 dark:bg-gray-900 rounded-md text-sm dark:text-gray-400">{{ $value }}</p>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        @endif

                        {{-- Section 4: Cadre Logique --}}
                        @if($project->logicalFramework)
                            <div class="bg-gray-100 dark:bg-gray-800 p-6 rounded-lg shadow">
                                <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                                    <i class="fas fa-bullseye mr-3 text-blue-600"></i> Cadre Logique
                                </h2>

                                <div class="mb-6">
                                    <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-2">But Général</h3>
                                    <p class="mt-1 p-3 bg-gray-50 dark:bg-gray-900 rounded-md text-sm text-gray-700 dark:text-gray-300">{{ $project->logicalFramework->general_objective }}</p>
                                </div>

                                @if($project->logicalFramework->specificObjectives->isNotEmpty())
                                    <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-3">Objectifs Spécifiques</h3>
                                    <div class="space-y-4">
                                        @foreach($project->logicalFramework->specificObjectives as $objective)
                                            <div class="bg-white dark:bg-gray-900 p-4 rounded-md shadow-sm border border-gray-200 dark:border-gray-700">
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

                        {{-- Section 5: Budgets Associés --}}
                        @if($project->budgets->isNotEmpty())
                            <div class="bg-gray-100 dark:bg-gray-800 p-6 rounded-lg shadow">
                                <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                                    <i class="fas fa-wallet mr-3 text-blue-600"></i> Budgets Prévisionnels
                                </h2>
                                <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                            <tr>
                                                <th scope="col" class="px-6 py-3">Description</th>
                                                <th scope="col" class="px-6 py-3">Quantité</th>
                                                <th scope="col" class="px-6 py-3">Coût Unitaire</th>
                                                <th scope="col" class="px-6 py-3">Coût Total</th>
                                                <th scope="col" class="px-6 py-3">Catégorie</th>
                                                <th scope="col" class="px-6 py-3">Responsable</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($project->budgets as $budget)
                                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                                    <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                        {{ $budget->description }}
                                                    </td>
                                                    <td class="px-6 py-4">{{ $budget->quantity }}</td>
                                                    <td class="px-6 py-4">{{ number_format($budget->unit_cost, 2, ',', ' ') }} F</td>
                                                    <td class="px-6 py-4">{{ number_format($budget->total_cost, 2, ',', ' ') }} F</td>
                                                    <td class="px-6 py-4">{{ $budget->category }}</td>
                                                    <td class="px-6 py-4">{{ $budget->responsibleUser->name ?? 'N/A' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif

                        {{-- Section 6: Documents Associés --}}
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
                            </div>
                        @endif

                        <div class="mt-8 flex justify-end">
                            <a href="{{ route('creator.proposal.project.edit', ['projectId' => $project->id ]) }}" class="px-6 py-3 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-100 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors font-semibold">
                                Modifier
                            </a>
                            <a href="{{ route('project.list') }}" class="px-6 py-3 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-100 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors font-semibold">
                                Retour a la liste
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
