<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Answer extends Model
{
    protected $fillable = ['question_id', 'submission_id', 'value'];
    public function question(): BelongsTo { return $this->belongsTo(Question::class); }
    public function submission(): BelongsTo { return $this->belongsTo(SurveySubmission::class, 'submission_id'); }
}
