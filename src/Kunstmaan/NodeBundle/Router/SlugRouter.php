<?php

namespace Kunstmaan\NodeBundle\Router;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\AdminBundle\Helper\DomainConfigurationInterface;
use Kunstmaan\NodeBundle\Controller\SlugController;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Repository\NodeTranslationRepository;
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

    /** @var string */
    protected $slugPattern;

    public function __construct(
        DomainConfigurationInterface $domainConfiguration,
        RequestStack $requestStack,
        EntityManagerInterface $em,
        string $adminKey,
    ) {
        $this->slugPattern = "[a-zA-Z0-9\-_\/]*";
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
     */
    public function match($pathinfo): array
    {
        $urlMatcher = new UrlMatcher(
            $this->getRouteCollection(),
            $this->getContext()
        );
        $result = $urlMatcher->match($pathinfo);

        if (!empty($result)) {
            $nodeTranslation = $this->getNodeTranslation($result);
            if (\is_null($nodeTranslation)) {
                throw new ResourceNotFoundException('No page found for slug ' . $pathinfo);
            }
            $result['_nodeTranslation'] = $nodeTranslation;
        }

        return $result;
    }

    public function getContext(): RequestContext
    {
        if (!isset($this->context)) {
            /** @var Request $request */
            $request = $this->getMasterRequest();

            $this->context = new RequestContext();
            $this->context->fromRequest($request);
        }

        return $this->context;
    }

    public function setContext(RequestContext $context): void
    {
        $this->context = $context;
    }

    /**
     * Generate an url for a supplied route.
     *
     * @param string   $name          The path
     * @param array    $parameters    The route parameters
     * @param int|bool $referenceType The type of reference to be generated (one of the UrlGeneratorInterface constants)
     */
    public function generate($name, $parameters = [], $referenceType = UrlGenerator::ABSOLUTE_PATH): string
    {
        $this->urlGenerator = new UrlGenerator(
            $this->getRouteCollection(),
            $this->getContext()
        );

        if (isset($parameters['_nodeTranslation'])) {
            unset($parameters['_nodeTranslation']);
        }

        return $this->urlGenerator->generate($name, $parameters, $referenceType);
    }

    public function getRouteCollection(): RouteCollection
    {
        if (\is_null($this->routeCollection)) {
            $this->routeCollection = new RouteCollection();

            $this->addPreviewRoute();
            $this->addSlugRoute();
        }

        return $this->routeCollection;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Request|null
     */
    protected function getMasterRequest()
    {
        if (\is_null($this->requestStack)) {
            return null;
        }

        return $this->requestStack->getMainRequest();
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
        $previewDefaults = [
            '_controller' => SlugController::class . '::slugAction',
            'preview' => true,
            'url' => '',
        ];
        $previewRequirements = [
            'url' => $this->getSlugPattern(),
        ];

        if ($this->isMultiLanguage()) {
            $previewPath = '/{_locale}' . $previewPath;
            $previewRequirements['_locale'] = $this->getEscapedLocales($this->getBackendLocales());
        } else {
            $previewDefaults['_locale'] = $this->getDefaultLocale();
        }

        return [
            'path' => $previewPath,
            'defaults' => $previewDefaults,
            'requirements' => $previewRequirements,
        ];
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
        ];
        $slugRequirements = [
            'url' => $this->getSlugPattern(),
        ];

        if ($this->isMultiLanguage()) {
            $slugPath = '/{_locale}' . $slugPath;
            $slugRequirements['_locale'] = $this->getEscapedLocales($this->getFrontendLocales());
        } else {
            $slugDefaults['_locale'] = $this->getDefaultLocale();
        }

        return [
            'path' => $slugPath,
            'defaults' => $slugDefaults,
            'requirements' => $slugRequirements,
        ];
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
     */
    protected function addRoute($name, array $parameters = [])
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
     * @return NodeTranslation
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
            NodeTranslation::class
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
        $escapedLocales = [];
        foreach ($locales as $locale) {
            $escapedLocales[] = str_replace('-', '\-', $locale);
        }

        return implode('|', $escapedLocales);
    }
}
