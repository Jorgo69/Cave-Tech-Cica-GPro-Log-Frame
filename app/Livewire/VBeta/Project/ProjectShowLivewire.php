<?php

namespace App\Livewire\VBeta\Project;

use Livewire\Component;
use App\Models\Project;
use App\Models\ProjectContext;
use App\Models\ProjectDocument;
use App\Models\LogicalFramework;
use App\Models\SpecificObjective;
use App\Models\Result;
use App\Models\Activity;
use App\Models\Risk;
use App\Models\Budget;
use App\Models\DynamicProjectField;

class ProjectShowLivewire extends Component
{
    public $projectId;
    public $project;
    public $dynamicFormFields = []; // Pour stocker les définitions des champs dynamiques

    /**
     * Monte le composant avec l'ID du projet.
     * Utilise l'eager loading pour charger toutes les relations nécessaires.
     */
    public function mount($projectId)
    {
        $this->projectId = $projectId;
        $this->loadProject();
    }

    /**
     * Charge le projet et toutes ses relations.
     *
     * J'ai mis à jour les relations pour qu'elles correspondent à votre modèle
     * et à votre formulaire de création/édition.
     */
    public function loadProject()
    {
        $this->project = Project::with([
            'projectType.dynamicFields', // Charge le type de projet et ses champs dynamiques
            'projectContext',
            'documents',
            'budgets',
            'logicalFramework.specificObjectives.results.activities', // La hiérarchie correcte
            'creator',
        ])->findOrFail($this->projectId);

        // Charge les définitions des champs dynamiques pour l'affichage
        if ($this->project->projectType) {
             $this->dynamicFormFields = $this->project->projectType->dynamicFields()
                ->orderBy('order')
                ->get()
                ->groupBy('section')
                ->toArray();
        }
    }

    /**
     * Rend la vue du composant.
     */
    public function render()
    {
        return view('livewire.v-beta.project.project-show-livewire');
    }
}
