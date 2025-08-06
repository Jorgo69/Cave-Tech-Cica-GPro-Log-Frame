<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SpecificObjective extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', 'logical_framework_id', 'description', 'indicators',
        'verification_sources', 'assumptions',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(fn ($model) => $model->{$model->getKeyName()} = (string) Str::uuid());
    }
    
    public function logicalFramework()
    {
        return $this->belongsTo(LogicalFramework::class, 'logical_framework_id', 'id');
    }
    public function results()
    {
        return $this->hasMany(Result::class, 'specific_objective_id', 'id');
    }
}