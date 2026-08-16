<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SurveyorController extends Controller
{
    private function surveyor(): void
    {
        abort_unless(Auth::check() && ! Auth::user()->is_admin && Auth::user()->is_active, 403);
    }

    public function dashboard(): View
    {
        $this->surveyor();
        $surveys = Auth::user()->assignedSurveys()->withCount(['questions', 'submissions'])->latest()->get();
        return view('surveyor.dashboard', compact('surveys'));
    }

    public function results(Survey $survey): View|RedirectResponse
    {
        $this->surveyor();
        abort_unless(Auth::user()->assignedSurveys()->whereKey($survey->id)->exists(), 403);
        $survey->load(['questions.answers', 'submissions']);
        return view('surveyor.results', compact('survey'));
    }
}
