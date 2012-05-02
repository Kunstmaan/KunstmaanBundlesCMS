<?php

namespace Kunstmaan\AdminBundle\Modules;

use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Router;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Bundle\DoctrineBundle\Registry as Doctrine;
use Kunstmaan\AdminBundle\Entity\LogItem;

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
	* @param Doctrine $doctrine
	*/
	public function __construct(SecurityContext $context, Doctrine $doctrine)
	{
		$this->context = $context;
		$this->em = $doctrine->getEntityManager();
	}
	
	/**
	* Do the magic.
	*
	* @param Request $request
	*/
	public function onLogoutSuccess(Request $request)
	{
		$user = $this->context->getToken()->getUser();
	
		$logitem = new LogItem();
		$logitem->setStatus("info");
		$logitem->setUser($user);
		$logitem->setMessage($user . " succesfully logged out from the cms");
		$this->em->persist($logitem);
		$this->em->flush();
		
		$referer_url = $request->headers->get('referer');
		$response = new RedirectResponse($referer_url);		
		return $response;
	}
}
