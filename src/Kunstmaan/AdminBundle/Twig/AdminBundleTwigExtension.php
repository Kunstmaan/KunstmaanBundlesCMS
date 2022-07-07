<?php

namespace Kunstmaan\AdminBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

final class AdminBundleTwigExtension extends AbstractExtension implements GlobalsInterface
{
    /** @var string */
    private $websiteTitle;

    /** @var string */
    private $defaultLocale;

    /** @var string */
    private $requiredLocales;

    public function __construct($websiteTitle, $defaultLocale, $requiredLocales)
    {
        $this->websiteTitle = $websiteTitle;
        $this->defaultLocale = $defaultLocale;
        $this->requiredLocales = $requiredLocales;
    }

    public function getGlobals(): array
    {
        return [
            'websitetitle' => $this->websiteTitle,
            'defaultlocale' => $this->defaultLocale,
            'requiredlocales' => $this->requiredLocales,
        ];
    }
}
