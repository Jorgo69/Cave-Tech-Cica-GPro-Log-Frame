<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class LogicalFramework extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', 'project_id', 'general_objective', 'general_obj_indicators',
        'general_obj_verification_sources', 'assumptions',
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
    
    public function specificObjectives()
    {
        return $this->hasMany(SpecificObjective::class, 'logical_framework_id', 'id');
    }
}