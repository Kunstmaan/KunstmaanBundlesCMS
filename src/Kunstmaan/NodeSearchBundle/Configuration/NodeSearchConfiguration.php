<?php

namespace Kunstmaan\NodeSearchBundle\Configuration;

use DoctrineExtensions\Taggable\Taggable;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\PagePartBundle\Helper\HasPagePartsInterface;
use Kunstmaan\SearchBundle\Configuration\SearchConfigurationInterface;
use Kunstmaan\SearchBundle\Helper\IndexControllerInterface;
use Kunstmaan\UtilitiesBundle\Helper\ClassLookup;
use Sherlock\Sherlock;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Search configuration for nodes. Creates an index and will populate it with all Nodes it retrieves recursively from the TopNodes
 * It iterates over all NodeTranslations and only uses the public NodeVersion
 */
class NodeSearchConfiguration implements SearchConfigurationInterface
{
    private $container;
    private $em;
    private $search;
    private $indexName = 'nodeindex';
    private $indexNodeType = 'page';

    public function __construct(ContainerInterface $container, $search)
    {
        $this->container = $container;
        $this->em = $this->container->get('doctrine')->getEntityManager();
        $this->search = $search;
    }

    public function create()
    {
        $index = $this->search->index($this->indexName);

        $index->mappings(
            Sherlock::mappingBuilder($this->indexNodeType)->String()->field('tags')->analyzer('keyword'),
            Sherlock::mappingBuilder($this->indexNodeType)->String()->field('type')->analyzer('keyword'),
            Sherlock::mappingBuilder($this->indexNodeType)->String()->field('slug')->analyzer('keyword')
        );

        $index->create();
    }

    public function index()
    {
        $nodeRepository = $this->em->getRepository('KunstmaanNodeBundle:Node');

        $topNodes = $nodeRepository->getAllTopNodes();

        foreach ($topNodes as $topNode) {
            $this->indexNode($topNode);
            $this->indexChildren($topNode);
        }
    }

    /**
     * Recursively index the children of the node
     *
     * @param $node Node
     */
    public function indexChildren($node)
    {
        foreach ($node->getChildren() as $childNode) {
            $this->indexNode($childNode);
            $this->indexChildren($childNode);
        }
    }

    public function indexNode(Node $node)
    {
        foreach ($node->getNodeTranslations() as $nodeTranslation) {
            $this->indexNodeTranslation($nodeTranslation);
        }
    }

    /**
     * @param      $nodeTranslation
     */
    public function indexNodeTranslation(NodeTranslation $nodeTranslation)
    {
        // Only index online NodeTranslations
        if ($nodeTranslation->isOnline()) {
            // Retrieve the public NodeVersion
            $publicNodeVersion = $nodeTranslation->getPublicNodeVersion();
            if ($publicNodeVersion) {
                $node = $nodeTranslation->getNode();
                // Retrieve the referenced entity from the public NodeVersion
                $page = $publicNodeVersion->getRef($this->em);
                // If the page doesn't implement IndexControllerInterfance or it return true on shouldBeIndexed, index the page
                if (!($page instanceof IndexControllerInterface) or $page->shouldBeIndexed()) {

                    $doc = array(
                        "node_id"            => $node->getId(),
                        "nodetranslation_id" => $nodeTranslation->getId(),
                        "nodeversion_id"     => $publicNodeVersion->getId(),
                        "title"              => $nodeTranslation->getTitle(),
                        "lang"               => $nodeTranslation->getLang(),
                        "slug"               => $nodeTranslation->getFullSlug(),
                        "type"               => ClassLookup::getClassName($page),

                    );

                    // Parent and Ancestors

                    $parent = $node->getParent();
                    if ($parent) {
                        $doc = array_merge($doc, array("parent" => $parent->getId()));
                        $ancestors = array();
                        do {
                            $ancestors[] = $parent->getId();
                            $parent = $parent->getParent();
                        } while ($parent);
                        $doc = array_merge($doc, array("ancestors" => implode(' ', $ancestors)));
                    }

                    // Content

                    $content = '';
                    if ($page instanceof HasPagePartsInterface) {
                        $this->container->enterScope('request');
                        $this->container->set('request', new Request(), 'request');
                        $pageparts = $this->em
                            ->getRepository('KunstmaanPagePartBundle:PagePartRef')
                            ->getPageParts($page);
                        $renderer = $this->container->get('templating');
                        $view = 'KunstmaanSearchBundle:PagePart:view.html.twig';
                        $content = strip_tags($renderer->render($view, array('page' => $page, 'pageparts' => $pageparts, 'pagepartviewresolver' => $this)));

                    }
                    $doc = array_merge($doc, array("content" => $content));

                    // Taggable

                    if ($page instanceof Taggable) {
                        $tags = array();
                        foreach ($page->getTags() as $tag) {
                            $tags[] = $tag->getName();
                        }
                        $doc = array_merge($doc, array("tags" => $tags));
                    }

                    // Add document to index

                    $uid = "nodetranslation_" . $nodeTranslation->getId();
                    $this->search->document($this->indexName, $this->indexNodeType, $doc, $uid);
                }
            }
        }
    }

    public function deleteNodeTranslation(NodeTranslation $nodeTranslation)
    {
        $uid = "nodetranslation_" . $nodeTranslation->getId();
        $this->search->deleteDocument($this->indexName, $this->indexNodeType, $uid);
    }

    public function delete()
    {
        $this->search->delete($this->indexName);
    }
}
