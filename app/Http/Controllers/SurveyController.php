<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Survey;
use App\Models\SurveySubmission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SurveyController extends Controller
{
    public function index(): View
    {
        return view('surveys.index', ['surveys' => Survey::withCount('questions')->where('is_active', true)->latest()->get()]);
    }

    public function show(Survey $survey): View|RedirectResponse
    {
        if (! $survey->is_active) return redirect()->route('surveys.index')->with('warning', 'Esta encuesta ya no está disponible.');
        return view('surveys.show', compact('survey'));
    }

    public function submit(Request $request, Survey $survey): RedirectResponse
    {
        abort_unless($survey->is_active, 404);
        $questions = $survey->questions;
        $rules = $questions->mapWithKeys(function ($question) {
            if ($question->type === 'multiple_choice' && $question->allow_multiple) {
                return ["answers.{$question->id}" => $question->is_required ? ['required', 'array', 'min:1'] : ['nullable', 'array']];
            }

            return ["answers.{$question->id}" => $question->is_required ? ['required', 'string'] : ['nullable', 'string']];
        })->all();
        foreach ($questions as $question) {
            if ($question->type === 'multiple_choice' && $question->allow_multiple) {
                $rules["answers.{$question->id}.*"] = ['string'];
                $rules["answers.{$question->id}"][] = 'max:'.($question->max_selections ?? 1);
            }
        }
        $data = $request->validate($rules + [
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'timezone' => ['nullable', 'string', 'max:100'],
            'locale' => ['nullable', 'string', 'max:20'],
        ]);
        $submission = SurveySubmission::create([
            'survey_id' => $survey->id,
            'ip_address' => $request->ip(),
            'latitude' => $survey->collect_location ? ($data['latitude'] ?? null) : null,
            'longitude' => $survey->collect_location ? ($data['longitude'] ?? null) : null,
            'timezone' => $data['timezone'] ?? null,
            'locale' => $data['locale'] ?? null,
        ]);
        foreach ($questions as $question) {
            $answerValue = $data['answers'][$question->id] ?? null;
            if ($question->type === 'multiple_choice' && $question->allow_multiple) {
                $answerValue = json_encode($answerValue ?? []);
            }

            Answer::create(['question_id' => $question->id, 'submission_id' => $submission->id, 'value' => $answerValue]);
        }
        return redirect()->route('surveys.index')->with('success', '¡Gracias por completar la encuesta!');
    }
}
