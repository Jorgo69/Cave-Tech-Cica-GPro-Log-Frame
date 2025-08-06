<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class QualitativeEvaluation extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', 'project_id', 'activity_id', 'evaluator_id', 'rating', 'score', 'comments',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(fn ($model) => $model->{$model->getKeyName()} = (string) Str::uuid());
    }
    
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }
    public function activity()
    {
        return $this->belongsTo(Activity::class, 'activity_id', 'id');
    }
    public function evaluator()
    {
        return $this->belongsTo(User::class, 'evaluator_id', 'id');
    }
}