<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Customer;

class ResetCustomerBalances extends Command
{
    protected $signature = 'customers:reset-balances';
    protected $description = 'Reset all customer balances to zero';

    public function handle()
    {
        $count = Customer::count();
        Customer::query()->update(['balance' => 0]);
        $this->info("Done. {$count} customer balances reset to 0.");
        return 0;
    }
}
