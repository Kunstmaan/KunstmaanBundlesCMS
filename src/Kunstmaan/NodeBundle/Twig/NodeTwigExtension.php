<?php

namespace Kunstmaan\NodeBundle\Twig;

use Twig_Extension;

use Doctrine\ORM\EntityManager;

use Kunstmaan\NodeBundle\Entity\AbstractPage;
use Kunstmaan\NodeBundle\Helper\Tabs\TabPane;

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
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return array(
            'get_node_for'             => new \Twig_Function_Method($this, 'getNodeFor'),
            'get_node_translation_for' => new \Twig_Function_Method($this, 'getNodeTranslationFor')
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
     * @return string
     */
    public function getName()
    {
        return 'node_twig_extension';
    }

}
