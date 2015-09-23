<?php

namespace Kunstmaan\MultiDomainBundle\Router;

use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Router\SlugRouter;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;

/**
 * Class DomainBasedLocaleRouter
 *
 * @package Kunstmaan\MultiDomainBundle\Router
 */
class DomainBasedLocaleRouter extends SlugRouter
{
    /**
     * Generate an url for a supplied route
     *
     * @param string $name       The path
     * @param array  $parameters The route parameters
     * @param bool   $absolute   Absolute url or not
     *
     * @return null|string
     */
    public function generate($name, $parameters = array(), $absolute = false)
    {
        if ('_slug' === $name) {
            if ($this->isMultiLanguage() && $this->isMultiDomainHost()) {
                $locale = isset($parameters['_locale']) ? $parameters['_locale'] : $this->getRequestLocale();

                $reverseLocaleMap = $this->getReverseLocaleMap();
                if (isset($reverseLocaleMap[$locale])) {
                    $parameters['_locale'] = $reverseLocaleMap[$locale];
                }
            }
        }

        return parent::generate($name, $parameters, $absolute);
    }

    /**
     * @param string $pathinfo
     *
     * @return array
     */
    public function match($pathinfo)
    {
        $urlMatcher = new UrlMatcher(
            $this->getRouteCollection(),
            $this->getContext()
        );

        $result     = $urlMatcher->match($pathinfo);
        if (!empty($result)) {
            // Remap locale for front-end requests
            if ($this->isMultiDomainHost() &&
                $this->isMultiLanguage() &&
                !$result['preview']
            ) {
                $localeMap                 = $this->getLocaleMap();
                $locale                    = $result['_locale'];
                $result['_locale']         = $localeMap[$locale];
            }

            $nodeTranslation = $this->getNodeTranslation($result);
            if (is_null($nodeTranslation)) {
                throw new ResourceNotFoundException(
                    'No page found for slug ' . $pathinfo
                );
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
        $locale  = $this->getDefaultLocale();
        if (!is_null($request)) {
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

        return $nodeTranslation;
    }

    /**
     * @return bool
     */
    private function isMultiDomainHost()
    {
        return $this->domainConfiguration->isMultiDomainHost();
    }

    /**
     * @return array
     */
    private function getLocaleMap()
    {
        return array_combine(
            $this->getFrontendLocales(),
            $this->getBackendLocales()
        );

    }

    /**
     * @return array
     */
    private function getReverseLocaleMap()
    {
        return array_combine(
            $this->getBackendLocales(),
            $this->getFrontendLocales()
        );
    }
}
