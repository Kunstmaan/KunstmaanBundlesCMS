<?php
namespace Kunstmaan\TranslatorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Kunstmaan\TranslatorBundle\Repository\TranslationRepository")
 * @ORM\Table(name="kuma_translation", uniqueConstraints={@ORM\UniqueConstraint(name="keyword_per_locale", columns={"keyword", "locale", "domain"})})
 * @ORM\HasLifecycleCallbacks
 */
class Translation
{
    const FLAG_NEW = 'new';
    const FLAG_UPDATED = 'updated';

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * The translations keyword to use in your template or call from the translator
     *
     * @ORM\Column(type="string")
     */
    protected $keyword;

     /**
     * The translations keyword to use in your template or call from the translator
     *
     * @ORM\Column(type="string")
     */
    protected $locale;

    /**
     * Location where the translation comes from
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $file;

    /**
     * Translation
     *
     * @var string
     * @ORM\Column(type="text")
     */
    protected $text;

    /**
     * @ORM\Column(type="string")
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

       return $this->id;
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

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getKeyword()
    {
        return $this->keyword;
    }

    public function setKeyword($keyword)
    {
        $this->keyword = $keyword;

        return $this;
    }

    public function getLocale()
    {
        return $this->locale;
    }

    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    public function getText()
    {
        return $this->text;
    }

    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    public function getDomain()
    {
        return $this->domain;
    }

    public function setDomain($domain)
    {
        $this->domain = $domain;

        return $this;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getFlag()
    {
        return $this->flag;
    }

    public function setFlag($flag)
    {
        $this->flag = $flag;

        return $this;
    }
}
