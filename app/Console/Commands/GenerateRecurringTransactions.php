<?php

namespace App\Console\Commands;

use App\Models\RecurringTransaction;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateRecurringTransactions extends Command
{
    protected $signature = 'transactions:generate-recurring';
    protected $description = 'Generate transactions for due recurring transactions';

    public function handle(): int
    {
        $today = Carbon::today();

        $dueRecurring = RecurringTransaction::where('is_active', true)
            ->whereDate('next_occurrence_date', '<=', $today)
            ->get();

        $count = 0;
        foreach ($dueRecurring as $recurring) {
            $nextOccurrenceDateString = $recurring->next_occurrence_date->toDateString();

            Transaction::create([
                'amount'                   => $recurring->amount,
                'sense'                    => $recurring->sense,
                'transaction_date'         => $nextOccurrenceDateString,
                'category_id'              => $recurring->category_id,
                'account_id'              => $recurring->account_id,
                'note'                     => $recurring->note,
                'recurring_transaction_id' => $recurring->id,
            ]);

            $recurring->update([
                'next_occurrence_date' => $this->nextDate($nextOccurrenceDateString, $recurring->frequency),
            ]);

            $count++;
        }

        $this->info("Generated {$count} transaction(s).");
        return Command::SUCCESS;
    }

    private function nextDate(string $currentDate, string $frequency): string
    {
        $date = Carbon::parse($currentDate);

        return match ($frequency) {
            'daily'   => $date->addDay()->toDateString(),
            'weekly'  => $date->addWeek()->toDateString(),
            'monthly' => $date->addMonth()->toDateString(),
            'yearly'  => $date->addYear()->toDateString(),
        };
    }
}
