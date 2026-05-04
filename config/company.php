<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Company membership & route permission enforcement
    |--------------------------------------------------------------------------
    |
    | When false, logged-in users are not redirected for missing company
    | assignment, module routes are not gated by role, and the sidebar shows
    | all module links. Masters / Settings still require super admin.
    | Set ENFORCE_COMPANY_ACCESS=true to restore the previous behaviour.
    |
    */

    'enforce_access' => env('ENFORCE_COMPANY_ACCESS', false),

];
