<?php

namespace Kunstmaan\MultiDomainBundle\Router;

use Kunstmaan\NodeBundle\Controller\SlugController;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Router\SlugRouter;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class DomainBasedLocaleRouter extends SlugRouter
{
    /** @var RouteCollection */
    protected $routeCollectionMultiLanguage;

    /**
     * @var array|null
     */
    private $otherSite;

    /**
     * @var array
     */
    private $cachedNodeTranslations = [];

    /**
     * Generate an url for a supplied route
     *
     * @param string   $name          The path
     * @param array    $parameters    The route parameters
     * @param int|bool $referenceType The type of reference to be generated (one of the UrlGeneratorInterface constants)
     */
    public function generate($name, $parameters = [], $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH): string
    {
        if ('_slug' === $name && $this->isMultiLanguage() && $this->isMultiDomainHost()) {
            $locale = isset($parameters['_locale']) ? $parameters['_locale'] : $this->getRequestLocale();

            $reverseLocaleMap = $this->getReverseLocaleMap();
            if (isset($reverseLocaleMap[$locale])) {
                $parameters['_locale'] = $reverseLocaleMap[$locale];
            }
        }

        if (isset($parameters['otherSite'])) {
            $this->otherSite = $this->domainConfiguration->getFullHostById($parameters['otherSite']);
        } else {
            $this->otherSite = null;
        }

        $this->urlGenerator = new UrlGenerator(
            $this->getRouteCollection(),
            $this->getContext()
        );

        if (isset($parameters['otherSite'])) {
            unset($parameters['otherSite']);
        }

        if (isset($parameters['_nodeTranslation'])) {
            unset($parameters['_nodeTranslation']);
        }

        return $this->urlGenerator->generate($name, $parameters, $referenceType);
    }

    /**
     * @param string $pathinfo
     */
    public function match($pathinfo): array
    {
        $urlMatcher = new UrlMatcher(
            $this->getRouteCollection(),
            $this->getContext()
        );

        $result = $urlMatcher->match($pathinfo);
        if (!empty($result)) {
            // Remap locale for front-end requests
            if ($this->isMultiDomainHost()
                && $this->isMultiLanguage()
                && !$result['preview']
            ) {
                $localeMap = $this->getLocaleMap();
                $locale = $result['_locale'];
                $result['_locale'] = $localeMap[$locale];
            }

            $nodeTranslation = $this->getNodeTranslation($result);
            if (\is_null($nodeTranslation)) {
                throw new ResourceNotFoundException('No page found for slug ' . $pathinfo);
            }
            $result['_nodeTranslation'] = $nodeTranslation;
        }

        return $result;
    }

    /**
     * @return string
     */
    protected function getRequestLocale()
    {
        $request = $this->getMasterRequest();
        $locale = $this->getDefaultLocale();
        if (!\is_null($request)) {
            $locale = $request->getLocale();
        }

        return $locale;
    }

    /**
     * @param array $matchResult
     *
     * @return \Kunstmaan\NodeBundle\Entity\NodeTranslation
     */
    protected function getNodeTranslation($matchResult)
    {
        $key = $matchResult['_controller'] . $matchResult['url'] . $matchResult['_locale'] . $matchResult['_route'];
        if (!isset($this->cachedNodeTranslations[$key])) {
            $rootNode = $this->domainConfiguration->getRootNode();

            // Lookup node translation
            $nodeTranslationRepo = $this->getNodeTranslationRepository();

            /* @var NodeTranslation $nodeTranslation */
            $nodeTranslation = $nodeTranslationRepo->getNodeTranslationForUrl(
                $matchResult['url'],
                $matchResult['_locale'],
                false,
                null,
                $rootNode
            );
            $this->cachedNodeTranslations[$key] = $nodeTranslation;
        }

        return $this->cachedNodeTranslations[$key];
    }

    private function isMultiDomainHost(): bool
    {
        return $this->domainConfiguration->isMultiDomainHost();
    }

    private function getHostLocales()
    {
        $host = null !== $this->otherSite ? $this->otherSite['host'] : null;

        return $this->domainConfiguration->getFrontendLocales($host);
    }

    private function getLocaleMap(): array
    {
        return array_combine(
            $this->getFrontendLocales(),
            $this->getBackendLocales()
        );
    }

    private function getReverseLocaleMap(): array
    {
        return array_combine(
            $this->getBackendLocales(),
            $this->getFrontendLocales()
        );
    }

    /**
     * Getter for routeCollection
     *
     * Override slug router
     */
    public function getRouteCollection(): RouteCollection
    {
        if (($this->otherSite && $this->isMultiLanguage($this->otherSite['host'])) || (!$this->otherSite && $this->isMultiLanguage())) {
            if (!$this->routeCollectionMultiLanguage) {
                $this->routeCollectionMultiLanguage = new RouteCollection();

                $this->addMultiLangPreviewRoute();
                $this->addMultiLangSlugRoute();
            }

            return $this->routeCollectionMultiLanguage;
        }

        if (!$this->routeCollection) {
            $this->routeCollection = new RouteCollection();

            $this->addPreviewRoute();
            $this->addSlugRoute();
        }

        return $this->routeCollection;
    }

    /**
     * Add the slug route to the route collection
     */
    protected function addSlugRoute()
    {
        $routeParameters = $this->getSlugRouteParameters();
        $this->addRoute('_slug', $routeParameters);
    }

    /**
     * Add the slug route to the route collection
     */
    protected function addMultiLangPreviewRoute()
    {
        $routeParameters = $this->getPreviewRouteParameters();
        $this->addMultiLangRoute('_slug_preview', $routeParameters);
    }

    /**
     * Add the slug route to the route collection multilanguage
     */
    protected function addMultiLangSlugRoute()
    {
        $routeParameters = $this->getSlugRouteParameters();
        $this->addMultiLangRoute('_slug', $routeParameters);
    }

    /**
     * @param string $name
     */
    protected function addMultiLangRoute($name, array $parameters = [])
    {
        $this->routeCollectionMultiLanguage->add(
            $name,
            new Route(
                $parameters['path'],
                $parameters['defaults'],
                $parameters['requirements']
            )
        );
    }

    /**
     * Return slug route parameters
     *
     * @return array
     */
    protected function getSlugRouteParameters()
    {
        $slugPath = '/{url}';
        $slugDefaults = [
            '_controller' => SlugController::class . '::slugAction',
            'preview' => false,
            'url' => '',
            '_locale' => $this->getDefaultLocale(),
        ];
        $slugRequirements = [
            'url' => $this->getSlugPattern(),
        ];

        $locales = [];

        // If other site provided and multilingual, get the locales from the host config.
        if ($this->otherSite && $this->isMultiLanguage($this->otherSite['host'])) {
            $locales = $this->getHostLocales();
        } elseif ($this->isMultiLanguage() && !$this->otherSite) {
            $locales = $this->getFrontendLocales();
        }

        // Make locale availables for the slug routes.
        if (!empty($locales)) {
            $slugPath = '/{_locale}' . $slugPath;
            unset($slugDefaults['_locale']);
            $slugRequirements['_locale'] = $this->getEscapedLocales($this->getHostLocales());
        }

        return [
            'path' => $slugPath,
            'defaults' => $slugDefaults,
            'requirements' => $slugRequirements,
        ];
    }
}
