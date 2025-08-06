<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Project extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id', 'creator_user_id', 'project_type_id', 'project_code', 'title',
        'short_title', 'description', 'general_objectives', 'start_date', 'end_date', 'status',
        'problem_analysis', 'strategy', 'justification',
    ];
    protected $casts = [
        'start_date' => 'date', 'end_date' => 'date',
    ];
    protected static function boot()
    {
        parent::boot();
        static::creating(fn ($model) => $model->{$model->getKeyName()} = (string) Str::uuid());
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_user_id', 'id');
    }
    public function projectType()
    {
        return $this->belongsTo(ProjectType::class, 'project_type_id', 'id');
    }
    public function contexts()
    {
        return $this->hasMany(ProjectContext::class, 'project_id', 'id');
    }
    public function documents()
    {
        return $this->hasMany(ProjectDocument::class, 'project_id', 'id');
    }
    public function logicalFramework()
    {
        return $this->hasOne(LogicalFramework::class, 'project_id', 'id');
    }
    public function budgets()
    {
        return $this->hasMany(Budget::class, 'project_id', 'id');
    }
    public function progressTrackers()
    {
        return $this->hasMany(ProgressTracker::class, 'project_id', 'id');
    }
    public function qualitativeEvaluations()
    {
        return $this->hasMany(QualitativeEvaluation::class, 'project_id', 'id');
    }

    // Autres relations et méthodes...
    public function projectContext()
    {
        return $this->hasOne(ProjectContext::class);
    }

    public function projectDocuments()
    {
        return $this->hasMany(ProjectDocument::class);
    }

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }
    
}