<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DynamicProjectField extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    
    protected $fillable = [
        'id', 'project_type_id', 'field_name', 'question_text', 'input_type',
        'options', 'order', 'target_project_field', 'section',
        'delimiter_start', 'delimiter_end', 'is_required',
    ];

    protected $casts = [
        'options' => 'array', 
        'is_required' => 'boolean'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(fn ($model) => $model->{$model->getKeyName()} = (string) Str::uuid());
    }

    public function projectType()
    {
        return $this->belongsTo(ProjectType::class, 'project_type_id', 'id');
    }
}