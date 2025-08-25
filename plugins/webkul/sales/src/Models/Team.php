<?php

namespace Webkul\Sale\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Webkul\Chatter\Traits\HasChatter;
use Webkul\Chatter\Traits\HasLogActivity;
use Webkul\Sale\Database\Factories\TeamFactory;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

class Team extends Model implements Sortable
{
    use HasChatter, HasFactory, HasLogActivity, SoftDeletes, SortableTrait;

    protected $table = 'sales_teams';

    protected $fillable = [
        'sort',
        'company_id',
        'user_id',
        'color',
        'creator_id',
        'name',
        'is_active',
        'invoiced_target',
    ];

    public $sortable = [
        'order_column_name'  => 'sort',
        'sort_when_creating' => true,
    ];

    protected array $logAttributes = [
        'name',
        'company.name'    => 'Company',
        'user.name'       => 'Team Leader',
        'creator.name'    => 'Creator',
        'is_active'       => 'Status',
        'invoiced_target' => 'Invoiced Target',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'sales_team_members', 'team_id', 'user_id');
    }

    protected static function newFactory(): TeamFactory
    {
        return TeamFactory::new();
    }
}
