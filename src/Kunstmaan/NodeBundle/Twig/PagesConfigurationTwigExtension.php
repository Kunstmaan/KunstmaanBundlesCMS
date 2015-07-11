<?php

namespace Kunstmaan\NodeBundle\Twig;

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
            'get_possible_child_types' => new \Twig_SimpleFunction('get_possible_child_types', function ($ref) {
                return $this->pagesConfiguration->getPossibleChildTypes($ref);
            })
        ];
    }


    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'pages_configuration_twig_extension';
    }
}