<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class MakeAdmin extends Command
{
    protected $signature = 'oeil360:make-admin
                            {email : Email du compte à promouvoir}
                            {--revoke : Retire les droits admin au lieu de les accorder}';

    protected $description = "Accorde (ou retire avec --revoke) les droits d'administration à un compte utilisateur.";

    public function handle(): int
    {
        $email = (string) $this->argument('email');
        $revoke = (bool) $this->option('revoke');

        $user = User::where('email', $email)->first();

        if ($user === null) {
            $this->error("Aucun utilisateur trouvé pour l'email : {$email}");

            return self::FAILURE;
        }

        $user->is_admin = ! $revoke;
        $user->save();

        $this->info($revoke
            ? "Droits admin retirés à {$email}."
            : "Droits admin accordés à {$email}.");

        return self::SUCCESS;
    }
}
