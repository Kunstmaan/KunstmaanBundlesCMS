<?php

namespace Kunstmaan\TranslatorBundle\Service\Stasher;

use Kunstmaan\TranslatorBundle\Entity\TranslationDomain;
use Kunstmaan\TranslatorBundle\Entity\Translation;

interface StasherInterface
{
    /**
     * Checks if the current domain exists in the used storage
     *
     * @return boolean
     **/
    public function doesDomainExist(TranslationDomain $domain);

    /**
     * Checks if the current translation exists
     *
     * @return boolean
     **/
    public function doesTranslationExist(Translation $translation);

    /**
     * Add a new translation
     *
     * @return Translation $translation
     **/
    public function addTranslation(Translation $translation);

    /**
     * Update a existing translation
     *
     * @return Translation $translation
     **/
    public function updateTranslation(Translation $translation);

    /**
     * Get all translations (in all languages) for a specified keyword/domain
     *
     * @return ArrayCollection of TranslationGroup objects
     **/
    public function getTranslationGroupByKeywordAndDomain($keyword, $domain);

    /**
     * Creates and returnes the translationDomain by the given name
     *
     * @return \Kunstmaan\TranslatorBundle\Entity\TranslationDomain
     **/
    public function createTranslationDomain($name);

    public function persist($entity);

    public function flush($entity = null);

}