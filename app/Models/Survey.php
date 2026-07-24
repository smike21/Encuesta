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
