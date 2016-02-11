<?php

namespace Kunstmaan\LanguageChooserBundle\Twig\Extension;

class LanguageChooserExtension extends \Twig_Extension
{

    private $languageChooserLanguages;

    public function getGlobals()
    {
        return array(
            'languagechooser_languages' => $this->languageChooserLanguages
        );
    }

    public function getName()
    {
        return 'kunstmaan_language_chooser_extension';
    }

    public function setLanguageChooserLanguages($languageChooserLanguages)
    {
        $this->languageChooserLanguages = $languageChooserLanguages;
    }
}
