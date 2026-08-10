<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

trait HasTranslations
{
    abstract public function translationModel(): string;

    public function translations(): HasMany
    {
        return $this->hasMany($this->translationModel(), 'object_id');
    }

    public function translation(): HasOne
    {
        return $this->hasOne($this->translationModel(), 'object_id')
            ->where('language_code', $this->requestedLanguage());
    }

    public function translated(string $field = 'name', $default = null)
    {
        $language = $this->requestedLanguage();
        $translations = $this->relationLoaded('translations')
            ? $this->translations
            : $this->translations()->get();

        $translation = $translations->firstWhere('language_code', $language)
            ?? $translations->firstWhere('language_code', config('app.fallback_locale'))
            ?? $translations->first();

        return $translation?->{$field} ?? $default;
    }

    private function requestedLanguage(): string
    {
        return strtolower((string) request()->query('language_code', app()->getLocale()));
    }
}
