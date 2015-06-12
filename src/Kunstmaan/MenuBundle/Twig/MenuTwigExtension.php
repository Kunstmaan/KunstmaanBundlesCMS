<?php

namespace Kunstmaan\MenuBundle\Twig;

use Doctrine\ORM\EntityManager;
use Kunstmaan\MenuBundle\Entity\MenuItem;
use Symfony\Component\Routing\RouterInterface;

class MenuTwigExtension extends \Twig_Extension
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @param EntityManager $em
     * @param RouterInterface $router
     */
    public function __construct(EntityManager $em, RouterInterface $router)
    {
        $this->em = $em;
        $this->router = $router;
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return array(
            'get_menu' => new \Twig_Function_Method($this, 'getMenu', array('is_safe' => array('html'))),
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
    public function getMenu($name, $lang, $options = array())
    {
        $repo = $this->em->getRepository('KunstmaanMenuBundle:MenuItem');
        $query = $this->em
            ->createQueryBuilder()
            ->select('mi, nt, p')
            ->from('KunstmaanMenuBundle:MenuItem', 'mi')
            ->innerJoin('mi.menu', 'm')
            ->leftJoin('mi.parent', 'p')
            ->leftJoin('mi.nodeTranslation', 'nt')
            ->leftJoin('nt.node', 'n')
            ->orderBy('mi.lft', 'ASC')
            ->where('m.locale = :locale')
            ->setParameter('locale', $lang)
            ->andWhere('m.name = :name')
            ->setParameter('name', $name)
            ->andWhere('(nt.online = 1 OR nt.online IS NULL)')
            ->andWhere('(n.deleted = 0 OR n.deleted IS NULL)')
            ->andWhere('(n.hiddenFromNav = 0 OR n.hiddenFromNav IS NULL)')
            ->getQuery();
        $arrayResult = $query->getArrayResult();

        // Make sure the parent item is not offline
        $foundIds = array();
        foreach ($arrayResult as $array) {
            $foundIds[] = $array['id'];
        }
        foreach ($arrayResult as $key => $array) {
            if (!is_null($array['parent']) && !in_array($array['parent']['id'], $foundIds))  {
                unset($arrayResult[$key]);
            }
        }

        $options = array_merge($this->getDefaultOptions(), $options);
        $html = $repo->buildTree($arrayResult, $options);

        return $html;
    }

    /**
     * Get the default options to render the html.
     *
     * @return array
     */
    private function getDefaultOptions()
    {
        $router = $this->router;

        return array(
            'decorate' => true,
            'rootOpen' => '<ul>',
            'rootClose' => '</ul>',
            'childOpen' => '<li>',
            'childClose' => '</li>',
            'nodeDecorator' => function($node) use ($router) {
                if ($node['type'] == MenuItem::TYPE_PAGE_LINK) {
                    $url = $router->generate('_slug', array('url' => $node['nodeTranslation']['url']));
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

                return '<a href="' . $url . '"' . ($node['newWindow'] ? ' target="_blank"' : '') . '>' . $title . '</a>';
            }
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
