<?php

/**
 * Vercel PHP Runtime — Laravel Bootstrap
 *
 * This file is the serverless entry point for Vercel.
 * It bootstraps Laravel and handles every incoming HTTP request.
 */

// ── 1. Detect Vercel serverless environment ─────────────────────
$isVercel = isset($_SERVER['VERCEL']) || getenv('VERCEL') === '1';

// ── 2. Resolve paths ────────────────────────────────────────────
$appPath = dirname(__DIR__);

// ── 3. Override writable paths for Vercel (only /tmp is writable) ──
if ($isVercel) {
    $tmpPath = sys_get_temp_dir() . '/smpabbs-ngajaryuk';

    // Ensure writable directories exist
    foreach (['storage/framework/cache', 'storage/framework/sessions', 'storage/framework/views', 'storage/logs', 'database'] as $dir) {
        $fullPath = $tmpPath . '/' . $dir;
        if (!is_dir($fullPath)) {
            mkdir($fullPath, 0775, true);
        }
    }

    // Point Laravel storage to /tmp
    $storagePath = $tmpPath . '/storage';
    if (!is_dir($storagePath)) {
        mkdir($storagePath, 0775, true);
    }

    $app = require $appPath . '/bootstrap/app.php';
    $app->useStoragePath($storagePath);

    // ── 4. Ensure SQLite database exists ────────────────────────────
    $dbDir  = $tmpPath . '/database';
    $dbPath = $dbDir . '/database.sqlite';

    if (!file_exists($dbPath)) {
        // Try to pull from GitHub first
        $githubToken = getenv('GITHUB_TOKEN') ?: '';
        $repoOwner   = getenv('GITHUB_REPO_OWNER') ?: 'smpabbs';
        $repoName    = getenv('GITHUB_REPO_NAME') ?: 'smpabbs-ngajaryuk';
        $repoBranch  = getenv('GITHUB_REPO_BRANCH') ?: 'main';

        if ($githubToken) {
            $url = "https://api.github.com/repos/{$repoOwner}/{$repoName}/contents/database/database.sqlite?ref={$repoBranch}";
            $context = stream_context_create([
                'http' => [
                    'method' => 'GET',
                    'header' => [
                        "Authorization: token {$githubToken}",
                        'Accept: application/vnd.github.v3.raw',
                        'User-Agent: smpabbs-ngajaryuk',
                    ],
                    'timeout' => 10,
                ],
            ]);

            $dbContent = @file_get_contents($url, false, $context);
            if ($dbContent !== false) {
                file_put_contents($dbPath, $dbContent);
            }
        }

        // If still doesn't exist, create empty database
        if (!file_exists($dbPath)) {
            touch($dbPath);
        }
    }

    // Override DB_DATABASE env so Laravel uses our path
    putenv("DB_DATABASE={$dbPath}");
    $_ENV['DB_DATABASE'] = $dbPath;
    $_SERVER['DB_DATABASE'] = $dbPath;

    // Force HTTPS behind Vercel proxy
    putenv('APP_ENV=production');
    $_ENV['APP_ENV'] = 'production';
    $_SERVER['APP_ENV'] = 'production';
} else {
    $app = require $appPath . '/bootstrap/app.php';
}

// ── 5. Handle the HTTP request ──────────────────────────────────
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$request = Illuminate\Http\Request::capture();
$response = $kernel->handle($request);
$response->send();

$kernel->terminate($request, $response);
