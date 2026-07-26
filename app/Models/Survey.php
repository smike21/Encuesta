<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Survey extends Model
{
    protected $fillable = [
        'title', 'description', 'collect_location', 'is_active', 'user_id',
        'welcome_title', 'welcome_text', 'thank_you_title', 'thank_you_text',
        'primary_color', 'background_color', 'text_color', 'button_text',
        'show_title', 'show_description', 'show_progress', 'show_submit_button',
        'survey_image'
    ];

    protected function casts(): array
    {
        return [
            'collect_location' => 'boolean',
            'is_active' => 'boolean',
            'show_title' => 'boolean',
            'show_description' => 'boolean',
            'show_progress' => 'boolean',
            'show_submit_button' => 'boolean',
        ];
    }

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    // Images are stored with the question. Do not ORDER BY here: MySQL may try to
    // sort very large JSON image values and exhaust its small Railway sort buffer.
    // Questions are inserted in their displayed position, so their natural order
    // remains stable without forcing a database sort.
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class)
            ->select([
                'id', 'survey_id', 'text', 'type', 'is_required', 'allow_multiple',
                'max_selections', 'image_size', 'options', 'question_images', 'option_images', 'position'
            ]);
    }
    public function submissions(): HasMany { return $this->hasMany(SurveySubmission::class); }
    public function surveyors(): BelongsToMany { return $this->belongsToMany(User::class, 'survey_user_accesses')->withTimestamps(); }
}

