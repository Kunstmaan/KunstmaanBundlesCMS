<?php

namespace Kunstmaan\NodeBundle\Twig;

use Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionMap;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Helper\NodeMenu;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig_Extension;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Kunstmaan\NodeBundle\Entity\AbstractPage;

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
     * @var UrlGeneratorInterface
     */
    private $generator;

    /**
     * @var SecurityContextInterface
     */
    private $securityContext;

    /**
     * @param EntityManager $em
     * @param UrlGeneratorInterface $generator
     * @param SecurityContextInterface $securityContext
     * @param AclHelper $aclHelper
     */
    public function __construct(
        EntityManager $em,
        UrlGeneratorInterface $generator,
        SecurityContextInterface $securityContext,
        AclHelper $aclHelper
    ) {
        $this->em              = $em;
        $this->generator       = $generator;
        $this->securityContext = $securityContext;
        $this->aclHelper       = $aclHelper;
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('get_node_for', array($this, 'getNodeFor')),
            new \Twig_SimpleFunction('get_node_translation_for', array($this, 'getNodeTranslationFor')),
            new \Twig_SimpleFunction('get_node_by_internal_name', array($this, 'getNodeByInternalName')),
            new \Twig_SimpleFunction('get_url_by_internal_name', array($this, 'getUrlByInternalName')),
            new \Twig_SimpleFunction('get_path_by_internal_name', array($this, 'getPathByInternalName')),
            new \Twig_SimpleFunction('get_page_by_node_translation', array($this, 'getPageByNodeTranslation')),
            new \Twig_SimpleFunction('get_node_menu', array($this, 'getNodeMenu')),
        );
    }

    /**
     * @param NodeTranslation $nt
     *
     * @return null|object
     */
    public function getPageByNodeTranslation(NodeTranslation $nt)
    {
        return $nt->getRef($this->em);
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
     * @param string  $internalName Internal name of the node
     * @param string  $locale       Locale
     * @param array   $parameters   (optional) extra parameters
     * @param boolean $relative     (optional) return relative path?
     *
     * @return string
     */
    public function getPathByInternalName($internalName, $locale, $parameters = array(), $relative = false)
    {
        $routeParameters = $this->getRouteParametersByInternalName($internalName, $locale, $parameters);

        return $this->generator->generate(
            '_slug',
            $routeParameters,
            $relative ? UrlGeneratorInterface::RELATIVE_PATH : UrlGeneratorInterface::ABSOLUTE_PATH
        );
    }

    /**
     * @param string  $internalName   Internal name of the node
     * @param string  $locale         Locale
     * @param array   $parameters     (optional) extra parameters
     * @param boolean $schemeRelative (optional) return relative scheme?
     *
     * @return string
     */
    public function getUrlByInternalName($internalName, $locale, $parameters = array(), $schemeRelative = false)
    {
        $routeParameters = $this->getRouteParametersByInternalName($internalName, $locale, $parameters);

        return $this->generator->generate(
            '_slug',
            $routeParameters,
            $schemeRelative ? UrlGeneratorInterface::NETWORK_PATH : UrlGeneratorInterface::ABSOLUTE_URL
        );
    }

    /**
     * @param string $locale
     * @param Node   $node
     * @param bool   $includeHiddenFromNav
     *
     * @return NodeMenu
     */
    public function getNodeMenu($locale, Node $node = null, $includeHiddenFromNav = false)
    {
        $nodeMenu = new NodeMenu(
            $this->em,
            $this->securityContext,
            $this->aclHelper,
            $locale,
            $node,
            PermissionMap::PERMISSION_VIEW,
            false,
            $includeHiddenFromNav
        );
        return $nodeMenu;
    }

    /**
     * @param $internalName
     * @param $locale
     * @param array $parameters
     * @return array
     */
    private function getRouteParametersByInternalName($internalName, $locale, $parameters = array())
    {
        $url         = '';
        $translation = $this->em->getRepository('KunstmaanNodeBundle:NodeTranslation')
            ->getNodeTranslationByLanguageAndInternalName($locale, $internalName);

        if (!is_null($translation)) {
            $url = $translation->getUrl();
        }

        return array_merge(
            array(
                'url'     => $url,
                '_locale' => $locale
            ),
            $parameters
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'node_twig_extension';
    }

}
