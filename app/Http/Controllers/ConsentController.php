<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ConsentController extends Controller
{
    public const POLICY_VERSION = '1.0';

    public function show(): View
    {
        return view('consent');
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
