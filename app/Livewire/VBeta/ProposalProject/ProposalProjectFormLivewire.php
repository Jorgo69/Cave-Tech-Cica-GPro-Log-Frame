<?php

namespace App\Livewire\VBeta\ProposalProject;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Project;
use App\Models\ProjectContext;
use App\Models\ProjectDocument;
use App\Models\LogicalFramework;
use App\Models\SpecificObjective;
use App\Models\Result;
use App\Models\Activity;
use App\Models\Budget;
use App\Models\User;
use App\Models\ProjectType;
use App\Models\DynamicProjectField;
use Illuminate\Validation\Rule;
use Carbon\Carbon; // Import de Carbon pour la manipulation des dates

class ProposalProjectFormLivewire extends Component
{
    use WithFileUploads;

    // Propriétés du wizard
    public $currentStep = 1;
    public $totalSteps = 7;
    public $stepDetails = [];

    // Données du projet principal (table 'projects')
    public $projectId; // Null en création, UUID en édition
    public $projectCode;
    public $projectTitle;
    public $projectShortTitle = ''; // Initialisé à une chaîne vide
    public $projectStartDate;
    public $projectEndDate;
    public $projectStatus = 'draft';

    // Données pour la sélection du type de projet et les champs dynamiques
    public $allProjectTypes = [];
    public $selectedProjectTypeId;
    public $dynamicFormFields = []; // Définitions des champs dynamiques groupés par section
    public $dynamicFieldValues = []; // Valeurs saisies par l'utilisateur pour les champs dynamiques

    // Données des étapes spécifiques
    public $contextDescription;
    public $uploadedDocuments = []; // Pour ProjectDocument
    public $initialLogicalFramework = [ // Pour LogicalFramework
        'general_objective' => '',
        'general_obj_indicators' => '',
        'general_obj_verification_sources' => '',
        'assumptions' => '',
    ];
    public $specificObjectives = []; // Tableau d'objectifs spécifiques
    public $expectedResults = []; // Tableau de résultats
    public $activities = []; // Tableau d'activités
    public $budgets = []; // Tableau de lignes budgétaires

    

    // Liste des utilisateurs pour les responsables (dropdowns)
    public $users = [];

    // Message de statut (succès/erreur)
    public $statusMessage = '';
    public $statusType = 'hidden';

    protected $listeners = ['stepChanged'];

    /**
     * Définit les règles de validation pour le formulaire.
     *
     * @return array
     */
    protected function rules()
    {
        $rules = [
            'projectCode' => ['required', 'string', 'max:255', Rule::unique('projects', 'project_code')->ignore($this->projectId)],
            'projectTitle' => 'required|string|max:255',
            'projectStartDate' => 'required|date',
            'projectEndDate' => 'required|date|after_or_equal:projectStartDate',
            'selectedProjectTypeId' => 'required|uuid|exists:project_types,id',
            'contextDescription' => 'nullable|string',
            'uploadedDocuments.*' => 'nullable|file|max:50000', // 50MB max par fichier

            'initialLogicalFramework.general_objective' => 'required|string',
            'initialLogicalFramework.general_obj_indicators' => 'nullable|string',
            'initialLogicalFramework.general_obj_verification_sources' => 'nullable|string',
            'initialLogicalFramework.assumptions' => 'nullable|string',
            'specificObjectives.*.description' => 'required|string',
            'specificObjectives.*.indicators' => 'nullable|string',
            'specificObjectives.*.verification_sources' => 'nullable|string',
            'specificObjectives.*.assumptions' => 'nullable|string',
            'expectedResults.*.description' => 'required|string',
            'activities.*.description' => 'required|string',
            'activities.*.responsible_user_id' => 'required|uuid|exists:users,id',
            'activities.*.start_date' => 'required|date',
            'activities.*.end_date' => 'required|date|after_or_equal:activities.*.start_date',
            'activities.*.status' => 'required|string|in:En cours,Terminée,En attente,En retard',
            'budgets.*.description' => 'required|string',
            'budgets.*.quantity' => 'nullable|integer|min:0',
            'budgets.*.unit_cost' => 'nullable|numeric|min:0',
            'budgets.*.total_cost' => 'nullable|numeric|min:0',
            'budgets.*.category' => 'nullable|string',
            'budgets.*.responsible_user_id' => 'nullable|uuid|exists:users,id',
        ];

       // Règles dynamiques BASÉES sur l'étape courante
    $currentSection = $this->getSectionFromStep($this->currentStep);
    if ($currentSection && isset($this->dynamicFormFields[$currentSection])) {
        foreach ($this->dynamicFormFields[$currentSection] as $field) {
            $key = 'dynamicFieldValues.'.$field['field_name'];
            $rules[$key] = $field['is_required'] ? 'required|string' : 'nullable|string';
            
            // Ajouter des règles spécifiques par type de champ
            if ($field['input_type'] === 'number') {
                $rules[$key] .= '|numeric';
            } elseif ($field['input_type'] === 'date') {
                $rules[$key] .= '|date';
            }
        }
    }

        return $rules;
    }

