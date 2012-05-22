<?php

namespace Kunstmaan\AdminBundle\Entity;

use Kunstmaan\AdminBundle\Entity\User as Baseuser;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;

/**
 * omnext addcommand
 *
 * @author Kristof Van Cauwenbergh
 *
 * @ORM\Entity
 * @ORM\Table(name="addcommand")
 *
 */
class AddCommand extends Command
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     */
    protected $user;

    public function __construct(EntityManager $em, Baseuser $user)
    {
        $this->em = $em;
        $this->user = $user;
    }

    public function executeimpl($options)
    {
        $this->em->persist($options['entity']);
        $this->em->flush();
    }

    public function removeimpl()
    {
        // TODO extra actions
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

    public function getUser()
    {
        return $this->user;
    }

    public function setUser($channel)
    {
        $this->user = $channel;
    }
}
