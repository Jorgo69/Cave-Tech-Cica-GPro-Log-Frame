<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProjectType extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['id', 'name', 'description', 'category'];
    protected static function boot()
    {
        parent::boot();
        static::creating(fn ($model) => $model->{$model->getKeyName()} = (string) Str::uuid());
    }
    public function projects()
    {
        return $this->hasMany(Project::class, 'project_type_id', 'id');
    }
    public function dynamicFields()
    {
        return $this->hasMany(DynamicProjectField::class, 'project_type_id', 'id')->orderBy('order');
    }
}