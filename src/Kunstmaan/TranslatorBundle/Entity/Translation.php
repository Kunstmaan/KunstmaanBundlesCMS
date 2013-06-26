<?php

namespace Kunstmaan\TranslatorBundle\Entity;

use Kunstmaan\AdminBundle\Entity\AbstractEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class that emulates a single symfony2 translation
 *
 * @ORM\Entity(repositoryClass="Kunstmaan\TranslatorBundle\Repository\TranslationRepository")
 * @ORM\Table(name="kuma_translation", uniqueConstraints={@ORM\UniqueConstraint(name="keyword_per_language", columns={"keyword", "language"})})
 *
 * @ORM\HasLifecycleCallbacks
 */
class Translation extends AbstractEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * The translations keyword to use in your template or call from the translator
     *
     * @var sting
     *
     * @ORM\Column(type="string")
     */
    protected $keyword;

     /**
     * The translations keyword to use in your template or call from the translator
     *
     * @var sting
     *
     * @ORM\Column(type="string", length=2)
     */
    protected $language;

     /**
     * @ORM\ManyToOne(targetEntity="TranslationDomain")
     */
    protected $domain;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getKeyword()
    {
        return $this->keyword;
    }

    public function setKeyword($keyword)
    {
        $this->keyword = $keyword;
    }

    public function getLanguage()
    {
        return $this->language;
    }

    public function setLanguage($language)
    {
        $this->language = $language;
    }

    public function getDomain()
    {
        return $this->domain;
    }

    public function setDomain($domain)
    {
        $this->domain = $domain;
    }
}