<?php

namespace Kunstmaan\AdminBundle\Entity;

use Kunstmaan\AdminBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminBundle\Entity\LogItem;

/**
 * omnext command
 *
 * @author Kristof Van Cauwenbergh
 *
 * @todo This should be removed when refactoring (logging should happen via a Listener)
 * @deprecated
 */
abstract class Command
{

    protected $em;
    protected $user;

    public function __construct(EntityManager $em, User $user)
    {
        $this->em = $em;
        $this->user = $user;
    }

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

    abstract function executeimpl($options);

    public function remove()
    {
        $this->removeimpl();

        $this->em->remove($this);
        $this->em->flush();
    }

    abstract function removeimpl();
}
