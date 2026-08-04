<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProfileController extends Controller
{
    public function show(): View
    {
        return view('mon-compte');
    }

    public function export(Request $request): StreamedResponse
    {
        $user = $request->user();

        $data = [
            'exported_at' => now()->toIso8601String(),
            'profile' => [
                'name' => $user->name,
                'email' => $user->email,
                'created_at' => $user->created_at?->toIso8601String(),
                'consent_given_at' => $user->consent_given_at?->toIso8601String(),
                'consent_version' => $user->consent_version,
            ],
            'accounts' => $user->accounts()->get()->toArray(),
            'categories' => $user->categories()->get()->toArray(),
            'transactions' => $user->transactions()->with(['category', 'account'])->get()->toArray(),
            'transfers' => $user->transfers()->with(['fromAccount', 'toAccount'])->get()->toArray(),
            'recurring_transactions' => $user->recurringTransactions()->with(['category', 'account'])->get()->toArray(),
        ];

        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $filename = 'mes-donnees-oeil360-'.now()->format('Y-m-d').'.json';

        return response()->streamDownload(
            fn () => print ($json),
            $filename,
            ['Content-Type' => 'application/json']
        );
    }

    public function destroy(Request $request): JsonResponse
    {
        $user = $request->user();

        // Déconnexion avant suppression pour éviter les erreurs de guard.
        // Le guard 'web' (Auth0) est explicité ; la session SSO Auth0 est terminée
        // côté navigateur via la redirection front vers /auth/logout.
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // La suppression cascade sur toutes les données financières (FK cascadeOnDelete)
        $user->delete();

        return response()->json([
            'data' => ['message' => 'Compte supprimé avec succès.'],
            'meta' => null,
            'error' => null,
        ]);
    }
}
