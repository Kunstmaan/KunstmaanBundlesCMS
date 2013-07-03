<?php

namespace Kunstmaan\TranslatorBundle\Service\Translator;

use Kunstmaan\TranslatorBundle\Entity\TranslationDomain;
use Kunstmaan\TranslatorBundle\Entity\Translation;
use Kunstmaan\TranslatorBundle\Model\Translation\TranslationGroup;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Translation\Loader\LoaderInterface;
use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Bundle\FrameworkBundle\Translation\Translator as SymfonyTranslator;


class Translator extends SymfonyTranslator
{

    private $stasher;

    public function addDatabaseResources()
    {
         $resources = $this->stasher->getTranslationDomainsByLocale();

         foreach ($resources as $resource) {
            //$this->addResource('database', 'DB', $resource['locale'], $resource['name']);
         }
    }

    public function setStasher($stasher)
    {
        $this->stasher = $stasher;
    }
}
