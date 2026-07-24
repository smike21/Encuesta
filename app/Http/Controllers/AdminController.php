<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\XLSX\Writer;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminController extends Controller
{
    private function guard(): void { abort_unless(Auth::check() && Auth::user()->is_admin, 403); }

    private function storeUploadedImages(array $files): array
    {
        return collect($files)->map(function ($file) {
            if (! $file) return null;
            return 'data:'.$file->getClientMimeType().';base64,'.base64_encode(file_get_contents($file->getRealPath()));
        })->filter()->values()->all();
    }
    
    public function uploadImage(Request $request)
    {
        $this->guard();
        $request->validate(['image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp'],]);
        $file = $request->file('image');
        try {
            if (Storage::disk('public')->exists('.')) {
                $path = Storage::disk('public')->putFile('uploads', $file);
                $url = Storage::disk('public')->url($path);
                return response()->json(['url' => $url]);
            }
        } catch (\Throwable $e) {
            // fall through to base64 fallback
        }

        // Fallback to base64 data URI
        $data = 'data:'.$file->getClientMimeType().';base64,'.base64_encode(file_get_contents($file->getRealPath()));
        return response()->json(['url' => $data]);
    }

    public function loginForm(): View { return view('admin.login'); }
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate(['email' => ['required', 'email'], 'password' => ['required', 'string']]);
        if (Auth::attempt($credentials + ['is_active' => true], $request->boolean('remember'))) { $request->session()->regenerate(); return redirect()->intended(Auth::user()->is_admin ? route('admin.dashboard') : route('surveyor.dashboard')); }
        return back()->withErrors(['email' => 'Las credenciales no son válidas.'])->onlyInput('email');
    }
    public function logout(Request $request): RedirectResponse { Auth::logout(); $request->session()->invalidate(); $request->session()->regenerateToken(); return redirect()->route('admin.login'); }
    public function dashboard(): View { $this->guard(); return view('admin.dashboard', ['surveys' => Survey::withCount(['questions', 'submissions'])->latest()->get()]); }
    public function create(): View { $this->guard(); return view('admin.create'); }
    public function edit(Survey $survey): View { $this->guard(); $survey->load('questions'); return view('admin.edit', compact('survey')); }
    public function store(Request $request): RedirectResponse
    {
        $this->guard();
        // Quick server-side guard against enormous multipart requests that would
        // otherwise be truncated or cause PHP/DB errors. Adjust MAX_SURVEY_UPLOAD_BYTES in .env if needed.
        $maxTotal = (int) env('MAX_SURVEY_UPLOAD_BYTES', 20 * 1024 * 1024);
        $contentLength = (int) ($request->server('CONTENT_LENGTH') ?? 0);
        if ($contentLength > 0 && $contentLength > $maxTotal) {
            return back()->withErrors(['__form' => "El formulario supera el límite de tamaño ({$maxTotal} bytes). Reduce el número/tamaño de imágenes o aumenta post_max_size en PHP."])->withInput();
        }
        $data = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
            'collect_location' => ['nullable', 'boolean'],
            'questions' => ['required', 'array', 'min:1'],
            'questions.*.text' => ['required', 'string', 'max:500'],
            'questions.*.type' => ['required', 'in:text,paragraph,multiple_choice,scale'],
            'questions.*.is_required' => ['nullable', 'boolean'],
            'questions.*.allow_multiple' => ['nullable', 'boolean'],
            'questions.*.max_selections' => ['nullable', 'integer', 'min:1'],
            'questions.*.image_size' => ['nullable', 'in:small,medium,large'],
            'questions.*.options' => ['nullable', 'array'],
            'questions.*.options.*' => ['required', 'string', 'max:255'],
            'questions.*.question_images' => ['nullable', 'array'],
            'questions.*.question_images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'questions.*.option_images' => ['nullable', 'array'],
            'questions.*.option_images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $survey = Auth::user()->surveys()->create([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'collect_location' => $request->boolean('collect_location'),
        ]);

        foreach ($data['questions'] as $position => $question) {
            $options = $question['type'] === 'multiple_choice' ? collect($question['options'] ?? [])->map(fn ($value) => trim($value))->filter()->values()->all() : null;
            $allowMultiple = $question['type'] === 'multiple_choice' && $request->boolean("questions.{$position}.allow_multiple");

            // Support async-uploaded URLs: prefer `question_images_urls` / `option_images_urls` if provided
            $questionImages = [];
            if (!empty($question['question_images_urls'] ?? null)) {
                $questionImages = array_values(array_filter($question['question_images_urls']));
            } elseif (!empty($question['question_images'] ?? null)) {
                $questionImages = $this->storeUploadedImages($question['question_images']);
            }

            $optionImages = [];
            if (!empty($question['option_images_urls'] ?? null)) {
                // maintain indexes (could be sparse)
                $optionImages = array_values(array_filter($question['option_images_urls']));
            } elseif (!empty($question['option_images'] ?? null)) {
                $optionImages = $this->storeUploadedImages($question['option_images']);
            }

            $survey->questions()->create([
                'text' => $question['text'],
                'type' => $question['type'],
                'is_required' => $request->boolean("questions.{$position}.is_required"),
                'allow_multiple' => $allowMultiple,
                'max_selections' => $allowMultiple ? max(1, min((int) ($question['max_selections'] ?? 1), count($options ?? []))) : null,
                'image_size' => $question['image_size'] ?? 'medium',
                'options' => $options,
                'question_images' => $questionImages,
                'option_images' => $optionImages,
                'position' => $position,
            ]);
        }

        return redirect()->route('admin.dashboard')->with('success', 'Encuesta creada exitosamente.');
    }
    public function update(Request $request, Survey $survey): RedirectResponse
    {
        $this->guard();
        $maxTotal = (int) env('MAX_SURVEY_UPLOAD_BYTES', 20 * 1024 * 1024);
        $contentLength = (int) ($request->server('CONTENT_LENGTH') ?? 0);
        if ($contentLength > 0 && $contentLength > $maxTotal) {
            return back()->withErrors(['__form' => "El formulario supera el límite de tamaño ({$maxTotal} bytes). Reduce el número/tamaño de imágenes o aumenta post_max_size en PHP."])->withInput();
        }
        $data = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
            'collect_location' => ['nullable', 'boolean'],
            'questions' => ['required', 'array', 'min:1'],
            'questions.*.text' => ['required', 'string', 'max:500'],
            'questions.*.type' => ['required', 'in:text,paragraph,multiple_choice,scale'],
            'questions.*.is_required' => ['nullable', 'boolean'],
            'questions.*.allow_multiple' => ['nullable', 'boolean'],
            'questions.*.max_selections' => ['nullable', 'integer', 'min:1'],
            'questions.*.image_size' => ['nullable', 'in:small,medium,large'],
            'questions.*.options' => ['nullable', 'array'],
            'questions.*.options.*' => ['required', 'string', 'max:255'],
            'questions.*.question_images' => ['nullable', 'array'],
            'questions.*.question_images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'questions.*.option_images' => ['nullable', 'array'],
            'questions.*.option_images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $survey->update([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'collect_location' => $request->boolean('collect_location'),
        ]);

        // Preserve existing question images/options when editing: map existing questions by id
        $existingById = $survey->questions()->get()->keyBy(fn ($q) => (string) $q->id)->all();

        $survey->questions()->delete();

        foreach ($data['questions'] as $position => $question) {
            $options = $question['type'] === 'multiple_choice' ? collect($question['options'] ?? [])->map(fn ($value) => trim($value))->filter()->values()->all() : null;
            $allowMultiple = $question['type'] === 'multiple_choice' && $request->boolean("questions.{$position}.allow_multiple");

            // Determine question images: prefer async-uploaded URLs, else uploaded files, else keep existing minus removals
            if (!empty($question['question_images_urls'] ?? null)) {
                $questionImages = array_values(array_filter($question['question_images_urls']));
            } elseif (!empty($question['question_images'] ?? null)) {
                $questionImages = $this->storeUploadedImages($question['question_images']);
            } else {
                $questionImages = $existingById[(string) $position]->question_images ?? [];
                // handle removals
                $removeQ = $question['remove_question_images'] ?? [];
                if (!empty($removeQ) && is_array($removeQ)) {
                    $filtered = [];
                    foreach (array_values($questionImages) as $idx => $val) {
                        if (empty($removeQ[$idx])) $filtered[] = $val;
                    }
                    $questionImages = $filtered;
                }
            }

            // Option images: accept URLs or files, otherwise keep existing minus removals
            if (!empty($question['option_images_urls'] ?? null)) {
                $optionImages = array_values(array_filter($question['option_images_urls']));
            } elseif (!empty($question['option_images'] ?? null)) {
                $optionImages = $this->storeUploadedImages($question['option_images']);
            } else {
                $optionImages = $existingById[(string) $position]->option_images ?? [];
                $removeOpt = $question['remove_option_images'] ?? [];
                if (!empty($removeOpt) && is_array($removeOpt)) {
                    $filtered = [];
                    foreach (array_values($optionImages) as $idx => $val) {
                        if (empty($removeOpt[$idx])) $filtered[] = $val;
                    }
                    $optionImages = $filtered;
                }
            }

            $survey->questions()->create([
                'text' => $question['text'],
                'type' => $question['type'],
                'is_required' => $request->boolean("questions.{$position}.is_required"),
                'allow_multiple' => $allowMultiple,
                'max_selections' => $allowMultiple ? max(1, min((int) ($question['max_selections'] ?? 1), count($options ?? []))) : null,
                'image_size' => $question['image_size'] ?? 'medium',
                'options' => $options,
                'question_images' => $questionImages,
                'option_images' => $optionImages,
                'position' => $position,
            ]);
        }

        return redirect()->route('admin.dashboard')->with('success', 'Encuesta actualizada exitosamente.');
    }
    public function results(Survey $survey): View { $this->guard(); $survey->load(['questions.answers', 'submissions']); return view('admin.results', compact('survey')); }
    public function surveyors(): View
    {
        $this->guard();
        $surveyors = User::where('is_admin', false)->with(['assignedSurveys' => fn ($query) => $query->withCount('submissions')])->withCount('assignedSurveys')->orderBy('name')->get();
        return view('admin.surveyors.index', compact('surveyors'));
    }
    public function createSurveyor(): View { $this->guard(); return view('admin.surveyors.create'); }
    public function storeSurveyor(Request $request): RedirectResponse
    {
        $this->guard();
        $data = $request->validate(['name' => ['required', 'string', 'max:255'], 'email' => ['required', 'email', 'max:255', 'unique:users,email'], 'password' => ['required', 'string', 'min:8', 'confirmed']]);
        User::create(['name' => $data['name'], 'email' => $data['email'], 'password' => $data['password'], 'is_admin' => false, 'is_active' => true]);
        return redirect()->route('admin.surveyors')->with('success', 'Cuenta de encuestador creada.');
    }
    public function surveyorAccess(User $user): View
    {
        $this->guard(); abort_if($user->is_admin, 404);
        return view('admin.surveyors.access', ['surveyor' => $user, 'surveys' => Survey::withCount('submissions')->latest()->get(), 'assignedIds' => $user->assignedSurveys()->pluck('surveys.id')->all()]);
    }
    public function updateSurveyorAccess(Request $request, User $user): RedirectResponse
    {
        $this->guard(); abort_if($user->is_admin, 404);
        $data = $request->validate(['surveys' => ['nullable', 'array'], 'surveys.*' => ['integer', 'exists:surveys,id']]);
        $user->assignedSurveys()->sync($data['surveys'] ?? []);
        return redirect()->route('admin.surveyors')->with('success', 'Permisos de resultados actualizados.');
    }
    public function toggleSurveyor(User $user): RedirectResponse
    {
        $this->guard(); abort_if($user->is_admin, 404);
        $user->update(['is_active' => ! $user->is_active]);
        return back()->with('success', $user->is_active ? 'Cuenta habilitada.' : 'Cuenta inhabilitada.');
    }
    public function export(Survey $survey): StreamedResponse
    {
        $this->guard();
        $survey->load(['questions', 'submissions.answers']);
        $headers = ['N° respuesta', 'Fecha y hora Perú', 'Zona horaria local', 'País / zona', 'Ubicación'];
        foreach ($survey->questions as $question) $headers[] = $question->text;
        $filename = 'resultados-'.str($survey->title)->slug().'.xlsx';
        return response()->streamDownload(function () use ($headers, $survey) {
            $writer = new Writer();
            $writer->openToFile('php://output');
            $writer->getCurrentSheet()->setName('Resultados');
            $writer->addRow(Row::fromValues($headers));
            foreach ($survey->submissions as $index => $submission) {
                $answerValues = $submission->answers->keyBy('question_id');
                $row = [$index + 1, $submission->created_at->copy()->timezone('America/Lima')->format('Y-m-d H:i:s'), $submission->timezone ?: 'No registrada', $submission->countryLabel(), $submission->latitude !== null ? $submission->latitude.', '.$submission->longitude : 'No disponible'];
                foreach ($survey->questions as $question) $row[] = $answerValues->get($question->id)?->value ?? '';
                $writer->addRow(Row::fromValues($row));
            }
            $writer->close();
        }, $filename, ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
    }
    public function toggle(Survey $survey): RedirectResponse { $this->guard(); $survey->update(['is_active' => ! $survey->is_active]); return back()->with('success', 'Estado de la encuesta actualizado.'); }
    public function destroy(Survey $survey): RedirectResponse { $this->guard(); $survey->delete(); return back()->with('success', 'Encuesta eliminada.'); }
    public function setup(): RedirectResponse
    {
        abort_if(User::where('is_admin', true)->exists(), 403);
        User::create(['name' => 'Administrador', 'email' => 'admin@encuestas.test', 'password' => Hash::make('admin123'), 'is_admin' => true]);
        return redirect()->route('admin.login')->with('success', 'Admin creado: admin@encuestas.test / admin123. Cámbialo después de ingresar.');
    }
}
