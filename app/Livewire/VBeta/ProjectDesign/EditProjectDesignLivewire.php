<?php

namespace App\Livewire\VBeta\ProjectDesign;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Project;
use App\Models\ProjectContext;
use App\Models\ProjectDocument;
use App\Models\EnvironmentAnalyse as EnvironmentAnalysis;
use App\Models\Stakeholder;
use App\Models\ProblemAnalysis;
use App\Models\Strategy;
use App\Models\Goal;
use App\Models\Objective;
use App\Models\Result;
use App\Models\Activity;
use App\Models\Risk;
use App\Models\User;

class EditProjectDesignLivewire extends Component
{
    use WithFileUploads;

    public $projectId; // Obligatoire en mode édition
    public $project; // L'objet Project pour faciliter l'accès

    public $currentStep = 1;
    public $totalSteps = 9; // Adjust based on your final number of steps

    // Project administrative data
    public $projectTitle;
    public $projectCode;
    public $projectShortTitle;
    public $projectDescriptionGeneral;
    public $projectStartDate;
    public $projectEndDate;
    public $projectStatus; // Ne pas initialiser 'draft' ici, ça viendra du projet existant

    // Wizard step data
    public $baseProjectDescription;
    public $uploadedDocuments = []; // Pour les nouveaux uploads
    public $existingDocuments = []; // Pour afficher les documents déjà uploadés
    public $environmentAnalysisText;
    public $stakeholders = [['name' => '', 'role' => '']];
    public $problemAnalysisText;
    public $strategyDefinitionText;
    public $generalGoal;
    public $specificObjectives = [['description' => '', 'results' => [['description' => '', 'indicators' => '', 'activities' => [['description' => '', 'responsible' => '']]]]]];
    public $risks = [['description' => '', 'impact' => '', 'probability' => '']];

    // Détails des étapes
    public $stepDetails = [
        ['title' => 'Informations Clés', 'description' => 'Détails de base du projet'],
        ['title' => 'Contexte pour l\'IA', 'description' => 'Description simplifiée pour l\'assistance IA'],
        ['title' => 'Analyse Environnementale', 'description' => 'PESTEL/SWOT'],
        ['title' => 'Parties Prenantes', 'description' => 'Identification des acteurs clés'],
        ['title' => 'Problématique Cible', 'description' => 'Analyse du problème principal'],
        ['title' => 'Stratégie & Approche', 'description' => 'Définition de la stratégie globale'],
        ['title' => 'But Général & Objectifs Spécifiques', 'description' => 'Cadre logique du projet'],
        ['title' => 'Résultats Attendus & Activités', 'description' => 'Détails de la mise en œuvre'],
        ['title' => 'Gestion des Incertitudes', 'description' => 'Analyse et mitigation des risques'],
        // 'Documents Associés' si c'est une étape séparée ou incluse ailleurs.
    ];

    public $users; // Pour la liste des responsables

    public function mount($projectId) // $projectId est obligatoire ici
    {
        // dd($this->projectId);
        $this->users = User::all();

        $this->projectId = $projectId;
        $this->loadProjectData();
    }

    protected function loadProjectData()
    {
        $this->project = Project::with([
            'context', 'documents', 'environmentAnalyses', 'stakeholders',
            'problemAnalyses', 'strategies', 'goals.objectives.results.activities', 'risks'
        ])->findOrFail($this->projectId);

        // Remplir les propriétés du composant avec les données du projet
        $this->projectTitle = $this->project->title;
        $this->projectCode = $this->project->project_code;
        $this->projectShortTitle = $this->project->short_title;
        $this->projectDescriptionGeneral = $this->project->description;
        $this->projectStartDate = $this->project->start_date?->format('Y-m-d');
        $this->projectEndDate = $this->project->end_date?->format('Y-m-d');
        $this->projectStatus = $this->project->status;

        if ($this->project->context) {
            $this->baseProjectDescription = $this->project->context->base_description;
        }
        $this->existingDocuments = $this->project->documents->toArray();

        if ($this->project->environmentAnalysis) {
            $this->environmentAnalysisText = $this->project->environmentAnalysis->analysis_text;
        }

        $this->stakeholders = $this->project->stakeholders->isNotEmpty()
            ? $this->project->stakeholders->map(fn($s) => ['name' => $s->name, 'role' => $s->role])->toArray()
            : [['name' => '', 'role' => '']];

        if ($this->project->problemAnalysis) {
            $this->problemAnalysisText = $this->project->problemAnalysis->problem_description;
        }

        if ($this->project->strategy) {
            $this->strategyDefinitionText = $this->project->strategy->strategy_description;
        }

        if ($this->project->goal) {
            $this->generalGoal = $this->project->goal->description;
            if ($this->project->goal->objectives->isNotEmpty()) {
                $this->specificObjectives = $this->project->goal->objectives->map(function ($objective) {
                    return [
                        'description' => $objective->description,
                        'results' => $objective->results->map(function ($result) {
                            return [
                                'description' => $result->description,
                                'indicators' => $result->indicators,
                                'activities' => $result->activities->map(fn($a) => ['description' => $a->description, 'responsible' => $a->responsible])->toArray()
                            ];
                        })->toArray()
                    ];
                })->toArray();
            } else {
                $this->specificObjectives = [['description' => '', 'results' => [['description' => '', 'indicators' => '', 'activities' => [['description' => '', 'responsible' => '']]]]]];
            }
        } else {
             $this->generalGoal = '';
             $this->specificObjectives = [['description' => '', 'results' => [['description' => '', 'indicators' => '', 'activities' => [['description' => '', 'responsible' => '']]]]]];
        }

        $this->risks = $this->project->risks->isNotEmpty()
            ? $this->project->risks->map(fn($r) => ['description' => $r->description, 'impact' => $r->impact, 'probability' => $r->probability])->toArray()
            : [['description' => '', 'impact' => '', 'probability' => '']];
    }

