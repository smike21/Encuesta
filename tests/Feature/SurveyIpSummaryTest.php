<?php

namespace Tests\Feature;

use App\Models\Survey;
use App\Models\SurveySubmission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SurveyIpSummaryTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_ip_summary_and_counts_for_a_survey(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $survey = Survey::create([
            'user_id' => $admin->id,
            'title' => 'Encuesta de prueba IPs',
            'description' => 'Participación con geolocalización',
            'collect_location' => true,
            'is_active' => true,
        ]);

        SurveySubmission::create([
            'survey_id' => $survey->id,
            'user_id' => null,
            'ip_address' => '203.0.113.10',
            'latitude' => -12.046,
            'longitude' => -77.042,
            'timezone' => 'America/Lima',
        ]);

        SurveySubmission::create([
            'survey_id' => $survey->id,
            'user_id' => null,
            'ip_address' => '203.0.113.10',
            'latitude' => -12.047,
            'longitude' => -77.043,
            'timezone' => 'America/Lima',
        ]);

        SurveySubmission::create([
            'survey_id' => $survey->id,
            'user_id' => null,
            'ip_address' => '198.51.100.88',
            'latitude' => -12.048,
            'longitude' => -77.044,
            'timezone' => 'America/Lima',
        ]);

        $response = $this->actingAs($admin, 'web')->get(route('admin.results', $survey));

        $response->assertOk();
        $response->assertSee('Ver IPs repetidas');

        $this->getJson(route('admin.ip_summary', $survey))
            ->assertOk()
            ->assertJsonPath('0.ip_address', '203.0.113.10')
            ->assertJsonPath('0.count', 2)
            ->assertJsonPath('1.ip_address', '198.51.100.88')
            ->assertJsonPath('1.count', 1);
    }
}
