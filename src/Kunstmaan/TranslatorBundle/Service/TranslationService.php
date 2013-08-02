<?php

namespace Kunstmaan\TranslatorBundle\Service;

use Kunstmaan\TranslatorBundle\Model\Translation\NewTranslation;
use Kunstmaan\TranslatorBundle\Entity\Translation;

class TranslationService
{

    private $stasher;
    private $newTranslationValidator;

    public function getAllDomains()
    {
        return $this->translationDomainRepository->findBy(array(), array('name' => 'asc'));
    }

    public function resetTranslationFlags()
    {
        $this->translationRepository->resetAllFlags();
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

        $groups = $this->translationGroupManager->getTranslationGroupsByDomain($domain);

        foreach ($groups as $keyword => $group) {
            foreach ($translationsUpdate[$keyword] as $locale => $text) {

                if (!$group->hasTranslation($locale) && trim($text) != '') {
                    $newTranslation = new Translation;
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
        $this->translationGroupManager->updateTranslationGroups($groups);
    }

    public function updateTranslationGroups(ArrayCollection $groups)
    {
        foreach ($groups as $group) {
            foreach ($group->getTranslations() as $translation) {
                $this->persist($translation);
            }
        }

        return $this->flush();
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
        $keyword = trim($newTranslation->getKeyword());

        foreach ($newTranslation->getLocales() as $locale => $text) {

            if (trim($text) == '') {
                continue;
            }

            $translation = new Translation;
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

    public function setNewTranslationValidator($newTranslationValidator)
    {
        $this->newTranslationValidator = $newTranslationValidator;
    }
}
