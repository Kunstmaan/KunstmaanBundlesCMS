<?php

namespace Kunstmaan\AdminBundle\Entity;

use Kunstmaan\AdminBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminBundle\Entity\LogItem;

/**
 * omnext command
 *
 * @todo This should be removed when refactoring (logging should happen via a Listener)
 * @deprecated This will be removed
 */
abstract class Command
{

    protected $em;
    protected $user;

    /**
     * @param EntityManager $em   The entity manager
     * @param User          $user The user
     */
    public function __construct(EntityManager $em, User $user)
    {
        $this->em = $em;
        $this->user = $user;
    }

    /**
     * @param string $message The message
     * @param array  $options The options
     */
    public function execute($message = "command executed", $options = array())
    {
        $this->executeimpl($options);

        $logitem = new LogItem();
        $logitem->setStatus("info");
        $logitem->setUser($this->user);
        $logitem->setMessage($message);
        $this->em->persist($logitem);
        $this->em->flush();

        $this->em->persist($this);
        $this->em->flush();
    }

    /**
     * @param array $options
     */
    abstract public function executeimpl($options);

    /**
     * remove
     */
    public function remove()
    {
        $this->removeimpl();

        $this->em->remove($this);
        $this->em->flush();
    }

    /**
     * remove impl
     */
    abstract public function removeimpl();
}
