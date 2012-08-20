<?php

namespace Kunstmaan\AdminBundle\Modules;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Security\Core\SecurityContext;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;
use Kunstmaan\AdminBundle\Entity\LogItem;

/**
 * logout listener to log the logout
 *
 * @author kristof van cauwenbergh
 *
 */
class LoginListener
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
	* @param Event $event
	*/
	public function onSecurityInteractiveLogin(Event $event)
	{
		$user = $this->context->getToken()->getUser();

		$logitem = new LogItem();
		$logitem->setStatus("info");
		$logitem->setUser($user);
		$logitem->setMessage($user . " succesfully logged in to the cms");
		$this->em->persist($logitem);
		$this->em->flush();
	}
}