    /**
     * Définit les attributs de validation personnalisés pour des messages d'erreur plus clairs.
     *
     * @return array
     */
    protected function validationAttributes()
    {
        $attributes = [
            'projectCode' => 'code du projet',
            'projectTitle' => 'titre du projet',
            'projectStartDate' => 'date de début',
            'projectEndDate' => 'date de fin',
            'selectedProjectTypeId' => 'type de projet',
            'contextDescription' => 'description du contexte',
            'initialLogicalFramework.general_objective' => 'objectif général',
            'initialLogicalFramework.general_obj_indicators' => 'indicateurs de l\'objectif général',
            'initialLogicalFramework.general_obj_verification_sources' => 'sources de vérification de l\'objectif général',
            'initialLogicalFramework.assumptions' => 'hypothèses de l\'objectif général',
            'specificObjectives.*.description' => 'description de l\'objectif spécifique',
            'specificObjectives.*.indicators' => 'indicateurs de l\'objectif spécifique',
            'specificObjectives.*.verification_sources' => 'sources de vérification de l\'objectif spécifique',
            'specificObjectives.*.assumptions' => 'hypothèses de l\'objectif spécifique',
            'expectedResults.*.description' => 'description du résultat attendu',
            'activities.*.description' => 'description de l\'activité',
            'activities.*.responsible_user_id' => 'responsable de l\'activité',
            'activities.*.start_date' => 'date de début de l\'activité',
            'activities.*.end_date' => 'date de fin de l\'activité',
            'activities.*.status' => 'statut de l\'activité',
            'budgets.*.description' => 'description de la ligne budgétaire',
            'budgets.*.quantity' => 'quantité',
            'budgets.*.unit_cost' => 'coût unitaire',
            'budgets.*.total_cost' => 'coût total',
            'budgets.*.category' => 'catégorie budgétaire',
            'budgets.*.responsible_user_id' => 'responsable budgétaire',
            'uploadedDocuments.*' => 'document', // Attribut générique pour les fichiers uploadés
        ];

        // Ajouter les attributs pour les champs dynamiques en utilisant leur question_text
        foreach ($this->dynamicFormFields as $section => $fields) {
            foreach ($fields as $field) {
                $attributes['dynamicFieldValues.' . $field['field_name']] = strtolower($field['question_text']);
            }
        }

        return $attributes;
    }

