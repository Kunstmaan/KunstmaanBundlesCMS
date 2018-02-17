<?php

declare(strict_types=1);

namespace Kunstmaan\UtilitiesBundle\Twig;

use Kunstmaan\UtilitiesBundle\Helper\SlugifierInterface;

class UtilitiesTwigExtension extends \Twig_Extension
{
    /**
     * @var SlugifierInterface
     */
    private $slugifier;

    /**
     * @param $slugifier
     */
    public function __construct(SlugifierInterface $slugifier)
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
            new \Twig_SimpleFilter('slugify', [$this, 'slugify']),
        ];
    }

    /**
     * @param string $text
     *
     * @return string
     */
    public function slugify(string $text): string
    {
        return $this->slugifier->slugify($text);
    }

}
