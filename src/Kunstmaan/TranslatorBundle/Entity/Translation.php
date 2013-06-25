<?php

namespace Users\Development\KunstmaanTranslatorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Translation class.
 *
 * This class emulates a translation in Symfony2
 *
 */
class Translation
{
    /**
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * The translations keyword to use in your template or call from the translator
     *
     * @var sting
     *
     * @ORM\Column(type="string", unique="true")
     */
    private $keyword;

     /**
     * The translations keyword to use in your template or call from the translator
     *
     * @var sting
     *
     * @ORM\Column(type="string", length="2")
     */
    private $language;

     /**
     * @OneToMany(targetEntity="TranslatorDomain", mappedBy="messages", cascade={"all"}, orphanRemoval=true)
     */
    private $domain;

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