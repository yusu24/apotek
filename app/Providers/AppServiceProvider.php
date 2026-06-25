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

        if (class_exists(\Doctrine\DBAL\Connection::class)) {
            try {
                \Illuminate\Support\Facades\DB::connection()->getDoctrineConnection()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
            } catch (\Throwable $e) {
                // Ignore database connection failures during early boot/CLI tasks
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
