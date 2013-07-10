<?php

namespace Kunstmaan\TranslatorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class that emulates a single symfony2 translation
 *
 * @ORM\Entity(repositoryClass="Kunstmaan\TranslatorBundle\Repository\TranslationRepository")
 * @ORM\Table(name="kuma_translation", uniqueConstraints={@ORM\UniqueConstraint(name="keyword_per_locale", columns={"keyword", "locale"})})
 * @ORM\HasLifecycleCallbacks
 */
class Translation extends \Kunstmaan\TranslatorBundle\Model\Translation\Translation
{

    const FLAG_NEW = 'new';
    const FLAG_UPDATED = 'updated';

    /**
     * The translations keyword to use in your template or call from the translator
     *
     * @var string
     *
     * @ORM\Column(type="string")
     * @ORM\Id
     */
    protected $keyword;

     /**
     * The translations keyword to use in your template or call from the translator
     *
     * @var string
     *
     * @ORM\Column(type="string", length=10)
     * @ORM\Id
     */
    protected $locale;

    /**
     * Location where the translation comes from
     *
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $file;

    /**
     * Translation
     *
     * @var string
     *
     * @ORM\Column(type="text")
     */
    protected $text;

     /**
     * @ORM\ManyToOne(targetEntity="TranslationDomain")
     * @ORM\JoinColumn(name="domain", referencedColumnName="name")
     */
    protected $domain;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    protected $updatedAt;

    /**
     *
     * A flag which defines the status of a specific translations ('updated', 'new', ..)
     *
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $flag = null;

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->flag = self::FLAG_NEW;
    }

    /**
     * @ORM\PreUpdate
     */
    public function preUpdate()
    {
        $this->updatedAt = new \DateTime();
        if ($this->flag == null) {
            $this->flag = self::FLAG_UPDATED;
        }
    }

    public function getKeyword()
    {
        return $this->keyword;
    }

    public function setKeyword($keyword)
    {
        $this->keyword = $keyword;
    }

    public function getLocale()
    {
        return $this->locale;
    }

    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    public function getDomain()
    {
        return $this->domain;
    }

    public function setDomain($domain)
    {
        $this->domain = $domain;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function setFile($file)
    {
        $this->file = $file;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    public function getText()
    {
        return $this->text;
    }

    public function setText($text)
    {
        $this->text = $text;
    }
}
