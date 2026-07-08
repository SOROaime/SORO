<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\PaymentOtpNotification;
use Illuminate\Console\Command;

class TestPaymentOtp extends Command
{
    protected $signature   = 'test:otp {email?}';
    protected $description = 'Envoie un OTP de test à l\'adresse email indiquée (ou au premier utilisateur)';

    public function handle(): int
    {
        $email = $this->argument('email');
        $user  = $email
            ? User::where('email', $email)->first()
            : User::first();

        if (!$user) {
            $this->error('Aucun utilisateur trouvé.');
            return self::FAILURE;
        }

        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $this->info("Envoi du code OTP [{$otp}] à {$user->email}...");

        $user->notify(new PaymentOtpNotification($otp, '25 000 FCFA'));

        $this->info('Email envoyé avec succès.');
        $this->line("  Destinataire : {$user->email}");
        $this->line("  Code OTP     : {$otp}");

        return self::SUCCESS;
    }
}
