<?php

namespace App\Livewire\VBeta\ProjectType;

use Livewire\Component;
use App\Models\ProjectType;

class ProjectTypeListLivewire extends Component
{
    // Propriété pour stocker la liste des types de projets
    public $projectTypes;

    // Méthode de montage pour charger les données
    public function mount()
    {
        $this->projectTypes = ProjectType::orderBy('name')->get();
    }

    // Méthode pour la suppression d'un type de projet
    public function deleteProjectType($id)
    {
        try {
            ProjectType::destroy($id);
            // Recharger la liste après la suppression
            $this->projectTypes = ProjectType::orderBy('name')->get();
            session()->flash('message', 'Le type de projet a été supprimé avec succès.');
        } catch (\Exception $e) {
            session()->flash('error', 'Impossible de supprimer le type de projet.');
        }
    }

    // La méthode render retourne la vue associée au composant
    public function render()
    {
        return view('livewire.v-beta.project-type.project-type-list-livewire');
    }
}

