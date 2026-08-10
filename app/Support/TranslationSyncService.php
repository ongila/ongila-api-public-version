<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Model;

class TranslationSyncService
{
    public function sync(Model $model, array $translations): void
    {
        foreach ($translations as $translation) {
            $languageCode = strtolower($translation['language_code']);
            unset($translation['id'], $translation['object_id'], $translation['language_code']);

            $model->translations()->updateOrCreate(
                ['language_code' => $languageCode],
                $translation
            );
        }
    }
}
