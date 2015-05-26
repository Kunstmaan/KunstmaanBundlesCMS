<?php

namespace Kunstmaan\UtilitiesBundle\Twig;

use Kunstmaan\UtilitiesBundle\Helper\SlugifierInterface;
use Twig_Extension;


class UtilitiesTwigExtension extends Twig_Extension
{
    /**
     * @var SlugifierInterface
     */
    private $slugifier;

    /**
     * @param $slugifier
     */
    function __construct($slugifier)
    {
        $this->slugifier = $slugifier;
    }

    /**
     * Returns a list of filters.
     *
     * @return array An array of filters
     */
    public function getFilters()
    {
        return array(
            'slugify' => new \Twig_Filter_Method($this, 'slugify'),
        );
    }

    /**
     * @param string $text
     *
     * @return string
     */
    public function slugify($text)
    {
        return $this->slugifier->slugify($text, '');
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'kunstmaan_utilities_twig_extension';
    }

}
