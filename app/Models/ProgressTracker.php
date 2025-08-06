<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProgressTracker extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', 'activity_id', 'project_id', 'date', 'progress_percentage',
        'status_update', 'justification', 'updated_by_user_id',
        'performance_score', 'evaluation_comment',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(fn ($model) => $model->{$model->getKeyName()} = (string) Str::uuid());
    }
    
    public function activity()
    {
        return $this->belongsTo(Activity::class, 'activity_id', 'id');
    }
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by_user_id', 'id');
    }
}