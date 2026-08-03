<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ConsentController extends Controller
{
    public const POLICY_VERSION = '1.1';

    public function show(Request $request): View
    {
        $user = $request->user();

        // Re-consentement : l'utilisateur a déjà consenti, mais à une version antérieure.
        $isUpdate = $user !== null
            && $user->consent_given_at !== null
            && $user->consent_version !== self::POLICY_VERSION;

        return view('consent', ['isUpdate' => $isUpdate]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'agree' => ['required', 'accepted'],
        ], [
            'agree.required' => 'Vous devez accepter la politique de confidentialité pour continuer.',
            'agree.accepted' => 'Vous devez accepter la politique de confidentialité pour continuer.',
        ]);

        $request->user()->update([
            'consent_given_at' => now(),
            'consent_version' => self::POLICY_VERSION,
        ]);

        return redirect()->intended('/');
    }
}
