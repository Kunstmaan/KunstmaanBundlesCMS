<?php

namespace Kunstmaan\AdminBundle\Controller;

use Symfony\Component\Security\Core\SecurityContext;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\UserBundle\Controller\SecurityController as BaseController;
use Kunstmaan\AdminBundle\Entity\LogItem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session;
use Doctrine\ORM\EntityManager;

class SecurityController extends BaseController
{
    public function loginAction()
    {
        /* @var Request */
        $request = $this->container->get('request');
        /* @var EntityManager */
        $em = $this->container->get('doctrine')->getEntityManager();
        /* @var Session */
        $session = $request->getSession();

        // get the error if any (works with forward and redirect -- see below)
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } elseif (null !== $session && $session->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = '';
        }

        if ($error) {
            // TODO: this is a potential security risk (see http://trac.symfony-project.org/ticket/9523)
            $error = $error->getMessage();
            $logItem = new LogItem();
            $logItem->setStatus("error");
            $logItem->setMessage(
                $session->get(SecurityContext::LAST_USERNAME) . " tried to login to the cms but got error: " . $error
            );
            $em->persist($logItem);
            $em->flush();
        }

        // last username entered by the user
        $lastUsername = (null === $session) ? '' : $session->get(SecurityContext::LAST_USERNAME);

        $csrfToken = $this->container->get('form.csrf_provider')->generateCsrfToken('authenticate');

        return $this->container->get('templating')->renderResponse(
            'FOSUserBundle:Security:login.html.' . $this->container->getParameter('fos_user.template.engine'),
            array(
                'last_username' => $lastUsername,
                'error'         => $error,
                'csrf_token'    => $csrfToken,
            )
        );
    }
}
