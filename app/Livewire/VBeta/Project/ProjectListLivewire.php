<?php

namespace App\Livewire\VBeta\Project;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ProjectListLivewire extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $responsibleUserFilter = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'responsibleUserFilter' => ['except' => ''],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
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
        $user = Auth::user();

        $projects = Project::query();

        // Filtrer par projets créés par l'utilisateur ou où l'utilisateur est responsable d'activités
        $projects->where(function ($query) use ($user) {
            $query->where('creator_user_id', $user->id)
                  ->orWhereHas('logicalFramework.specificObjectives.results.activities', function ($subQuery) use ($user) {
                      $subQuery->where('responsible_user_id', $user->id);
                  });
        });

        // Appliquer la recherche textuelle
        if ($this->search) {
            $projects->where(function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%')
                      ->orWhere('short_title', 'like', '%' . $this->search . '%')
                      ->orWhere('project_code', 'like', '%' . $this->search . '%');
            });
        }

        // Appliquer le filtre de statut
        if ($this->statusFilter) {
            $projects->where('status', $this->statusFilter);
        }

        // Appliquer le filtre par responsable (le créateur du projet)
        if ($this->responsibleUserFilter) {
            $projects->where('creator_user_id', $this->responsibleUserFilter);
        }

        // Appliquer le tri
        $projects->orderBy($this->sortField, $this->sortDirection);

        // Obtenir les options pour les filtres (par exemple, tous les utilisateurs disponibles)
        $availableUsers = User::orderBy('name')->get();

        // Obtenir les statuts de projet uniques (si vous voulez un filtre dynamique)
        $projectStatuses = Project::select('status')->distinct()->get()->pluck('status');


        return view('livewire.v-beta.project.project-list-livewire', [
            'projects' => $projects->paginate(10),
            'availableUsers' => $availableUsers,
            'projectStatuses' => $projectStatuses,
        ]);
    }
}