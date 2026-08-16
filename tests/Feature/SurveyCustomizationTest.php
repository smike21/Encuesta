<?php

namespace Tests\Feature;

use App\Models\Survey;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SurveyCustomizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_surveys_can_store_and_render_customization_options(): void
    {
        $user = User::factory()->create(['is_admin' => true]);

        $survey = Survey::create([
            'user_id' => $user->id,
            'title' => 'Encuesta de prueba',
            'description' => 'Descripción inicial',
            'welcome_title' => 'Bienvenido',
            'welcome_text' => 'Gracias por participar.',
            'thank_you_title' => '¡Listo!',
            'thank_you_text' => 'Tu opinión fue registrada.',
            'primary_color' => '#123456',
            'background_color' => '#f5f5f5',
            'text_color' => '#222222',
            'button_text' => 'Enviar ahora',
            'show_title' => true,
            'show_description' => true,
            'show_progress' => true,
            'is_active' => true,
        ]);

        $response = $this->get(route('surveys.show', $survey));

        $response->assertSee('Bienvenido');
        $response->assertSee('Gracias por participar.');
        $response->assertSee('Enviar ahora');
        $response->assertSee('Tu opinión fue registrada.');
    }
}
