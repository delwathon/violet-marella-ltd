<?php

namespace App\Console\Commands;

use App\Models\StudioSession;
use Illuminate\Console\Command;

class AutoStartStudioSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'studio:sessions-auto-start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto-start photo studio sessions whose preparation time has elapsed';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $started = StudioSession::autoStartDueSessions();

        if ($started > 0) {
            $this->info("Auto-started {$started} studio session(s).");
        }

        return self::SUCCESS;
    }
}
