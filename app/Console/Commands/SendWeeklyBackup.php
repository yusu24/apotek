<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Exports\WeeklyDataExport;
use App\Mail\WeeklyBackupMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;

class SendWeeklyBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-weekly-backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Excel report and send it via email (Weekly)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Weekly Excel Export generation...');

        // Define temporary path for Excel file
        $tempDir = storage_path('app/temp');
        if (!File::exists($tempDir)) {
            File::makeDirectory($tempDir, 0755, true);
        }

        $filename = 'weekly_export_' . time() . '.xlsx';
        $fullPath = $tempDir . DIRECTORY_SEPARATOR . $filename;

        // Generate Excel file
        try {
            Excel::store(new WeeklyDataExport(), 'temp/' . $filename, 'local');
            $this->info('Excel file generated successfully.');
        } catch (\Exception $e) {
            $this->error('Failed to generate Excel file: ' . $e->getMessage());
            return 1;
        }

        // Send Email
        $targetEmail = config('mail.from.address');
        if (empty($targetEmail)) {
            $this->error('Target email (MAIL_FROM_ADDRESS) is not configured.');
            return 1;
        }

        $this->info("Sending email to {$targetEmail}...");

        try {
            Mail::to($targetEmail)->send(new WeeklyBackupMail($fullPath));
            $this->info('Email sent successfully.');
        } catch (\Exception $e) {
            $this->error('Failed to send email: ' . $e->getMessage());
        }

        // Delete the temporary file
        if (File::exists($fullPath)) {
            File::delete($fullPath);
            $this->info('Temporary Excel file deleted from system.');
        }

        return 0;
    }
}