    public function render()
    {
        return view('livewire.v-beta.project-design.edit-project-design-livewire', [
            'users' => $this->users,
        ]);
    }

    // Validation (restent les mêmes, mais peuvent avoir des règles différentes pour l'unicité par exemple)
    public function validateCurrentStep()
    {
        // ... (votre logique de validation actuelle) ...
        // Exemple (simplifié pour l'exemple, adaptez à votre vraie validation)
        if ($this->currentStep == 1) {
             $this->validate([
                'projectTitle' => 'required|string|max:255',
                // Pour l'édition, la règle unique doit ignorer l'ID actuel
                'projectCode' => 'required|string|max:50|unique:projects,project_code,' . $this->projectId,
                'projectStartDate' => 'required|date',
                'projectEndDate' => 'required|date|after_or_equal:projectStartDate',
            ]);
        }
        // ... ajoutez d'autres validations pour chaque étape ...
    }

    // Navigation (restent les mêmes)
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

    // CRUD for dynamic fields (remain the same)
    public function addStakeholder() { $this->stakeholders[] = ['name' => '', 'role' => '']; }
    public function removeStakeholder($index) { unset($this->stakeholders[$index]); $this->stakeholders = array_values($this->stakeholders); }
    public function addObjective() { $this->specificObjectives[] = ['description' => '', 'results' => [['description' => '', 'indicators' => '', 'activities' => [['description' => '', 'responsible' => '']]]]]; }
    public function removeObjective($index) { unset($this->specificObjectives[$index]); $this->specificObjectives = array_values($this->specificObjectives); }
    public function addResult($objectiveIndex) { $this->specificObjectives[$objectiveIndex]['results'][] = ['description' => '', 'indicators' => '', 'activities' => [['description' => '', 'responsible' => '']]]; }
    public function removeResult($objectiveIndex, $resultIndex) { unset($this->specificObjectives[$objectiveIndex]['results'][$resultIndex]); $this->specificObjectives[$objectiveIndex]['results'] = array_values($this->specificObjectives[$objectiveIndex]['results']); }
    public function addActivity($objectiveIndex, $resultIndex) { $this->specificObjectives[$objectiveIndex]['results'][$resultIndex]['activities'][] = ['description' => '', 'responsible' => '']; }
    public function removeActivity($objectiveIndex, $resultIndex, $activityIndex) { unset($this->specificObjectives[$objectiveIndex]['results'][$resultIndex]['activities'][$activityIndex]); $this->specificObjectives[$objectiveIndex]['results'][$resultIndex]['activities'] = array_values($this->specificObjectives[$objectiveIndex]['results'][$resultIndex]['activities']); }
    public function addRisk() { $this->risks[] = ['description' => '', 'impact' => '', 'probability' => '']; }
    public function removeRisk($index) { unset($this->risks[$index]); $this->risks = array_values($this->risks); }

    // Pour les documents, vous devrez ajouter une méthode pour supprimer des documents existants
    public function removeExistingDocument($documentId)
    {
        $document = ProjectDocument::find($documentId);
        if ($document) {
            // Supprimez le fichier du stockage
            \Illuminate\Support\Facades\Storage::delete($document->file_path);
            // Supprimez l'entrée de la base de données
            $document->delete();
            // Mettez à jour la liste des documents existants dans le Livewire
            $this->existingDocuments = collect($this->existingDocuments)->filter(fn($doc) => $doc['id'] !== $documentId)->toArray();
            session()->flash('success', 'Document supprimé.');
        } else {
             session()->flash('error', 'Document non trouvé.');
        }
    }

