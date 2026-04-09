<?php

namespace App\Http\Controllers;

use App\Ai\Agents\QuizMaster;
use Throwable;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        return Inertia::render('Dashboard', []);
    }

    public function aiRequestHandler(Request $request)
    {
        $prompt = $request->query('prompt', 'Geef 1 korte quizvraag over Laravel en geef daarna het antwoord.');

        try {
            $agent = new QuizMaster;
            $response = $agent->prompt(prompt: $prompt, provider: $agent->provider());

            return response()->json([
                'response' => $response->text,
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'ok' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
