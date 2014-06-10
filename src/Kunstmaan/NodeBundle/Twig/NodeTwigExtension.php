<?php

namespace Kunstmaan\NodeBundle\Twig;

use Symfony\Component\Routing\RouterInterface;
use Twig_Extension;

use Doctrine\ORM\EntityManager;

use Kunstmaan\NodeBundle\Entity\AbstractPage;
use Kunstmaan\AdminBundle\Helper\FormWidgets\Tabs\TabPane;

/**
 * Extension to fetch node / translation by page in Twig templates
 */
class NodeTwigExtension extends Twig_Extension
{

    /**
     * @var EntityManager $em
     */
    private $em;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @param EntityManager   $em
     * @param RouterInterface $router
     */
    public function __construct(EntityManager $em, RouterInterface $router)
    {
        $this->em     = $em;
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
            'get_node_for'              => new \Twig_Function_Method($this, 'getNodeFor'),
            'get_node_translation_for'  => new \Twig_Function_Method($this, 'getNodeTranslationFor'),
            'get_node_by_internal_name' => new \Twig_Function_Method($this, 'getNodeByInternalName'),
            'get_slug_by_internal_name' => new \Twig_Function_Method($this, 'getSlugByInternalName'),
        );
    }

    /**
     * @param AbstractPage $page
     *
     * @return Node
     */
    public function getNodeFor(AbstractPage $page)
    {
        return $this->em->getRepository('KunstmaanNodeBundle:Node')->getNodeFor($page);
    }

    /**
     * @param AbstractPage $page
     *
     * @return NodeTranslation
     */
    public function getNodeTranslationFor(AbstractPage $page)
    {
        return $this->em->getRepository('KunstmaanNodeBundle:NodeTranslation')->getNodeTranslationFor($page);
    }

    /**
     * @param string $internalName
     * @param string $locale
     *
     * @return Node
     */
    public function getNodeByInternalName($internalName, $locale)
    {
        $nodes = $this->em->getRepository('KunstmaanNodeBundle:Node')->getNodesByInternalName($internalName, $locale);
        if (!empty($nodes)) {
            return $nodes[0];
        }

        return null;
    }

    /**
     * @param string $internalName Internal name of the node
     * @param string $locale       Locale
     * @param array  $parameters   (optional) extra parameters
     * @param bool   $absolutePath Return absolute path?
     *
     * @return string
     */
    public function getSlugByInternalName($internalName, $locale, $parameters = array(), $absolutePath = false)
    {
        $nodes = $this->em->getRepository('KunstmaanNodeBundle:Node')->getNodesByInternalName($internalName, $locale);

        $slug = '';
        if (!empty($nodes)) {
            $translation = $nodes[0]->getNodeTranslation($locale);
            if (!is_null($translation)) {
                $slug = $translation->getSlug();
            }
        }

        $params = array_merge(
            array(
                'url'     => $slug,
                '_locale' => $locale
            ),
            $parameters
        );

        return $this->router->generate('_slug', $params, $absolutePath);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'node_twig_extension';
    }

}
