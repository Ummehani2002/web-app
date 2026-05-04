<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Super admin emails (optional)
    |--------------------------------------------------------------------------
    |
    | Comma-separated list in SUPER_ADMIN_EMAILS. These users are treated as
    | super admins even if is_super_admin is 0 in the database — useful on
    | Laravel Cloud when you prefer env over SQL. DB flag is_super_admin = 1
    | still grants super admin.
    |
    */

    'super_admin_emails' => array_values(array_filter(array_map(
        static fn (string $e): string => strtolower(trim($e)),
        explode(',', (string) env('SUPER_ADMIN_EMAILS', ''))
    ))),

];
