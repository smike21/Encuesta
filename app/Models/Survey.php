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
        return $this->hasMany(Question::class)->orderBy('position');
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
