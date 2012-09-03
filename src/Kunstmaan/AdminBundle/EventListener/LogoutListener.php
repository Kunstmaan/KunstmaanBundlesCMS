<?php

namespace Kunstmaan\AdminBundle\EventListener;

use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Router;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Security\Core\SecurityContext;

use Doctrine\ORM\EntityManager;

use Kunstmaan\AdminBundle\Entity\LogItem;
use Kunstmaan\AdminBundle\Entity\User;

/**
 * login listener to log the logout
 *
 * @author kristof van cauwenbergh
 *
 */
class LogoutListener implements LogoutSuccessHandlerInterface
{
    private $context;
    private $em;

    /**
     * Constructor
     *
     * @param SecurityContext $context
     * @param EntityManager   $em
     */
    public function __construct(SecurityContext $context, EntityManager $em)
    {
        $this->context = $context;
        $this->em      = $em;
    }

    /**
     * Do the magic.
     *
     * @param Request $request
     */
    public function onLogoutSuccess(Request $request)
    {
        /* @var User $user */
        $user = $this->context->getToken()->getUser();

        $logItem = new LogItem();
        $logItem->setStatus("info");
        $logItem->setUser($user);
        $logItem->setMessage($user . " succesfully logged out from the cms");
        $this->em->persist($logItem);
        $this->em->flush();

        $referrerUrl = $request->headers->get('referer');
        $response    = new RedirectResponse($referrerUrl);

        return $response;
    }
}
