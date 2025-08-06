<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Activity extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', 'result_id', 'description', 'start_date', 'end_date',
        'responsible_user_id', 'status', 'justification', 'is_milestone', 'progress_percentage',
    ];

    protected $casts = [
        'start_date' => 'date', 
        'end_date' => 'date',
        'is_milestone' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(fn ($model) => $model->{$model->getKeyName()} = (string) Str::uuid());
    }
    
    public function result()
    {
        return $this->belongsTo(Result::class, 'result_id', 'id');
    }
    public function responsibleUser()
    {
        return $this->belongsTo(User::class, 'responsible_user_id', 'id');
    }
    public function resources()
    {
        return $this->hasMany(Resource::class, 'activity_id', 'id');
    }
    public function progressTrackers()
    {
        return $this->hasMany(ProgressTracker::class, 'activity_id', 'id');
    }
    public function qualitativeEvaluations()
    {
        return $this->hasMany(QualitativeEvaluation::class, 'activity_id', 'id');
    }
}