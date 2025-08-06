<main class="lg:ml-64 pt-24 min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="max-w-7xl mx-auto space-y-8">
        <div class="flex justify-between items-center">
            <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white">
                Types de Projets
            </h2>
            <a href="{{ route('admin.it.project.types.create') }}" class="px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 transition-colors">
                Créer un nouveau type
            </a>
        </div>
        
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

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($projectTypes as $type)
                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">{{ $type->name }}</h3>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">{{ $type->description }}</p>
                    <div class="mt-4 flex justify-between items-center">
                        <span class="text-xs font-medium text-blue-600 dark:text-blue-400">Catégorie: {{ $type->category ?? 'N/A' }}</span>
                        <div class="space-x-2">
                            <a href="{{ route('admin.it.project.types.edit', ['projectTypeId' => $type->id]) }}" class="px-4 py-2 text-sm font-medium rounded-lg text-blue-600 dark:text-blue-400 hover:bg-blue-100 dark:hover:bg-blue-700 transition-colors">
                                Éditer
                            </a>
                            <button wire:click="deleteProjectType('{{ $type->id }}')" onclick="confirm('Êtes-vous sûr de vouloir supprimer ce type de projet ?') || event.stopImmediatePropagation()" class="px-4 py-2 text-sm font-medium rounded-lg text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-700 transition-colors">
                                Supprimer
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center p-8 bg-white dark:bg-gray-800 rounded-lg shadow-lg">
                    <p class="text-gray-700 dark:text-gray-300">Aucun type de projet n'a encore été créé.</p>
                </div>
            @endforelse
        </div>

    </div>
</main>
