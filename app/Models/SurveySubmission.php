<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SurveySubmission extends Model
{
    protected $fillable = ['survey_id', 'user_id', 'ip_address', 'latitude', 'longitude', 'timezone', 'locale'];
    public function survey(): BelongsTo { return $this->belongsTo(Survey::class); }
    public function answers(): HasMany { return $this->hasMany(Answer::class, 'submission_id'); }

    public function countryLabel(): string
    {
        return match ($this->timezone) {
            'America/Lima' => 'Perú',
            'America/Bogota' => 'Colombia',
            'America/Guayaquil' => 'Ecuador',
            'America/La_Paz' => 'Bolivia',
            'America/Santiago' => 'Chile',
            'America/Argentina/Buenos_Aires' => 'Argentina',
            'America/Asuncion' => 'Paraguay',
            'America/Montevideo' => 'Uruguay',
            'America/Caracas' => 'Venezuela',
            'America/Mexico_City' => 'México',
            'America/Guatemala' => 'Guatemala',
            'America/Costa_Rica' => 'Costa Rica',
            'America/Panama' => 'Panamá',
            'America/El_Salvador' => 'El Salvador',
            'America/Santo_Domingo' => 'República Dominicana',
            default => $this->timezone ?: 'No registrada',
        };
    }
}
