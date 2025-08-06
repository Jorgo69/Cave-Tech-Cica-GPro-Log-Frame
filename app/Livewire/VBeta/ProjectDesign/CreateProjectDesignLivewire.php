<?php

namespace App\Livewire\VBeta\ProjectDesign;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\Project;
use App\Models\ProjectContext;
use App\Models\ProjectDocument;
use App\Models\EnvironmentAnalysis; // Modèle corrigé
use App\Models\Stakeholder;
use App\Models\ProblemAnalysis;     // Modèle corrigé
use App\Models\Strategy;
use App\Models\Goal;
use App\Models\Objective;
use App\Models\Result;
use App\Models\Activity;
use App\Models\Risk;
use App\Models\User; // Ajout du modèle User si nécessaire pour les responsables

class CreateProjectDesignLivewire extends Component
{
    use WithFileUploads;

    // Nouvelle propriété pour stocker l'ID du projet si nous sommes en mode édition
    public $projectId; // Sera null en mode création, et l'UUID en mode édition

    // Propriétés du wizard
    public $currentStep = 1;
    public $totalSteps = 9;

    // Détails des étapes pour la navigation latérale et la barre de progression
    public $stepDetails = [
        ['title' => 'Informations Clés', 'description' => 'Détails de base du projet'],
        ['title' => 'Contexte Environnemental', 'description' => 'Analyse PESTEL/SWOT'],
        ['title' => 'Acteurs Clés', 'description' => 'Identification des parties prenantes'],
        ['title' => 'Problématique Cible', 'description' => 'Analyse détaillée du problème'],
        ['title' => 'Stratégie & Approche', 'description' => 'Définition de la stratégie globale'],
        ['title' => 'Buts et Objectifs', 'description' => 'Objectif général et objectifs spécifiques'],
        ['title' => 'Résultats & Activités', 'description' => 'Définition des résultats et activités'],
        ['title' => 'Analyse des Risques', 'description' => 'Identification et évaluation des risques'],
        ['title' => 'Finalisation', 'description' => 'Vérification et soumission'],
    ];
    // Données du projet
    public $projectCode;
    public $projectTitle;
    public $projectShortTitle;
    public $projectStartDate;
    public $projectEndDate;
    public $projectStatus = 'Brouillon';

    // Données des étapes
    public $contextDescription;
    public $uploadedDocuments = [];
    public $environmentAnalysisText;
    public $stakeholders = [['name' => '', 'role' => '', 'influence' => '', 'interest' => '']];
    public $problemAnalysisText;
    public $strategyDefinitionText;
    public $generalGoal;
    public $specificObjectives = [['description' => '']];
    public $expectedResults = [['description' => '', 'indicators' => '', 'activities' => [['description' => '', 'responsible_user_id' => '', 'start_date' => '', 'end_date' => '', 'status' => 'En cours']]]];
    public $risks = [['description' => '', 'impact' => '', 'probability' => '', 'mitigation_strategy' => '']];

    // Listeners Livewire
    protected $listeners = ['stepChanged'];

    protected $rules = [
        'projectCode' => 'required|string|max:255|unique:projects,project_code',
        'projectTitle' => 'required|string|max:255',
        'projectStartDate' => 'required|date',
        'projectEndDate' => 'required|date|after_or_equal:projectStartDate',
        'contextDescription' => 'nullable|string',
        'uploadedDocuments.*' => 'nullable|file|max:50000', // 50MB max
        'environmentAnalysisText' => 'nullable|string',
        'stakeholders.*.name' => 'required|string',
        'stakeholders.*.role' => 'nullable|string',
        'problemAnalysisText' => 'nullable|string',
        'strategyDefinitionText' => 'nullable|string',
        'generalGoal' => 'required|string',
        'specificObjectives.*.description' => 'required|string',
        'expectedResults.*.description' => 'required|string',
        'risks.*.description' => 'required|string',
    ];

    /**
     * Gère les changements de step
     */
    public function nextStep()
    {
        $this->validateCurrentStep();
        if ($this->currentStep < $this->totalSteps) {
            $this->currentStep++;
        }
    }

    public function previousStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    /**
     * Valide les données de l'étape actuelle
     */
    public function validateCurrentStep()
    {
        switch ($this->currentStep) {
            case 1:
                $this->validateOnly('projectCode');
                $this->validateOnly('projectTitle');
                $this->validateOnly('projectStartDate');
                $this->validateOnly('projectEndDate');
                break;
            case 2:
                $this->validateOnly('contextDescription');
                $this->validateOnly('environmentAnalysisText');
                $this->validateOnly('uploadedDocuments.*');
                break;
            case 3:
                $this->validateOnly('stakeholders.*.name');
                break;
            case 4:
                $this->validateOnly('problemAnalysisText');
                break;
            case 5:
                $this->validateOnly('strategyDefinitionText');
                break;
            case 6:
                $this->validateOnly('generalGoal');
                $this->validateOnly('specificObjectives.*.description');
                break;
            case 7:
                $this->validateOnly('expectedResults.*.description');
                $this->validateOnly('expectedResults.*.activities.*.description');
                break;
            case 8:
                $this->validateOnly('risks.*.description');
                break;
        }
    }

    /**
     * Méthode pour ajouter un stakeholder
     */
    public function addStakeholder()
    {
        $this->stakeholders[] = ['name' => '', 'role' => '', 'influence' => '', 'interest' => ''];
    }
    public function removeStakeholder($index)
    {
        unset($this->stakeholders[$index]);
        $this->stakeholders = array_values($this->stakeholders);
    }
    
