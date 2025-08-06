<x-app-layout>

    {{-- @livewire('v-beta.project-type.project-type-form-livewire', ['projectTypeId' => request()->route('projectTypeId')]) --}}

    @if (request()->route('projectId'))
        {{-- Mode Édition : le composant est monté avec l'ID --}}
        @livewire('v-beta.proposal-project.proposal-project-form-livewire', ['projectId' => request()->route('projectId')])
    {{-- @elseif (request()->route('admin.it.type.of.project'))
        @livewire('project-type.project-type-list') --}}
    @else
        {{-- Mode Création : le composant est monté sans ID --}}
        @livewire('v-beta.proposal-project.proposal-project-form-livewire')
    @endif

</x-app-layout>
