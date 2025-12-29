<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sale;
use App\Models\JournalEntry;
use App\Services\AccountingService;

class PostMissingJournalsSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info("🔄 Posting missing journal entries...");
        
        $accountingService = new AccountingService();
        
        // Get all completed sales
        $sales = Sale::where('status', 'completed')->get();
        
        // Get existing journal entry sale IDs
        $existingJournalSaleIds = JournalEntry::where('source', 'sale')
            ->pluck('source_id')
            ->toArray();
        
        // Filter sales without journals
        $salesWithoutJournals = $sales->filter(function($sale) use ($existingJournalSaleIds) {
            return !in_array($sale->id, $existingJournalSaleIds);
        });
        
        $this->command->info("Found {$salesWithoutJournals->count()} sales without journals");
        
        $posted = 0;
        foreach ($salesWithoutJournals as $sale) {
            try {
                $accountingService->postSaleJournal($sale->id);
                
                // Update journal created_at to match sale date
                $entry = JournalEntry::where('source', 'sale')
                    ->where('source_id', $sale->id)
                    ->first();
                    
                if ($entry) {
                    $entry->update(['created_at' => $sale->date]);
                    $posted++;
                }
            } catch (\Exception $e) {
                $this->command->error("Failed for Sale #{$sale->id}: " . $e->getMessage());
            }
        }
        
        $this->command->info("✅ Posted {$posted} journal entries!");
    }
}
