<?php

namespace Kunstmaan\TranslatorBundle\Model;

use Symfony\Component\Validator\Constraints as Assert;

class TextWithLocale
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    protected $locale;

    /**
     * @var string
     */
    protected $text;

    /**
     * @param string $id
     * @return TextWithLocale
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $locale
     * @return TextWithLocale
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param string $text
     * @return TextWithLocale
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }
}