<?php

namespace App\Services;

use App\Repository\LanguagesRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class LanguagesService
{
    public function getLanguages()
    {
        $languages = $this->languagesRepository->findBy([
            'isActive' => true
        ]);
        if($languages){
            return $languages;
        }
        else{
            return null;
        }
    }

    public function LanguageSelected()
    {
        $session = $this->requestStack->getCurrentRequest()->getSession();
        if( $session->get('selected_language')) {
            $language = $session->get('selected_language');
            return $this->languagesRepository->findOneBy(['language' => $language]);
        }
        else{
            $default_language = $this->languagesRepository->findOneBy(['language' => 'English']);
            if($default_language){
                return $default_language;
            }
            else{
                return null;
            }
        }
    }

    public function getSelectedLanguageCode()
    {
        $session = $this->requestStack->getCurrentRequest()->getSession();
        if( $session->get('selected_language')) {
            $language = $session->get('selected_language');
            if ($language == 'English') {
                return '';
            } elseif ($language == 'French') {
                return 'FR';
            } elseif ($language == 'German') {
                return 'DE';
            }
        }
        return '';
    }
public function getLanguagesByRankingOrder()
{
    return $this->languagesRepository->getLanguagesByRankingOrder();
}

    public function __construct(LanguagesRepository $languagesRepository, RequestStack $requestStack)
    {
        $this->languagesRepository = $languagesRepository;
        $this->requestStack = $requestStack;
    }
}