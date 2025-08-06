<?php

namespace App\Livewire\VBeta\Project;

use Livewire\Component;


    
use Livewire\WithPagination; // Trait pour la pagination
use App\Models\Project;
use App\Models\User; // Pour le filtre par responsable

class ProjectListLivewire extends Component
{
    use WithPagination;

    public $search = ''; // Pour la barre de recherche
    public $statusFilter = ''; // Pour le filtre par statut
    public $responsibleUserFilter = ''; // Pour le filtre par responsable
    public $sortField = 'created_at'; // Colonne par défaut pour le tri
    public $sortDirection = 'desc'; // Direction par défaut pour le tri

    protected $queryString = [ // Synchronise les propriétés avec l'URL pour persister les filtres
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'responsibleUserFilter' => ['except' => ''],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function updatingSearch()
    {
        $this->resetPage(); // Réinitialise la pagination lors d'une nouvelle recherche
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingResponsibleUserFilter()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortField = $field;
    }

    public function render()
    {
        $user = auth()->user();

        $projects = Project::query();

        // Filtrer par projets créés par l'utilisateur ou où l'utilisateur est responsable d'activités
        $projects->where(function ($query) use ($user) {
            $query->where('user_id', $user->id)
                  ->orWhereHas('goals.objectives.results.activities', function ($subQuery) use ($user) {
                      $subQuery->where('responsible_user_id', $user->id);
                  });
        });


        // Appliquer la recherche textuelle
        if ($this->search) {
            $projects->where(function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%')
                      ->orWhere('short_title', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        // Appliquer le filtre de statut
        if ($this->statusFilter) {
            $projects->where('status', $this->statusFilter);
        }

        // Appliquer le filtre par responsable (le créateur du projet)
        // Note: Si vous voulez filtrer par les responsables d'activités, c'est plus complexe car un projet peut avoir plusieurs responsables d'activités.
        // Pour l'instant, nous filtrons sur le 'user_id' principal du projet.
        if ($this->responsibleUserFilter) {
            $projects->where('user_id', $this->responsibleUserFilter);
        }

        // Appliquer le tri
        $projects->orderBy($this->sortField, $this->sortDirection);

        // Obtenir les options pour les filtres (par exemple, tous les utilisateurs disponibles)
        // Vous pouvez affiner cette liste pour seulement les utilisateurs ayant des projets ou activités
        $availableUsers = User::orderBy('name')->get();

        // Obtenir les statuts de projet uniques (si vous voulez un filtre dynamique)
        $projectStatuses = Project::select('status')->distinct()->get()->pluck('status');


        return view('livewire.v-beta.project.project-list-livewire', [
            'projects' => $projects->paginate(10), // Pagination par 10 projets par page
            'availableUsers' => $availableUsers,
            'projectStatuses' => $projectStatuses,
        ]);
    }
}