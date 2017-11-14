<?php

namespace Kunstmaan\AdminBundle\Entity;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="Kunstmaan\AdminBundle\Repository\ExceptionRepository")
 * @ORM\Table(
 *      name="kuma_exception",
 *      indexes={
 *          @ORM\Index(name="idx_exception_is_mark", columns={"is_mark"})
 *      }
 * )
 * @UniqueEntity("hash")
 * @ORM\HasLifecycleCallbacks()
 */
class Exception extends AbstractEntity
{
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=3)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $urlReferer;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=false, unique=true, length=32)
     */
    private $hash;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    private $used;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", name="is_mark")
     */
    private $isMark;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="created_at")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="updated_at")
     */
    private $updatedAt;

    public function __construct()
    {
        $this->isMark = false;
        $this->setCreatedAt(new \DateTime());
        $this->setUpdatedAt(new \DateTime());
        $this->setUsed(1);
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
    public function getUsed()
    {
        return $this->used;
    }

    /**
     * @param int $used
     */
    public function setUsed($used)
    {
        $this->used = $used;
    }

    /**
     * @param int $used
     */
    public function increaseUsed()
    {
        $this->used++;
    }

    /**
     * @return bool
     */
    public function isMark()
    {
        return (bool) $this->isMark;
    }

    /**
     * @param bool $isMark
     */
    public function setIsMark($isMark)
    {
        $this->isMark = $isMark;
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
    public function setCreatedAt(DateTimeInterface $createdAt)
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
    public function setUpdatedAt(DateTimeInterface $updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @ORM\PreUpdate()
     */
    public function preUpdate()
    {
        $this->setUpdatedAt(new \DateTime());
    }

}