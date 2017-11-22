<?php

namespace Kunstmaan\MultiDomainBundle\Controller;

use Kunstmaan\MultiDomainBundle\Helper\DomainConfiguration;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Class SiteSwitchController
 * @package Kunstmaan\MultiDomainBundle\Controller
 */
class SiteSwitchController extends Controller
{
    /** @var DomainConfiguration $domainConfiguration */
    protected $domainConfiguration;

    /** @var bool $differentHost */
    protected $differentHost;

    /**
     * SiteSwitchController constructor.
     */
    public function __construct()
    {
        $this->domainConfiguration = $this->get('kunstmaan_admin.domain_configuration');
    }

    /**
     * @Route("/switch-site", name="KunstmaanMultiDomainBundle_switch_site")
     * @Method({"GET"})
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return Response
     * @throws AccessDeniedException
     * @throws NotFoundHttpException
     */
    public function switchAction(Request $request)
    {
        $host = $request->query->get('host');
        $hosts = $this->getHosts($host);
        $currentHost = $this->domainConfiguration->getHost();
        $this->setIsDifferentHost($request, $host, $hosts, $currentHost);
        $route = $this->getRoute($request);
        $defaultLocale = $this->getDefaultLocale($host, $hosts);
        $routeParams = $this->getRouteParams($request);
        $routeParams['_locale'] = $defaultLocale;
        $this->setSessionVars($request, $host);
        $url = $this->get('router')->generate($route, $routeParams);
        $response = new RedirectResponse($url);

        return $response;
    }

    /**
     * @param $host
     * @return array
     */
    private function getHosts($host)
    {
        $hosts = $this->domainConfiguration->getFullHostConfig();
        if (!array_key_exists($host, $hosts)) {
            throw $this->createNotFoundException('Invalid host specified');
        }

        return $hosts;
    }

    /**
     * @param Request $request
     * @param $host
     */
    private function setSessionVars(Request $request, $host)
    {
        $session = $request->getSession();
        if ($request->get('from_url_chooser')) {
            $session->set(DomainConfiguration::SWITCH_HOST, $host);
        } else {
            $session->set(DomainConfiguration::OVERRIDE_HOST, $host);
        }
    }

    /**
     * @param Request $request
     * @param $host
     * @param $hosts
     * @param $currentHost
     */
    private function setIsDifferentHost(Request $request, $host, array $hosts, $currentHost)
    {
        $this->differentHost =
            (($hosts[$host]['type'] !== $hosts[$currentHost]['type']) || (!$request->query->has('route')))
            && (!$request->get('from_url_chooser'));

    }

    /**
     * @return bool
     */
    public function isDifferentHost()
    {
        return $this->differentHost;
    }

    /**
     * @param Request $request
     * @return mixed|string
     */
    private function getRoute(Request $request)
    {
        return $this->isDifferentHost() ? 'KunstmaanAdminBundle_homepage' : $request->query->get('route');
    }

    /**
     * @param Request $request
     * @return array|mixed
     */
    private function getRouteParams(Request $request)
    {
        return $this->isDifferentHost() ? [] : $request->query->get('route_params');
    }

    /**
     * @param $host
     * @param array $hosts
     * @return mixed
     */
    private function getDefaultLocale($host, array $hosts)
    {
        return $this->isDifferentHost()
            ? $this->get('kunstmaan_admin.domain_configuration')->getDefaultLocale()
            : $hosts[$host]['default_locale'];
    }
}
