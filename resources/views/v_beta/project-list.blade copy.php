<x-guest-layout>
    


    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Mes Projets') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white mb-4">Gestion des Projets</h1>
                <p class="text-gray-600 dark:text-gray-400 mb-6">Visualisez, filtrez et gérez tous vos projets.</p>

                {{-- Ici, nous incluons notre composant Livewire --}}
                <livewire:v-beta.project.project-list-livewire/>

            </div>
        </div>
    </div>
</x-guest-layout>