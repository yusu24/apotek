<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class BackupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:backup-db';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup the database to a file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting database backup...');

        $filename = "backup-" . now()->format('Y-m-d_H-i-s') . ".sql";
        $directory = storage_path('app/backups');
        
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        $path = $directory . DIRECTORY_SEPARATOR . $filename;

        // Configuration
        $dbHost = config('database.connections.mysql.host');
        $dbName = config('database.connections.mysql.database');
        $dbUser = config('database.connections.mysql.username');
        $dbPass = config('database.connections.mysql.password');
        
        // Detect OS and set mysqldump path
        $mysqldumpPath = 'mysqldump'; // Default for Linux/VPS or if in PATH
        
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $xamppPath = 'C:\xampp\mysql\bin\mysqldump.exe';
            if (file_exists($xamppPath)) {
                $mysqldumpPath = $xamppPath;
            }
        }

        $command = [
            $mysqldumpPath,
            "--user={$dbUser}",
            "--password={$dbPass}",
            "--host={$dbHost}",
            $dbName,
            "--result-file={$path}"
        ];

        $process = new Process($command);
        
        try {
            $process->mustRun();
            
            // Compress the backup
            if (file_exists($path)) {
                $content = file_get_contents($path);
                file_put_contents($path . '.gz', gzencode($content, 9));
                unlink($path); // Delete the uncompressed .sql file
                $this->info("Backup created and compressed: {$filename}.gz");
            }
            
            // Cleanup older backups (keep last 30 days)
            $this->cleanup();
            
            $this->info('Backup process completed successfully.');
            return 0;
        } catch (ProcessFailedException $exception) {
            $this->error('The backup process failed.');
            $this->error($exception->getMessage());
            return 1;
        }
    }

    private function cleanup()
    {
        $files = Storage::disk('local')->files('backups');
        $now = time();
        $daysToKeep = 30;

        foreach ($files as $file) {
            if (Storage::disk('local')->lastModified($file) < ($now - ($daysToKeep * 24 * 60 * 60))) {
                Storage::disk('local')->delete($file);
                $this->info("Deleted old backup: {$file}");
            }
        }
    }
}
