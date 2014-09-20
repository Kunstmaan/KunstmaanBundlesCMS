<?php

namespace Kunstmaan\RedirectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\AdminBundle\Entity\AbstractEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Redirect
 *
 * @ORM\Table(name="kuma_redirects")
 * @ORM\Entity(repositoryClass="Kunstmaan\RedirectBundle\Repository\RedirectRepository")
 */
class Redirect extends AbstractEntity
{
    /**
     * @var string
     *
     * @ORM\Column(name="origin", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $origin;

    /**
     * @var string
     *
     * @ORM\Column(name="target", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $target;

    /**
     * @var boolean
     *
     * @ORM\Column(name="permanent", type="boolean")
     */
    private $permanent;


    /**
     * Set origin
     *
     * @param string $origin
     * @return Redirect
     */
    public function setOrigin($origin)
    {
        $this->origin = $origin;

        return $this;
    }

    /**
     * Get origin
     *
     * @return string 
     */
    public function getOrigin()
    {
        return $this->origin;
    }

    /**
     * Set target
     *
     * @param string $target
     * @return Redirect
     */
    public function setTarget($target)
    {
        $this->target = $target;

        return $this;
    }

    /**
     * Get target
     *
     * @return string 
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * Set permanent
     *
     * @param boolean $permanent
     * @return Redirect
     */
    public function setPermanent($permanent)
    {
        $this->permanent = $permanent;

        return $this;
    }

    /**
     * Get permanent
     *
     * @return boolean
     */
    public function isPermanent()
    {
        return $this->permanent;
    }
}