    /**
     * Méthode d'initialisation du composant.
     *
     * @param string|null $projectId L'ID du projet si en mode édition.
     * @return void
     */
    public function mount($projectId = null)
    {
        $this->users = User::all();
        $this->allProjectTypes = ProjectType::all();

        $this->stepDetails = [
            ['title' => 'Informations Clés', 'description' => 'Détails de base du projet'],
            ['title' => 'Contexte & Documents', 'description' => 'Description et fichiers pertinents'],
            ['title' => 'Cadre Logique', 'description' => 'But et objectifs spécifiques'],
            ['title' => 'Résultats Attendus', 'description' => 'Livrables concrets du projet'],
            ['title' => 'Activités Initiales', 'description' => 'Actions préliminaires du projet'],
            ['title' => 'Budget Prévisionnel', 'description' => 'Estimation des coûts initiaux'],
            ['title' => 'Finalisation', 'description' => 'Vérification et soumission'],
        ];

        if ($projectId) {
            $this->projectId = $projectId;
            Log::debug("Chargement du projet existant avec ID: {$this->projectId}");
            $project = Project::with([
                'projectContext',
                'projectDocuments',
                'logicalFramework.specificObjectives.results',
            'logicalFramework.specificObjectives.results.activities', // Charger les activités via les résultats
                'budgets',
                'projectType'
            ])->find($projectId); // Utiliser find() au lieu de findOrFail() pour éviter l'exception

            if (!$project) {
                // Si le projet n'est pas trouvé, réinitialiser et agir comme en mode création
                Log::warning("Projet avec l'ID: {$this->projectId} non trouvé lors du montage. Initialisation pour la création.");
                $this->projectId = null;
                $this->addSpecificObjective();
                $this->addExpectedResult();
                $this->addActivity();
                $this->addBudget();
                return;
            }

            $this->projectCode = $project->project_code;
            $this->projectTitle = $project->title;
            $this->projectShortTitle = $project->short_title;
            $this->projectStartDate = $project->start_date instanceof Carbon ? $project->start_date->format('Y-m-d') : $project->start_date;
            $this->projectEndDate = $project->end_date instanceof Carbon ? $project->end_date->format('Y-m-d') : $project->end_date;
            $this->projectStatus = $project->status;
            $this->selectedProjectTypeId = $project->project_type_id;

            // Charger les champs dynamiques et les valeurs existantes
            $this->loadDynamicFields();
            Log::debug("Champs dynamiques chargés pour le type de projet: {$this->selectedProjectTypeId}");

            // Remplir les valeurs des champs dynamiques à partir des champs du projet
            foreach ($this->dynamicFormFields as $section => $fields) {
                foreach ($fields as $fieldDef) {
                    $targetField = $fieldDef['target_project_field'];
                    $fieldName = $fieldDef['field_name'];
                    if (isset($project->$targetField)) {
                        $pattern = '/' . preg_quote($fieldDef['delimiter_start'], '/') . '(.*?)' . preg_quote($fieldDef['delimiter_end'], '/') . '/s';
                        if (preg_match($pattern, $project->$targetField, $matches)) {
                            $this->dynamicFieldValues[$fieldName] = $matches[1];
                        }
                    }
                }
            }
            
            // Charger les données des relations
            $this->contextDescription = $project->projectContext->context_description ?? '';
            
            if ($project->logicalFramework) {
                $this->initialLogicalFramework = $project->logicalFramework->toArray();
                $this->specificObjectives = $project->logicalFramework->specificObjectives->toArray();
                
                // Remplir expectedResults en se basant sur les résultats liés aux objectifs spécifiques
                $this->expectedResults = [];
                foreach ($project->logicalFramework->specificObjectives as $obj) {
                    foreach ($obj->results as $res) {
                        $this->expectedResults[] = $res->toArray();
                    }
                }
            } else {
                // Si pas de cadre logique, initialiser pour éviter les erreurs
                $this->addSpecificObjective();
                $this->addExpectedResult();
            }

            // Assurez-vous que les activités et budgets sont des tableaux, même s'ils sont vides
            // Charger les activités en itérant sur les résultats
            $this->activities = [];
            foreach ($project->logicalFramework->specificObjectives as $obj) {
                foreach ($obj->results as $res) {
                    foreach ($res->activities as $act) {
                        $this->activities[] = $act->toArray();
                    }
                }
            }
            $this->budgets = $project->budgets->toArray();

        } else {
            // Initialisation pour la création
            Log::debug("Création d'un nouveau projet, initialisation des propriétés.");
            $this->projectStartDate = null; // Initialisation explicite à null
            $this->projectEndDate = null;   // Initialisation explicite à null
            $this->addSpecificObjective();
            $this->addExpectedResult();
            $this->addActivity();
            $this->addBudget();
        }

        \Log::debug("Dynamic Fields Preview", [
            'sections' => array_keys($this->dynamicFormFields),
            'first_field' => $this->dynamicFormFields[array_key_first($this->dynamicFormFields)][0] ?? null,
            'values' => $this->dynamicFieldValues
        ]);

        // Charger les champs dynamiques SI un type est déjà sélectionné
        if ($this->selectedProjectTypeId) {
            $this->loadDynamicFields();
        }
    }

    /**
     * Méthode appelée quand le type de projet est sélectionné ou mis à jour.
     *
     * @param string $value L'ID du type de projet sélectionné.
     * @return void
     */
    public function updatedSelectedProjectTypeId($value)
    {
        $this->loadDynamicFields();
        // Réinitialiser les valeurs des champs dynamiques si le type de projet change
        $this->dynamicFieldValues = [];
    }

