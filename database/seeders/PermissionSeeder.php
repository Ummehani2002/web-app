<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['slug' => 'users.manage', 'name' => 'Manage users & roles for company'],
            ['slug' => 'settings.access', 'name' => 'Access Settings screens'],
            ['slug' => 'masters.access', 'name' => 'Access Masters data'],
            ['slug' => 'modules.access', 'name' => 'Access general module screens'],
            ['slug' => 'item_issue.access', 'name' => 'Item Issue module'],
            ['slug' => 'pr.access', 'name' => 'Purchase Requisition module'],
            ['slug' => 'grn.access', 'name' => 'Goods Receive Note (GRN) module'],
        ];

        foreach ($rows as $row) {
            Permission::query()->firstOrCreate(
                ['slug' => $row['slug']],
                ['name' => $row['name']]
            );
        }
    }
}
