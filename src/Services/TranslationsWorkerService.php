<?php

namespace App\Services;

use App\Repository\TranslationRepository;

class TranslationsWorkerService
{
    public function getTranslations($text)
    {
        if ($this->languages->LanguageSelected()) {
            $language_selected = $this->languages->LanguageSelected()->getLanguage();
            $function = 'get' . $language_selected;
            $find_translation_text = $this->translationRepository->findOneBy(['english' => $text]);
            if ($find_translation_text) {
                if ($find_translation_text->$function() != null) {
                    return $find_translation_text->$function();
                } else {
                    return $text;
                }
            }
        }
        return $text;
    }

    public function __construct(TranslationRepository $translationRepository, LanguagesService $languages)
    {
        $this->translationRepository = $translationRepository;
        $this->languages = $languages;
    }
}