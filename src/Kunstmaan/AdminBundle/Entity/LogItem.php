<?php

namespace Kunstmaan\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * omnext logitem
 *
 * @author Kristof Van Cauwenbergh
 *
 * @ORM\Entity(repositoryClass="Kunstmaan\AdminBundle\Repository\LogItemRepository")
 * @ORM\Table(name="logitem")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"logitem" = "LogItem", "errorlogitem" = "ErrorLogItem"})
 * @ORM\HasLifecycleCallbacks
 */
class LogItem
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $status;

    /**
     * @ORM\Column(type="string")
     */
    protected $message;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     */
    protected $user;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $createdat;

    /**
     * @todo Why the //?
     * //@ORM\ManyToOne(targetEntity="Command")
     */
    //protected $command;

    public function __construct()
    {
        $this->createdat = new \DateTime();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id
     *
     * @param id integer
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($channel)
    {
        $this->status = $channel;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser($channel)
    {
        $this->user = $channel;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function setMessage($message)
    {
        $this->message = $message;
    }

    public function getCreatedAt()
    {
        return $this->createdat;
    }

    public function setCreatedAt($createdat)
    {
        $this->createdat = $createdat;
    }
}
