<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BackupManagement extends Component
{
    public $backups = [];

    public function mount()
    {
        abort_unless(auth()->user()->can('manage backups'), 403);
        $this->loadBackups();
    }

    public function loadBackups()
    {
        $files = Storage::disk('local')->files('backups');
        $this->backups = collect($files)->map(function ($file) {
            return [
                'name' => basename($file),
                'size' => round(Storage::disk('local')->size($file) / 1024, 2) . ' KB',
                'date' => date('Y-m-d H:i:s', Storage::disk('local')->lastModified($file)),
                'path' => $file
            ];
        })->sortByDesc('date')->values()->toArray();
    }

    public function createBackup()
    {
        abort_unless(auth()->user()->can('manage backups'), 403);
        try {
            $exitCode = Artisan::call('app:backup-db');
            
            if ($exitCode === 0) {
                session()->flash('success', 'Pencadangan berhasil dibuat.');
            } else {
                session()->flash('error', 'Gagal membuat pencadangan. Periksa log atau konfigurasi server.');
            }
            
            $this->loadBackups();
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal membuat pencadangan: ' . $e->getMessage());
        }
    }

    public function download($path): StreamedResponse
    {
        abort_unless(auth()->user()->can('manage backups'), 403);
        $safePath = 'backups/' . basename($path);

        if (Storage::disk('local')->exists($safePath)) {
            return Storage::disk('local')->download($safePath);
        }

        session()->flash('error', 'File tidak ditemukan.');
        return redirect()->back();
    }

    public function delete($path)
    {
        abort_unless(auth()->user()->can('manage backups'), 403);
        $safePath = 'backups/' . basename($path);

        if (Storage::disk('local')->exists($safePath)) {
            Storage::disk('local')->delete($safePath);
            session()->flash('success', 'File pencadangan berhasil dihapus.');
            $this->loadBackups();
        } else {
            session()->flash('error', 'File tidak ditemukan.');
        }
    }

    public function render()
    {
        return view('livewire.admin.backup-management')
            ->layout('layouts.app');
    }
}
