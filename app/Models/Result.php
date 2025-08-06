<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Result extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', 
        'specific_objective_id', 
        'description',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(fn ($model) => $model->{$model->getKeyName()} = (string) Str::uuid());
    }
    
    public function specificObjective()
    {
        return $this->belongsTo(SpecificObjective::class, 'specific_objective_id', 'id');
    }
    public function activities()
    {
        return $this->hasMany(Activity::class, 'result_id', 'id');
    }
}