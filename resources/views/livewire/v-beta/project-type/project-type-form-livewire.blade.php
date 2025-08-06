<main class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-gray-100 dark:bg-gray-900">
    <div class="max-w-4xl w-full space-y-8 bg-white dark:bg-gray-800 p-10 rounded-xl shadow-2xl">
        <div class="text-center">
            <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white">
                Créer un nouveau type de projet
            </h2>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                Définissez les informations de base et les champs dynamiques pour un nouveau modèle de projet.
            </p>
        </div>

        <!-- Formulaire principal -->
        <form wire:submit.prevent="save" class="mt-8 space-y-6">
            <!-- Message de statut -->
            @if (session()->has('message'))
                <div class="p-4 mb-4 text-sm rounded-lg bg-green-100 text-green-800" role="alert">
                    {{ session('message') }}
                </div>
            @endif
            @if (session()->has('error'))
                <div class="p-4 mb-4 text-sm rounded-lg bg-red-100 text-red-800" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Section 1: Informations de base du type de projet -->
            <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg shadow-inner">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Informations Clés</h3>
                <div class="space-y-4">
                    <div>
                        <label for="project-type-name" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Nom du type de projet</label>
                        <input type="text" id="project-type-name" wire:model.defer="name" required class="form-input mt-1">
                        @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="project-type-description" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Description</label>
                        <textarea id="project-type-description" wire:model.defer="description" rows="3" class="form-input mt-1"></textarea>
                    </div>
                    <div>
                        <label for="project-type-category" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Catégorie</label>
                        <input type="text" id="project-type-category" wire:model.defer="category" class="form-input mt-1">
                    </div>
                </div>
            </div>

            <!-- Section 2: Champs dynamiques -->
            <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg shadow-inner">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Champs Dynamiques</h3>
                <div class="space-y-6">
                    @foreach ($fields as $index => $field)
                        <div class="dynamic-field p-4 border border-gray-200 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 relative">
                            <button type="button" wire:click="removeField({{ $index }})" class="absolute top-2 right-2 text-gray-400 hover:text-red-500 transition-colors">
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Libellé de la question</label>
                                    <input type="text" wire:model.defer="fields.{{ $index }}.question_text" required class="form-input mt-1">
                                    @error('fields.' . $index . '.question_text') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Type de champ</label>
                                    <select wire:model.defer="fields.{{ $index }}.input_type" class="form-input mt-1">
                                        <option value="text">Texte (simple)</option>
                                        <option value="textarea">Zone de texte (long)</option>
                                        <option value="select">Liste déroulante</option>
                                        <option value="date">Date</option>
                                        <option value="number">Nombre</option>
                                    </select>
                                </div>
                                <div class="col-span-1 md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Nom du champ</label>
                                    <input type="text" wire:model.defer="fields.{{ $index }}.field_name" required class="form-input mt-1">
                                    @error('fields.' . $index . '.field_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                @if ($field['input_type'] === 'select')
                                    <div class="col-span-1 md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Options (séparées par une virgule)</label>
                                        <textarea wire:model.defer="fields.{{ $index }}.options" rows="2" class="form-input mt-1" placeholder="Option 1, Option 2, Option 3"></textarea>
                                    </div>
                                @endif
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Ordre</label>
                                    <input type="number" wire:model.defer="fields.{{ $index }}.order" required class="form-input mt-1">
                                    @error('fields.' . $index . '.order') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Champ cible (ex: 'title')</label>
                                    <input type="text" wire:model.defer="fields.{{ $index }}.target_project_field" class="form-input mt-1" placeholder="Ex: 'title'">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Section</label>
                                    <input type="text" wire:model.defer="fields.{{ $index }}.section" class="form-input mt-1" placeholder="Ex: 'Informations de base'">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Délimiteur de début</label>
                                    <input type="text" wire:model.defer="fields.{{ $index }}.delimiter_start" class="form-input mt-1">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Délimiteur de fin</label>
                                    <input type="text" wire:model.defer="fields.{{ $index }}.delimiter_end" class="form-input mt-1">
                                </div>
                                <div class="flex items-center">
                                    <div class="flex items-center h-5">
                                        <input id="is_required-{{ $index }}" wire:model.defer="fields.{{ $index }}.is_required" type="checkbox" class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="is_required-{{ $index }}" class="font-medium text-gray-700 dark:text-gray-200">Obligatoire</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <button type="button" wire:click="addField" class="mt-4 w-full flex justify-center items-center px-4 py-2 border border-dashed border-gray-400 dark:border-gray-500 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors font-medium">
                    <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    Ajouter un champ
                </button>
            </div>

            <!-- Bouton de soumission -->
            <div class="flex justify-end">
                <button type="submit" class="px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 transition-colors">
                    Sauvegarder le type de projet
                </button>
            </div>
        </form>
    </div>
</main>
