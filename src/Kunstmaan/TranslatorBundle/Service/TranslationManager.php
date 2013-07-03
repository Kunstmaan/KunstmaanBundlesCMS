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

    public function getTranslationGroupsByDomain($domain)
    {
        return $this->stasher->getTranslationGroupsByDomain($domain);
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
