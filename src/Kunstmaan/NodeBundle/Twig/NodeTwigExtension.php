<?php

namespace Kunstmaan\NodeBundle\Twig;

use Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionMap;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Helper\DomainConfigurationInterface;
use Kunstmaan\NodeBundle\Helper\LocaleServiceInterface;
use Kunstmaan\NodeBundle\Helper\NodeMenu;
use Symfony\Component\HttpFoundation\RequestStack;
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
     * @var DomainConfigurationInterface
     */
    private $domainConfiguration;

    /**
     * @var NodeMenu
     */
    private $nodeMenu;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @param EntityManager                $em
     * @param UrlGeneratorInterface        $generator
     * @param DomainConfigurationInterface $domainConfiguration
     * @param NodeMenu                     $nodeMenu
     */
    public function __construct(
        EntityManager $em,
        UrlGeneratorInterface $generator,
        DomainConfigurationInterface $domainConfiguration,
        NodeMenu $nodeMenu,
        RequestStack $requestStack
    ) {
        $this->em                  = $em;
        $this->generator           = $generator;
        $this->domainConfiguration = $domainConfiguration;
        $this->nodeMenu            = $nodeMenu;
        $this->requestStack        = $requestStack;
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
                'get_node_for', array($this, 'getNodeFor')
            ),
            new \Twig_SimpleFunction(
                'get_node_translation_for',
                array($this, 'getNodeTranslationFor')
            ),
            new \Twig_SimpleFunction(
                'get_node_by_internal_name',
                array($this, 'getNodeByInternalName')
            ),
            new \Twig_SimpleFunction(
                'get_url_by_internal_name',
                array($this, 'getUrlByInternalName')
            ),
            new \Twig_SimpleFunction(
                'get_path_by_internal_name',
                array($this, 'getPathByInternalName')
            ),
            new \Twig_SimpleFunction(
                'get_page_by_node_translation',
                array($this, 'getPageByNodeTranslation')
            ),
            new \Twig_SimpleFunction(
                'get_node_menu',
                array($this, 'getNodeMenu')
            ),
            new \Twig_SimpleFunction('get_locales', array($this, 'getLocales')),
            new \Twig_SimpleFunction('get_backend_locales', array($this, 'getBackendLocales')),
        );
    }

    /**
     * @param NodeTranslation $nodeTranslation
     *
     * @return null|object
     */
    public function getPageByNodeTranslation(NodeTranslation $nodeTranslation)
    {
        return $nodeTranslation->getRef($this->em);
    }

    /**
     * @param AbstractPage $page
     *
     * @return Node
     */
    public function getNodeFor(AbstractPage $page)
    {
        return $this->em->getRepository('KunstmaanNodeBundle:Node')->getNodeFor(
            $page
        );
    }

    /**
     * @param AbstractPage $page
     *
     * @return NodeTranslation
     */
    public function getNodeTranslationFor(AbstractPage $page)
    {
        return $this->em->getRepository('KunstmaanNodeBundle:NodeTranslation')
            ->getNodeTranslationFor($page);
    }

    /**
     * @param string $internalName
     * @param string $locale
     *
     * @return Node
     */
    public function getNodeByInternalName($internalName, $locale)
    {
        $nodes = $this->em->getRepository('KunstmaanNodeBundle:Node')
            ->getNodesByInternalName($internalName, $locale);
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
    public function getPathByInternalName(
        $internalName,
        $locale,
        $parameters = array(),
        $relative = false
    ) {
        $routeParameters = $this->getRouteParametersByInternalName(
            $internalName,
            $locale,
            $parameters
        );

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
    public function getUrlByInternalName(
        $internalName,
        $locale,
        $parameters = array(),
        $schemeRelative = false
    ) {
        $routeParameters = $this->getRouteParametersByInternalName(
            $internalName,
            $locale,
            $parameters
        );

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
    public function getNodeMenu(
        $locale,
        Node $node = null,
        $includeHiddenFromNav = false
    ) {
        $request = $this->requestStack->getMasterRequest();
        $isPreview = $request->attributes->has('preview') && $request->attributes->get('preview') === true;
        $this->nodeMenu->setLocale($locale);
        $this->nodeMenu->setCurrentNode($node);
        $this->nodeMenu->setIncludeOffline($isPreview);
        $this->nodeMenu->setIncludeHiddenFromNav($includeHiddenFromNav);

        return $this->nodeMenu;
    }

    /**
     * @return array
     */
    public function getLocales()
    {
        return $this->domainConfiguration->getFrontendLocales();
    }

    /**
     * @return array
     */
    public function getBackendLocales()
    {
        return $this->domainConfiguration->getBackendLocales();
    }

    /**
     * @param       $internalName
     * @param       $locale
     * @param array $parameters
     *
     * @return array
     */
    private function getRouteParametersByInternalName(
        $internalName,
        $locale,
        $parameters = array()
    ) {
        $url         = '';
        $translation = $this->em->getRepository(
            'KunstmaanNodeBundle:NodeTranslation'
        )
            ->getNodeTranslationByLanguageAndInternalName(
                $locale,
                $internalName
            );

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
