<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Category;
use App\Models\User;

class UserBootstrapService
{
    private const DEFAULT_CATEGORIES = [
        ['name' => 'Salaire',        'type' => 'income'],
        ['name' => 'Freelance',      'type' => 'income'],
        ['name' => 'Autres revenus', 'type' => 'income'],
        ['name' => 'Alimentation',   'type' => 'expense'],
        ['name' => 'Transport',      'type' => 'expense'],
        ['name' => 'Logement',       'type' => 'expense'],
        ['name' => 'Santé',          'type' => 'expense'],
        ['name' => 'Loisirs',        'type' => 'expense'],
        ['name' => 'Autres dépenses', 'type' => 'expense'],
    ];

    private const DEFAULT_ACCOUNTS = [
        ['name' => 'Caisse',       'type' => 'cash'],
        ['name' => 'Mobile Money', 'type' => 'mobile_money'],
        ['name' => 'Banque',       'type' => 'bank'],
    ];

    public function bootstrapNewUser(User $user): void
    {
        foreach (self::DEFAULT_CATEGORIES as $data) {
            Category::create(['user_id' => $user->id] + $data);
        }

        foreach (self::DEFAULT_ACCOUNTS as $data) {
            Account::create(['user_id' => $user->id] + $data);
        }
    }
}
