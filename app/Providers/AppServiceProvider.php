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
    }
}
