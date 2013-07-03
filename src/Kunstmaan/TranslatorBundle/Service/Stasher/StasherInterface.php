<?php

namespace Kunstmaan\TranslatorBundle\Service\Stasher;

use Kunstmaan\TranslatorBundle\Entity\TranslationDomain;
use Kunstmaan\TranslatorBundle\Entity\Translation;

interface StasherInterface
{

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