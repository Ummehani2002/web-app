<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Company membership & route permission enforcement
    |--------------------------------------------------------------------------
    |
    | When false, logged-in users are not redirected for missing company
    | assignment, module routes are not gated by role, and the sidebar shows
    | all module links. Masters and Settings are available to any signed-in user.
    | Set ENFORCE_COMPANY_ACCESS=true to restore super-admin-only admin screens
    | and role-based module access.
    |
    */

    'enforce_access' => env('ENFORCE_COMPANY_ACCESS', false),

];
