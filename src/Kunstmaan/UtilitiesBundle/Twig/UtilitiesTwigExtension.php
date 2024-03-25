<?php

namespace Kunstmaan\UtilitiesBundle\Twig;

use Kunstmaan\UtilitiesBundle\Helper\SlugifierInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class UtilitiesTwigExtension extends AbstractExtension
{
    /**
     * @var SlugifierInterface
     */
    private $slugifier;

    public function __construct($slugifier)
    {
        $this->slugifier = $slugifier;
    }

    /**
     * Returns a list of filters.
     *
     * @return array An array of filters
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('slugify', [$this, 'slugify']),
        ];
    }

    /**
     * @param string $text
     */
    public function slugify($text): string
    {
        return $this->slugifier->slugify($text);
    }
}
