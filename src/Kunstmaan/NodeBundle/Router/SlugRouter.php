<?php

namespace Kunstmaan\NodeBundle\Router;

use Kunstmaan\AdminBundle\Helper\DomainConfigurationInterface;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Repository\NodeTranslationRepository;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;

/**
 * The SlugRouter takes care of routing the paths for slugs. It should have the
 * lowest priority as it's a catch-all router that routes (almost) all requests
 * to the SlugController
 */
class SlugRouter implements RouterInterface
{
    /** @var RequestContext */
    protected $context;

    /** @var RouteCollection */
    protected $routeCollection;

    /** @var UrlGenerator */
    protected $urlGenerator;

    /** @var ContainerInterface */
    protected $container;

    /** @var string */
    protected $slugPattern;

    /** @var DomainConfigurationInterface */
    protected $domainConfiguration;

    /**
     * The constructor for this service
     *
     * @param ContainerInterface $container
     */
    public function __construct($container)
    {
        $this->container           = $container;
        $this->slugPattern         = "[a-zA-Z0-9\-_\/]*";
        $this->domainConfiguration = $container->get('kunstmaan_admin.domain_configuration');
    }

    /**
     * Match given urls via the context to the routes we defined.
     * This functionality re-uses the default Symfony way of routing and its
     * components
     *
     * @param string $pathinfo
     *
     * @throws ResourceNotFoundException
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
     * Gets the request context.
     *
     * @return RequestContext The context
     *
     * @api
     */
    public function getContext()
    {
        if (!isset($this->context)) {
            /** @var Request $request */
            $request = $this->getMasterRequest();

            $this->context = new RequestContext();
            $this->context->fromRequest($request);
        }

        return $this->context;
    }

    /**
     * Sets the request context.
     *
     * @param RequestContext $context The context
     *
     * @api
     */
    public function setContext(RequestContext $context)
    {
        $this->context = $context;
    }

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
        $this->urlGenerator = new UrlGenerator(
            $this->getRouteCollection(),
            $this->getContext()
        );

        return $this->urlGenerator->generate($name, $parameters, $absolute);
    }

    /**
     * Getter for routeCollection
     *
     * @return \Symfony\Component\Routing\RouteCollection
     */
    public function getRouteCollection()
    {
        if (is_null($this->routeCollection)) {
            $this->routeCollection = new RouteCollection();

            $this->addPreviewRoute();
            $this->addSlugRoute();
        }

        return $this->routeCollection;
    }

    /**
     * @return null|\Symfony\Component\HttpFoundation\Request
     */
    protected function getMasterRequest()
    {
        /** @var RequestStack $requestStack */
        $requestStack = $this->container->get('request_stack');
        if (is_null($requestStack)) {
            return null;
        }

        return $requestStack->getMasterRequest();
    }

    /**
     * Add the preview route to the route collection
     */
    protected function addPreviewRoute()
    {
        $routeParameters = $this->getPreviewRouteParameters();
        $this->addRoute('_slug_preview', $routeParameters);
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
     * Return preview route parameters
     *
     * @return array
     */
    protected function getPreviewRouteParameters()
    {
        $previewPath         = '/admin/preview/{url}';
        $previewDefaults     = array(
            '_controller' => 'KunstmaanNodeBundle:Slug:slug',
            'preview'     => true,
            'url'         => '',
            '_locale'     => $this->getDefaultLocale()
        );
        $previewRequirements = array(
            'url' => $this->getSlugPattern()
        );

        if ($this->isMultiLanguage()) {
            $previewPath = '/{_locale}'.$previewPath;
            unset($previewDefaults['_locale']);
            $previewRequirements['_locale'] = $this->getEscapedLocales($this->getBackendLocales());
        }

        return array(
            'path'         => $previewPath,
            'defaults'     => $previewDefaults,
            'requirements' => $previewRequirements
        );
    }

    /**
     * Return slug route parameters
     *
     * @return array
     */
    protected function getSlugRouteParameters()
    {
        $slugPath         = '/{url}';
        $slugDefaults     = array(
            '_controller' => 'KunstmaanNodeBundle:Slug:slug',
            'preview'     => false,
            'url'         => '',
            '_locale'     => $this->getDefaultLocale()
        );
        $slugRequirements = array(
            'url' => $this->getSlugPattern()
        );

        if ($this->isMultiLanguage()) {
            $slugPath = '/{_locale}'.$slugPath;
            unset($slugDefaults['_locale']);
            $slugRequirements['_locale'] = $this->getEscapedLocales($this->getFrontendLocales());
        }

        return array(
            'path'         => $slugPath,
            'defaults'     => $slugDefaults,
            'requirements' => $slugRequirements
        );
    }

    /**
     * @return boolean
     */
    protected function isMultiLanguage()
    {
        return $this->domainConfiguration->isMultiLanguage();
    }

    /**
     * @return array
     */
    protected function getFrontendLocales()
    {
        return $this->domainConfiguration->getFrontendLocales();
    }

    /**
     * @return array
     */
    protected function getBackendLocales()
    {
        return $this->domainConfiguration->getBackendLocales();
    }

    /**
     * @return string
     */
    protected function getDefaultLocale()
    {
        return $this->domainConfiguration->getDefaultLocale();
    }

    /**
     * @return string
     */
    protected function getHost()
    {
        return $this->domainConfiguration->getHost();
    }

    /**
     * @return string
     */
    protected function getSlugPattern()
    {
        return $this->slugPattern;
    }

    /**
     * @param string $name
     * @param array  $parameters
     */
    protected function addRoute($name, array $parameters = array())
    {
        $this->routeCollection->add(
            $name,
            new Route(
                $parameters['path'],
                $parameters['defaults'],
                $parameters['requirements']
            )
        );
    }

    /**
     * @param array $matchResult
     *
     * @return \Kunstmaan\NodeBundle\Entity\NodeTranslation
     */
    protected function getNodeTranslation($matchResult)
    {
        // The route matches, now check if it actually exists (needed for proper chain router chaining!)
        $nodeTranslationRepo = $this->getNodeTranslationRepository();

        /* @var NodeTranslation $nodeTranslation */
        $nodeTranslation = $nodeTranslationRepo->getNodeTranslationForUrl(
            $matchResult['url'],
            $matchResult['_locale']
        );

        return $nodeTranslation;
    }

    /**
     * @return \Kunstmaan\NodeBundle\Repository\NodeTranslationRepository
     */
    protected function getNodeTranslationRepository()
    {
        $em = $this->container->get('doctrine.orm.entity_manager');

        /* @var NodeTranslationRepository $nodeTranslationRepo */
        $nodeTranslationRepo = $em->getRepository(
            'KunstmaanNodeBundle:NodeTranslation'
        );

        return $nodeTranslationRepo;
    }

    /**
     * @param array $locales
     *
     * @return string
     */
    protected function getEscapedLocales($locales)
    {
        $escapedLocales = array();
        foreach ($locales as $locale) {
            $escapedLocales[] = str_replace('-', '\-', $locale);
        }

        return join('|', $escapedLocales);
    }
}