    /**
     * Charge les définitions des champs dynamiques pour le type de projet sélectionné,
     * en les groupant par section.
     *
     * @return void
     */
    public function loadDynamicFields()
    {
        if (!$this->selectedProjectTypeId) {
            $this->dynamicFormFields = [];
            return;
        }

        // Debug crucial
        logger()->debug("Chargement des champs pour type", [
            'type_id' => $this->selectedProjectTypeId,
            'exists' => ProjectType::where('id', $this->selectedProjectTypeId)->exists()
        ]);

        $fields = DynamicProjectField::where('project_type_id', $this->selectedProjectTypeId)
            ->orderBy('order')
            ->get()
            ->groupBy('section')
            ->toArray();

        // Debug des champs trouvés
        logger()->debug("Champs dynamiques chargés", [
            'sections' => array_keys($fields),
            'total_fields' => array_sum(array_map('count', $fields))
        ]);

        $this->dynamicFormFields = $fields;

        $this->dynamicFormFields = collect($this->dynamicFormFields)
        ->filter(fn($fields, $section) => !empty($section) && count($fields) > 0)
        ->toArray();
    }

    /**
     * Passe à l'étape suivante du formulaire.
     *
     * @return void
     */
    public function nextStep()
    {
        try {
            $this->validateCurrentStep();
            if ($this->currentStep < $this->totalSteps) {
                $this->currentStep++;
            }
            $this->dispatch('stepChanged'); // Émettre un événement pour Alpine.js
            Log::debug("Passage à l'étape suivante: {$this->currentStep}");
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Erreur de validation à l\'étape ' . $this->currentStep . ': ' . $e->getMessage(), ['errors' => $e->errors()]);
            // Livewire gère automatiquement l'affichage des erreurs de validation dans la vue
        } catch (\Exception $e) {
            Log::error('Erreur inattendue lors du passage à l\'étape suivante: ' . $e->getMessage());
            session()->flash('error', 'Une erreur inattendue est survenue: ' . $e->getMessage());
        }
    }

    /**
     * Revient à l'étape précédente du formulaire.
     *
     * @return void
     */
    public function previousStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
        $this->dispatch('stepChanged');
        Log::debug("Retour à l'étape précédente: {$this->currentStep}");
    }

    /**
     * Permet de naviguer directement à une étape spécifique.
     *
     * @param int $step Le numéro de l'étape cible.
     * @return void
     */
    public function goToStep($step)
    {
        // Optionnel: Valider les étapes précédentes avant de sauter
        // Pour l'instant, on permet de sauter si l'étape est déjà passée.
        if ($step >= 1 && $step <= $this->totalSteps) {
            $this->currentStep = $step;
            $this->dispatch('stepChanged');
            Log::debug("Navigation directe à l'étape: {$this->currentStep}");
        }
    }

    /**
     * Valide uniquement les champs de l'étape en cours.
     *
     * @return void
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validateCurrentStep()
    {
        // Validation des champs de base pour chaque étape
        switch ($this->currentStep) {
            case 1:
                $this->validateOnly('selectedProjectTypeId');
                $this->validateOnly('projectCode');
                $this->validateOnly('projectTitle');
                $this->validateOnly('projectStartDate');
                $this->validateOnly('projectEndDate');
                break;
            case 2:
                $this->validateOnly('contextDescription');
                if (!empty($this->uploadedDocuments)) {
                    $this->validateOnly('uploadedDocuments.*');
                }
                break;
            case 3:
                $this->validateOnly('initialLogicalFramework.general_objective');
                $this->validateOnly('initialLogicalFramework.general_obj_indicators');
                $this->validateOnly('initialLogicalFramework.general_obj_verification_sources');
                $this->validateOnly('initialLogicalFramework.assumptions');
                
                foreach ($this->specificObjectives as $index => $objective) {
                    $this->validateOnly('specificObjectives.' . $index . '.description');
                    $this->validateOnly('specificObjectives.' . $index . '.indicators');
                    $this->validateOnly('specificObjectives.' . $index . '.verification_sources');
                    $this->validateOnly('specificObjectives.' . $index . '.assumptions');
                }
                break;
            case 4:
                foreach ($this->expectedResults as $index => $result) {
                    $this->validateOnly('expectedResults.' . $index . '.description');
                }
                break;
            case 5:
                foreach ($this->activities as $index => $activity) {
                    $this->validateOnly('activities.' . $index . '.description');
                    $this->validateOnly('activities.' . $index . '.responsible_user_id');
                    $this->validateOnly('activities.' . $index . '.start_date');
                    $this->validateOnly('activities.' . $index . '.end_date');
                    $this->validateOnly('activities.' . $index . '.status');
                }
                break;
            case 6:
                foreach ($this->budgets as $index => $budget) {
                    $this->validateOnly('budgets.' . $index . '.description');
                    $this->validateOnly('budgets.' . $index . '.quantity');
                    $this->validateOnly('budgets.' . $index . '.unit_cost');
                    $this->validateOnly('budgets.' . $index . '.total_cost');
                    $this->validateOnly('budgets.' . $index . '.category');
                    $this->validateOnly('budgets.' . $index . '.responsible_user_id');
                }
                break;
            // Pas de validation pour l'étape 7 (finalisation)
        }

        // Validation des champs dynamiques POUR L'ÉTAPE COURANTE SEULEMENT
        $sectionName = $this->getSectionFromStep($this->currentStep);
        if (isset($this->dynamicFormFields[$sectionName])) {
            $dynamicRules = [];
            
            foreach ($this->dynamicFormFields[$sectionName] as $field) {
                $key = 'dynamicFieldValues.' . $field['field_name'];
                $dynamicRules[$key] = $field['is_required'] ? 'required|string' : 'nullable|string';
                
                // Ajouter des règles spécifiques si nécessaire
                if ($field['input_type'] === 'number') {
                    $dynamicRules[$key] .= '|numeric';
                } elseif ($field['input_type'] === 'date') {
                    $dynamicRules[$key] .= '|date';
                }
            }
            
            $this->validate($dynamicRules);
        }
    }
    
    /**
     * Retourne le nom de la section associée à un numéro d'étape.
     *
     * @param int $step Le numéro de l'étape.
     * @return string|null
     */
    private function getSectionFromStep(int $step): ?string
    {
        $stepSectionMap = [
            1 => 'informations_cles',
            2 => 'contexte_documents',
            3 => 'cadre_logique',
            4 => 'resultats_attendus',
            5 => 'activites_initiales',
            6 => 'budget_previsionnel',
            7 => 'finalisation', // Bien que non utilisé pour les champs dynamiques, utile pour la cohérence
        ];
        return $stepSectionMap[$step] ?? null;
    }

    /**
     * Ajoute un nouvel objectif spécifique au tableau.
     *
     * @return void
     */
    public function addSpecificObjective()
    {
        $this->specificObjectives[] = [
            'id' => Str::uuid()->toString(),
            'description' => '',
            'indicators' => '',
            'verification_sources' => '',
            'assumptions' => '',
        ];
    }

    /**
     * Supprime un objectif spécifique du tableau.
     *
     * @param int $index L'index de l'objectif à supprimer.
     * @return void
     */
    public function removeSpecificObjective($index)
    {
        unset($this->specificObjectives[$index]);
        $this->specificObjectives = array_values($this->specificObjectives);
    }

    /**
     * Ajoute un nouveau résultat attendu au tableau.
     *
     * @return void
     */
    public function addExpectedResult()
    {
        $this->expectedResults[] = [
            'id' => Str::uuid()->toString(),
            'description' => '',
        ];
    }

    /**
     * Supprime un résultat attendu du tableau.
     *
     * @param int $index L'index du résultat à supprimer.
     * @return void
     */
    public function removeExpectedResult($index)
    {
        unset($this->expectedResults[$index]);
        $this->expectedResults = array_values($this->expectedResults);
    }

    /**
     * Ajoute une nouvelle activité au tableau.
     *
     * @return void
     */
    public function addActivity()
    {
        $this->activities[] = [
            'id' => Str::uuid()->toString(),
            'description' => '',
            'responsible_user_id' => '',
            'start_date' => '',
            'end_date' => '',
            'status' => 'En cours',
            'justification' => '',
            'is_milestone' => false,
            'progress_percentage' => 0,
        ];
    }

    /**
     * Supprime une activité du tableau.
     *
     * @param int $index L'index de l'activité à supprimer.
     * @return void
     */
    public function removeActivity($index)
    {
        unset($this->activities[$index]);
        $this->activities = array_values($this->activities);
    }

    /**
     * Ajoute une nouvelle ligne budgétaire au tableau.
     *
     * @return void
     */
    public function addBudget()
    {
        $this->budgets[] = [
            'id' => Str::uuid()->toString(),
            'description' => '',
            'quantity' => null,
            'unit_cost' => null,
            'total_cost' => null,
            'category' => '',
            'responsible_user_id' => null,
        ];
    }

    /**
     * Supprime une ligne budgétaire du tableau.
     *
     * @param int $index L'index de la ligne budgétaire à supprimer.
     * @return void
     */
    public function removeBudget($index)
    {
        unset($this->budgets[$index]);
        $this->budgets = array_values($this->budgets);
    }

    /**
     * Soumet le formulaire complet et sauvegarde les données du projet.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function submitForm()
    {
        // Valide toutes les règles définies dans la méthode rules()
        $this->validate();

        Log::debug("Validation du formulaire réussie. Début de la transaction de sauvegarde.");
        Log::debug("projectStartDate (avant traitement): " . (is_string($this->projectStartDate) ? "'" . $this->projectStartDate . "'" : (is_null($this->projectStartDate) ? "NULL" : "Other Type")));
        Log::debug("projectEndDate (avant traitement): " . (is_string($this->projectEndDate) ? "'" . $this->projectEndDate . "'" : (is_null($this->projectEndDate) ? "NULL" : "Other Type")));


        try {
            DB::beginTransaction();

            // Standardiser les valeurs des dates: si elles sont vides, les rendre null
            // Ceci est une mesure de sécurité si la validation 'required|date' ne convertit pas les chaînes vides en null
            $startDate = empty($this->projectStartDate) ? null : $this->projectStartDate;
            $endDate = empty($this->projectEndDate) ? null : $this->projectEndDate;

            // Préparer les données du projet principal
            $projectData = [
                'project_code' => $this->projectCode,
                'title' => $this->projectTitle,
                'short_title' => $this->projectShortTitle,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'status' => $this->projectStatus,
                'creator_user_id' => Auth::id(), // Utilisateur connecté
                'project_type_id' => $this->selectedProjectTypeId,
                'created_by_user_id' => Auth::id(),
                'updated_by_user_id' => Auth::id(),
            ];

            // Intégrer les valeurs des champs dynamiques dans les champs cibles du projet
            $dynamicDataForProjectFields = [];
            foreach ($this->dynamicFormFields as $section => $fields) {
                foreach ($fields as $fieldDef) {
                    $fieldName = $fieldDef['field_name'];
                    $value = $this->dynamicFieldValues[$fieldName] ?? '';
                    $targetProjectField = $fieldDef['target_project_field'];

                    // On s'assure que le champ cible n'est pas un champ de date par défaut du projet
                    // Et on ne met à jour la valeur que si elle n'est pas vide
                    // Ajout d'une vérification pour les champs de type 'date' pour s'assurer qu'ils sont valides
                    if (!in_array($targetProjectField, ['created_at', 'updated_at'])) {
                        if ($fieldDef['input_type'] === 'date') {
                            // Si c'est un champ de date dynamique, valider le format avant de l'ajouter
                            if (!empty($value) && Carbon::parse($value)->isValid()) {
                                $dynamicDataForProjectFields[$targetProjectField] = $value;
                            } else {
                                Log::warning("Champ dynamique de type date invalide ou vide ignoré: {$fieldName} avec valeur '{$value}'");
                            }
                        } elseif ($value !== '') {
                            $delimitedValue = $fieldDef['delimiter_start'] . $value . $fieldDef['delimiter_end'];
                            $dynamicDataForProjectFields[$targetProjectField] = ($dynamicDataForProjectFields[$targetProjectField] ?? '') . $delimitedValue;
                        }
                    }
                }
            }
            $projectData = array_merge($projectData, $dynamicDataForProjectFields);
            
            Log::debug("Final projectData avant l'opération DB:", $projectData);

            // Créer ou Mettre à jour le Projet
            if ($this->projectId) {
                $project = Project::find($this->projectId);

                if ($project) {
                    // Le projet a été trouvé, on le met à jour
                    $project->update($projectData);
                    Log::debug("Projet existant mis à jour avec l'ID: {$project->id}");

                    // Gérer la suppression des anciennes dépendances avant de les recréer
                    // Suppression des budgets
                    $project->budgets()->delete();
                    Log::debug("Anciens budgets du projet {$project->id} supprimés.");

                    // Suppression du cadre logique et de toutes ses dépendances (objectifs, résultats, activités)
                    // Assurez-vous que les relations sont configurées avec onDelete('cascade') dans vos migrations
                    // Sinon, vous devrez supprimer manuellement dans l'ordre inverse des dépendances
                    if ($project->logicalFramework) {
                        $project->logicalFramework->specificObjectives->each(function ($objective) {
                            $objective->results->each(function ($result) {
                                $result->activities()->delete(); // Supprime les activités liées à ce résultat
                            });
                            $objective->results()->delete(); // Supprime les résultats liés à cet objectif
                        });
                        $project->logicalFramework->specificObjectives()->delete(); // Supprime les objectifs liés au cadre logique
                        $project->logicalFramework()->delete(); // Supprime le cadre logique
                        Log::debug("Ancien LogicalFramework et ses dépendances du projet {$project->id} supprimés.");
                    }
                    
                    // Suppression des documents existants si vous voulez les remplacer, sinon, ne pas supprimer ici
                    // $project->projectDocuments()->delete(); 
                    // Log::debug("Anciens documents du projet {$project->id} supprimés.");

                } else {
                    // Le projet n'a pas été trouvé, on en crée un nouveau
                    Log::warning("Projet avec l'ID: {$this->projectId} non trouvé. Création d'un nouveau projet.");
                    $project = Project::create(array_merge($projectData, ['id' => (string) Str::uuid()]));
                    $this->projectId = $project->id;
                    Log::debug("Nouveau projet créé avec l'ID: {$project->id}");
                }
            } else {
                // Pas d'ID de projet, on en crée un nouveau
                $project = Project::create(array_merge($projectData, ['id' => (string) Str::uuid()]));
                $this->projectId = $project->id;
                Log::debug("Nouveau projet créé avec l'ID: {$project->id}");
            }

            // Créer/Mettre à jour ProjectContext
            ProjectContext::updateOrCreate(
                ['project_id' => $project->id],
                ['id' => (string) Str::uuid(), 'context_description' => $this->contextDescription]
            );
            Log::debug("ProjectContext créé/mis à jour pour le projet: {$project->id}");

            // Gérer les ProjectDocuments (toujours créer de nouveaux documents pour les uploads)
            foreach ($this->uploadedDocuments as $document) {
                $path = $document->store('documents/' . $project->id, 'public');
                ProjectDocument::create([
                    'id' => (string) Str::uuid(),
                    'project_id' => $project->id,
                    'uploaded_by_user_id' => Auth::id(),
                    'file_path' => $path,
                    'file_name' => $document->getClientOriginalName(),
                    'file_type' => $document->getMimeType(),
                ]);
            }
            Log::debug("Documents du projet sauvegardés.");

            // Créer le LogicalFramework
            $logicalFramework = LogicalFramework::create(array_merge(
                ['id' => (string) Str::uuid(), 'project_id' => $project->id],
                $this->initialLogicalFramework
            ));
            Log::debug("LogicalFramework créé avec l'ID: {$logicalFramework->id}");

            // Gérer les objectifs spécifiques (liés au LogicalFramework)
            // foreach ($this->specificObjectives as $objData) {
            //     $specificObjective = SpecificObjective::create(array_merge(
            //         ['id' => (string) Str::uuid(), 'logical_framework_id' => $logicalFramework->id],
            //         $objData
            //     ));
            //     Log::debug("Objectif spécifique créé avec l'ID: {$specificObjective->id}");
            // }

            foreach ($this->specificObjectives as $objData) {
                unset($objData['logical_framework_id']); // Supprime l'ancien ID s'il existe
                $specificObjective = SpecificObjective::create(array_merge(
                    ['id' => (string) Str::uuid(), 'logical_framework_id' => $logicalFramework->id],
                    $objData
                ));
                Log::debug("Objectif spécifique créé avec l'ID: {$specificObjective->id}");
            }


            // Gérer les résultats (liés aux objectifs spécifiques)
            // Nous devons nous assurer que les objectifs spécifiques existent avant de lier les résultats.
            // Si le nombre de résultats dépasse le nombre d'objectifs, nous pouvons boucler sur les objectifs.
            $allSpecificObjectives = $logicalFramework->specificObjectives;
            if ($allSpecificObjectives->isEmpty()) {
                // Créer un objectif générique si aucun n'existe pour lier les résultats
                $specificObjective = SpecificObjective::create([
                    'id' => (string) Str::uuid(),
                    'logical_framework_id' => $logicalFramework->id,
                    'description' => 'Objectif spécifique générique',
                ]);
                $allSpecificObjectives->push($specificObjective);
                Log::debug("Objectif spécifique générique créé pour lier les résultats.");
            }

            $objectiveIndex = 0;
            // foreach ($this->expectedResults as $resData) {
            //     $specificObjective = $allSpecificObjectives->get($objectiveIndex % $allSpecificObjectives->count());
            //     Result::create(array_merge(
            //         ['id' => (string) Str::uuid(), 'specific_objective_id' => $specificObjective->id],
            //         $resData
            //     ));
            //     Log::debug("Résultat créé pour l'objectif: {$specificObjective->id}");
            //     $objectiveIndex++;
            // }

            foreach ($this->expectedResults as $resData) {
                unset($resData['specific_objective_id']);
                unset($resData['id']);
                $specificObjective = $allSpecificObjectives->get($objectiveIndex % $allSpecificObjectives->count());
                Result::create(array_merge(
                    ['id' => (string) Str::uuid(), 'specific_objective_id' => $specificObjective->id],
                    $resData
                ));
                $objectiveIndex++;
            }


            // Gérer les activités (liées aux résultats)
            // foreach ($this->activities as $activityData) {
            //     // Tenter de lier l'activité au premier résultat disponible du projet
            //     // Il est crucial que $project->logicalFramework et ses dépendances existent ici
            //     $firstResult = $logicalFramework->specificObjectives?->first()?->results?->first();
            //     $resultId = $firstResult?->id;
                
            //     // Fallback si aucun résultat n'existe encore, créer un résultat générique
            //     if (!$resultId) {
            //         $specificObjectiveForActivity = $logicalFramework->specificObjectives?->first() ?? SpecificObjective::create([
            //             'id' => (string) Str::uuid(),
            //             'logical_framework_id' => $logicalFramework->id,
            //             'description' => 'Objectif générique pour activités (Fallback)',
            //         ]);
            //         $resultForActivity = Result::create([
            //             'id' => (string) Str::uuid(),
            //             'specific_objective_id' => $specificObjectiveForActivity->id,
            //             'description' => 'Résultat générique pour activité initiale (Fallback)',
            //         ]);
            //         $resultId = $resultForActivity->id;
            //         Log::debug("Résultat générique créé pour lier l'activité (Fallback): {$resultId}");
            //     }

            //     Activity::create(array_merge(
            //         ['id' => (string) Str::uuid(), 'result_id' => $resultId],
            //         $activityData
            //     ));
            //     Log::debug("Activité créée pour le résultat: {$resultId}");
            // }

            foreach ($this->activities as $activityData) {
                unset($activityData['result_id']);
                unset($activityData['id']);
                $firstResult = $logicalFramework->specificObjectives?->first()?->results?->first();
                $resultId = $firstResult?->id;

                if (!$resultId) {
                    $specificObjectiveForActivity = $logicalFramework->specificObjectives?->first() ?? SpecificObjective::create([
                        'id' => (string) Str::uuid(),
                        'logical_framework_id' => $logicalFramework->id,
                        'description' => 'Objectif générique pour activités (Fallback)',
                    ]);
                    $resultForActivity = Result::create([
                        'id' => (string) Str::uuid(),
                        'specific_objective_id' => $specificObjectiveForActivity->id,
                        'description' => 'Résultat générique pour activité initiale (Fallback)',
                    ]);
                    $resultId = $resultForActivity->id;
                }

                Activity::create(array_merge(
                    ['id' => (string) Str::uuid(), 'result_id' => $resultId],
                    $activityData
                ));
            }


            // Gérer les Budgets (création)
            // foreach ($this->budgets as $budgetData) {
            //     Budget::create(array_merge(
            //         ['id' => (string) Str::uuid(), 'project_id' => $project->id],
            //         $budgetData
            //     ));
            // }
            foreach ($this->budgets as $budgetData) {
                unset($budgetData['id']);
                Budget::create(array_merge(
                    ['id' => (string) Str::uuid(), 'project_id' => $project->id],
                    $budgetData
                ));
            }

            Log::debug("Budgets du projet créés.");

            DB::commit();
            Log::debug("Transaction de sauvegarde réussie et validée.");

            session()->flash('success', 'Le projet a été ' . ($this->projectId ? 'mis à jour' : 'créé') . ' avec succès !');
            return $this->redirectRoute('dashboard'); // Rediriger vers le tableau de bord

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la sauvegarde du projet : ' . $e->getMessage(), ['exception' => $e]);
            session()->flash('error', 'Une erreur est survenue lors de la sauvegarde du projet. Veuillez réessayer. Détail: ' . $e->getMessage());
        }
    }

    /**
     * Rend la vue du composant.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function render()
    {
        return view('livewire.v-beta.proposal-project.proposal-project-form-livewire');
    }
}
