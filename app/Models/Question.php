<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    protected $fillable = ['survey_id', 'text', 'type', 'is_required', 'allow_multiple', 'max_selections', 'options', 'question_images', 'option_images', 'position'];

    protected function casts(): array
    {
        return [
            'is_required' => 'boolean',
            'allow_multiple' => 'boolean',
            'max_selections' => 'integer',
            'options' => 'array',
            'question_images' => 'array',
            'option_images' => 'array',
        ];
    }

    public function survey(): BelongsTo { return $this->belongsTo(Survey::class); }
    public function answers(): HasMany { return $this->hasMany(Answer::class); }
}
