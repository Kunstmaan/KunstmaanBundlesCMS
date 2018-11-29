<?php

namespace Kunstmaan\NodeBundle\Twig;

use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Kunstmaan\NodeBundle\Helper\PagesConfiguration;

class PagesConfigurationTwigExtension extends \Twig_Extension
{
    /** @var PagesConfiguration */
    private $pagesConfiguration;

    /**
     * @param PagesConfiguration $pagesConfiguration
     */
    public function __construct(PagesConfiguration $pagesConfiguration)
    {
        $this->pagesConfiguration = $pagesConfiguration;
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return [
            'get_possible_child_types' => new \Twig_SimpleFunction(
                'get_possible_child_types', [$this, 'getPossibleChildTypes']
            ),
            'get_homepage_types' => new \Twig_SimpleFunction(
                'get_homepage_types', [$this, 'getHomepageTypes']
            ),
        ];
    }

    /**
     * @param string|HasNodeInterface $reference
     *
     * @return array
     */
    public function getPossibleChildTypes($reference)
    {
        return $this->pagesConfiguration->getPossibleChildTypes($reference);
    }

    /**
     * @return array
     */
    public function getHomepageTypes()
    {
        return $this->pagesConfiguration->getHomepageTypes();
    }
}
