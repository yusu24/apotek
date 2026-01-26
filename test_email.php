<?php

use Illuminate\Support\Facades\Mail;

try {
    echo "Attempting to send test email...\n";
    
    Mail::raw('Ini adalah email percobaan dari sistem Apotek Localhost.', function ($message) {
        $message->to('yusuf24ef@gmail.com')
                ->subject('Test Email Localhost');
    });

    echo "Email sent successfully.\n";
} catch (\Exception $e) {
    echo "Failed to send email: " . $e->getMessage() . "\n";
}
