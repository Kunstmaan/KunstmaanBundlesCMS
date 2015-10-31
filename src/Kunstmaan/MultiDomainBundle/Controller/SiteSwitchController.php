<?php

namespace Kunstmaan\MultiDomainBundle\Controller;

use Kunstmaan\MultiDomainBundle\Helper\DomainConfiguration;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class SiteSwitchController extends Controller
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
        $domainConfiguration = $this->get('kunstmaan_admin.domain_configuration');
        $host = $request->query->get('host');
        $hosts = $domainConfiguration->getHosts();
        if (!in_array($host, $hosts)) {
            throw $this->createNotFoundException('Invalid host specified');
        }

        $session = $request->getSession();
        $session->set(DomainConfiguration::OVERRIDE_HOST, $host);
        $defaultLocale = $this->get('kunstmaan_admin.domain_configuration')->getDefaultLocale();

        $response = new RedirectResponse(
            $this->get('router')->generate('KunstmaanAdminBundle_homepage', array('_locale' => $defaultLocale))
        );

        return $response;
    }
}
