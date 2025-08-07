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
use App\Models\LogicalFramework;
use App\Models\SpecificObjective;
use App\Models\Result;
use App\Models\Activity;
use App\Models\User;

class EditProjectDesignLivewire extends Component
{
    use WithFileUploads;

    public $projectId;
    public $project;

    public $currentStep = 1;
    public $totalSteps = 4; // Mise à jour du nombre total d'étapes
    public $stepDetails = [
        ['title' => 'Informations Clés', 'description' => 'Détails de base du projet'],
        ['title' => 'Contexte', 'description' => 'Description simplifiée pour l\'IA'],
        ['title' => 'Documents', 'description' => 'Gérer les documents associés'],
        ['title' => 'Cadre Logique', 'description' => 'Objectifs, résultats et activités'], // Étape mise à jour
    ];

    // Project administrative data and new columns from the model
    public $projectTitle;
    public $projectCode;
    public $projectShortTitle;
    public $projectDescriptionGeneral;
    public $projectStartDate;
    public $projectEndDate;
    public $projectStatus;
    public $problemAnalysis;
    public $strategy;
    public $justification;

    // Wizard step data
    public $baseProjectDescription;
    public $uploadedDocuments = [];
    public $existingDocuments = [];

    // Logical Framework
    public $generalGoal;
    public $specificObjectives = [['description' => '', 'results' => [['description' => '', 'indicators' => '', 'activities' => [['description' => '', 'responsible' => '']]]]]];

    public $users;

    protected function getValidationRules()
    {
        $rules = [];
        if ($this->currentStep == 1) {
            $rules = [
                'projectTitle' => 'required|string|max:255',
                'projectCode' => 'required|string|max:50|unique:projects,project_code,' . $this->projectId,
                'projectStartDate' => 'required|date',
                'projectEndDate' => 'required|date|after_or_equal:projectStartDate',
                'problemAnalysis' => 'nullable|string',
                'strategy' => 'nullable|string',
                'justification' => 'nullable|string',
            ];
        } elseif ($this->currentStep == 2) {
            $rules = [
                'baseProjectDescription' => 'required|string',
            ];
        } elseif ($this->currentStep == 3) {
            $rules = [
                'uploadedDocuments.*' => 'file|max:10240', // 10MB
            ];
        } elseif ($this->currentStep == 4) {
            $rules = [
                'generalGoal' => 'required|string|max:255',
                'specificObjectives.*.description' => 'required|string|max:500',
                'specificObjectives.*.results.*.description' => 'required|string|max:500',
                'specificObjectives.*.results.*.activities.*.description' => 'required|string|max:500',
            ];
        }
        return $rules;
    }

    public function mount($projectId)
    {
        $this->users = User::orderBy('name')->get();
        $this->projectId = $projectId;
        $this->loadProjectData();
    }

