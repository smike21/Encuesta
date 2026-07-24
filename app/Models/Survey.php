<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Survey extends Model
{
    protected $fillable = ['title', 'description', 'collect_location', 'is_active', 'user_id'];

    protected function casts(): array
    {
        return ['collect_location' => 'boolean', 'is_active' => 'boolean'];
    }

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    // Select only essential columns to avoid loading large image data into memory,
    // but include `options` and `option_images` so alternatives render correctly.
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class)
            ->select([
                'id', 'survey_id', 'text', 'type', 'is_required', 'allow_multiple',
                'max_selections', 'image_size', 'options', 'option_images', 'position'
            ])
            ->orderBy('position');
    }
    public function submissions(): HasMany { return $this->hasMany(SurveySubmission::class); }
    public function surveyors(): BelongsToMany { return $this->belongsToMany(User::class, 'survey_user_accesses')->withTimestamps(); }
}
