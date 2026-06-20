<?php
namespace Database\Seeders;

use App\Models\Account;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            ['name' => 'Espèces',           'type' => 'cash',         'initial_balance' => 0],
            ['name' => 'MTN Mobile Money',   'type' => 'mobile_money', 'initial_balance' => 0],
            ['name' => 'Compte bancaire',    'type' => 'bank',         'initial_balance' => 0],
        ];

        foreach ($accounts as $data) {
            Account::firstOrCreate(['name' => $data['name']], $data);
        }
    }
}