    protected function loadProjectData()
    {
        // Fetch relations correctly based on the user's model
        $this->project = Project::with([
            'projectContext',
            'projectDocuments',
            'logicalFramework.specificObjectives.results.activities' // Charger les activités via les résultats
        ])->findOrFail($this->projectId);

        $this->projectTitle = $this->project->title;
        $this->projectCode = $this->project->project_code;
        $this->projectShortTitle = $this->project->short_title;
        $this->projectDescriptionGeneral = $this->project->description;
        $this->projectStartDate = $this->project->start_date?->format('Y-m-d');
        $this->projectEndDate = $this->project->end_date?->format('Y-m-d');
        $this->projectStatus = $this->project->status;

        // Load the new columns
        $this->problemAnalysis = $this->project->problem_analysis;
        $this->strategy = $this->project->strategy;
        $this->justification = $this->project->justification;

        if ($this->project->projectContext) {
            $this->baseProjectDescription = $this->project->projectContext->base_description;
        }

        $this->existingDocuments = $this->project->projectDocuments->toArray();

        if ($this->project->logicalFramework) {
            $this->generalGoal = $this->project->logicalFramework->general_goal;
            if ($this->project->logicalFramework->specificObjectives->isNotEmpty()) {
                $this->specificObjectives = $this->project->logicalFramework->specificObjectives->map(function ($objective) {
                    return [
                        'description' => $objective->description,
                        'results' => $objective->results->map(function($result) {
                            return [
                                'description' => $result->description,
                                'indicators' => $result->indicators,
                                'activities' => $result->activities->map(fn($a) => ['description' => $a->description, 'responsible' => $a->responsible_user_id])->toArray()
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
    }

    public function validateCurrentStep()
    {
        $this->validate($this->getValidationRules());
    }

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

    // Logical Framework methods
    public function addObjective() { $this->specificObjectives[] = ['description' => '', 'results' => [['description' => '', 'indicators' => '', 'activities' => [['description' => '', 'responsible' => '']]]]]; }
    public function removeObjective($index) { unset($this->specificObjectives[$index]); $this->specificObjectives = array_values($this->specificObjectives); }
    public function addResult($objectiveIndex) { $this->specificObjectives[$objectiveIndex]['results'][] = ['description' => '', 'indicators' => '', 'activities' => [['description' => '', 'responsible' => '']]]; }
    public function removeResult($objectiveIndex, $resultIndex) { unset($this->specificObjectives[$objectiveIndex]['results'][$resultIndex]); $this->specificObjectives[$objectiveIndex]['results'] = array_values($this->specificObjectives[$objectiveIndex]['results']); }
    
    // Nested Activity methods
    public function addActivity($objectiveIndex, $resultIndex) { $this->specificObjectives[$objectiveIndex]['results'][$resultIndex]['activities'][] = ['description' => '', 'responsible' => '']; }
    public function removeActivity($objectiveIndex, $resultIndex, $activityIndex) { unset($this->specificObjectives[$objectiveIndex]['results'][$resultIndex]['activities'][$activityIndex]); $this->specificObjectives[$objectiveIndex]['results'][$resultIndex]['activities'] = array_values($this->specificObjectives[$objectiveIndex]['results'][$resultIndex]['activities']); }

    public function removeExistingDocument($documentId)
    {
        $document = ProjectDocument::find($documentId);
        if ($document) {
            \Illuminate\Support\Facades\Storage::delete($document->file_path);
            $document->delete();
            $this->existingDocuments = collect($this->existingDocuments)->filter(fn($doc) => $doc['id'] !== $documentId)->toArray();
            session()->flash('success', 'Document supprimé.');
        } else {
             session()->flash('error', 'Document non trouvé.');
        }
    }

    public function submit()
    {
        $this->validateCurrentStep();

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
                'problem_analysis' => $this->problemAnalysis,
                'strategy' => $this->strategy,
                'justification' => $this->justification,
            ]);

            // PROJECT CONTEXT (hasOne)
            $project->projectContext()->updateOrCreate(
                ['project_id' => $project->id],
                ['base_description' => $this->baseProjectDescription]
            );

            // DOCUMENTS (hasMany)
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
            $this->uploadedDocuments = [];

            // LOGICAL FRAMEWORK (hasOne nested hasMany and activities)
            if ($project->logicalFramework) {
                $project->logicalFramework->delete();
            }
            if ($this->generalGoal) {
                $logicalFramework = $project->logicalFramework()->create([
                    'id' => Str::uuid(),
                    'general_goal' => $this->generalGoal,
                ]);
                foreach ($this->specificObjectives as $objectiveData) {
                    if (!empty($objectiveData['description'])) {
                        $objective = $logicalFramework->specificObjectives()->create([
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
                                
                                // ACTIVITIES (hasMany on Result model)
                                foreach ($resultData['activities'] as $activityData) {
                                    if (!empty($activityData['description'])) {
                                        $result->activities()->create([
                                            'id' => Str::uuid(),
                                            'description' => $activityData['description'],
                                            'responsible_user_id' => $activityData['responsible'],
                                        ]);
                                    }
                                }
                            }
                        }
                    }
                }
            }
            
            DB::commit();
            session()->flash('success', 'Projet mis à jour avec succès !');
            return redirect()->route('project.show', $project->id);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erreur lors de la mise à jour du projet: " . $e->getMessage() . " sur la ligne " . $e->getLine() . " dans " . $e->getFile());
            session()->flash('error', 'Une erreur est survenue lors de la mise à jour du projet: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.v-beta.project-design.edit-project-design-livewire', [
            'users' => $this->users,
        ]);
    }
}