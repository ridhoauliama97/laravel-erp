<?php

namespace Webkul\Security\Models;

use App\Models\User as BaseUser;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Traits\HasRoles;
use Webkul\Employee\Models\Department;
use Webkul\Employee\Models\Employee;
use Webkul\Partner\Models\Partner;
use Webkul\Support\Models\Company;

class User extends BaseUser implements FilamentUser
{
    use HasRoles, SoftDeletes;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'default_company_id' => 'integer',
    ];

    /**
     * Determine if the user can access the Filament panel.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    /**
     * Get image url for the product image.
     *
     * @return string
     */
    public function getAvatarUrlAttribute()
    {
        return $this->partner?->avatar_url;
    }

    /**
     * The teams that belong to the user.
     */
    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'user_team', 'user_id', 'team_id');
    }

    public function employee()
    {
        return $this->hasOne(Employee::class, 'user_id');
    }

    public function departments()
    {
        return $this->hasMany(Department::class, 'manager_id');
    }

    /**
     * The companies that the user owns.
     */
    public function companies(): HasMany
    {
        return $this->hasMany(Company::class);
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class, 'partner_id');
    }

    /**
     * The companies that the user is allowed to access.
     */
    public function allowedCompanies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class, 'user_allowed_companies', 'user_id', 'company_id');
    }

    /**
     * The user's default company.
     */
    public function defaultCompany(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'default_company_id');
    }

    /**
     * Bootstrap the model and its traits.
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function ($user) {
            if (! $user->partner_id) {
                $user->handlePartnerCreation($user);
            } else {
                $user->handlePartnerUpdation($user);
            }
        });
    }

    /**
     * Handle the creation of a partner.
     */
    private function handlePartnerCreation(self $user)
    {
        $partner = $user->partner()->create([
            'creator_id' => Auth::user()->id ?? $user->id,
            'user_id'    => $user->id,
            'sub_type'   => 'partner',
            ...Arr::except($user->toArray(), ['id']),
        ]);

        $user->partner_id = $partner->id;
        $user->save();
    }

    /**
     * Handle the updation of a partner.
     */
    private function handlePartnerUpdation(self $user)
    {
        $partner = Partner::updateOrCreate(
            ['id' => $user->partner_id],
            [
                'creator_id' => Auth::user()->id ?? $user->id,
                'user_id'    => $user->id,
                'sub_type'   => 'partner',
                ...Arr::except($user->toArray(), ['id']),
            ]
        );

        if ($user->partner_id !== $partner->id) {
            $user->partner_id = $partner->id;
            $user->save();
        }
    }
}
