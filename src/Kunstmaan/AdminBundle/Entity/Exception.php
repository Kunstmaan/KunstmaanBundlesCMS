<?php

namespace Kunstmaan\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\AdminBundle\Repository\ExceptionRepository;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="Kunstmaan\AdminBundle\Repository\ExceptionRepository")
 * @ORM\Table(
 *      name="kuma_exception",
 *      indexes={
 *          @ORM\Index(name="idx_exception_is_resolved", columns={"is_resolved"})
 *      }
 * )
 * @ORM\HasLifecycleCallbacks()
 */
#[ORM\Entity(repositoryClass: ExceptionRepository::class)]
#[ORM\Table(name: 'kuma_exception')]
#[ORM\Index(name: 'idx_exception_is_resolved', columns: ['is_resolved'])]
#[UniqueEntity('hash')]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity('hash')]
class Exception extends AbstractEntity
{
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=3)
     */
    #[ORM\Column(name: 'code', type: 'string', length: 3)]
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    #[ORM\Column(name: 'url', type: 'text')]
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    #[ORM\Column(name: 'url_referer', type: 'text', nullable: true)]
    private $urlReferer;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=false, unique=true, length=32)
     */
    #[ORM\Column(name: 'hash', type: 'string', nullable: false, unique: true, length: 32)]
    private $hash;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    #[ORM\Column(name: 'events', type: 'integer', nullable: false)]
    private $events;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", name="is_resolved")
     */
    #[ORM\Column(name: 'is_resolved', type: 'boolean')]
    private $isResolved;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="created_at")
     */
    #[ORM\Column(name: 'created_at', type: 'datetime')]
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="updated_at")
     */
    #[ORM\Column(name: 'updated_at', type: 'datetime')]
    private $updatedAt;

    public function __construct()
    {
        $this->isResolved = false;
        $this->setCreatedAt(new \DateTime());
        $this->setUpdatedAt(new \DateTime());
        $this->setEvents(1);
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getUrlReferer()
    {
        return $this->urlReferer;
    }

    /**
     * @param string $urlReferer
     */
    public function setUrlReferer($urlReferer)
    {
        $this->urlReferer = $urlReferer;
    }

    /**
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * @param string $hash
     */
    public function setHash($hash)
    {
        $this->hash = $hash;
    }

    /**
     * @return int
     */
    public function getEvents()
    {
        return $this->events;
    }

    public function setEvents($events)
    {
        $this->events = $events;
    }

    public function increaseEvents()
    {
        ++$this->events;
    }

    /**
     * @return bool
     */
    public function isResolved()
    {
        return (bool) $this->isResolved;
    }

    /**
     * @param bool $isResolved
     */
    public function setResolved($isResolved)
    {
        $this->isResolved = $isResolved;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTimeInterface $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt(\DateTimeInterface $updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @ORM\PreUpdate()
     */
    #[ORM\PreUpdate]
    public function preUpdate()
    {
        $this->setUpdatedAt(new \DateTime());
    }
}
