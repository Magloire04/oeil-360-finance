<?php

namespace App\Http\Controllers;

use App\Exports\StatementExport;
use App\Services\StatementService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\Response;

class StatementController extends Controller
{
    public function __construct(private StatementService $statementService) {}

    /**
     * Génère et télécharge le relevé financier de l'utilisateur courant, au format
     * PDF (défaut) ou Excel, sur une période optionnelle (dates absentes = tout).
     */
    public function download(Request $request): Response
    {
        // Champs de dates vides du formulaire (start=&end=) normalisés en null
        // avant validation, pour que « laisser vide = tout » ne déclenche pas la règle date.
        $input = [
            'format' => $request->query('format') ?: 'pdf',
            'start' => $request->query('start') ?: null,
            'end' => $request->query('end') ?: null,
        ];

        // Route web (téléchargement) : on renvoie un 422 explicite en cas d'entrée
        // invalide, plutôt que la redirection par défaut de $request->validate().
        $validator = Validator::make($input, [
            'format' => [Rule::in(['pdf', 'excel'])],
            'start' => ['nullable', 'date'],
            'end' => ['nullable', 'date', 'after_or_equal:start'],
        ]);

        if ($validator->fails()) {
            abort(422, $validator->errors()->first());
        }

        ['format' => $format, 'start' => $start, 'end' => $end] = $input;

        // Périmètre strict : uniquement les données de l'utilisateur authentifié.
        $report = $this->statementService->buildStatement($request->user(), $start, $end);

        $filename = 'releve-oeil360-'.now()->format('Y-m-d');

        if ($format === 'excel') {
            return Excel::download(new StatementExport($report), $filename.'.xlsx');
        }

        return Pdf::loadView('exports.statement', ['report' => $report])
            ->setPaper('a4')
            ->download($filename.'.pdf');
    }
}
