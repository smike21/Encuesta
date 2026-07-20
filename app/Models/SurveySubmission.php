<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SurveySubmission extends Model
{
    protected $fillable = ['survey_id', 'user_id', 'ip_address', 'latitude', 'longitude'];
    public function survey(): BelongsTo { return $this->belongsTo(Survey::class); }
    public function answers(): HasMany { return $this->hasMany(Answer::class, 'submission_id'); }
}
