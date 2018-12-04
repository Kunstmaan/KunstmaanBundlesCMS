<?php

namespace Kunstmaan\TranslatorBundle\Service\Translator;

use Doctrine\Common\Collections\ArrayCollection;
use Kunstmaan\TranslatorBundle\Entity\Translation;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Translation\Translator as SymfonyTranslator;

/**
 * Translator
 *
 * NEXT_MAJOR remove the $profilerEnable constructor parameter en remove the profileTranslation method.
 */
class Translator extends SymfonyTranslator
{
    /** @var bool */
    private $profilerEnabled;

    private $translationRepository;

    /**
     * Resource Cacher
     *
     * @var Kunstmaan\TranslatorBundle\Service\Translator\ResourceCacher
     */
    private $resourceCacher;

    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    protected $request;

    public function __construct(ContainerInterface $container, $formatter, $defaultLocale = null, array $loaderIds = array(), array $options = array(), $profilerEnable = false)
    {
        parent::__construct($container, $formatter, $defaultLocale, $loaderIds, $options);

        $this->profilerEnabled = $profilerEnable;
    }

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
        return;
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
     *
     * @param bool $cacheResources cache resources after retrieving them from the stasher
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

    public function trans($id, array $parameters = array(), $domain = 'messages', $locale = null)
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
     * @deprecated This method is deprecated since KunstmaanTranslatorBundle version 5.1 and will be removed in KunstmaanTranslatorBundle version 6.0
     */
    public function profileTranslation($id, $parameters, $domain, $locale, $trans)
    {
        if (!$this->request || $this->profilerEnabled === false) {
            return;
        }

        if ($locale === null) {
            $locale = $this->request->get('_locale');
        }

        $translation = new Translation();
        $translation->setKeyword($id);
        $translation->setDomain($domain);
        $translation->setLocale($locale);
        $translation->setText($trans);

        $translationCollection = $this->request->request->get('usedTranslations');

        if (!$translationCollection instanceof \Doctrine\Common\Collections\ArrayCollection) {
            $translationCollection = new ArrayCollection();
        }

        $translationCollection->set($domain . $id . $locale, $translation);

        $this->request->request->set('usedTranslations', $translationCollection);
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
