<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class QuarterlyBudget extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', 
        'budget_id', 
        'year', 
        'quarter', 
        'amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];
    
    protected static function boot()
    {
        parent::boot();
        static::creating(fn ($model) => $model->{$model->getKeyName()} = (string) Str::uuid());
    }

    public function budget()
    {
        return $this->belongsTo(Budget::class, 'budget_id', 'id');
    }
}