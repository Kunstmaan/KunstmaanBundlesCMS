<?php

namespace Kunstmaan\TranslatorBundle\Service;

use Kunstmaan\TranslatorBundle\Model\Translation\Translation;
use Kunstmaan\TranslatorBundle\Model\Translation\TranslationGroup;


class TranslationManager
{

    private $stasher;
    private $translationClass;
    private $translationDomainClass;

    public function getAllDomains()
    {
        return $this->stasher->getAllDomains();
    }

    public function getFirstDefaultDomainName()
    {
        $domains = $this->getAllDomains();

        if(count($domains) <= 0) {
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
            foreach($translationsUpdate[$keyword] as $locale => $text) {

                if(!$group->hasTranslation($locale) && trim($text) != '') {
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
}
