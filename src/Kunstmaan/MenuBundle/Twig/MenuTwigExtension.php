<?php

namespace Kunstmaan\MenuBundle\Twig;

use Kunstmaan\MenuBundle\Entity\MenuItem;
use Kunstmaan\MenuBundle\Repository\MenuItemRepositoryInterface;
use Kunstmaan\MenuBundle\Service\RenderService;
use Symfony\Component\Routing\RouterInterface;

class MenuTwigExtension extends \Twig_Extension
{
    /**
     * @var RenderService $renderService
     */
    private $renderService;

    /**
     * @var MenuItemRepositoryInterface
     */
    private $repository;

    /**
     * @param MenuItemRepositoryInterface $repository
     * @param RenderService $renderService
     */
    public function __construct(MenuItemRepositoryInterface $repository, RenderService $renderService)
    {
        $this->renderService = $renderService;
        $this->repository = $repository;
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction(
                'get_menu',
                array($this, 'getMenu'),
                array(
                    'is_safe' => array('html'),
                    'needs_environment' => true,
                )
            ),
            new \Twig_SimpleFunction('get_menu_items', array($this, 'getMenuItems')),
        );
    }

    /**
     * Get a html representation of a menu.
     *
     * @param string $name
     * @param string $lang
     * @param array $options
     * @return string
     */
    public function getMenu(\Twig_Environment $environment, $name, $lang, $options = array())
    {
        $options = array_merge($this->getDefaultOptions(), $options);

        $renderService = $this->renderService;
        $options['nodeDecorator'] = function ($node) use ($environment, $renderService, $options) {
            return $renderService->renderMenuItemTemplate($environment, $node, $options);
        };

        $arrayResult = $this->getMenuItems($name, $lang);
        $html = $this->repository->buildTree($arrayResult, $options);

        return $html;
    }

    /**
     * Get an array with menu items of a menu.
     *
     * @param string $name
     * @param string $lang
     * @return array
     */
    public function getMenuItems($name, $lang)
    {
        /** @var MenuItem $menuRepo */
        $arrayResult = $this->repository->getMenuItemsForLanguage($name, $lang);

        // Make sure the parent item is not offline
        $foundIds = array();
        foreach ($arrayResult as $array) {
            $foundIds[] = $array['id'];
        }
        foreach ($arrayResult as $key => $array) {
            if (!is_null($array['parent']) && !in_array($array['parent']['id'], $foundIds)) {
                unset($arrayResult[$key]);
            }
        }

        return $arrayResult;
    }

    /**
     * Get the default options to render the html.
     *
     * @return array
     */
    private function getDefaultOptions()
    {
        return array(
            'decorate' => true,
            'rootOpen' => '<ul>',
            'rootClose' => '</ul>',
            'childOpen' => '<li>',
            'childClose' => '</li>',
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'kunstmaan_menu_twig_extension';
    }
}
