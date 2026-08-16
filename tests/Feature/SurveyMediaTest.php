<?php

namespace Tests\Feature;

use App\Models\Question;
use App\Models\Survey;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SurveyMediaTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_a_survey_with_question_and_option_images(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create([
            'is_admin' => true,
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.store'), [
            'title' => 'Encuesta con imágenes',
            'description' => 'Prueba',
            'questions' => [
                [
                    'text' => '¿Qué te gusta?',
                    'type' => 'multiple_choice',
                    'options' => 'Pasear, Leer, Viajar',
                    'question_images' => [
                        UploadedFile::fake()->image('question-1.png'),
                        UploadedFile::fake()->image('question-2.png'),
                    ],
                    'option_images' => [
                        UploadedFile::fake()->image('option-1.png'),
                        UploadedFile::fake()->image('option-2.png'),
                        UploadedFile::fake()->image('option-3.png'),
                    ],
                ],
            ],
        ]);

        $response->assertRedirect(route('admin.dashboard'));

        $survey = Survey::query()->firstOrFail();
        $question = $survey->questions()->firstOrFail();

        $this->assertCount(2, $question->question_images);
        $this->assertCount(3, $question->option_images);
        $this->assertSame('Pasear', $question->options[0]);

        $showResponse = $this->get(route('surveys.show', $survey));
        $showResponse->assertOk();
        $showResponse->assertSee('Pregunta');
        $showResponse->assertSee('img', false);
    }
}
