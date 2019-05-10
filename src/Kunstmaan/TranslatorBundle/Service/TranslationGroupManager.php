<?php

namespace Kunstmaan\TranslatorBundle\Service;

use Kunstmaan\TranslatorBundle\Model\Translation\Translation;
use Kunstmaan\TranslatorBundle\Model\Translation\TranslationGroup;
use Kunstmaan\TranslatorBundle\Repository\TranslationRepository;

/**
 * TranslationGroupManager
 * For managing/creating TranslationGroup objects
 */
class TranslationGroupManager
{
    /** @var TranslationRepository */
    private $translationRepository;

    /** @var array */
    private $dbCopy;

    /** @var int */
    private $maxId = 1;

    public function __construct(TranslationRepository $translationRepository)
    {
        $this->translationRepository = $translationRepository;
    }

    /**
     * Checks if the translation exists in the group for this locale, if not, it creates it
     */
    public function addTranslation(TranslationGroup $translationGroup, $locale, $text, $filename)
    {
        $translation = new \Kunstmaan\TranslatorBundle\Entity\Translation();
        $translation->setLocale($locale);
        $translation->setText($text);
        $translation->setDomain($translationGroup->getDomain());
        $translation->setFile($filename);
        $translation->setKeyword($translationGroup->getKeyword());
        $translation->setCreatedAt(new \DateTime());
        $translation->setUpdatedAt(new \DateTime());
        $translation->setTranslationId($translationGroup->getId());

        $this->translationRepository->persist($translation);
        $this->dbCopy[] = $translation;
    }

    public function updateTranslation(TranslationGroup $translationGroup, $locale, $text, $filename)
    {
        $translation = $translationGroup->getTranslationByLocale($locale);
        $translation->setText($text);
        $translation->setFile($filename);

        $this->translationRepository->persist($translation);
    }

    public function pullDBInMemory()
    {
        $this->dbCopy = $this->translationRepository->findAll();
        $this->maxId = $this->translationRepository->getUniqueTranslationId();
    }

    public function flushAndClearDBFromMemory()
    {
        $this->translationRepository->flush();
        unset($this->dbCopy);
        $this->maxId = 1;
    }

    /**
     * Returns a TranslationGroup with the given keyword and domain, and fills in the translations
     */
    public function getTranslationGroupByKeywordAndDomain($keyword, $domain)
    {
        $translations = $this->findTranslations($keyword, $domain);
        $translationGroup = new TranslationGroup();
        $translationGroup->setDomain($domain);
        $translationGroup->setKeyword($keyword);
        if (empty($translations)) {
            $translationGroup->setId($this->maxId);
            ++$this->maxId;
        } else {
            $translationGroup->setId($translations[0]->getTranslationId());
        }
        $translationGroup->setTranslations($translations);

        return $translationGroup;
    }

    private function findTranslations($keyword, $domain)
    {
        $result = [];

        /** @var \Kunstmaan\TranslatorBundle\Entity\Translation $translation */
        foreach ($this->dbCopy as $translation) {
            if ($translation->getKeyword() === $keyword && $translation->getDomain() === $domain) {
                $result[] = $translation;
            }
        }

        return $result;
    }
}
