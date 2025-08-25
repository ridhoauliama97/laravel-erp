<?php

namespace Webkul\Employee\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webkul\Employee\Database\Factories\SkillTypeFactory;
use Webkul\Field\Traits\HasCustomFields;
use Webkul\Security\Models\User;

class SkillType extends Model
{
    use HasCustomFields, HasFactory, SoftDeletes;

    protected $table = 'employees_skill_types';

    protected $fillable = [
        'name',
        'color',
        'creator_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function skillLevels(): HasMany
    {
        return $this->hasMany(SkillLevel::class, 'skill_type_id');
    }

    public function skills(): HasMany
    {
        return $this->hasMany(Skill::class, 'skill_type_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    /**
     * Get the factory instance for the model.
     */
    protected static function newFactory(): SkillTypeFactory
    {
        return SkillTypeFactory::new();
    }
}
