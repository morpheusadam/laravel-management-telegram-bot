<?php

namespace App\Http\Package\LaravelTranslation;

use JoeDixon\Translation\Drivers\File;
use JoeDixon\Translation\Drivers\Translation;


class TranslationFile extends File
{

    /**
     * Get all translations for a given language merged with the source language.
     *
     * @param string $language
     * @return \Illuminate\Support\Collection
     */
    public function getSourceLanguageTranslationsWith($language)
    {
        $sourceTranslations = $this->allTranslationsFor($this->sourceLanguage);
        $languageTranslations = $this->allTranslationsFor($language);

        return $sourceTranslations->map(function ($groups, $type) use ($language, $languageTranslations) {
            return $groups->map(function ($translations, $group) use ($type, $language, $languageTranslations) {
                $translations = $translations->toArray();

                $translations_copy = [];
                foreach($translations as $key => $translation) {
                	$value = [
                	    $this->sourceLanguage => $translation,
                	    $language => $languageTranslations->get($type, collect())->get($group, collect())->get($key),
                	];

                	$translations_copy[$key] = $value;
                }

                return $translations_copy;
            });
        });
    }
}
