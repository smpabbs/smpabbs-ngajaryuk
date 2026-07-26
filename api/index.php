<?php

/**
 * Vercel PHP Runtime — Laravel Bootstrap
 *
 * This file is the serverless entry point for Vercel.
 * It bootstraps Laravel and handles every incoming HTTP request.
 */

// ── 1. Show all errors for debugging ──────────────────────────
error_reporting(E_ALL);
ini_set('display_errors', '1');

// ── 2. Load Composer autoloader ───────────────────────────────
$appPath = dirname(__DIR__);
require $appPath . '/vendor/autoload.php';

// ── 3. Detect Vercel serverless environment ───────────────────
$isVercel = isset($_SERVER['VERCEL']) || getenv('VERCEL') === '1';

// ── 4. Bootstrap Laravel ──────────────────────────────────────
$app = require $appPath . '/bootstrap/app.php';
echo "APP LOADED\n";

if ($isVercel) {
    echo "VERCEL MODE\n";
    echo "TMP: " . sys_get_temp_dir() . "\n";
    $tmpPath = sys_get_temp_dir() . '/smpabbs-ngajaryuk';

    foreach (['storage/framework/cache', 'storage/framework/sessions', 'storage/framework/views', 'storage/logs', 'database'] as $dir) {
        $fullPath = $tmpPath . '/' . $dir;
        if (!is_dir($fullPath)) {
            mkdir($fullPath, 0775, true);
        }
    }

    $storagePath = $tmpPath . '/storage';
    if (!is_dir($storagePath)) {
        mkdir($storagePath, 0775, true);
    }

    $app->useStoragePath($storagePath);

    // ── 5. Ensure SQLite database exists ──────────────────────────
    $dbDir  = $tmpPath . '/database';
    $dbPath = $dbDir . '/database.sqlite';

    if (!file_exists($dbPath)) {
        $githubToken = getenv('GITHUB_TOKEN') ?: '';
        $repoOwner   = getenv('GITHUB_REPO_OWNER') ?: 'smpabbs';
        $repoName    = getenv('GITHUB_REPO_NAME') ?: 'smpabbs-ngajaryuk';
        $repoBranch  = getenv('GITHUB_REPO_BRANCH') ?: 'main';

        if ($githubToken) {
            $url = "https://api.github.com/repos/{$repoOwner}/{$repoName}/contents/database/database.sqlite?ref={$repoBranch}";
            $ctx = stream_context_create([
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
            $dbContent = @file_get_contents($url, false, $ctx);
            if ($dbContent !== false) {
                file_put_contents($dbPath, $dbContent);
            }
        }

        if (!file_exists($dbPath)) {
            touch($dbPath);
        }
    }

    putenv("DB_DATABASE={$dbPath}");
    $_ENV['DB_DATABASE'] = $dbPath;
    $_SERVER['DB_DATABASE'] = $dbPath;

    putenv('APP_ENV=production');
    $_ENV['APP_ENV'] = 'production';
    $_SERVER['APP_ENV'] = 'production';
}

// ── 6. Handle request through HTTP Kernel ─────────────────────
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::capture();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
