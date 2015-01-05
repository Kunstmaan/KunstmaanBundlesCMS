<?php

namespace Kunstmaan\LanguageChooserBundle\Router;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;


/**
 * The LanguageChooserRouter catches requests for the root (/) and will provide a way
 * to nicely handle different options on a multilanguage website
 */
class LanguageChooserRouter implements RouterInterface
{

    /** @var  RequestContext */
    private $context;

    /** @var RouteCollection */
    private $routeCollection;

    /** @var UrlGenerator */
    private $urlGenerator;

    /** @var ContainerInterface */
    private $container;

    /**
     * The constructor for this service
     *
     * @param ContainerInterface $container
     */
    public function __construct($container)
    {
        $this->container = $container;
        $this->routeCollection = new RouteCollection();

        $this->routeCollection->add('_languagechooser', new Route(
            '/',
            array(
                '_controller' => 'KunstmaanLanguageChooserBundle:LanguageChooser:index'
            )
        ));
    }


    /**
     * Match given urls via the context to the routes we defined.
     * This functionality re-uses the default Symfony way of routing and its components
     *
     * @param string $pathinfo
     *
     * @return array
     */
    public function match($pathinfo)
    {
        $enableAutodetect = $this->container->getParameter('kunstmaan_language_chooser.autodetectlanguage');
        $enableSplashpage = $this->container->getParameter('kunstmaan_language_chooser.showlanguagechooser');

        // splashpage AND autodetect are disabled, this request may not be routed
        if (!$enableSplashpage && !$enableAutodetect) {
            throw new ResourceNotFoundException('Autodetect and splashpage disabled, can not possibly match.');
        }

        $urlMatcher = new UrlMatcher($this->routeCollection, $this->getContext());

        return $urlMatcher->match($pathinfo);
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
            $this->context = new RequestContext();
            $this->context->fromRequest($this->container->get('request'));
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
     * @param string $name The path
     * @param array $parameters The route parameters
     * @param bool $absolute Absolute url or not
     *
     * @return null|string
     */
    public function generate($name, $parameters = array(), $absolute = false)
    {
        $this->urlGenerator = new UrlGenerator($this->routeCollection, $this->context);

        return $this->urlGenerator->generate($name, $parameters, $absolute);
    }

    /**
     * Getter for routeCollection
     *
     * @return \Symfony\Component\Routing\RouteCollection
     */
    public function getRouteCollection()
    {
        return $this->routeCollection;
    }
}
