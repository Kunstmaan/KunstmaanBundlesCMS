<?php

namespace Kunstmaan\MenuBundle\Twig;

use Kunstmaan\MenuBundle\Entity\MenuItem;
use Kunstmaan\MenuBundle\Repository\MenuItemRepositoryInterface;
use Kunstmaan\MenuBundle\Service\RenderService;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @final since 5.4
 */
class MenuTwigExtension extends AbstractExtension
{
    /**
     * @var RenderService
     */
    private $renderService;

    /**
     * @var MenuItemRepositoryInterface
     */
    private $repository;

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
        return [
            new TwigFunction(
                'get_menu',
                [$this, 'getMenu'],
                [
                    'is_safe' => ['html'],
                    'needs_environment' => true,
                ]
            ),
            new TwigFunction('get_menu_items', [$this, 'getMenuItems']),
        ];
    }

    /**
     * Get a html representation of a menu.
     *
     * @param string $name
     * @param string $lang
     * @param array  $options
     *
     * @return string
     */
    public function getMenu(Environment $environment, $name, $lang, $options = [])
    {
        $options = array_merge($this->getDefaultOptions(), $options);

        $renderService = $this->renderService;
        $options['nodeDecorator'] = function ($node) use ($environment, $renderService, $options) {
            return $renderService->renderMenuItemTemplate($environment, $node, $options);
        };

        $arrayResult = $this->getMenuItems($name, $lang);

        return $this->repository->buildTree($arrayResult, $options);
    }

    /**
     * Get an array with menu items of a menu.
     *
     * @param string $name
     * @param string $lang
     *
     * @return array
     */
    public function getMenuItems($name, $lang)
    {
        /** @var MenuItem $menuRepo */
        $arrayResult = $this->repository->getMenuItemsForLanguage($name, $lang);

        // Make sure the parent item is not offline
        $foundIds = [];
        foreach ($arrayResult as $array) {
            $foundIds[] = $array['id'];
        }
        foreach ($arrayResult as $key => $array) {
            if (!\is_null($array['parent']) && !\in_array($array['parent']['id'], $foundIds)) {
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
        return [
            'decorate' => true,
            'rootOpen' => '<ul>',
            'rootClose' => '</ul>',
            'childOpen' => '<li>',
            'childClose' => '</li>',
        ];
    }
}
