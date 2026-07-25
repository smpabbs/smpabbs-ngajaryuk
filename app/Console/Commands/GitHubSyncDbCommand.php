<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\GitHubDatabaseService;

class GitHubSyncDbCommand extends Command
{
    protected $signature = 'github:sync-db
                            {--action=pull : Action to perform: pull or push}
                            {--message= : Custom commit message for push}';

    protected $description = 'Sync SQLite database with GitHub repository';

    public function handle(GitHubDatabaseService $service): int
    {
        $action = $this->option('action');

        if (!$service->isConfigured()) {
            $this->error('GitHub token not configured. Set GITHUB_TOKEN env variable.');
            return Command::FAILURE;
        }

        if ($action === 'pull') {
            $this->info('Pulling database from GitHub...');
            if ($service->pull()) {
                $this->info('✓ Database pulled successfully.');
            } else {
                $this->warn('Could not pull database. Starting fresh.');
            }
        } elseif ($action === 'push') {
            $this->info('Pushing database to GitHub...');
            $message = $this->option('message') ?: '📦 Database sync: ' . now()->format('Y-m-d H:i');
            if ($service->push($message)) {
                $this->info('✓ Database pushed successfully.');
            } else {
                $this->error('Failed to push database.');
                return Command::FAILURE;
            }
        } else {
            $this->error("Unknown action: {$action}");
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