    /**
     * Méthode pour ajouter un objectif
     */
    public function addObjective()
    {
        $this->specificObjectives[] = ['description' => ''];
    }
    public function removeObjective($index)
    {
        unset($this->specificObjectives[$index]);
        $this->specificObjectives = array_values($this->specificObjectives);
    }

    /**
     * Méthode pour ajouter un résultat
     */
    public function addResult()
    {
        $this->expectedResults[] = ['description' => '', 'indicators' => '', 'activities' => [['description' => '', 'responsible_user_id' => '', 'start_date' => '', 'end_date' => '', 'status' => 'En cours']]];
    }
    public function removeResult($index)
    {
        unset($this->expectedResults[$index]);
        $this->expectedResults = array_values($this->expectedResults);
    }

    /**
     * Méthode pour ajouter une activité à un résultat
     */
    public function addActivity($resultIndex)
    {
        $this->expectedResults[$resultIndex]['activities'][] = ['description' => '', 'responsible_user_id' => '', 'start_date' => '', 'end_date' => '', 'status' => 'En cours'];
    }
    public function removeActivity($resultIndex, $activityIndex)
    {
        unset($this->expectedResults[$resultIndex]['activities'][$activityIndex]);
        $this->expectedResults[$resultIndex]['activities'] = array_values($this->expectedResults[$resultIndex]['activities']);
    }

    /**
     * Méthode pour ajouter un risque
     */
    public function addRisk()
    {
        $this->risks[] = ['description' => '', 'impact' => '', 'probability' => '', 'mitigation_strategy' => ''];
    }
    public function removeRisk($index)
    {
        unset($this->risks[$index]);
        $this->risks = array_values($this->risks);
    }

    /**
     * Soumet le formulaire
     */
    public function submitForm()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            $project = Project::create([
                'id' => (string) Str::uuid(),
                'project_code' => $this->projectCode,
                'title' => $this->projectTitle,
                'short_title' => $this->projectShortTitle,
                'description' => $this->contextDescription,
                'start_date' => $this->projectStartDate,
                'end_date' => $this->projectEndDate,
                'status' => $this->projectStatus,
                'creator_user_id' => auth()->id(),
                'updated_by_user_id' => auth()->id(),
            ]);

            // Save ProjectContext
            ProjectContext::create([
                'id' => (string) Str::uuid(),
                'project_id' => $project->id,
                'description' => $this->contextDescription,
            ]);

            // Save EnvironmentAnalysis
            EnvironmentAnalysis::create([
                'id' => (string) Str::uuid(),
                'project_id' => $project->id,
                'description' => $this->environmentAnalysisText,
            ]);

            // Save Stakeholders
            foreach ($this->stakeholders as $stakeholderData) {
                Stakeholder::create([
                    'id' => (string) Str::uuid(),
                    'project_id' => $project->id,
                    'name' => $stakeholderData['name'],
                    'role' => $stakeholderData['role'],
                    'influence' => $stakeholderData['influence'],
                    'interest' => $stakeholderData['interest'],
                ]);
            }

            // Save ProblemAnalysis
            ProblemAnalysis::create([
                'id' => (string) Str::uuid(),
                'project_id' => $project->id,
                'description' => $this->problemAnalysisText,
            ]);

            // Save Strategy
            Strategy::create([
                'id' => (string) Str::uuid(),
                'project_id' => $project->id,
                'description' => $this->strategyDefinitionText,
            ]);
            
            // Save Goal and Objectives
            $goal = Goal::create([
                'id' => (string) Str::uuid(),
                'project_id' => $project->id,
                'description' => $this->generalGoal,
            ]);

            foreach ($this->specificObjectives as $objectiveData) {
                $objective = Objective::create([
                    'id' => (string) Str::uuid(),
                    'goal_id' => $goal->id,
                    'description' => $objectiveData['description'],
                ]);
                
                // Save Results and Activities
                foreach ($this->expectedResults as $resultData) {
                    $result = Result::create([
                        'id' => (string) Str::uuid(),
                        'objective_id' => $objective->id,
                        'description' => $resultData['description'],
                        'indicators' => $resultData['indicators'],
                    ]);

                    foreach ($resultData['activities'] as $activityData) {
                        Activity::create([
                            'id' => (string) Str::uuid(),
                            'result_id' => $result->id,
                            'responsible_user_id' => $activityData['responsible_user_id'],
                            'description' => $activityData['description'],
                            'start_date' => $activityData['start_date'],
                            'end_date' => $activityData['end_date'],
                            'status' => $activityData['status'],
                        ]);
                    }
                }
            }

            // Save Risks
            foreach ($this->risks as $riskData) {
                Risk::create([
                    'id' => (string) Str::uuid(),
                    'project_id' => $project->id,
                    'description' => $riskData['description'],
                    'impact' => $riskData['impact'],
                    'probability' => $riskData['probability'],
                    'mitigation_strategy' => $riskData['mitigation_strategy'],
                ]);
            }

            // Save uploaded documents
            foreach ($this->uploadedDocuments as $document) {
                $path = $document->store('documents/' . $project->id, 'public');
                ProjectDocument::create([
                    'id' => (string) Str::uuid(),
                    'project_id' => $project->id,
                    'file_path' => $path,
                    'file_name' => $document->getClientOriginalName(),
                    'file_mime_type' => $document->getMimeType(),
                ]);
            }

            DB::commit();
            session()->flash('success', 'Le projet a été créé avec succès.');
            return redirect()->route('dashboard');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la création du projet : ' . $e->getMessage());
            session()->flash('error', 'Une erreur est survenue lors de la création du projet. Veuillez réessayer.');
        }
    }

    /**
     * Rendu du composant
     */
    public function render()
    {
        return view('livewire.v-beta.project-design.create-project-design-livewire', [
            'users' => User::all(['id', 'name']),
        ]);
    }
}
