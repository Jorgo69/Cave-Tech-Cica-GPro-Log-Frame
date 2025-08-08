<main class="lg:ml-64 pt-16 min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="p-6" wire:loading.class="opacity-50">

        <div class="mb-6 flex justify-between items-center">
            <div class="flex-grow flex items-center space-x-4">
                {{-- Champ de recherche --}}
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Rechercher un projet..."
                    class="form-input rounded-md shadow-sm mt-1 block w-full md:w-1/3 dark:bg-gray-700 dark:text-gray-200 border-gray-300 dark:border-gray-600 focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">

                {{-- Filtre par Statut --}}
                <select wire:model.live="statusFilter" class="form-select rounded-md shadow-sm mt-1 block w-full md:w-1/4 dark:bg-gray-700 dark:text-gray-200 border-gray-300 dark:border-gray-600 focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <option value="">Tous les statuts</option>
                    @foreach ($projectStatuses as $status)
                        <option value="{{ $status }}">{{ Str::ucfirst(str_replace('_', ' ', $status == 'draft' ? 'Brouillons' : $status )) }}</option>
                    @endforeach
                </select>

                {{-- Filtre par Responsable (Créateur du projet) --}}
                <select wire:model.live="responsibleUserFilter" class="form-select rounded-md shadow-sm mt-1 block w-full md:w-1/4 dark:bg-gray-700 dark:text-gray-200 border-gray-300 dark:border-gray-600 focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <option value="">Tous les responsables</option>
                    @foreach ($availableUsers as $userOption)
                        <option value="{{ $userOption->id }}">{{ $userOption->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Bouton Nouveau Projet --}}
            <a href="{{ route('creator.proposal.project.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path></svg>
                Nouveau Projet
            </a>
        </div>

        {{-- Tableau des Projets --}}
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                @if ($projects->isEmpty())
                    <p class="text-center text-gray-500 dark:text-gray-400">Aucun projet trouvé pour cette sélection.</p>
                @else
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer" wire:click="sortBy('title')">
                                    Titre
                                    @if ($sortField === 'title')
                                        <span class="ml-1 text-sm">{{ $sortDirection === 'asc' ? '▲' : '▼' }}</span>
                                    @endif
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer" wire:click="sortBy('project_code')">
                                    Code
                                    @if ($sortField === 'project_code')
                                        <span class="ml-1 text-sm">{{ $sortDirection === 'asc' ? '▲' : '▼' }}</span>
                                    @endif
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer" wire:click="sortBy('status')">
                                    Statut
                                    @if ($sortField === 'status')
                                        <span class="ml-1 text-sm">{{ $sortDirection === 'asc' ? '▲' : '▼' }}</span>
                                    @endif
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Responsable
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer" wire:click="sortBy('start_date')">
                                    Début
                                    @if ($sortField === 'start_date')
                                        <span class="ml-1 text-sm">{{ $sortDirection === 'asc' ? '▲' : '▼' }}</span>
                                    @endif
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer" wire:click="sortBy('end_date')">
                                    Fin
                                    @if ($sortField === 'end_date')
                                        <span class="ml-1 text-sm">{{ $sortDirection === 'asc' ? '▲' : '▼' }}</span>
                                    @endif
                                </th>
                                <th scope="col" class="relative px-6 py-3">
                                    <span class="sr-only">Actions</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($projects as $project)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $project->title }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                        {{ $project->project_code }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        {{ 
                                            $project->status === 'Actif' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200' :
                                            ($project->status === 'draft' ? 'bg-amber-300 text-amber-800 dark:bg-amber-900 dark:text-amber-200' :
                                            ($project->status === 'Terminé' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' :
                                            ($project->status === 'En attente' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' :
                                            'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
                                            )))
                                        }}
                                        ">
                                            {{ Str::ucfirst(str_replace('_', ' ', $project->status == 'draft' ? 'Brouillon' : $project->status )) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                        {{ $project->creator->name ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                        {{ \Carbon\Carbon::parse($project->start_date)->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                        {{ \Carbon\Carbon::parse($project->end_date)->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('project.show', $project->id) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-200 mr-2">Voir</a>
                                        <a href="{{ route('creator.proposal.project.edit', $project->id) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-200">Modifier</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{-- Pagination Livewire --}}
                    <div class="mt-4">
                        {{ $projects->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</main>