<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class SetupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup Application inside a container.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $this->call('config:cache');
        $this->call('view:cache');
        $this->call('storage:link');
        foreach (['boostrap/cache/config.php', 'storage/framework/views', 'public/storage'] as $item) {
            (new Process(['chown', '-R', 'www-data:www-data', $item]))->run();
        }
        $this->call('migrate', [
            '--force' => true,
        ]);

        return self::SUCCESS;
    }
}
