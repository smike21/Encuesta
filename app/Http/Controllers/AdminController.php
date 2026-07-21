<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminController extends Controller
{
    private function guard(): void { abort_unless(Auth::check() && Auth::user()->is_admin, 403); }
    public function loginForm(): View { return view('admin.login'); }
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate(['email' => ['required', 'email'], 'password' => ['required', 'string']]);
        if (Auth::attempt($credentials, $request->boolean('remember'))) { $request->session()->regenerate(); return redirect()->intended(route('admin.dashboard')); }
        return back()->withErrors(['email' => 'Las credenciales no son válidas.'])->onlyInput('email');
    }
    public function logout(Request $request): RedirectResponse { Auth::logout(); $request->session()->invalidate(); $request->session()->regenerateToken(); return redirect()->route('admin.login'); }
    public function dashboard(): View { $this->guard(); return view('admin.dashboard', ['surveys' => Survey::withCount(['questions', 'submissions'])->latest()->get()]); }
    public function create(): View { $this->guard(); return view('admin.create'); }
    public function store(Request $request): RedirectResponse
    {
        $this->guard();
        $data = $request->validate(['title' => ['required', 'string', 'max:200'], 'description' => ['nullable', 'string'], 'collect_location' => ['nullable', 'boolean'], 'questions' => ['required', 'array', 'min:1'], 'questions.*.text' => ['required', 'string', 'max:500'], 'questions.*.type' => ['required', 'in:text,paragraph,multiple_choice,scale'], 'questions.*.options' => ['nullable', 'string']]);
        $survey = Auth::user()->surveys()->create(['title' => $data['title'], 'description' => $data['description'] ?? null, 'collect_location' => $request->boolean('collect_location')]);
        foreach ($data['questions'] as $position => $question) $survey->questions()->create(['text' => $question['text'], 'type' => $question['type'], 'options' => $question['type'] === 'multiple_choice' ? collect(explode(',', $question['options'] ?? ''))->map(fn ($v) => trim($v))->filter()->values()->all() : null, 'position' => $position]);
        return redirect()->route('admin.dashboard')->with('success', 'Encuesta creada exitosamente.');
    }
    public function results(Survey $survey): View { $this->guard(); $survey->load(['questions.answers', 'submissions']); return view('admin.results', compact('survey')); }
    public function export(Survey $survey): StreamedResponse
    {
        $this->guard();
        $survey->load(['questions', 'submissions.answers']);
        $book = new Spreadsheet();
        $sheet = $book->getActiveSheet();
        $sheet->setTitle('Resultados');
        $headers = ['N° respuesta', 'Fecha y hora Perú', 'Zona horaria local', 'País / zona', 'Ubicación'];
        foreach ($survey->questions as $question) $headers[] = $question->text;
        $sheet->fromArray($headers, null, 'A1');
        $lastColumn = Coordinate::stringFromColumnIndex(count($headers));
        $sheet->getStyle('A1:'.$lastColumn.'1')->getFont()->setBold(true);
        foreach ($survey->submissions as $index => $submission) {
            $answerValues = $submission->answers->keyBy('question_id');
            $row = [
                $index + 1,
                $submission->created_at->copy()->timezone('America/Lima')->format('Y-m-d H:i:s'),
                $submission->timezone ?: 'No registrada',
                $submission->countryLabel(),
                $submission->latitude !== null ? $submission->latitude.', '.$submission->longitude : 'No disponible',
            ];
            foreach ($survey->questions as $question) $row[] = $answerValues->get($question->id)?->value ?? '';
            $sheet->fromArray($row, null, 'A'.($index + 2));
        }
        for ($column = 1; $column <= count($headers); $column++) $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($column))->setAutoSize(true);
        $filename = 'resultados-'.str($survey->title)->slug().'.xlsx';
        return response()->streamDownload(function () use ($book) { (new Xlsx($book))->save('php://output'); }, $filename, ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
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
