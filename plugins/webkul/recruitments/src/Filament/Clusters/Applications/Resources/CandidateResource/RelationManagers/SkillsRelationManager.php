<?php

namespace Webkul\Recruitment\Filament\Clusters\Applications\Resources\CandidateResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Webkul\Recruitment\Traits\CandidateSkillRelation;

class SkillsRelationManager extends RelationManager
{
    use CandidateSkillRelation;

    protected static string $relationship = 'skills';
}
