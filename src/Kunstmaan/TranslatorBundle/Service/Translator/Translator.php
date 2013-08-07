<?php

namespace Kunstmaan\TranslatorBundle\Service\Translator;

use Kunstmaan\TranslatorBundle\Entity\Translation;
use Symfony\Bundle\FrameworkBundle\Translation\Translator as SymfonyTranslator;
use Doctrine\Common\Collections\ArrayCollection;

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
     * Add resources from the database
     * So the translator knows where to look (first) for specific translations
     * This function will also look if these resources are loaded from the stash or from the cache
     */
    public function addDatabaseResources()
    {
        if ($this->addResourcesFromCacher() === false) {
            $this->addResourcesFromDatabaseAndCacheThem();
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
    public function addResourcesFromDatabaseAndCacheThem($cacheResources = true)
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

    public function trans($id, array $parameters = array(), $domain = 'messages', $locale = null)
    {

        $showTranslationsSource = $this->container->get('request')->get('transSource');
        if($showTranslationsSource !== null) {
            $trans =  sprintf('%s (%s)', $id, $domain);
        } else {
            $trans = parent::trans($id, $parameters, $domain, $locale);

        }

        $this->profileTranslation($id, $parameters, $domain, $locale, $trans);

        return $trans;
    }

    public function profileTranslation($id, $parameters, $domain, $locale, $trans)
    {

        if ($this->container->getParameter('kuma_translator.profiler') === false) {
            return;
        }

        if ($locale === null) {
            $locale = $this->container->get('request')->get('_locale');
        }

        $translation = new Translation;
        $translation->setKeyword($id);
        $translation->setDomain($domain);
        $translation->setLocale($locale);
        $translation->setText($trans);

        $translationCollection = $this->container->get('request')->request->get('usedTranslations');

        if (!$translationCollection instanceof \Doctrine\Common\Collections\ArrayCollection) {
            $translationCollection = new ArrayCollection;
        }

        $translationCollection->set($domain.$id.$locale, $translation);

        $this->container->get('request')->request->set('usedTranslations', $translationCollection);

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
