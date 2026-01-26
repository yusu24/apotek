<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

echo "Current Mailer: " . Config::get('mail.default') . "\n";
echo "Host: " . Config::get('mail.mailers.smtp.host') . "\n";
echo "Port: " . Config::get('mail.mailers.smtp.port') . "\n";
echo "Username: " . Config::get('mail.mailers.smtp.username') . "\n";
// Do not print password

try {
    echo "Attempting to send...\n";
    Mail::raw('Test email from Apotek Localhost', function($msg) { 
        $msg->to('yusuf24ef@gmail.com')->subject('Test Laravel Email'); 
    });
    echo "SUCCESS: Email sent.\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
