<?php


namespace App\Livewire\VBeta\Project;

use Livewire\Component;
use App\Models\Project;
use App\Models\ProjectContext;
use App\Models\ProjectDocument;
use App\Models\EnvironmentAnalysis;
use App\Models\Stakeholder;
use App\Models\ProblemAnalysis;
use App\Models\Strategy;
use App\Models\Goal;
use App\Models\Objective;
use App\Models\Result;
use App\Models\Activity;
use App\Models\Risk;

class ProjectShowLivewire extends Component
{
    public $projectId;
    public $project;

    // Assurez-vous d'utiliser le bon namespace pour vos modèles si différent (ex: App\Models\Project)
    // Et assurez-vous que toutes les relations sont correctement définies dans vos modèles.

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
     */
    public function loadProject()
    {
        // Charge le projet avec toutes ses relations pour minimiser les requêtes N+1
        $this->project = Project::with([
            'context',
            'documents',
            'environmentAnalyses',
            'stakeholders',
            'problemAnalyses',
            'strategies',
            'goals.objectives.results.activities', // Charge la hiérarchie But -> Objectifs -> Résultats -> Activités
            'risks'
        ])->findOrFail($this->projectId);
    }

    /**
     * Rend la vue du composant.
     */
    public function render()
    {
        return view('livewire.v-beta.project.project-show-livewire');
    }

    // Exemple de méthode si vous souhaitez des opérations futures (édition, etc.)
    // public function toggleEditMode()
    // {
    //     // Logique pour basculer en mode édition
    // }
}