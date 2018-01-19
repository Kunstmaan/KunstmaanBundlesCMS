<?php

namespace Kunstmaan\TranslatorBundle\Service\Translator;

use Doctrine\Common\Collections\ArrayCollection;
use Kunstmaan\TranslatorBundle\Entity\Translation;
use Kunstmaan\TranslatorBundle\Repository\TranslationRepository;
use Symfony\Bundle\FrameworkBundle\Translation\Translator as SymfonyTranslator;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class Translator
 *
 * @package Kunstmaan\TranslatorBundle\Service\Translator
 */
class Translator extends SymfonyTranslator
{
    /** @var TranslationRepository */
    private $translationRepository;

    /** @var ResourceCacher */
    private $resourceCacher;

    /** @var Request */
    protected $request;

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
     * {@inheritdoc}
     */
    public function warmUp($cacheDir)
    {
        return null;
    }

    /**
     * Add resources to the Translator from the cache
     */
    public function addResourcesFromCacher()
    {
        $resources = $this->resourceCacher->getCachedResources();

        if ($resources !== false) {
            $this->addResources($resources);

            return true;
        }

        return false;
    }

    /**
     * Add resources from the stash and cache them
     *
     * @param boolean $cacheResources cache resources after retrieving them from the stasher
     */
    public function addResourcesFromDatabaseAndCacheThem($cacheResources = true)
    {
        try {
            $resources = $this->translationRepository->getAllDomainsByLocale();
            $this->addResources($resources);

            if ($cacheResources === true) {
                $this->resourceCacher->cacheResources($resources);
            }
        } catch (\Exception $ex) {
            // don't load if the database doesn't work
        }
    }

    /**
     * Add resources to the Translator
     * Resources is an array[0] => array('name' => 'messages', 'locale' => 'en')
     * Where name is the domain of the domain
     *
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

    /**
     * @param string      $id
     * @param array       $parameters
     * @param string      $domain
     * @param string|null $locale
     *
     * @return string
     */
    public function trans($id, array $parameters = [], $domain = 'messages', $locale = null)
    {
        if (!$this->request = $this->container->get('request_stack')->getCurrentRequest()) {
            return parent::trans($id, $parameters, $domain, $locale);
        }

        $showTranslationsSource = $this->request->get('transSource');
        if ($showTranslationsSource !== null) {
            $trans = sprintf('%s (%s)', $id, $domain);
        } else {
            $trans = parent::trans($id, $parameters, $domain, $locale);
        }

        $this->profileTranslation($id, $parameters, $domain, $locale, $trans);

        return $trans;
    }

    /**
     * @param string $id
     * @param array  $parameters
     * @param string $domain
     * @param string $locale
     * @param string $trans
     */
    public function profileTranslation($id, $parameters, $domain, $locale, $trans)
    {

        if (!$this->request || $this->container->getParameter('kuma_translator.profiler') === false) {
            return;
        }

        if ($locale === null) {
            $locale = $this->request->get('_locale');
        }

        $translation = new Translation;
        $translation->setKeyword($id);
        $translation->setDomain($domain);
        $translation->setLocale($locale);
        $translation->setText($trans);

        $translationCollection = $this->request->request->get('usedTranslations');

        if (!$translationCollection instanceof ArrayCollection) {
            $translationCollection = new ArrayCollection;
        }

        $translationCollection->set($domain.$id.$locale, $translation);

        $this->request->request->set('usedTranslations', $translationCollection);

    }

    /**
     * @return TranslationRepository
     */
    public function getTranslationRepository()
    {
        return $this->translationRepository;
    }

    /**
     * @param $translationRepository
     */
    public function setTranslationRepository($translationRepository)
    {
        $this->translationRepository = $translationRepository;
    }

    /**
     * @param $resourceCacher
     */
    public function setResourceCacher($resourceCacher)
    {
        $this->resourceCacher = $resourceCacher;
    }
}
