<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (class_exists(\Doctrine\DBAL\Types\Type::class)) {
            if (! \Doctrine\DBAL\Types\Type::hasType('enum')) {
                try {
                    \Doctrine\DBAL\Types\Type::addType('enum', \Doctrine\DBAL\Types\StringType::class);
                } catch (\Throwable $e) {
                    // Ignore if already registered or fails
                }
            }
        }

        // Skip DB queries on CLI commands like `serve`, `view:clear`, `config:clear`, etc.
        $isServeOrCacheCommand = app()->runningInConsole() && isset($_SERVER['argv'][1]) && in_array($_SERVER['argv'][1], [
            'serve', 'view:clear', 'view:cache', 'config:clear', 'config:cache', 'cache:clear', 'route:clear', 'route:cache', 'optimize', 'optimize:clear', 'package:discover'
        ]);

        if (!$isServeOrCacheCommand) {
            try {
                // Quick check if DB connection is reachable before running schema/doctrine queries
                \Illuminate\Support\Facades\DB::connection()->getPdo();

                if (class_exists(\Doctrine\DBAL\Connection::class)) {
                    \Illuminate\Support\Facades\DB::connection()->getDoctrineConnection()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
                }

                if (\Illuminate\Support\Facades\Schema::hasTable('settings')) {
                    $mailSettings = \App\Models\Setting::whereIn('key', [
                        'mail_host', 'mail_port', 'mail_username', 'mail_password', 'mail_encryption', 'mail_from_address', 'mail_from_name'
                    ])->pluck('value', 'key')->toArray();

                    if (!empty($mailSettings['mail_host'])) {
                        $mailPassword = $mailSettings['mail_password'] ?? config('mail.mailers.smtp.password');
                        if (!empty($mailPassword)) {
                            try {
                                $mailPassword = \Illuminate\Support\Facades\Crypt::decryptString($mailPassword);
                            } catch (\Throwable $e) {
                                // Leave as is if already plain text or decryption fails
                            }
                        }

                        config([
                            'mail.mailers.smtp.host' => $mailSettings['mail_host'],
                            'mail.mailers.smtp.port' => $mailSettings['mail_port'] ?? config('mail.mailers.smtp.port'),
                            'mail.mailers.smtp.encryption' => $mailSettings['mail_encryption'] ?? config('mail.mailers.smtp.encryption'),
                            'mail.mailers.smtp.username' => $mailSettings['mail_username'] ?? config('mail.mailers.smtp.username'),
                            'mail.mailers.smtp.password' => $mailPassword,
                            'mail.from.address' => $mailSettings['mail_from_address'] ?? config('mail.from.address'),
                            'mail.from.name' => $mailSettings['mail_from_name'] ?? config('mail.from.name'),
                        ]);
                    }
                }
            } catch (\Throwable $e) {
                // Ignore DB errors during early boot/migrations or when MySQL is stopped
            }
        }

        // Prevent modification when impersonating
        if (!app()->runningInConsole()) {
            \Illuminate\Database\Eloquent\Model::saving(function ($model) {
                if ($model instanceof \App\Models\ActivityLog) {
                    return;
                }
                
                try {
                    if (request()->hasSession() && session()->has('impersonator_id')) {
                        throw new \Exception("Mode Lihat Saja: Anda tidak diperbolehkan melakukan perubahan data saat dalam mode Impersonate.");
                    }
                } catch (\Throwable $e) {
                    if ($e instanceof \Exception) {
                        throw $e;
                    }
                }
            });

            \Illuminate\Database\Eloquent\Model::deleting(function ($model) {
                if ($model instanceof \App\Models\ActivityLog) {
                    return;
                }
                
                try {
                    if (request()->hasSession() && session()->has('impersonator_id')) {
                        throw new \Exception("Mode Lihat Saja: Anda tidak diperbolehkan melakukan perubahan data saat dalam mode Impersonate.");
                    }
                } catch (\Throwable $e) {
                    if ($e instanceof \Exception) {
                        throw $e;
                    }
                }
            });
        }
    }
}
