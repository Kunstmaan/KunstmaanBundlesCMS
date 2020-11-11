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
     * @var string|null
     *
     * @ORM\Column(name="domain", type="string", length=255, nullable=true)
     */
    private $domain;

    /**
     * @var string|null
     *
     * @ORM\Column(name="origin", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $origin;

    /**
     * @var string|null
     *
     * @ORM\Column(name="note", type="string", length=255, nullable=true)
     */
    private $note;

    /**
     * @var string|null
     *
     * @ORM\Column(name="target", type="text")
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
     * @var bool
     *
     * @ORM\Column(name="is_auto_redirect", type="boolean", nullable=true)
     */
    private $isAutoRedirect = false;

    public function getDomain(): ?string
    {
        return $this->domain;
    }

    public function setDomain(?string $domain): Redirect
    {
        $this->domain = $domain;

        return $this;
    }

    public function getOrigin(): ?string
    {
        return $this->origin;
    }

    public function setOrigin(?string $origin): Redirect
    {
        $this->origin = $origin;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): Redirect
    {
        $this->note = $note;

        return $this;
    }

    public function getTarget(): ?string
    {
        return $this->target;
    }

    public function setTarget(?string $target): Redirect
    {
        $this->target = $target;

        return $this;
    }

    public function isPermanent(): bool
    {
        return $this->permanent;
    }

    public function setPermanent(bool $permanent): Redirect
    {
        $this->permanent = $permanent;

        return $this;
    }

    public function isAutoRedirect(): ?bool
    {
        return $this->isAutoRedirect;
    }

    public function setIsAutoRedirect(?bool $isAutoRedirect): Redirect
    {
        $this->isAutoRedirect = $isAutoRedirect;

        return $this;
    }

    /**
     * @Assert\Callback
     *
     * @param ExecutionContextInterface $context
     */
    public function validate(ExecutionContextInterface $context): void
    {
        if ($this->getOrigin() === $this->getTarget()) {
            $context->buildViolation('errors.redirect.origin_same_as_target')
                ->atPath('target')
                ->addViolation();
        }
    }
}
