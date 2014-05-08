<?php

namespace Kunstmaan\LanguageChooserBundle\LocaleGuesser;

use Lunetics\LocaleBundle\LocaleGuesser\AbstractLocaleGuesser;
use Lunetics\LocaleBundle\Validator\MetaValidator;
use Symfony\Component\HttpFoundation\Request;

/**
 * Locale Guesser for detecting the locale in the url
 *
 * @author Matthias Breddin <mb@lunetics.com>
 * @author Christophe Willemsen <willemsen.christophe@gmail.com>
 */
class UrlLocaleGuesser extends AbstractLocaleGuesser
{
    /**
     * @var MetaValidator
     */
    private $metaValidator;

    /**
     * Constructor
     *
     * @param MetaValidator $metaValidator MetaValidator
     */
    public function __construct(MetaValidator $metaValidator)
    {
        $this->metaValidator = $metaValidator;
    }

    /**
     * Method that guess the locale based on the Url
     *
     * @param Request $request
     *
     * @return boolean True if locale is detected, false otherwise
     */
    public function guessLocale(Request $request)
    {
        $localeValidator = $this->metaValidator;

        $path = $request->getPathInfo();
        if ($request->attributes->has('path')) {
            $path = $request->attributes->get('path');
        }

        if (!$path) {
            return false;
        }

        $parts = array_filter(explode("/", $path));
        $locale = array_shift($parts);

        if ($localeValidator->isAllowed($locale)) {
            $this->identifiedLocale = $locale;

            return true;
        }

        return false;
    }
}
