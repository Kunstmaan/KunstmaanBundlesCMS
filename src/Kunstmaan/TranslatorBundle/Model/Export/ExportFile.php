<?php
namespace Kunstmaan\TranslatorBundle\Model\Export;

use Doctrine\Common\Collections\ArrayCollection;
use Kunstmaan\TranslatorBundle\Entity\Translation;

/**
 * A representation of a translation export into a file
 */
class ExportFile
{
    private $extension;

    private $domain;

    private $locale;

    /**
     * ArrayCollection with keyword as key, text as value
     * @var ArrayCollection
     */
    private $translations;

    /**
     * Translations converted into an array
     * @var array
     */
    private $array = array();

    private $content ='';

    public function __construct()
    {
        $this->translations = new ArrayCollection;
    }

    public function fillArray()
    {
        foreach ($this->translations as $keyword => $text) {
            $this->assignArrayByPath($array, $keyword, $text);
            $this->array = array_merge_recursive($array, $this->array);
        }
    }

    public function assignArrayByPath(&$arr, $path, $value)
    {
        $keys = explode('.', $path);

        while ($key = array_shift($keys)) {
            $arr = &$arr[$key];
        }

        $arr = $value;
    }

    public function getExtension()
    {
        return $this->extension;
    }

    public function setExtension($extension)
    {
        $this->extension = $extension;
    }

    public function getDomain()
    {
        return $this->domain;
    }

    public function setDomain($domain)
    {
        $this->domain = $domain;
    }

    public function getLocale()
    {
        return $this->locale;
    }

    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    public function getTranslations()
    {
        return $this->translations;
    }

    public function setTranslations($translations)
    {
        $this->translations = $translations;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function addTranslation(Translation $translation)
    {
        $this->translations->set($translation->getKeyword(), $translation->getText());
    }

    public function getArray()
    {
        return $this->array;
    }

    public function setArray($array)
    {
        $this->array = $array;
    }
}
