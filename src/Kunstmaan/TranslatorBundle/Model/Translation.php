<?php

namespace Kunstmaan\TranslatorBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

class Translation
{
    /**
     * The translation domain
     *
     * @Assert\NotBlank()
     * @var string
     */
    protected $domain;

    /**
     * The translation keyword
     *
     * @Assert\NotBlank()
     * @var string
     */
    protected $keyword;

    /**
     * @var ArrayCollection
     */
    protected $texts;

    /**
     *
     */
    public function __construct()
    {
        $this->texts = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @param string $domain
     * @return Translation
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;

        return $this;
    }

    /**
     * @return string
     */
    public function getKeyword()
    {
        return $this->keyword;
    }

    /**
     * @param string $keyword
     * @return Translation
     */
    public function setKeyword($keyword)
    {
        $this->keyword = $keyword;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getTexts()
    {
        return $this->texts;
    }

    /**
     * @param ArrayCollection $texts
     * @return Translation
     */
    public function setTexts($texts)
    {
        $this->texts = $texts;

        return $this;
    }

    /**
     * @param string $locale
     * @param string $text
     * @param int|null $id
     * @return Translation
     */
    public function addText($locale, $text, $id = null)
    {
        $textWithLocale = new TextWithLocale();
        $textWithLocale
            ->setLocale($locale)
            ->setText($text);
        if (!is_null($id)) {
            $textWithLocale->setId($id);
        }
        $this->texts->add($textWithLocale);

        return $this;
    }
}
