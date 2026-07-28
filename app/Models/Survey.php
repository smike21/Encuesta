<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Survey extends Model
{
    protected $fillable = [
        'user_id', 'title', 'description', 'collect_location', 'is_active',
        'welcome_title', 'welcome_text', 'thank_you_title', 'thank_you_text',
        'primary_color', 'background_color', 'text_color', 'button_text',
        'show_title', 'show_description', 'show_progress', 'show_submit_button',
    ];

    protected $casts = [
        'collect_location' => 'boolean',
        'is_active' => 'boolean',
        'show_title' => 'boolean',
        'show_description' => 'boolean',
        'show_progress' => 'boolean',
        'show_submit_button' => 'boolean',
    ];

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    /**
     * Return the questions relation as a sorted collection by `position`.
     * This sorts in PHP to avoid relying on a DB filesort/index while the
     * production DB may not have the needed index applied yet.
     *
     * Eloquent will use this accessor when `$survey->questions` is accessed.
     */
    public function getQuestionsAttribute($value)
    {
        $questions = $this->getRelationValue('questions') ?? $this->questions()->get();

        if ($questions instanceof \Illuminate\Support\Collection) {
            return $questions->sortBy('position')->values();
        }

        return $questions;
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(SurveySubmission::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignedUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'survey_user_accesses')->withTimestamps();
    }
}
