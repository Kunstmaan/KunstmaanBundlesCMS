<?php

namespace Kunstmaan\MenuBundle\Service;

use Kunstmaan\MenuBundle\Entity\MenuItem;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Templating\EngineInterface;

class RenderService
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @param \Twig_Environment $environment
     * @param $node
     * @param array $options
     * @return string
     */
    public function renderMenuItemTemplate(\Twig_Environment $environment, $node, $options = array())
    {
        $template = isset($options['template']) ? $options['template'] : false;
        if ($template === false) {
            $template = 'KunstmaanMenuBundle::menu-item.html.twig';
        }

        $active = false;
        if ($node['type'] == MenuItem::TYPE_PAGE_LINK) {
            $url = $this->router->generate('_slug', array('url' => $node['nodeTranslation']['url']));

            if ($this->router->getContext()->getPathInfo() == $url) {
                $active = true;
            }
        } else {
            $url = $node['url'];
        }

        if ($node['type'] == MenuItem::TYPE_PAGE_LINK) {
            if ($node['title']) {
                $title = $node['title'];
            } else {
                $title = $node['nodeTranslation']['title'];
            }
        } else {
            $title = $node['title'];
        }

        return $environment->render($template, array(
            'menuItem' => $node,
            'url' => $url,
            'options' => $options,
            'title' => $title,
            'active' => $active
        ));
    }
}
