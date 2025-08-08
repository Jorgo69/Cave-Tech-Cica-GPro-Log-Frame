<main class="lg:ml-64 pt-16 min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="p-6" wire:loading.class="opacity-50">

        {{-- Messages de session --}}
        @if (session()->has('success'))
            <div class="mb-4 p-4 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-200 rounded-xl">
                {{ session('success') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-4 p-4 bg-rose-50 dark:bg-rose-900/30 border border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-200 rounded-xl">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white dark:bg-gray-900 overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6 sm:px-20 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-6">
                    @if ($projectId)
                        Éditer la Proposition de Projet
                    @else
                        Créer une Nouvelle Proposition de Projet
                    @endif
                </h1>
                
                <div x-data="{
                    currentStep: @entangle('currentStep'),
                    totalSteps: @entangle('totalSteps'),
                    stepDetails: @entangle('stepDetails'),
                    updateProgress: function() {
                        const progress = (this.currentStep / this.totalSteps) * 100;
                        this.$refs.progressBarFill.style.width = `${progress}%`;
                    },
                    init() {
                        this.updateProgress();
                        this.$watch('currentStep', () => this.updateProgress());
                        Livewire.on('stepChanged', () => {
                            this.updateProgress();
                            window.scrollTo({ top: 0, behavior: 'smooth' });
                        });
                    }
                }" class="flex flex-col lg:flex-row gap-8">

                    {{-- Colonne de navigation des étapes --}}
                    <div class="lg:w-1/4 bg-gray-100 dark:bg-gray-800 p-6 rounded-lg shadow-inner">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Étapes</h2>
                        <ul class="space-y-3">
                            <template x-for="(step, index) in stepDetails" :key="index">
                                <li class="flex items-center space-x-3 cursor-pointer p-2 rounded-md transition-colors"
                                    :class="{
                                        'bg-blue-600 text-white shadow-md': (index + 1) === currentStep,
                                        'text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700': (index + 1) !== currentStep,
                                        'opacity-50 cursor-not-allowed': (index + 1) > currentStep // Désactiver les étapes futures
                                    }"
                                    @click="(index + 1) <= currentStep && $wire.goToStep(index + 1)">
                                    <span class="font-bold" x-text="index + 1"></span>
                                    <div>
                                        <p class="font-medium" x-text="step.title"></p>
                                        <p class="text-sm"
                                           :class="{'text-blue-200': (index + 1) === currentStep, 'text-gray-500 dark:text-gray-400': (index + 1) !== currentStep}"
                                           x-text="step.description"></p>
                                    </div>
                                </li>
                            </template>
                        </ul>

                        {{-- Barre de progression --}}
                        <div class="mt-8">
                            <div class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Progression</div>
                            <div class="w-full bg-gray-300 dark:bg-gray-700 rounded-full h-2.5">
                                <div x-ref="progressBarFill" class="bg-blue-600 h-2.5 rounded-full transition-all duration-300 ease-out" style="width: 0%;"></div>
                            </div>
                            <div class="text-right text-sm text-gray-600 dark:text-gray-400 mt-1">
                                <span x-text="currentStep"></span> / <span x-text="totalSteps"></span>
                            </div>
                        </div>
                    </div>

                    {{-- Contenu du formulaire des étapes --}}
                    <form wire:submit.prevent="submitForm" class="lg:w-3/4 bg-gray-100 dark:bg-gray-800 p-6 rounded-lg shadow-inner">
                        {{-- @csrf <!-- Indispensable pour la sécurité CSRF avec Livewire --> --}}

                        {{-- Fenêtre 1 : Informations Clés du Projet --}}
                        <div x-show="currentStep === 1" class="space-y-6">
                            <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Informations Clés du Projet</h2>
                            <p class="text-gray-600 dark:text-gray-300 mb-6">Renseignez les détails administratifs de base et choisissez le type de projet.</p>

                            <div>
                                <label for="selectedProjectTypeId" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Type de Projet <span class="text-red-500">*</span></label>
                                <select id="selectedProjectTypeId" wire:model.live="selectedProjectTypeId"
                                        class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Sélectionner un type de projet</option>
                                    @foreach($allProjectTypes as $type)
                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                    @endforeach
                                </select>
                                @error('selectedProjectTypeId') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                            </div>

                            @if ($selectedProjectTypeId)
                                <div class="bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800 text-blue-700 dark:text-blue-200 p-4 rounded-lg mb-6">
                                    <p class="font-semibold">Description du type de projet :</p>
                                    <p>{{ $allProjectTypes->where('id', $selectedProjectTypeId)->first()->description ?? 'N/A' }}</p>
                                </div>
                            @endif

                            <div>
                                <label for="projectTitle" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Titre du Projet <span class="text-red-500">*</span></label>
                                <input type="text" id="projectTitle" wire:model.defer="projectTitle"
                                       class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500"
                                       placeholder="Ex: Système de gestion de projet IA">
                                @error('projectTitle') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="projectCode" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Code du Projet <span class="text-red-500">*</span></label>
                                <input type="text" id="projectCode" wire:model.defer="projectCode"
                                       class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500"
                                       placeholder="Ex: PRJ-ALPHA-001">
                                @error('projectCode') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="projectShortTitle" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Titre Abrégé (Optionnel)</label>
                                <input type="text" id="projectShortTitle" wire:model.defer="projectShortTitle"
                                       class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500"
                                       placeholder="Ex: SysGProj IA">
                                @error('projectShortTitle') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="projectStartDate" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date de Début <span class="text-red-500">*</span></label>
                                    <input type="date" id="projectStartDate" wire:model.defer="projectStartDate"
                                           class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500">
                                    @error('projectStartDate') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label for="projectEndDate" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date de Fin <span class="text-red-500">*</span></label>
                                    <input type="date" id="projectEndDate" wire:model.defer="projectEndDate"
                                           class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500">
                                    @error('projectEndDate') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            
                            {{-- Champs dynamiques pour cette section --}}
                            @include('livewire.v-beta.proposal-project.dynamic-fields-section')
                        </div>

                        {{-- Fenêtre 2 : Contexte & Documents --}}
                        <div x-show="currentStep === 2" class="space-y-6">
                            <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Contexte & Documents</h2>
                            <p class="text-gray-600 dark:text-gray-300 mb-6">Fournissez une description du contexte du projet et téléchargez les documents pertinents.</p>

                            <div>
                                <label for="contextDescription" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description du Contexte (Optionnel)</label>
                                <textarea id="contextDescription" wire:model.defer="contextDescription" rows="5"
                                          class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500"
                                          placeholder="Décrivez le contexte général dans lequel le projet s'inscrit."></textarea>
                                @error('contextDescription') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="uploadedDocuments" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Documents Pertinents (Optionnel)</label>
                                <input type="file" id="uploadedDocuments" wire:model="uploadedDocuments" multiple
                                       class="mt-1 block w-full text-sm text-gray-900 dark:text-gray-100 file:mr-4 file:py-2 file:px-4
                                              file:rounded-full file:border-0 file:text-sm file:font-semibold
                                              file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100
                                              dark:file:bg-blue-800 dark:file:text-blue-200 dark:hover:file:bg-blue-700">
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Formats acceptés : PDF, DOCX, XLSX, JPG, PNG. Taille max : 50MB par fichier.</p>
                                @error('uploadedDocuments.*') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                                @if (count($uploadedDocuments) > 0)
                                    <div class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                                        Fichiers sélectionnés:
                                        <ul class="list-disc list-inside">
                                            @foreach ($uploadedDocuments as $file)
                                                <li>{{ $file->getClientOriginalName() }} ({{ round($file->getSize() / 1024 / 1024, 2) }} MB)</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div>

                            {{-- Champs dynamiques pour cette section --}}
                            {{-- @if (isset($dynamicFormFields['contexte_documents']))
                                <div class="mt-6 border-t pt-4 border-gray-200 dark:border-gray-700">
                                    <h3 class="text-lg font-semibold mb-3 text-gray-900 dark:text-gray-100">Informations Complémentaires</h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        @foreach ($dynamicFormFields['contexte_documents'] as $field)
                                            <div>
                                                <label for="dynamic-{{ $field['field_name'] }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                    {{ $field['question_text'] }} @if($field['is_required']) <span class="text-red-500">*</span> @endif
                                                </label>
                                                @if($field['input_type'] === 'textarea')
                                                    <textarea id="dynamic-{{ $field['field_name'] }}" wire:model.defer="dynamicFieldValues.{{ $field['field_name'] }}" 
                                                              rows="3" class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500"></textarea>
                                                @elseif($field['input_type'] === 'select')
                                                    <select id="dynamic-{{ $field['field_name'] }}" wire:model.defer="dynamicFieldValues.{{ $field['field_name'] }}" 
                                                            class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500">
                                                        <option value="">Sélectionner une option</option>
                                                        @foreach(explode(',', $field['options']) as $option)
                                                            <option value="{{ trim($option) }}">{{ trim($option) }}</option>
                                                        @endforeach
                                                    </select>
                                                @else
                                                    <input type="{{ $field['input_type'] }}" id="dynamic-{{ $field['field_name'] }}" wire:model.defer="dynamicFieldValues.{{ $field['field_name'] }}" 
                                                           class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500">
                                                @endif
                                                @error('dynamicFieldValues.' . $field['field_name']) <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif --}}
                        </div>

                        {{-- Fenêtre 3 : Cadre Logique (But & Objectifs Spécifiques) --}}
                        <div x-show="currentStep === 3" class="space-y-6">
                            <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Cadre Logique du Projet</h2>
                            <p class="text-gray-600 dark:text-gray-300 mb-6">Définissez le but général et les objectifs spécifiques de votre projet.</p>

                            <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mt-6 mb-3">But Général</h3>
                            <div>
                                <label for="general_objective" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Objectif Général <span class="text-red-500">*</span></label>
                                <textarea id="general_objective" wire:model.defer="initialLogicalFramework.general_objective" rows="3"
                                          class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500"
                                          placeholder="Ex: Contribuer à l'amélioration de la santé maternelle et infantile dans la région X."></textarea>
                                @error('initialLogicalFramework.general_objective') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="general_obj_indicators" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Indicateurs de l'Objectif Général (Optionnel)</label>
                                <textarea id="general_obj_indicators" wire:model.defer="initialLogicalFramework.general_obj_indicators" rows="2"
                                          class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500"
                                          placeholder="Ex: Réduction de 15% du taux de mortalité infantile d'ici 2025."></textarea>
                                @error('initialLogicalFramework.general_obj_indicators') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="general_obj_verification_sources" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Sources de Vérification (Optionnel)</label>
                                <input type="text" id="general_obj_verification_sources" wire:model.defer="initialLogicalFramework.general_obj_verification_sources"
                                       class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500"
                                       placeholder="Ex: Rapports du ministère de la Santé.">
                                @error('initialLogicalFramework.general_obj_verification_sources') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="assumptions" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Hypothèses (Optionnel)</label>
                                <textarea id="assumptions" wire:model.defer="initialLogicalFramework.assumptions" rows="2"
                                          class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500"
                                          placeholder="Ex: Stabilité politique de la région."></textarea>
                                @error('initialLogicalFramework.assumptions') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                            </div>

                            <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mt-6 mb-3">Objectifs Spécifiques</h3>
                            <div class="space-y-4">
                                @foreach($specificObjectives as $index => $objective)
                                    <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg relative">
                                        <button type="button" wire:click="removeSpecificObjective({{ $index }})" class="absolute top-2 right-2 text-red-500 hover:text-red-700 text-lg">&times;</button>
                                        <div>
                                            <label for="specific-objective-description-{{ $index }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description <span class="text-red-500">*</span></label>
                                            <textarea id="specific-objective-description-{{ $index }}" wire:model.defer="specificObjectives.{{ $index }}.description" rows="2"
                                                      class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500"
                                                      placeholder="Ex: Augmenter l'accès aux soins prénatals pour les femmes enceintes de 30% en 1 an."></textarea>
                                            @error('specificObjectives.' . $index . '.description') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                                        </div>
                                        <div>
                                            <label for="specific-objective-indicators-{{ $index }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Indicateurs (Optionnel)</label>
                                            <textarea id="specific-objective-indicators-{{ $index }}" wire:model.defer="specificObjectives.{{ $index }}.indicators" rows="2"
                                                      class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500"
                                                      placeholder="Ex: Nombre de consultations prénatales effectuées."></textarea>
                                            @error('specificObjectives.' . $index . '.indicators') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                                        </div>
                                        <div>
                                            <label for="specific-objective-verification-{{ $index }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Sources de Vérification (Optionnel)</label>
                                            <input type="text" id="specific-objective-verification-{{ $index }}" wire:model.defer="specificObjectives.{{ $index }}.verification_sources"
                                                   class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500"
                                                   placeholder="Ex: Registres des centres de santé.">
                                            @error('specificObjectives.' . $index . '.verification_sources') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                                        </div>
                                        <div>
                                            <label for="specific-objective-assumptions-{{ $index }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Hypothèses (Optionnel)</label>
                                            <textarea id="specific-objective-assumptions-{{ $index }}" wire:model.defer="specificObjectives.{{ $index }}.assumptions" rows="2"
                                                      class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500"
                                                      placeholder="Ex: Disponibilité des personnels de santé."></textarea>
                                            @error('specificObjectives.' . $index . '.assumptions') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                @endforeach
                                <button type="button" wire:click="addSpecificObjective" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors font-semibold dark:bg-blue-600 dark:text-gray-100 dark:hover:bg-blue-700">
                                    Ajouter un Objectif Spécifique
                                </button>
                            </div>

                            {{-- Champs dynamiques pour cette section --}}
                            @if (isset($dynamicFormFields['cadre_logique']))
                                <div class="mt-6 border-t pt-4 border-gray-200 dark:border-gray-700">
                                    <h3 class="text-lg font-semibold mb-3 text-gray-900 dark:text-gray-100">Informations Complémentaires</h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        @foreach ($dynamicFormFields['cadre_logique'] as $field)
                                            <div>
                                                <label for="dynamic-{{ $field['field_name'] }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                    {{ $field['question_text'] }} @if($field['is_required']) <span class="text-red-500">*</span> @endif
                                                </label>
                                                @if($field['input_type'] === 'textarea')
                                                    <textarea id="dynamic-{{ $field['field_name'] }}" wire:model.defer="dynamicFieldValues.{{ $field['field_name'] }}" 
                                                              rows="3" class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500"></textarea>
                                                @elseif($field['input_type'] === 'select')
                                                    <select id="dynamic-{{ $field['field_name'] }}" wire:model.defer="dynamicFieldValues.{{ $field['field_name'] }}" 
                                                            class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500">
                                                        <option value="">Sélectionner une option</option>
                                                        @foreach(explode(',', $field['options']) as $option)
                                                            <option value="{{ trim($option) }}">{{ trim($option) }}</option>
                                                        @endforeach
                                                    </select>
                                                @else
                                                    <input type="{{ $field['input_type'] }}" id="dynamic-{{ $field['field_name'] }}" wire:model.defer="dynamicFieldValues.{{ $field['field_name'] }}" 
                                                           class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500">
                                                @endif
                                                @error('dynamicFieldValues.' . $field['field_name']) <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- Fenêtre 4 : Résultats Attendus --}}
                        <div x-show="currentStep === 4" class="space-y-6">
                            <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Résultats Attendus</h2>
                            <p class="text-gray-600 dark:text-gray-300 mb-6">Décrivez les résultats concrets que le projet doit atteindre.</p>

                            <div class="space-y-4">
                                @foreach($expectedResults as $index => $result)
                                    <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg relative">
                                        <button type="button" wire:click="removeExpectedResult({{ $index }})" class="absolute top-2 right-2 text-red-500 hover:text-red-700 text-lg">&times;</button>
                                        <div>
                                            <label for="expected-result-description-{{ $index }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description du Résultat <span class="text-red-500">*</span></label>
                                            <textarea id="expected-result-description-{{ $index }}" wire:model.defer="expectedResults.{{ $index }}.description" rows="2"
                                                      class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500"
                                                      placeholder="Ex: 500 femmes enceintes ont accès à des consultations prénatales régulières."></textarea>
                                            @error('expectedResults.' . $index . '.description') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                @endforeach
                                <button type="button" wire:click="addExpectedResult" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors font-semibold dark:bg-blue-600 dark:text-gray-100 dark:hover:bg-blue-700">
                                    Ajouter un Résultat
                                </button>
                            </div>

                            {{-- Champs dynamiques pour cette section --}}
                            @if (isset($dynamicFormFields['resultats_attendus']))
                                <div class="mt-6 border-t pt-4 border-gray-200 dark:border-gray-700">
                                    <h3 class="text-lg font-semibold mb-3 text-gray-900 dark:text-gray-100">Informations Complémentaires</h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        @foreach ($dynamicFormFields['resultats_attendus'] as $field)
                                            <div>
                                                <label for="dynamic-{{ $field['field_name'] }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                    {{ $field['question_text'] }} @if($field['is_required']) <span class="text-red-500">*</span> @endif
                                                </label>
                                                @if($field['input_type'] === 'textarea')
                                                    <textarea id="dynamic-{{ $field['field_name'] }}" wire:model.defer="dynamicFieldValues.{{ $field['field_name'] }}" 
                                                              rows="3" class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500"></textarea>
                                                @elseif($field['input_type'] === 'select')
                                                    <select id="dynamic-{{ $field['field_name'] }}" wire:model.defer="dynamicFieldValues.{{ $field['field_name'] }}" 
                                                            class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500">
                                                        <option value="">Sélectionner une option</option>
                                                        @foreach(explode(',', $field['options']) as $option)
                                                            <option value="{{ trim($option) }}">{{ trim($option) }}</option>
                                                        @endforeach
                                                    </select>
                                                @else
                                                    <input type="{{ $field['input_type'] }}" id="dynamic-{{ $field['field_name'] }}" wire:model.defer="dynamicFieldValues.{{ $field['field_name'] }}" 
                                                           class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500">
                                                @endif
                                                @error('dynamicFieldValues.' . $field['field_name']) <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- Fenêtre 5 : Activités Initiales --}}
                        <div x-show="currentStep === 5" class="space-y-6">
                            <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Activités Initiales</h2>
                            <p class="text-gray-600 dark:text-gray-300 mb-6">Listez les activités principales pour atteindre les résultats attendus.</p>

                            <div class="space-y-4">
                                @foreach($activities as $index => $activity)
                                    <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg relative">
                                        <button type="button" wire:click="removeActivity({{ $index }})" class="absolute top-2 right-2 text-red-500 hover:text-red-700 text-lg">&times;</button>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label for="activity-description-{{ $index }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description de l'Activité <span class="text-red-500">*</span></label>
                                                <textarea id="activity-description-{{ $index }}" wire:model.defer="activities.{{ $index }}.description" rows="2"
                                                          class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500"
                                                          placeholder="Ex: Organiser des sessions de sensibilisation."></textarea>
                                                @error('activities.' . $index . '.description') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                                            </div>
                                            <div>
                                                <label for="activity-responsible-{{ $index }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Responsable <span class="text-red-500">*</span></label>
                                                <select id="activity-responsible-{{ $index }}" wire:model.defer="activities.{{ $index }}.responsible_user_id"
                                                        class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500">
                                                    <option value="">Sélectionner un responsable</option>
                                                    @foreach($users as $user)
                                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('activities.' . $index . '.responsible_user_id') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                                            </div>
                                            <div>
                                                <label for="activity-start-date-{{ $index }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date de Début <span class="text-red-500">*</span></label>
                                                <input type="date" id="activity-start-date-{{ $index }}" wire:model.defer="activities.{{ $index }}.start_date"
                                                       class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500">
                                                @error('activities.' . $index . '.start_date') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                                            </div>
                                            <div>
                                                <label for="activity-end-date-{{ $index }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date de Fin <span class="text-red-500">*</span></label>
                                                <input type="date" id="activity-end-date-{{ $index }}" wire:model.defer="activities.{{ $index }}.end_date"
                                                       class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500">
                                                @error('activities.' . $index . '.end_date') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                                            </div>
                                            <div>
                                                <label for="activity-status-{{ $index }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Statut <span class="text-red-500">*</span></label>
                                                <select id="activity-status-{{ $index }}" wire:model.defer="activities.{{ $index }}.status"
                                                        class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500">
                                                    <option value="En cours">En cours</option>
                                                    <option value="Terminée">Terminée</option>
                                                    <option value="En attente">En attente</option>
                                                    <option value="En retard">En retard</option>
                                                </select>
                                                @error('activities.' . $index . '.status') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                                            </div>
                                            <div>
                                                <label for="activity-justification-{{ $index }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Justification (Optionnel)</label>
                                                <textarea id="activity-justification-{{ $index }}" wire:model.defer="activities.{{ $index }}.justification" rows="1"
                                                          class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500"
                                                          placeholder="Raison du statut (si en attente/retard)."></textarea>
                                            </div>
                                            <div class="flex items-center col-span-full">
                                                <input type="checkbox" id="activity-milestone-{{ $index }}" wire:model.defer="activities.{{ $index }}.is_milestone"
                                                       class="rounded border-gray-300 dark:border-gray-700 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-900 dark:checked:bg-blue-600">
                                                <label for="activity-milestone-{{ $index }}" class="ml-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Jalon important</label>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                <button type="button" wire:click="addActivity" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors font-semibold dark:bg-blue-600 dark:text-gray-100 dark:hover:bg-blue-700">
                                    Ajouter une Activité
                                </button>
                            </div>

                            {{-- Champs dynamiques pour cette section --}}
                            @if (isset($dynamicFormFields['activites_initiales']))
                                <div class="mt-6 border-t pt-4 border-gray-200 dark:border-gray-700">
                                    <h3 class="text-lg font-semibold mb-3 text-gray-900 dark:text-gray-100">Informations Complémentaires</h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        @foreach ($dynamicFormFields['activites_initiales'] as $field)
                                            <div>
                                                <label for="dynamic-{{ $field['field_name'] }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                    {{ $field['question_text'] }} @if($field['is_required']) <span class="text-red-500">*</span> @endif
                                                </label>
                                                @if($field['input_type'] === 'textarea')
                                                    <textarea id="dynamic-{{ $field['field_name'] }}" wire:model.defer="dynamicFieldValues.{{ $field['field_name'] }}" 
                                                              rows="3" class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500"></textarea>
                                                @elseif($field['input_type'] === 'select')
                                                    <select id="dynamic-{{ $field['field_name'] }}" wire:model.defer="dynamicFieldValues.{{ $field['field_name'] }}" 
                                                            class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500">
                                                        <option value="">Sélectionner une option</option>
                                                        @foreach(explode(',', $field['options']) as $option)
                                                            <option value="{{ trim($option) }}">{{ trim($option) }}</option>
                                                        @endforeach
                                                    </select>
                                                @else
                                                    <input type="{{ $field['input_type'] }}" id="dynamic-{{ $field['field_name'] }}" wire:model.defer="dynamicFieldValues.{{ $field['field_name'] }}" 
                                                           class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500">
                                                @endif
                                                @error('dynamicFieldValues.' . $field['field_name']) <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- Fenêtre 6 : Budget Prévisionnel --}}
                        <div x-show="currentStep === 6" class="space-y-6">
                            <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Budget Prévisionnel</h2>
                            <p class="text-gray-600 dark:text-gray-300 mb-6">Estimez les coûts initiaux pour les principales lignes budgétaires du projet.</p>

                            <div class="space-y-4">
                                @foreach($budgets as $index => $budget)
                                    <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg relative">
                                        <button type="button" wire:click="removeBudget({{ $index }})" class="absolute top-2 right-2 text-red-500 hover:text-red-700 text-lg">&times;</button>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label for="budget-description-{{ $index }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description de la Ligne Budgétaire <span class="text-red-500">*</span></label>
                                                <textarea id="budget-description-{{ $index }}" wire:model.defer="budgets.{{ $index }}.description" rows="2"
                                                          class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500"
                                                          placeholder="Ex: Frais de personnel, Matériel de formation."></textarea>
                                                @error('budgets.' . $index . '.description') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                                            </div>
                                            <div>
                                                <label for="budget-category-{{ $index }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Catégorie (Optionnel)</label>
                                                <input type="text" id="budget-category-{{ $index }}" wire:model.defer="budgets.{{ $index }}.category"
                                                       class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500"
                                                       placeholder="Ex: Ressources humaines, Logistique.">
                                                @error('budgets.' . $index . '.category') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                                            </div>
                                            <div>
                                                <label for="budget-quantity-{{ $index }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Quantité (Optionnel)</label>
                                                <input type="number" id="budget-quantity-{{ $index }}" wire:model.defer="budgets.{{ $index }}.quantity"
                                                       class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500">
                                                @error('budgets.' . $index . '.quantity') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                                            </div>
                                            <div>
                                                <label for="budget-unit-cost-{{ $index }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Coût Unitaire (Optionnel)</label>
                                                <input type="number" step="0.01" id="budget-unit-cost-{{ $index }}" wire:model.defer="budgets.{{ $index }}.unit_cost"
                                                       class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500">
                                                @error('budgets.' . $index . '.unit_cost') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                                            </div>
                                            <div>
                                                <label for="budget-total-cost-{{ $index }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Coût Total (Optionnel)</label>
                                                <input type="number" step="0.01" id="budget-total-cost-{{ $index }}" wire:model.defer="budgets.{{ $index }}.total_cost"
                                                       class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500">
                                                @error('budgets.' . $index . '.total_cost') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                                            </div>
                                            <div>
                                                <label for="budget-responsible-{{ $index }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Responsable (Optionnel)</label>
                                                <select id="budget-responsible-{{ $index }}" wire:model.defer="budgets.{{ $index }}.responsible_user_id"
                                                        class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500">
                                                    <option value="">Sélectionner un responsable</option>
                                                    @foreach($users as $user)
                                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('budgets.' . $index . '.responsible_user_id') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                <button type="button" wire:click="addBudget" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors font-semibold dark:bg-blue-600 dark:text-gray-100 dark:hover:bg-blue-700">
                                    Ajouter une Ligne Budgétaire
                                </button>
                            </div>

                            {{-- Champs dynamiques pour cette section --}}
                            @if (isset($dynamicFormFields['budget_previsionnel']))
                                <div class="mt-6 border-t pt-4 border-gray-200 dark:border-gray-700">
                                    <h3 class="text-lg font-semibold mb-3 text-gray-900 dark:text-gray-100">Informations Complémentaires</h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        @foreach ($dynamicFormFields['budget_previsionnel'] as $field)
                                            <div>
                                                <label for="dynamic-{{ $field['field_name'] }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                    {{ $field['question_text'] }} @if($field['is_required']) <span class="text-red-500">*</span> @endif
                                                </label>
                                                @if($field['input_type'] === 'textarea')
                                                    <textarea id="dynamic-{{ $field['field_name'] }}" wire:model.defer="dynamicFieldValues.{{ $field['field_name'] }}" 
                                                              rows="3" class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500"></textarea>
                                                @elseif($field['input_type'] === 'select')
                                                    <select id="dynamic-{{ $field['field_name'] }}" wire:model.defer="dynamicFieldValues.{{ $field['field_name'] }}" 
                                                            class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500">
                                                        <option value="">Sélectionner une option</option>
                                                        @foreach(explode(',', $field['options']) as $option)
                                                            <option value="{{ trim($option) }}">{{ trim($option) }}</option>
                                                        @endforeach
                                                    </select>
                                                @else
                                                    <input type="{{ $field['input_type'] }}" id="dynamic-{{ $field['field_name'] }}" wire:model.defer="dynamicFieldValues.{{ $field['field_name'] }}" 
                                                           class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500">
                                                @endif
                                                @error('dynamicFieldValues.' . $field['field_name']) <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- Fenêtre 7 : Finalisation --}}
                        <div x-show="currentStep === 7" class="space-y-6">
                            <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Finalisation de la Proposition</h2>
                            <p class="text-gray-600 dark:text-gray-300 mb-6">Veuillez vérifier toutes les informations avant de soumettre votre proposition de projet pour validation.</p>

                            <div class="p-6 bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-800 rounded-lg text-yellow-700 dark:text-yellow-200">
                                <p class="font-medium">Une fois soumis, votre projet passera au statut "Brouillon" et sera visible par les validateurs.</p>
                            </div>

                            {{-- Résumé des données saisies --}}
                            <div class="bg-white dark:bg-gray-700 p-4 rounded-lg shadow">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Résumé du Projet</h3>
                                <div class="space-y-2 text-gray-700 dark:text-gray-300">
                                    <p><strong>Titre:</strong> {{ $projectTitle }}</p>
                                    <p><strong>Code:</strong> {{ $projectCode }}</p>
                                    <p><strong>Type:</strong> {{ $allProjectTypes->where('id', $selectedProjectTypeId)->first()->name ?? 'N/A' }}</p>
                                    <p><strong>Période:</strong> Du {{ $projectStartDate }} au {{ $projectEndDate }}</p>
                                    
                                    @if ($contextDescription)
                                        <p><strong>Contexte:</strong> {{ Str::limit($contextDescription, 100) }}</p>
                                    @endif

                                    @if (!empty($initialLogicalFramework['general_objective']))
                                        <p><strong>Objectif Général:</strong> {{ Str::limit($initialLogicalFramework['general_objective'], 100) }}</p>
                                    @endif

                                    @if (count($specificObjectives) > 0)
                                        <p><strong>Objectifs Spécifiques:</strong> {{ count($specificObjectives) }}</p>
                                    @endif

                                    @if (count($expectedResults) > 0)
                                        <p><strong>Résultats Attendus:</strong> {{ count($expectedResults) }}</p>
                                    @endif

                                    @if (count($activities) > 0)
                                        <p><strong>Activités:</strong> {{ count($activities) }}</p>
                                    @endif

                                    @if (count($budgets) > 0)
                                        <p><strong>Lignes Budgétaires:</strong> {{ count($budgets) }}</p>
                                    @endif

                                    @php
                                        $hasDynamicValues = false;
                                        foreach ($dynamicFieldValues as $value) {
                                            if (!empty($value)) {
                                                $hasDynamicValues = true;
                                                break;
                                            }
                                        }
                                    @endphp
                                    @if ($hasDynamicValues)
                                        <h4 class="font-semibold mt-4">Champs Dynamiques Saisis:</h4>
                                        <ul class="list-disc ml-5">
                                            @foreach ($dynamicFormFields as $section => $fields)
                                                @foreach ($fields as $field)
                                                    @if (!empty($dynamicFieldValues[$field['field_name']]))
                                                        <li><strong>{{ $field['question_text'] }}:</strong> {{ Str::limit($dynamicFieldValues[$field['field_name']], 70) }}</li>
                                                    @endif
                                                @endforeach
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Boutons de navigation --}}
                        <div class="flex justify-between items-center mt-8">
                            @if ($currentStep > 1)
                                <button type="button" wire:click="previousStep" class="px-6 py-3 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-100 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors font-semibold">
                                    Précédent
                                </button>
                            @else
                                <div></div>
                            @endif

                            @if ($currentStep < $totalSteps)
                                <button type="button" wire:click="nextStep" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-semibold dark:bg-blue-600 dark:text-gray-100 dark:hover:bg-blue-700">
                                    Suivant
                                </button>
                            @else
                                <button type="submit" class="px-6 py-3 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors font-semibold dark:bg-emerald-600 dark:text-gray-100 dark:hover:bg-emerald-700" wire:loading.attr="disabled">
                                    <span wire:loading.remove wire:target="submitForm">Soumettre la Proposition</span>
                                    <span wire:loading wire:target="submitForm">Soumission...</span>
                                </button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>