    // Submit for UPDATE operation only
    public function submit()
    {
        $this->validateCurrentStep(); // Valider la dernière étape avant soumission

        try {
            DB::beginTransaction();

            $project = Project::findOrFail($this->projectId);
            $project->update([
                'title' => $this->projectTitle,
                'project_code' => $this->projectCode,
                'short_title' => $this->projectShortTitle,
                'description' => $this->projectDescriptionGeneral,
                'start_date' => $this->projectStartDate,
                'end_date' => $this->projectEndDate,
                'status' => $this->projectStatus,
                'updated_by_user_id' => auth()->id(), // Enregistrer l'utilisateur qui met à jour
            ]);

            // Mettre à jour les données liées (similaire au mode création, mais en utilisant updateOrCreate ou delete/recreate)
            // CONTEXT
            $project->context()->updateOrCreate(
                ['project_id' => $project->id],
                ['base_description' => $this->baseProjectDescription]
            );

            // DOCUMENTS (gère les nouveaux uploads, la suppression des anciens est via removeExistingDocument)
            foreach ($this->uploadedDocuments as $file) {
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('public/project_documents', $fileName);
                ProjectDocument::create([
                    'id' => Str::uuid(),
                    'project_id' => $project->id,
                    'file_name' => $fileName,
                    'file_path' => $filePath,
                    'file_mime_type' => $file->getMimeType(),
                ]);
            }
            $this->uploadedDocuments = []; // Clear new uploads after saving

            // ENVIRONMENT ANALYSIS
            $project->environmentAnalyses()->updateOrCreate(
                ['project_id' => $project->id],
                ['analysis_text' => $this->environmentAnalysisText]
            );

            // STAKEHOLDERS (supprimez les anciens et recréez pour la simplicité)
            $project->stakeholders()->delete();
            foreach ($this->stakeholders as $stakeholderData) {
                if (!empty($stakeholderData['name'])) {
                    $project->stakeholders()->create([
                        'id' => Str::uuid(),
                        'name' => $stakeholderData['name'],
                        'role' => $stakeholderData['role'],
                    ]);
                }
            }

            // PROBLEM ANALYSIS
            $project->problemAnalysis()->updateOrCreate(
                ['project_id' => $project->id],
                ['problem_description' => $this->problemAnalysisText]
            );

            // STRATEGY
            $project->strategy()->updateOrCreate(
                ['project_id' => $project->id],
                ['strategy_description' => $this->strategyDefinitionText]
            );

            // GOAL, OBJECTIVES, RESULTS, ACTIVITIES (supprime et recrée toute la hiérarchie pour la simplicité)
            $project->goal()->delete();
            if ($this->generalGoal) {
                $goal = $project->goal()->create([
                    'id' => Str::uuid(),
                    'description' => $this->generalGoal,
                ]);

                foreach ($this->specificObjectives as $objectiveData) {
                    if (!empty($objectiveData['description'])) {
                        $objective = $goal->objectives()->create([
                            'id' => Str::uuid(),
                            'description' => $objectiveData['description'],
                        ]);

                        foreach ($objectiveData['results'] as $resultData) {
                            if (!empty($resultData['description'])) {
                                $result = $objective->results()->create([
                                    'id' => Str::uuid(),
                                    'description' => $resultData['description'],
                                    'indicators' => $resultData['indicators'],
                                ]);

                                foreach ($resultData['activities'] as $activityData) {
                                    if (!empty($activityData['description'])) {
                                        Activity::create([
                                            'id' => Str::uuid(),
                                            'result_id' => $result->id,
                                            'description' => $activityData['description'],
                                            'responsible' => $activityData['responsible'],
                                        ]);
                                    }
                                }
                            }
                        }
                    }
                }
            }

            // RISKS (supprime et recrée)
            $project->risks()->delete();
            foreach ($this->risks as $riskData) {
                if (!empty($riskData['description'])) {
                    $project->risks()->create([
                        'id' => Str::uuid(),
                        'description' => $riskData['description'],
                        'impact' => $riskData['impact'],
                        'probability' => $riskData['probability'],
                    ]);
                }
            }

            DB::commit();
            session()->flash('success', 'Projet mis à jour avec succès !');
            // Rediriger vers la page de visualisation du projet
            return redirect()->route('projects.show', $project->id);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erreur lors de la mise à jour du projet: " . $e->getMessage() . " sur la ligne " . $e->getLine() . " dans " . $e->getFile());
            session()->flash('error', 'Une erreur est survenue lors de la mise à jour du projet: ' . $e->getMessage());
        }
    }
}