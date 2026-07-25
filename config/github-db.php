<?php

/**
 * GitHub Database Sync Configuration
 */
return [
    /*
    |--------------------------------------------------------------------------
    | GitHub Repository
    |--------------------------------------------------------------------------
    |
    | The owner and name of the GitHub repository where the SQLite database
    | will be stored and synced.
    |
    */
    'owner' => env('GITHUB_REPO_OWNER', 'smpabbs'),
    'repo'  => env('GITHUB_REPO_NAME', 'smpabbs-ngajaryuk'),
    'branch' => env('GITHUB_REPO_BRANCH', 'main'),

    /*
    |--------------------------------------------------------------------------
    | GitHub Token
    |--------------------------------------------------------------------------
    |
    | A GitHub Personal Access Token with 'repo' scope to read/write
    | the database file stored in the repository.
    |
    */
    'token' => env('GITHUB_TOKEN', ''),

    /*
    |--------------------------------------------------------------------------
    | Database File Path in Repo
    |--------------------------------------------------------------------------
    |
    | Path to the SQLite database file inside the GitHub repository.
    |
    */
    'db_path_in_repo' => 'database/database.sqlite',

    /*
    |--------------------------------------------------------------------------
    | Auto Sync
    |--------------------------------------------------------------------------
    |
    | Whether to automatically push the database to GitHub after
    | every write operation. Disable for high-traffic deployments.
    |
    */
    'auto_sync' => env('GITHUB_DB_AUTO_SYNC', false),
];
