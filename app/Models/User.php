<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'role',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Génère un UUID et l'assigne à la clé primaire si elle n'est pas déjà définie
            $model->{$model->getKeyName()} = (string) Str::uuid();
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }
    public function createdProjects(): HasMany
    {
        return $this->hasMany(Project::class, 'creator_user_id', 'id');
    }
    public function uploadedDocuments(): HasMany
    {
        return $this->hasMany(ProjectDocument::class, 'uploaded_by_user_id', 'id');
    }
    public function responsibleActivities(): HasMany
    {
        return $this->hasMany(Activity::class, 'responsible_user_id', 'id');
    }
    public function responsibleResources(): HasMany
    {
        return $this->hasMany(Resource::class, 'responsible_user_id', 'id');
    }
    public function responsibleBudgets(): HasMany
    {
        return $this->hasMany(Budget::class, 'responsible_user_id', 'id');
    }
    public function progressUpdates(): HasMany
    {
        return $this->hasMany(ProgressTracker::class, 'updated_by_user_id', 'id');
    }
    public function qualitativeEvaluations(): HasMany
    {
        return $this->hasMany(QualitativeEvaluation::class, 'evaluator_id', 'id');
    }
}
