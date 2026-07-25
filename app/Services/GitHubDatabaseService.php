<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * GitHubDatabaseService — sync SQLite database to/from GitHub
 *
 * This service stores the application's SQLite database in a GitHub
 * repository, making it persist across Vercel serverless cold starts.
 *
 * Usage:
 *   app(GitHubDatabaseService::class)->pull();  // download DB from GitHub
 *   app(GitHubDatabaseService::class)->push();  // upload DB to GitHub
 */
class GitHubDatabaseService
{
    protected string $owner;
    protected string $repo;
    protected string $branch;
    protected string $token;
    protected string $dbPath;
    protected string $dbPathInRepo;

    public function __construct()
    {
        $this->owner       = config('github-db.owner', 'smpabbs');
        $this->repo        = config('github-db.repo', 'smpabbs-ngajaryuk');
        $this->branch      = config('github-db.branch', 'main');
        $this->token       = config('github-db.token', env('GITHUB_TOKEN', ''));
        $this->dbPath      = database_path('database.sqlite');
        $this->dbPathInRepo = config('github-db.db_path_in_repo', 'database/database.sqlite');
    }

    /**
     * Check if the service is configured (token exists).
     */
    public function isConfigured(): bool
    {
        return !empty($this->token);
    }

    /**
     * Pull database from GitHub → local storage.
     * Returns true on success, false on failure.
     */
    public function pull(): bool
    {
        if (!$this->isConfigured()) {
            Log::warning('GitHubDatabaseService: not configured (no token)');
            return false;
        }

        $url = "https://api.github.com/repos/{$this->owner}/{$this->repo}/contents/{$this->dbPathInRepo}?ref={$this->branch}";

        $response = Http::withHeaders([
            'Authorization' => "token {$this->token}",
            'Accept'        => 'application/vnd.github.v3.raw',
            'User-Agent'    => 'smpabbs-ngajaryuk/1.0',
        ])->timeout(15)->get($url);

        if ($response->successful()) {
            $dir = dirname($this->dbPath);
            if (!is_dir($dir)) {
                mkdir($dir, 0775, true);
            }
            file_put_contents($this->dbPath, $response->body());
            Log::info('GitHubDatabaseService: database pulled successfully');
            return true;
        }

        if ($response->status() === 404) {
            Log::info('GitHubDatabaseService: no database found on GitHub, starting fresh');
            return false;
        }

        Log::warning('GitHubDatabaseService: pull failed', [
            'status' => $response->status(),
        ]);
        return false;
    }

    /**
     * Push local database → GitHub repository.
     * Returns true on success, false on failure.
     */
    public function push(string $commitMessage = '⚡ Auto-sync database [skip ci]'): bool
    {
        if (!$this->isConfigured()) {
            Log::warning('GitHubDatabaseService: not configured (no token)');
            return false;
        }

        if (!file_exists($this->dbPath)) {
            Log::warning('GitHubDatabaseService: local database not found, nothing to push');
            return false;
        }

        $url = "https://api.github.com/repos/{$this->owner}/{$this->repo}/contents/{$this->dbPathInRepo}";

        // Get current file SHA (if file exists on GitHub)
        $existing = Http::withHeaders([
            'Authorization' => "token {$this->token}",
            'Accept'        => 'application/vnd.github.v3+json',
            'User-Agent'    => 'smpabbs-ngajaryuk/1.0',
        ])->get($url);

        $sha = $existing->successful() ? ($existing->json()['sha'] ?? null) : null;

        // Encode file content
        $content = base64_encode(file_get_contents($this->dbPath));

        $payload = [
            'message' => $commitMessage,
            'content' => $content,
            'branch'  => $this->branch,
        ];

        if ($sha) {
            $payload['sha'] = $sha;
        }

        $response = Http::withHeaders([
            'Authorization' => "token {$this->token}",
            'Accept'        => 'application/vnd.github.v3+json',
            'User-Agent'    => 'smpabbs-ngajaryuk/1.0',
        ])->timeout(30)->put($url, $payload);

        if ($response->successful()) {
            Log::info('GitHubDatabaseService: database pushed successfully');
            return true;
        }

        Log::warning('GitHubDatabaseService: push failed', [
            'status' => $response->status(),
            'body'   => $response->body(),
        ]);
        return false;
    }

    /**
     * Ensure the local database exists.
     * If local DB is missing or empty, try to pull from GitHub.
     * If GitHub also has none, create an empty SQLite database.
     */
    public function ensureDatabase(): void
    {
        // Pull if local doesn't exist or is empty
        if (!file_exists($this->dbPath) || filesize($this->dbPath) === 0) {
            $pulled = $this->pull();
            if (!$pulled) {
                $dir = dirname($this->dbPath);
                if (!is_dir($dir)) {
                    mkdir($dir, 0775, true);
                }
                if (!file_exists($this->dbPath)) {
                    touch($this->dbPath);
                }
                Log::info('GitHubDatabaseService: created empty database');
            }
        }
    }
}
