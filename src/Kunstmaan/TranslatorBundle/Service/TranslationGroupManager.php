<?php

namespace Kunstmaan\TranslatorBundle\Service;

use Kunstmaan\TranslatorBundle\Model\Translation\Translation;
use Kunstmaan\TranslatorBundle\Model\Translation\TranslationGroup;

/**
 * TranslationGroupManager
 * For managing/creating TranslationGroup objects
 */
class TranslationGroupManager
{

    private $translationRepository;

    /**
     * Get an empty TranslationGroup with the given keyword and domain
     */
    public function create($keyword, $domain)
    {
        $translationGroup = $this->newGroupInstance();
        $translationGroup->setKeyword($keyword);
        $translationGroup->setDomain($domain);
        $translationGroup->setId($this->translationRepository->getUniqueTranslationId());

        return $translationGroup;
    }

    /**
     * Create new TranslationGroup instance (with the given locales of any are set)
     * @param  array            $locales
     * @return TranslationGroup
     */
    public function newGroupInstance($locales = array())
    {
        $translationGroup = new TranslationGroup;

        foreach ($locales as $locale) {
            $translation = new \Kunstmaan\TranslatorBundle\Entity\Translation();
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

        $translation = new \Kunstmaan\TranslatorBundle\Entity\Translation;
        $translation->setLocale($locale);
        $translation->setText($text);
        $translation->setDomain($translationGroup->getDomain());
        $translation->setFile($filename);
        $translation->setKeyword($translationGroup->getKeyword());
        $translation->setCreatedAt(new \DateTime());
        $translation->setUpdatedAt(new \DateTime());
        $translation->setTranslationId($translationGroup->getId());

        $this->translationRepository->persist($translation);

        return $translation;
    }

    public function updateTranslation(TranslationGroup $translationGroup, $locale, $text, $filename)
    {
        $translation = $translationGroup->getTranslationByLocale($locale);
        $translation->setText($text);
        $translation->setFile($filename);

        return $this->translationRepository->persist($translation);
    }

    /**
     * Returns a TranslationGroup with the given keyword and domain, and fills in the translations
     */
    public function getTranslationGroupByKeywordAndDomain($keyword, $domain)
    {
        $translations = $this->translationRepository->findBy(array('keyword' => $keyword, 'domain' => $domain));
        $translationGroup = new TranslationGroup;
        $translationGroup->setDomain($domain);
        $translationGroup->setKeyword($keyword);
        if (empty($translations)) {
            $translationGroup->setId($this->translationRepository->getUniqueTranslationId());
        } else {
            $translationGroup->setId($translations[0]->getTranslationId());
        }
        $translationGroup->setTranslations($translations);

        return $translationGroup;
    }

    public function getTranslationGroupsByDomain($domain)
    {
        return array();
    }

    public function setTranslationRepository($translationRepository)
    {
        $this->translationRepository = $translationRepository;
    }

}
