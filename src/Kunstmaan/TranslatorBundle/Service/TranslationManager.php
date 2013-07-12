<?php

namespace Kunstmaan\TranslatorBundle\Service;

use Kunstmaan\TranslatorBundle\Model\Translation\NewTranslation;

class TranslationManager
{

    private $stasher;
    private $translationClass;
    private $translationDomainClass;
    private $newTranslationValidator;

    public function getAllDomains()
    {
        return $this->stasher->getAllDomains();
    }

    public function getFirstDefaultDomainName()
    {
        $domains = $this->getAllDomains();

        if (count($domains) <= 0) {
            return false;
        }

        $domain = reset($domains);

        return $domain->getName();
    }

    public function getTranslationGroupsByDomain($domain)
    {
        return $this->stasher->getTranslationGroupsByDomain($domain);
    }

    // TODO: validation
    public function updateTranslationsFromArray($domain, array $translations)
    {
        $translationsUpdate = $translations[$domain];
        $translationDomain = $this->stasher->getDomainByName($domain);

        $groups = $this->getTranslationGroupsByDomain($domain);

        foreach ($groups as $keyword => $group) {
            foreach ($translationsUpdate[$keyword] as $locale => $text) {

                if (!$group->hasTranslation($locale) && trim($text) != '') {
                    $newTranslation = new $this->translationClass();
                    $newTranslation->setLocale($locale);
                    $newTranslation->setText($text);
                    $newTranslation->setDomain($translationDomain);
                    $newTranslation->setKeyword($keyword);
                    $group->addTranslation($newTranslation);
                } elseif (trim($text) != '') {
                    $group->getTranslationByLocale($locale)->setText($text);
                }

            }
        }
        $this->stasher->updateTranslationGroups($groups);
    }

    /**
     * Insert translations from an array (mostly from a POST)
     * @param  array $newTranslations
     * @return void
     */
    public function newTranslationsFromArray(array $newTranslations)
    {
        foreach ($newTranslations as $newTranslation) {
            $translation = new NewTranslation;
            $translation->setKeyword($newTranslation['keyword']);
            $translation->setLocales($newTranslation['locales']);
            $translation->setDomain($newTranslation['domain']);
            $this->newTranslation($translation);
        }
    }

    /**
     * Insert one new translation in the given locales
     * @param  NewTranslation $newTranslation
     * @return void
     */
    public function newTranslation(NewTranslation $newTranslation)
    {
        $this->newTranslationValidator->validate($newTranslation);

        $translationDomain = $this->stasher->getDomainByName($newTranslation->getDomain());
        $keyword = $newTranslation->getKeyword();

        foreach ($newTranslation->getLocales() as $locale => $text) {
            $translation = new $this->translationClass();
            $translation->setLocale($locale);
            $translation->setText($text);
            $translation->setDomain($translationDomain);
            $translation->setKeyword($keyword);
            $this->stasher->persist($translation);
        }

        $this->stasher->flush();

    }

    /**
     * Reset all translation and translation domain flags to null
     * @return void
     */
    public function resetAllTranslationFlags()
    {
        $this->stasher->resetTranslationDomainFlags();
        $this->stasher->resetTranslationFlags();
    }

    public function setStasher($stasher)
    {
        $this->stasher = $stasher;
    }

    public function setTranslationClass($translationClass)
    {
        $this->translationClass = $translationClass;
    }

    public function setTranslationDomainClass($translationDomainClass)
    {
        $this->translationDomainClass = $translationDomainClass;
    }

    public function setNewTranslationValidator($newTranslationValidator)
    {
        $this->newTranslationValidator = $newTranslationValidator;
    }
}
