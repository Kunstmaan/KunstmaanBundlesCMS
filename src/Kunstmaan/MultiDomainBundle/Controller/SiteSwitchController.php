<?php

namespace Kunstmaan\MultiDomainBundle\Controller;

use Kunstmaan\AdminBundle\Helper\DomainConfigurationInterface;
use Kunstmaan\MultiDomainBundle\Helper\DomainConfiguration;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

final class SiteSwitchController
{
    /** @var DomainConfigurationInterface */
    private $domainConfiguration;
    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    public function __construct(DomainConfigurationInterface $domainConfiguration, UrlGeneratorInterface $urlGenerator)
    {
        $this->domainConfiguration = $domainConfiguration;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @Route("/switch-site", name="KunstmaanMultiDomainBundle_switch_site", methods={"GET"})
     *
     * @return Response
     *
     * @throws AccessDeniedException
     * @throws NotFoundHttpException
     */
    public function switchAction(Request $request)
    {
        $host = $request->query->get('host');
        $hosts = $this->domainConfiguration->getFullHostConfig();
        if (!\array_key_exists($host, $hosts)) {
            throw new NotFoundHttpException('Invalid host specified');
        }

        $currentHost = $this->domainConfiguration->getHost();

        $session = $request->getSession();
        if ($request->get('from_url_chooser')) {
            $session->set(DomainConfiguration::SWITCH_HOST, $host);
        } else {
            $session->set(DomainConfiguration::OVERRIDE_HOST, $host);
        }

        /*
         * If current host type is different then the host going to, redirect to it's homepage.
         * If coming from url chooser, don't redirect to homepage if other host.
         */
        if ((($hosts[$host]['type'] !== $hosts[$currentHost]['type']) || (!$request->query->has('route'))) && (!$request->get('from_url_chooser'))) {
            $route = 'KunstmaanAdminBundle_homepage';
            $defaultLocale = $this->domainConfiguration->getDefaultLocale();
        } else {
            $route = $request->query->get('route');
            $routeParams = $request->query->get('route_params');
            $defaultLocale = $hosts[$host]['default_locale'];
        }

        $routeParams['_locale'] = $defaultLocale;

        return new RedirectResponse($this->urlGenerator->generate($route, $routeParams));
    }
}
