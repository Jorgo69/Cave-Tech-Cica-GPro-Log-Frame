<?php

namespace App\Livewire\VBeta\ProjectType;

use App\Models\Project;
use Livewire\Component;

use App\Models\ProjectType;
use Illuminate\Support\Str;
use App\Models\DynamicProjectField;


class ProjectTypeFormLivewire extends Component
{
    // Propriétés pour le type de projet
    public $projectTypeId;
    public $name = '';
    public $description = '';
    public $category = '';

    // Propriétés pour les champs dynamiques
    public $fields = [];

    // Message de statut
    public $statusMessage = '';
    public $statusType = 'hidden'; // 'success', 'error', 'hidden'

    // Méthode de montage, appelée à l'initialisation du composant.
    // Gère le mode édition si un ID de projet est fourni.
    public function mount($projectTypeId = null)
    {
        if ($projectTypeId) {
            // dd('fffff');
            $this->projectTypeId = $projectTypeId;
            $projectType = Project::with('dynamicFields')->findOrFail($projectTypeId);
            $this->name = $projectType->name;
            $this->description = $projectType->description;
            $this->category = $projectType->category;
            $this->fields = $projectType->dynamicFields->toArray();
        } else {
            // Ajoute un champ vide par défaut en mode création
            $this->addField();
        }
    }

    // Ajoute un nouveau champ dynamique au formulaire
    public function addField()
    {
        $this->fields[] = [
            'field_name' => '',
            'question_text' => '',
            'input_type' => 'text',
            'options' => '',
            'order' => count($this->fields) + 1,
            'target_project_field' => '',
            'section' => '',
            'delimiter_start' => '',
            'delimiter_end' => '',
            'is_required' => false,
        ];
    }

    // Retire un champ dynamique du formulaire
    public function removeField($index)
    {
        unset($this->fields[$index]);
        $this->fields = array_values($this->fields);
    }

    // Sauvegarde ou met à jour le type de projet et ses champs
    public function save()
    {
        // Validation des données du formulaire
        $this->validate([
            'name' => 'required|string|max:100|unique:project_types,name,' . $this->projectTypeId,
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:255',
            'fields.*.question_text' => 'required|string|max:255',
            'fields.*.field_name' => 'required|string|max:100',
            'fields.*.input_type' => 'required|string|in:text,textarea,select,date,number',
            'fields.*.options' => 'nullable|string',
            'fields.*.order' => 'required|integer',
            'fields.*.target_project_field' => 'nullable|string|max:100',
            'fields.*.section' => 'nullable|string|max:100',
            'fields.*.delimiter_start' => 'nullable|string|max:255',
            'fields.*.delimiter_end' => 'nullable|string|max:255',
            'fields.*.is_required' => 'boolean',
        ]);

        try {
            // Création ou mise à jour du ProjectType
            if ($this->projectTypeId) {
                $projectType = ProjectType::findOrFail($this->projectTypeId);
                $projectType->update([
                    'name' => $this->name,
                    'description' => $this->description,
                    'category' => $this->category,
                ]);
            } else {
                // dd('');
                $projectType = ProjectType::create([
                    'id' => (string) Str::uuid(),
                    'name' => $this->name,
                    'description' => $this->description,
                    'category' => $this->category,
                ]);
                $this->projectTypeId = $projectType->id;
            }

            // Gestion des champs dynamiques (mise à jour/création)
            // On supprime les anciens champs qui n'existent plus dans le formulaire
            if ($this->projectTypeId) {
                $existingFieldNames = collect($this->fields)->pluck('field_name')->toArray();
                DynamicProjectField::where('project_type_id', $this->projectTypeId)
                    ->whereNotIn('field_name', $existingFieldNames)
                    ->delete();
            }

            foreach ($this->fields as $index => $fieldData) {
                // S'assurer que le champ a un nom unique pour l'updateOrCreate
                $fieldData['project_type_id'] = $this->projectTypeId;
                $fieldData['order'] = $index + 1;

                DynamicProjectField::updateOrCreate(
                    [
                        'project_type_id' => $this->projectTypeId,
                        'field_name' => $fieldData['field_name']
                    ],
                    $fieldData
                );
            }

            // Utiliser session() pour les messages flash
            session()->flash('message', 'Type de projet sauvegardé avec succès !');
            return redirect()->route('admin.it.type.of.project');

        } catch (\Exception $e) {
            session()->flash('error', 'Une erreur est survenue : ' . $e->getMessage());
        }
    }

    // La méthode render retourne la vue associée au composant
    public function render()
    {
        return view('livewire.v-beta.project-type.project-type-form-livewire');
    }
}
