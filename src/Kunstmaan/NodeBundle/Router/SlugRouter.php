<?php

namespace Kunstmaan\NodeBundle\Router;

use Doctrine\ORM\EntityManagerInterface;
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
    public static $SLUG = '_slug';

    public static $SLUG_PREVIEW = '_slug_preview';

    /** @var DomainConfigurationInterface */
    protected $domainConfiguration;

    /** @var RequestStack */
    private $requestStack;

    /** @var EntityManagerInterface */
    private $em;

    /** @var string */
    protected $adminKey;

    /** @var RequestContext */
    protected $context;

    /** @var RouteCollection */
    protected $routeCollection;

    /** @var UrlGenerator */
    protected $urlGenerator;

    /**
     * @var ContainerInterface
     *
     * @deprecated in KunstmaanNodeBundle 5.1 and will be removed in KunstmaanNodeBundle 6.0.
     */
    protected $container;

    /** @var string */
    protected $slugPattern;

    /**
     * The constructor for this service
     *
     * @param ContainerInterface $container
     */
    public function __construct(
        /* DomainConfigurationInterface */ $domainConfiguration,
        RequestStack $requestStack = null,
        EntityManagerInterface $em = null,
        $adminKey = null
    ) {
        $this->slugPattern = "[a-zA-Z0-9\-_\/]*";

        if ($domainConfiguration instanceof ContainerInterface) {
            @trigger_error('Container injection and the usage of the container is deprecated in KunstmaanNodeBundle 5.1 and will be removed in KunstmaanNodeBundle 6.0.', E_USER_DEPRECATED);

            $this->container = $domainConfiguration;
            $this->domainConfiguration = $this->container->get('kunstmaan_admin.domain_configuration');
            $this->adminKey = $this->container->getParameter('kunstmaan_admin.admin_prefix');
            $this->requestStack = $this->container->get('request_stack');
            $this->em = $this->container->get('doctrine.orm.entity_manager');

            return;
        }

        $this->domainConfiguration = $domainConfiguration;
        $this->adminKey = $adminKey;
        $this->requestStack = $requestStack;
        $this->em = $em;
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
        $result = $urlMatcher->match($pathinfo);

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
     * Generate an url for a supplied route.
     *
     * @param string   $name          The path
     * @param array    $parameters    The route parameters
     * @param int|bool $referenceType The type of reference to be generated (one of the UrlGeneratorInterface constants)
     *
     * @return null|string
     */
    public function generate($name, $parameters = array(), $referenceType = UrlGenerator::ABSOLUTE_PATH)
    {
        $this->urlGenerator = new UrlGenerator(
            $this->getRouteCollection(),
            $this->getContext()
        );

        return $this->urlGenerator->generate($name, $parameters, $referenceType);
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
        if (is_null($this->requestStack)) {
            return null;
        }

        return $this->requestStack->getMasterRequest();
    }

    /**
     * Add the preview route to the route collection
     */
    protected function addPreviewRoute()
    {
        $routeParameters = $this->getPreviewRouteParameters();
        $this->addRoute(self::$SLUG_PREVIEW, $routeParameters);
    }

    /**
     * Add the slug route to the route collection
     */
    protected function addSlugRoute()
    {
        $routeParameters = $this->getSlugRouteParameters();
        $this->addRoute(self::$SLUG, $routeParameters);
    }

    /**
     * Return preview route parameters
     *
     * @return array
     */
    protected function getPreviewRouteParameters()
    {
        $previewPath = sprintf('/%s/preview/{url}', $this->adminKey);
        $previewDefaults = array(
            '_controller' => 'KunstmaanNodeBundle:Slug:slug',
            'preview' => true,
            'url' => '',
            '_locale' => $this->getDefaultLocale(),
        );
        $previewRequirements = array(
            'url' => $this->getSlugPattern(),
        );

        if ($this->isMultiLanguage()) {
            $previewPath = '/{_locale}' . $previewPath;
            unset($previewDefaults['_locale']);
            $previewRequirements['_locale'] = $this->getEscapedLocales($this->getBackendLocales());
        }

        return array(
            'path' => $previewPath,
            'defaults' => $previewDefaults,
            'requirements' => $previewRequirements,
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
        $slugDefaults = array(
            '_controller' => 'KunstmaanNodeBundle:Slug:slug',
            'preview' => false,
            'url' => '',
            '_locale' => $this->getDefaultLocale(),
        );
        $slugRequirements = array(
            'url' => $this->getSlugPattern(),
        );

        if ($this->isMultiLanguage()) {
            $slugPath = '/{_locale}' . $slugPath;
            unset($slugDefaults['_locale']);
            $slugRequirements['_locale'] = $this->getEscapedLocales($this->getFrontendLocales());
        }

        return array(
            'path' => $slugPath,
            'defaults' => $slugDefaults,
            'requirements' => $slugRequirements,
        );
    }

    /**
     * @return bool
     */
    protected function isMultiLanguage($host = null)
    {
        return $this->domainConfiguration->isMultiLanguage($host);
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
        /* @var NodeTranslationRepository $nodeTranslationRepo */
        $nodeTranslationRepo = $this->em->getRepository(
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

        return implode('|', $escapedLocales);
    }
}
