<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Reference options import (SHA → HIMS)
    |--------------------------------------------------------------------------
    |
    | SHA (or ops tooling) calls HIMS integration endpoints with header:
    |   X-Scheme-Preauth-Import-Token: <import_token>
    |
    | Alternatively, a logged-in Master Admin / manage-roles user may call
    | the same endpoints from the browser (session auth).
    |
    */
    'import_enabled' => (bool) env('SCHEME_PREAUTH_IMPORT_ENABLED', true),

    'import_token' => env('SCHEME_PREAUTH_IMPORT_TOKEN'),

    /*
    | Optional second DB connection name (see config/database.php "sha") used when
    | source=sha_database on the import endpoint.
    */
    'sha_db_connection' => env('SCHEME_PREAUTH_SHA_DB_CONNECTION', 'sha'),

];
