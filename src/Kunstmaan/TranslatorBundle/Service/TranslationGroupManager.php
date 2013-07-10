<?php

namespace Kunstmaan\TranslatorBundle\Service;

use Kunstmaan\TranslatorBundle\Model\Translation\Translation;
use Kunstmaan\TranslatorBundle\Model\Translation\TranslationGroup;

/**
 * TranslationGroupManager.
 * A bridge between a stasher
 */
class TranslationGroupManager
{

    /**
     * Stasher
     * @var Kunstmaan\TranslatorBundle\Service\Stasher\StasherInterface
     */
    private $stasher;

    private $translationClass;
    private $translationDomainClass;

    public function create($keyword, $domain)
    {
        $translationGroup = $this->newGroupInstance();
        $translationGroup->setKeyword($keyword);
        $translationGroup->setDomain($domain);

        return $translationGroup;
    }

    public function newGroupInstance($locales = array())
    {
        $translationClass = $this->translationClass;
        $translationGroup = new TranslationGroup;

        foreach ($locales as $locale) {
            $translation = new $translationClass();
            $translation->setlocale($locale);
            $translationGroup->addTranslation($translation);
        }

        return $translationGroup;
    }

    /**
     * Checks if the translation exists in the group for this locale, if not, it creates it
     */
    public function addTranslation(TranslationGroup $translationGroup, $locale, $text, $filename)
    {
        $translation = null;

        if ($translationGroup->hasTranslation($locale)) {
            return null;
        }

        $translationDomainClass = $this->translationDomainClass;

        $domain = $this->stasher->getDomainByName($translationGroup->getDomain());

        if (! $domain instanceof $translationDomainClass) {
            $domain = new $translationDomainClass;
            $domain->setName($translationGroup->getDomain());
            $this->stasher->flush($domain);
        }

        $translationClass = $this->translationClass;
        $translation = new $translationClass();
        $translation->setLocale($locale);
        $translation->setText($text);
        $translation->setDomain($domain);
        $translation->setFile($filename);
        $translation->setKeyword($translationGroup->getKeyword());
        $translation->setCreatedAt(new \DateTime());
        $translation->setUpdatedAt(new \DateTime());

        $this->stasher->persist($translation);

        return $translation;
    }

    public function updateTranslation(TranslationGroup $translationGroup, $locale, $text, $filename)
    {
        $translation = $translationGroup->getTranslationByLocale($locale);
        $translation->setText($text);
        $translation->setFile($filename);

        return $this->stasher->persist($translation);
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
