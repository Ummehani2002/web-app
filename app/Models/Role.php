<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    protected $fillable = [
        'company_id',
        'name',
        'slug',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'permission_role');
    }

    public function companyMemberships(): BelongsToMany
    {
        return $this->belongsToMany(CompanyMembership::class, 'company_membership_roles');
    }

    public function hasPermission(string $slug): bool
    {
        return $this->permissions->contains('slug', $slug);
    }

    /**
     * Ensure Admin, User, and Store keeper roles exist and have up-to-date permissions.
     */
    public static function ensurePresetRoles(Company $company): void
    {
        if (! Permission::query()->exists()) {
            return;
        }

        $allIds = Permission::query()->pluck('id');

        $admin = static::query()->firstOrCreate(
            [
                'company_id' => $company->id,
                'slug' => 'admin',
            ],
            ['name' => 'Admin']
        );
        $admin->permissions()->sync($allIds);

        $userSlugs = ['settings.access', 'modules.access', 'item_issue.access'];
        $userIds = Permission::query()->whereIn('slug', $userSlugs)->pluck('id');
        $user = static::query()->firstOrCreate(
            [
                'company_id' => $company->id,
                'slug' => 'user',
            ],
            ['name' => 'User']
        );
        $user->permissions()->sync($userIds);

        $keeperSlugs = ['settings.access', 'pr.access', 'grn.access'];
        $keeperIds = Permission::query()->whereIn('slug', $keeperSlugs)->pluck('id');
        $keeper = static::query()->firstOrCreate(
            [
                'company_id' => $company->id,
                'slug' => 'store_keeper',
            ],
            ['name' => 'Store keeper']
        );
        $keeper->permissions()->sync($keeperIds);
    }

    /**
     * @deprecated Use ensurePresetRoles()
     */
    public static function bootstrapForCompany(Company $company): void
    {
        static::ensurePresetRoles($company);
    }
}
