<?php

namespace Webkul\Recruitment\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Employee\Models\Skill;
use Webkul\Employee\Models\SkillLevel;
use Webkul\Employee\Models\SkillType;
use Webkul\Security\Models\User;

class CandidateSkill extends Model
{
    protected $table = 'recruitments_candidate_skills';

    protected $fillable = [
        'candidate_id',
        'skill_id',
        'skill_level_id',
        'skill_type_id',
        'creator_id',
        'user_id',
    ];

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }

    public function skill()
    {
        return $this->belongsTo(Skill::class);
    }

    public function skillLevel()
    {
        return $this->belongsTo(SkillLevel::class);
    }

    public function skillType()
    {
        return $this->belongsTo(SkillType::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
