<?php

namespace Kunstmaan\TranslatorBundle\Service\Translator;

use Kunstmaan\TranslatorBundle\Entity\Translation;
use Symfony\Bundle\FrameworkBundle\Translation\Translator as SymfonyTranslator;

/**
 * Translator
 */
class Translator extends SymfonyTranslator
{

    private $translationRepository;

    /**
     * Resource Cacher
     * @var Kunstmaan\TranslatorBundle\Service\Translator\ResourceCacher
     */
    private $resourceCacher;

    /**
     * Add resources from the stasher into the translator resources
     * So the translator knows where to look (first) for specific translations
     * This function will also look if these resources are loaded from the stash or from the cache
     */
    public function addStasherResources()
    {
        if ($this->addResourcesFromCacher() === false) {
            $this->addResourcesFromStasherAndCache();
        }
    }

    /**
     * Add resources to the Translator from the cache
     */
    public function addResourcesFromCacher()
    {
        $resources = $this->resourceCacher->getCachedResources(false);

        if ($resources !== false) {
            $this->addResources($resources);

            return true;
        }

        return false;
    }

    /**
     * Add resources from the stash and cache them
     * @param boolean $cacheResources cache resources after retrieving them from the stasher
     */
    public function addResourcesFromStasherAndCache($cacheResources = true)
    {
        $resources = $this->translationRepository->getAllDomainsByLocale();
        $this->addResources($resources);

        if ($cacheResources === true) {
            $this->resourceCacher->cacheResources($resources);
        }

    }

    /**
     * Add resources to the Translator
     * Resources is an array[0] => array('name' => 'messages', 'locale' => 'en')
     * Where name is the domain of the domain
     * @param array $resources
     */
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

        if ($this->options['debug'] === true) {
            $this->options['cache_dir'] = null; // disable caching for debug
        }

        return parent::loadCatalogue($locale);
    }

    public function getTranslationRepository()
    {
        return $this->translationRepository;
    }

    public function setTranslationRepository($translationRepository)
    {
        $this->translationRepository = $translationRepository;
    }

    public function setResourceCacher($resourceCacher)
    {
        $this->resourceCacher = $resourceCacher;
    }
}
