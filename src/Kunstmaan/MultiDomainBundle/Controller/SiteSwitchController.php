<?php

namespace Kunstmaan\MultiDomainBundle\Controller;

use Kunstmaan\MultiDomainBundle\Helper\DomainConfiguration;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class SiteSwitchController extends AbstractController
{
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
        $domainConfiguration = $this->container->get('kunstmaan_admin.domain_configuration');
        $host = $request->query->get('host');
        $hosts = $domainConfiguration->getFullHostConfig();
        if (!array_key_exists($host, $hosts)) {
            throw $this->createNotFoundException('Invalid host specified');
        }

        $currentHost = $domainConfiguration->getHost();

        /**
         * If current host type is different then the host going to, redirect to it's homepage.
         * If coming from url chooser, don't redirect to homepage if other host.
         */
        if ((($hosts[$host]['type'] !== $hosts[$currentHost]['type']) || (!$request->query->has('route'))) && (!$request->get('from_url_chooser'))) {
            $route = "KunstmaanAdminBundle_homepage";
            $defaultLocale = $this->container->get('kunstmaan_admin.domain_configuration')->getDefaultLocale();
        } else {
            $route = $request->query->get('route');
            $routeParams = $request->query->get('route_params');
            $defaultLocale = $hosts[$host]['default_locale'];
        }

        $routeParams['_locale'] = $defaultLocale;

        $session = $request->getSession();
        if ($request->get('from_url_chooser')) {
            $session->set(DomainConfiguration::SWITCH_HOST, $host);
        } else {
            $session->set(DomainConfiguration::OVERRIDE_HOST, $host);
        }

        $response = new RedirectResponse(
            $this->generateUrl($route, $routeParams)
        );

        return $response;
    }
}
