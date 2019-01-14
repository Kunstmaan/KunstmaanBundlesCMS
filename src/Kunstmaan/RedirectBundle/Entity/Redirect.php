<?php

namespace Kunstmaan\RedirectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\AdminBundle\Entity\AbstractEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Table(
 *     name="kuma_redirects",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="kuma_redirects_idx_domain_origin", columns={"domain", "origin"})
 *     }
 * )
 * @ORM\Entity(repositoryClass="Kunstmaan\RedirectBundle\Repository\RedirectRepository")
 * @UniqueEntity(fields={"origin", "domain"})
 */
class Redirect extends AbstractEntity
{
    /**
     * @var string
     *
     * @ORM\Column(name="domain", type="string", length=255, nullable=true)
     */
    private $domain;

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
     * @ORM\Column(name="note", type="string", length=255, nullable=true)
     */
    private $note;

    /**
     * @var string
     *
     * @ORM\Column(name="target", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $target;

    /**
     * @var bool
     *
     * @ORM\Column(name="permanent", type="boolean")
     */
    private $permanent;

    /**
     * Get domain
     *
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * Set domain
     *
     * @param string $domain
     *
     * @return Redirect
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;

        return $this;
    }

    /**
     * Set origin
     *
     * @param string $origin
     *
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
     *
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
     * @param bool $permanent
     *
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
     * @return bool
     */
    public function isPermanent()
    {
        return $this->permanent;
    }

    /**
     * @return string
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * @param string $note
     *
     * @return Redirect
     */
    public function setNote($note)
    {
        $this->note = $note;
    }

    /**
     * @Assert\Callback
     *
     * @param ExecutionContextInterface $context
     */
    public function validate(ExecutionContextInterface $context)
    {
        if ($this->getOrigin() === $this->getTarget()) {
            $context->buildViolation('errors.redirect.origin_same_as_target')
                ->atPath('target')
                ->addViolation();
        }
    }
}
