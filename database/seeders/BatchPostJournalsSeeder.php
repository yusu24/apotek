<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sale;
use App\Models\JournalEntry;
use App\Models\Account;
use App\Models\JournalEntryLine;
use Illuminate\Support\Facades\DB;

class BatchPostJournalsSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info("🔄 Batch posting Nov-Dec sale journals...");
        
        // Get accounts
        $cashAccount = Account::where('code', '1-1100')->first();
        $salesAccount = Account::where('code', '4-1000')->first();
        $cogsAccount = Account::where('code', '5-1000')->first();
        $inventoryAccount = Account::where('code', '1-1400')->first();
        
        if (!$cashAccount || !$salesAccount || !$cogsAccount || !$inventoryAccount) {
            $this->command->error("❌ Required accounts not found!");
            return;
        }
        
        // Get Nov-Dec sales without journals
        $sales = Sale::whereBetween('date', ['2025-11-01', '2025-12-31'])
            ->where('status', 'completed')
            ->get();
            
        $existingJournalSaleIds = JournalEntry::where('source', 'sale')
            ->pluck('source_id')
            ->toArray();
        
        $salesWithoutJournals = $sales->filter(function($sale) use ($existingJournalSaleIds) {
            return !in_array($sale->id, $existingJournalSaleIds);
        });
        
        $this->command->info("Found {$salesWithoutJournals->count()} sales to process");
        
        $posted = 0;
        foreach ($salesWithoutJournals as $sale) {
            DB::beginTransaction();
            try {
                // Calculate COGS
                $cogsTotal = 0;
               foreach ($sale->saleItems as $item) {
                    if ($item->batch) {
                        $cogsTotal += $item->quantity * $item->batch->buy_price;
                    }
                }
                
                // Create journal entry
                $entry = JournalEntry::create([
                    'entry_number' => 'JE-SALE-' . $sale->invoice_no,
                    'date' => $sale->date,
                    'description' => 'Penjualan - ' . $sale->invoice_no,
                    'source' => 'sale',
                    'source_id' => $sale->id,
                    'user_id' => $sale->user_id,
                    'created_at' => $sale->date,
                ]);
                
                // Dr. Kas
                JournalEntryLine::create([
                    'journal_entry_id' => $entry->id,
                    'account_id' => $cashAccount->id,
                    'debit' => $sale->grand_total,
                    'credit' => 0,
                    'notes' => 'Penjualan ' . $sale->invoice_no,
                ]);
                
                // Cr. Penjualan
                JournalEntryLine::create([
                    'journal_entry_id' => $entry->id,
                    'account_id' => $salesAccount->id,
                    'debit' => 0,
                    'credit' => $sale->grand_total,
                    'notes' => 'Penjualan ' . $sale->invoice_no,
                ]);
                
                // COGS entries if > 0
                if ($cogsTotal > 0) {
                    // Dr. COGS
                    JournalEntryLine::create([
                        'journal_entry_id' => $entry->id,
                        'account_id' => $cogsAccount->id,
                        'debit' => $cogsTotal,
                        'credit' => 0,
                        'notes' => 'COGS - ' . $sale->invoice_no,
                    ]);
                    
                    // Cr. Persediaan
                    JournalEntryLine::create([
                        'journal_entry_id' => $entry->id,
                        'account_id' => $inventoryAccount->id,
                        'debit' => 0,
                        'credit' => $cogsTotal,
                        'notes' => 'Penjualan Persediaan - ' . $sale->invoice_no,
                    ]);
                }
                
                // Post the entry
                $entry->update(['is_posted' => true]);
                
                // Update account balances
                foreach ($entry->lines as $line) {
                    $account = Account::find($line->account_id);
                    if ($account) {
                        if (in_array($account->type, ['asset', 'expense'])) {
                            $account->increment('balance', $line->debit);
                            $account->decrement('balance', $line->credit);
                        } else {
                            $account->increment('balance', $line->credit);
                            $account->decrement('balance', $line->debit);
                        }
                    }
                }
                
                DB::commit();
                $posted++;
                
                if ($posted % 50 == 0) {
                    $this->command->info("  ✓ Processed {$posted} journals...");
                }
            } catch (\Exception $e) {
                DB::rollBack();
                $this->command->error("Failed for Sale #{$sale->id}: " . $e->getMessage());
            }
        }
        
        $this->command->info("✅ Successfully posted {$posted} journal entries!");
    }
}
