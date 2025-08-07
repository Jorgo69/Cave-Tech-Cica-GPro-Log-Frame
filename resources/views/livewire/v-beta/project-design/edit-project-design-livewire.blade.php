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
                <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-6">Modifier le Projet : <span class="text-blue-600">{{ $projectTitle }}</span></h1>

                {{-- Wizard Navigation --}}
                <div x-data="{ currentStep: @entangle('currentStep'), totalSteps: @entangle('totalSteps'), stepDetails: @entangle('stepDetails'), updateProgress: function() { const progress = (this.currentStep / this.totalSteps) * 100; this.$refs.progressBarFill.style.width = `${progress}%`; }, init() { this.updateProgress(); this.$watch('currentStep', () => this.updateProgress()); Livewire.on('stepChanged', () => { this.updateProgress(); window.scrollTo({ top: 0, behavior: 'smooth' }); }); Livewire.on('showAlert', (data) => { Swal.fire({ title: data.title, text: data.text, icon: data.icon, confirmButtonText: data.confirmButtonText }); }); } }" class="flex flex-col lg:flex-row gap-8">
                    {{-- Colonne de navigation des étapes --}}
                    <div class="lg:w-1/4 bg-gray-100 dark:bg-gray-800 p-6 rounded-lg shadow-inner">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Étapes</h2>
                        <ul class="space-y-3">
                            <template x-for="(step, index) in stepDetails" :key="index">
                                <li class="flex items-center space-x-3 cursor-pointer p-2 rounded-md transition-colors"
                                    :class="{ 'bg-blue-600 text-white shadow-md': (index + 1) === currentStep, 'text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700': (index + 1) !== currentStep, 'opacity-50 cursor-not-allowed': (index + 1) > currentStep }"
                                    @click="(index + 1) <= currentStep && $wire.goToStep(index + 1)">
                                    <span class="font-bold" x-text="index + 1"></span>
                                    <div>
                                        <p class="font-medium" x-text="step.title"></p>
                                        <p class="text-sm" :class="{'text-blue-200': (index + 1) === currentStep, 'text-gray-500 dark:text-gray-400': (index + 1) !== currentStep}" x-text="step.description"></p>
                                    </div>
                                </li>
                            </template>
                        </ul>
                        {{-- Barre de progression --}}
                        <div class="mt-8">
                            <div class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Progression
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                                <div x-ref="progressBarFill" class="bg-blue-600 h-2.5 rounded-full transition-all duration-500 ease-out" style="width: 0%;"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Contenu des étapes --}}
                    <div class="lg:w-3/4">
                        <form wire:submit.prevent="submit">
                            {{-- Étape 1: Informations Clés --}}
                            @if ($currentStep == 1)
                                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                                    <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Informations Clés du Projet</h2>
                                    <p class="text-gray-600 dark:text-gray-400 mb-6">Mettez à jour les détails de base de votre projet.</p>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        {{-- Titre du Projet --}}
                                        <div>
                                            <label for="projectTitle" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Titre du Projet</label>
                                            <input type="text" id="projectTitle" wire:model.defer="projectTitle" class="form-input mt-1 block w-full rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-200">
                                            @error('projectTitle') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                                        </div>

                                        {{-- Code du Projet --}}
                                        <div>
                                            <label for="projectCode" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Code du Projet</label>
                                            <input type="text" id="projectCode" wire:model.defer="projectCode" class="form-input mt-1 block w-full rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-200">
                                            @error('projectCode') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                                        </div>

                                        {{-- Date de Début --}}
                                        <div>
                                            <label for="projectStartDate" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date de Début</label>
                                            <input type="date" id="projectStartDate" wire:model.defer="projectStartDate" class="form-input mt-1 block w-full rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-200">
                                            @error('projectStartDate') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                                        </div>

                                        {{-- Date de Fin --}}
                                        <div>
                                            <label for="projectEndDate" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date de Fin</label>
                                            <input type="date" id="projectEndDate" wire:model.defer="projectEndDate" class="form-input mt-1 block w-full rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-200">
                                            @error('projectEndDate') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                    <div class="mt-6">
                                        <label for="projectDescriptionGeneral" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description Générale</label>
                                        <textarea id="projectDescriptionGeneral" wire:model.defer="projectDescriptionGeneral" rows="4" class="form-textarea mt-1 block w-full rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-200"></textarea>
                                        @error('projectDescriptionGeneral') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="mt-6">
                                        <label for="problemAnalysis" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Analyse du Problème</label>
                                        <textarea id="problemAnalysis" wire:model.defer="problemAnalysis" rows="4" class="form-textarea mt-1 block w-full rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-200"></textarea>
                                        @error('problemAnalysis') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="mt-6">
                                        <label for="strategy" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Stratégie</label>
                                        <textarea id="strategy" wire:model.defer="strategy" rows="4" class="form-textarea mt-1 block w-full rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-200"></textarea>
                                        @error('strategy') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="mt-6">
                                        <label for="justification" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Justification</label>
                                        <textarea id="justification" wire:model.defer="justification" rows="4" class="form-textarea mt-1 block w-full rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-200"></textarea>
                                        @error('justification') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            @endif

                            {{-- Étape 2: Contexte --}}
                            @if ($currentStep == 2)
                                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                                    <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Contexte du Projet</h2>
                                    <p class="text-gray-600 dark:text-gray-400 mb-6">Veuillez fournir une description simple pour assister la génération par l'IA.</p>
                                    <div class="mt-6">
                                        <label for="baseProjectDescription" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description simplifiée</label>
                                        <textarea id="baseProjectDescription" wire:model.defer="baseProjectDescription" rows="4" class="form-textarea mt-1 block w-full rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-200"></textarea>
                                        @error('baseProjectDescription') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            @endif

                            {{-- Étape 3: Documents --}}
                            @if ($currentStep == 3)
                                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                                    <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Documents Associés</h2>
                                    <p class="text-gray-600 dark:text-gray-400 mb-6">Gérez les documents existants et ajoutez de nouveaux fichiers.</p>

                                    {{-- Documents existants --}}
                                    @if (!empty($existingDocuments))
                                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">Documents Actuels</h3>
                                        <ul class="list-disc list-inside space-y-2 mb-4">
                                            @foreach ($existingDocuments as $document)
                                                <li class="flex items-center justify-between text-gray-700 dark:text-gray-300">
                                                    <span>{{ $document['file_name'] }}</span>
                                                    <button type="button" wire:click="removeExistingDocument('{{ $document['id'] }}')" class="text-red-500 hover:text-red-700 text-sm">Supprimer</button>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif

                                    {{-- Nouveau upload --}}
                                    <div>
                                        <label for="uploadedDocuments" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Ajouter de nouveaux documents</label>
                                        <input type="file" id="uploadedDocuments" wire:model="uploadedDocuments" multiple class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                        <div wire:loading wire:target="uploadedDocuments" class="text-sm text-blue-500 mt-2">Chargement des fichiers...</div>
                                        @error('uploadedDocuments.*') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            @endif

                            {{-- Étape 4: Cadre Logique (But, Objectifs, Résultats et Activités) --}}
                            @if ($currentStep == 4)
                                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                                    <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Cadre Logique</h2>

                                    {{-- But Général --}}
                                    <div class="mb-6">
                                        <label for="generalGoal" class="block text-sm font-medium text-gray-700 dark:text-gray-300">But Général</label>
                                        <input type="text" id="generalGoal" wire:model.defer="generalGoal" class="form-input mt-1 block w-full rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-200">
                                        @error('generalGoal') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                                    </div>

                                    {{-- Objectifs Spécifiques et Résultats --}}
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">Objectifs Spécifiques</h3>
                                    @foreach ($specificObjectives as $objIndex => $objective)
                                        <div class="border-l-4 border-blue-500 pl-4 mb-6">
                                            <div class="flex items-center space-x-2 mb-2">
                                                <input type="text" wire:model.defer="specificObjectives.{{ $objIndex }}.description" placeholder="Description de l'objectif spécifique" class="form-input flex-grow rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-200">
                                                <button type="button" wire:click="removeObjective({{ $objIndex }})" class="text-red-500 hover:text-red-700">
                                                    <svg xmlns="[http://www.w3.org/2000/svg](http://www.w3.org/2000/svg)" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 100 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm8-1a1 1 0 011 1v6a1 1 0 11-2 0V8a1 1 0 011-1z" clip-rule="evenodd" /></svg>
                                                </button>
                                            </div>
                                            @error("specificObjectives.$objIndex.description") <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror

                                            {{-- Résultats --}}
                                            <h4 class="text-md font-medium text-gray-700 dark:text-gray-300 mt-4 mb-2">Résultats</h4>
                                            @foreach ($objective['results'] as $resIndex => $result)
                                                <div class="border-l-2 border-gray-400 pl-3 mb-4">
                                                    <div class="flex items-center space-x-2 mb-2">
                                                        <input type="text" wire:model.defer="specificObjectives.{{ $objIndex }}.results.{{ $resIndex }}.description" placeholder="Description du résultat" class="form-input flex-grow rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-200">
                                                        <button type="button" wire:click="removeResult({{ $objIndex }}, {{ $resIndex }})" class="text-red-500 hover:text-red-700">
                                                            <svg xmlns="[http://www.w3.org/2000/svg](http://www.w3.org/2000/svg)" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 100 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm8-1a1 1 0 011 1v6a1 1 0 11-2 0V8a1 1 0 011-1z" clip-rule="evenodd" /></svg>
                                                        </button>
                                                    </div>
                                                    <input type="text" wire:model.defer="specificObjectives.{{ $objIndex }}.results.{{ $resIndex }}.indicators" placeholder="Indicateurs de succès" class="form-input w-full rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-200">
                                                    
                                                    {{-- Activités imbriquées --}}
                                                    <h5 class="text-sm font-medium text-gray-600 dark:text-gray-400 mt-4 mb-2">Activités</h5>
                                                    @foreach ($result['activities'] as $actIndex => $activity)
                                                        <div class="flex items-center space-x-2 mb-2">
                                                            <div class="flex-grow">
                                                                <input type="text" wire:model.defer="specificObjectives.{{ $objIndex }}.results.{{ $resIndex }}.activities.{{ $actIndex }}.description" placeholder="Description de l'activité" class="form-input w-full rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-200 text-sm">
                                                            </div>
                                                            <div class="w-1/3">
                                                                <select wire:model.defer="specificObjectives.{{ $objIndex }}.results.{{ $resIndex }}.activities.{{ $actIndex }}.responsible" class="form-select w-full rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-200 text-sm">
                                                                    <option value="">Responsable</option>
                                                                    @foreach ($users as $user)
                                                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <button type="button" wire:click="removeActivity({{ $objIndex }}, {{ $resIndex }}, {{ $actIndex }})" class="text-red-500 hover:text-red-700">
                                                                <svg xmlns="[http://www.w3.org/2000/svg](http://www.w3.org/2000/svg)" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 100 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm8-1a1 1 0 011 1v6a1 1 0 11-2 0V8a1 1 0 011-1z" clip-rule="evenodd" /></svg>
                                                            </button>
                                                        </div>
                                                    @endforeach
                                                    <button type="button" wire:click="addActivity({{ $objIndex }}, {{ $resIndex }})" class="mt-2 text-xs text-blue-500 hover:text-blue-700">
                                                        + Ajouter une activité
                                                    </button>
                                                </div>
                                            @endforeach
                                            <button type="button" wire:click="addResult({{ $objIndex }})" class="mt-2 text-sm text-blue-500 hover:text-blue-700">
                                                + Ajouter un résultat
                                            </button>
                                        </div>
                                    @endforeach
                                    <button type="button" wire:click="addObjective" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-semibold">
                                        Ajouter un objectif spécifique
                                    </button>
                                </div>
                            @endif

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
                                    <button type="button" wire:click="nextStep" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-semibold">
                                        Suivant
                                    </button>
                                @else
                                    <button type="submit" class="px-6 py-3 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors font-semibold">
                                        Mettre à jour le Projet
                                    </button>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>