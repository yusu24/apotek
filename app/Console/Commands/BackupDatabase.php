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
        $mysqldumpPath = env('DB_DUMP_PATH', 'mysqldump'); // Default for Linux/VPS or if in PATH
        
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' && $mysqldumpPath === 'mysqldump') {
            $possiblePaths = [
                'C:\xampp\mysql\bin\mysqldump.exe',
                'D:\xampp\mysql\bin\mysqldump.exe',
            ];
            
            foreach ($possiblePaths as $p) {
                if (file_exists($p)) {
                    $mysqldumpPath = $p;
                    break;
                }
            }

            // Check for Laragon if not found in XAMPP
            if ($mysqldumpPath === 'mysqldump') {
                $laragonPaths = glob('C:\laragon\bin\mysql\*\bin\mysqldump.exe');
                if (!empty($laragonPaths)) {
                    $mysqldumpPath = $laragonPaths[0];
                }
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

        // Remove password argument if password is empty to avoid some mysqldump issues
        if (empty($dbPass)) {
            $command = array_filter($command, fn($arg) => !str_starts_with($arg, '--password='));
            $command = array_values($command); // re-index
        }

        $process = new Process($command);
        
        try {
            $process->mustRun();
            
            // Compress the backup using stream to prevent memory exhaustion
            if (file_exists($path)) {
                $src = fopen($path, 'rb');
                $dest = gzopen($path . '.gz', 'wb9');
                if ($src && $dest) {
                    while (!feof($src)) {
                        gzwrite($dest, fread($src, 1024 * 512)); // 512KB chunks
                    }
                    fclose($src);
                    gzclose($dest);
                    unlink($path); // Delete the uncompressed .sql file
                    $this->info("Backup created and compressed: {$filename}.gz");
                } else {
                    $this->error("Failed to open file for compression.");
                }
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
