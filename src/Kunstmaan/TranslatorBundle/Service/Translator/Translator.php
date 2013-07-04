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
    private $resourceCacher;

    public function addDatabaseResources()
    {
        if($this->addResourcesFromCacher() === false) {
            $this->addResourcesFromStasherAndCache();
        }
    }

    public function addResourcesFromCacher()
    {
        $resources = $this->resourceCacher->getCachedResources(false);

        if($resources !== false) {
            $this->addResources($resources);
            return true;
        }

        return false;
    }

    public function addResourcesFromStasherAndCache()
    {
        $resources = $this->stasher->getTranslationDomainsByLocale();
        $this->addResources($resources);
        $this->resourceCacher->cacheResources($resources);
    }

    public function addResources($resources)
    {
        foreach ($resources as $resource) {
            $this->addResource('database', 'DB', $resource['locale'], $resource['name']);
         }
    }

    /**
     * {@inheritdoc}
     */
    protected function loadCatalogue($locale)
    {

        if($this->options['debug'] === true) {
            $this->options['cache_dir'] = null; // disable caching for debug
        }

        return parent::loadCatalogue($locale);
    }

    public function setStasher($stasher)
    {
        $this->stasher = $stasher;
    }

    public function setResourceCacher($resourceCacher)
    {
        $this->resourceCacher = $resourceCacher;
    }
}
