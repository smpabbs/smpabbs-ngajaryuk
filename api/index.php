<?php

/**
 * Vercel PHP Runtime — Laravel Bootstrap
 *
 * This file is the serverless entry point for Vercel.
 * It bootstraps Laravel and handles every incoming HTTP request.
 */

// ── 1. Suppress deprecation warnings (PHP 8.5 compat) ─────────
error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);

// ── 2. Load Composer autoloader ───────────────────────────────
$appPath = dirname(__DIR__);
require $appPath . '/vendor/autoload.php';

// ── 3. Detect Vercel serverless environment ───────────────────
$isVercel = isset($_SERVER['VERCEL']) || getenv('VERCEL') === '1';

// ── 4. Override writable paths for Vercel (only /tmp is writable) ──
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

    // ── 5. Ensure SQLite database exists ──────────────────────────
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

    // Force production environment
    putenv('APP_ENV=production');
    $_ENV['APP_ENV'] = 'production';
    $_SERVER['APP_ENV'] = 'production';
} else {
    $app = require $appPath . '/bootstrap/app.php';
}

// ── 6. Handle the HTTP request (Laravel 12 style) ────────────
try {
    $app->handleRequest(Illuminate\Http\Request::capture());
} catch (\Throwable $e) {
    $code = $e->getCode() >= 400 && $e->getCode() < 600 ? (int)$e->getCode() : 500;
    http_response_code($code);
    header('Content-Type: text/plain; charset=utf-8');
    echo "[" . get_class($e) . "] " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    if ($e->getPrevious()) {
        echo "Previous: " . $e->getPrevious()->getMessage() . "\n";
    }
}
