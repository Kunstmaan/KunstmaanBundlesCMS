<?php

namespace Kunstmaan\NodeSearchBundle\Configuration;

use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeSearchBundle\Event\Events;
use Kunstmaan\NodeSearchBundle\Event\IndexNodeEvent;
use Kunstmaan\PagePartBundle\Helper\HasPagePartsInterface;
use Kunstmaan\SearchBundle\Configuration\SearchConfigurationInterface;
use Kunstmaan\SearchBundle\Helper\ShouldBeIndexed;
use Kunstmaan\SearchBundle\Search\Search;
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
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    private $em;

    /**
     * @var \Kunstmaan\SearchBundle\Search\Search
     */
    private $search;

    /**
     * @var string
     */
    private $indexName;

    /**
     * @var string
     */
    private $indexType;

    public function __construct(ContainerInterface $container, $search, $indexName, $indexType)
    {
        $this->container = $container;
        $this->em = $this->container->get('doctrine')->getEntityManager();
        $this->search = $search;
        $this->indexName = $indexName;
        $this->indexType = $indexType;
    }

    public function createIndex()
    {
        $index = $this->search->createIndex($this->indexName);

        $index->mappings(
            Sherlock::mappingBuilder($this->indexType)->Analyzer()->path("contentanalyzer"),
            Sherlock::mappingBuilder($this->indexType)->String()->field('parents')->analyzer('keyword'),
            Sherlock::mappingBuilder($this->indexType)->String()->field('ancestors')->analyzer('keyword'),
            Sherlock::mappingBuilder($this->indexType)->String()->field('tags')->analyzer('keyword'),
            Sherlock::mappingBuilder($this->indexType)->String()->field('type')->analyzer('keyword'),
            Sherlock::mappingBuilder($this->indexType)->String()->field('slug')->analyzer('keyword')

        );

        $index->create();
    }

    public function populateIndex()
    {
        $nodeRepository = $this->em->getRepository('KunstmaanNodeBundle:Node');

        $languages = explode('|', $this->container->getParameter('requiredlocales'));

        $nodes = $nodeRepository->getAllTopNodes();

        foreach ($languages as $lang) {
            foreach ($nodes as $node) {
                $this->indexNode($node, $lang);
            }
        }
    }

    public function indexNode(Node $node, $lang)
    {
        $nodeTranslation = $node->getNodeTranslation($lang);
        if ($nodeTranslation) {
            if ($this->indexNodeTranslation($nodeTranslation)) {
                $this->indexChildren($node, $lang);
            }
        }
    }

    public function indexChildren(Node $node, $lang)
    {
        foreach ($node->getChildren() as $childNode) {
            $this->indexNode($childNode, $lang);
        }
    }

    /**
     * @param  NodeTranslation $nodeTranslation
     * @return bool            Return true of document has been indexed
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

                // If the page doesn't implement ShouldBeIndexed interface or it return true on shouldBeIndexed, index the page
                if (!($page instanceof ShouldBeIndexed) or $page->shouldBeIndexed()) {

                    $doc = array(
                        "node_id"               => $node->getId(),
                        "nodetranslation_id"    => $nodeTranslation->getId(),
                        "nodeversion_id"        => $publicNodeVersion->getId(),
                        "title"                 => $nodeTranslation->getTitle(),
                        "lang"                  => $nodeTranslation->getLang(),
                        "slug"                  => $nodeTranslation->getFullSlug(),
                        "type"                  => ClassLookup::getClassName($page),
                    );
                    $language = $this->container->getParameter('analyzer_languages');
                    $language = $language[$nodeTranslation->getLang()]['analyzer'];

                    $doc['contentanalyzer'] = $language;

                    // Parent and Ancestors

                    $parent = $node->getParent();
                    $parentNodeTranslation = null;

                    if ($parent) {
                        $parentNodeTranslation = $parent->getNodeTranslation($nodeTranslation->getLang());
                        $doc = array_merge($doc, array("parent" => $parent->getId()));
                        $ancestors = array();
                        do {
                            $ancestors[] = $parent->getId();
                            $parent = $parent->getParent();
                        } while ($parent);
                        $doc = array_merge($doc, array("ancestors" => $ancestors));
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
                        $view = 'KunstmaanNodeSearchBundle:PagePart:view.html.twig';
                        $content = strip_tags($renderer->render($view, array('page' => $page, 'pageparts' => $pageparts, 'pagepartviewresolver' => $this)));
                    }
                    $doc = array_merge($doc, array("content" => $content));

                    // Trigger index node event
                    $event = new IndexNodeEvent($page, $doc);

                    $dispatcher = $this->container->get('event_dispatcher');
                    $dispatcher->dispatch(Events::INDEX_NODE, $event);

                    // Add document to index

                    $uid = "nodetranslation_" . $nodeTranslation->getId();
                    $suffix = $uid;
                    if ($parentNodeTranslation) {
                        $puid = "nodetranslation_" . $parentNodeTranslation->getId();
                        //$suffix .=  "?parent=". $puid;
                    }

                    $this->search->addDocument($this->indexName, $this->indexType, $doc, $suffix);

                    return true;
                }
            }
        }

        return false;
    }

    public function deleteNodeTranslation(NodeTranslation $nodeTranslation)
    {
        $uid = "nodetranslation_" . $nodeTranslation->getId();
        $this->search->deleteDocument($this->indexName, $this->indexType, $uid);

        $ancestorQuery = Sherlock::queryBuilder()->Term()->field("ancestors")->term($nodeTranslation->getNode()->getId());
        $langQuery =  Sherlock::queryBuilder()->Term()->field("lang")->term($nodeTranslation->getLang());

        $query = Sherlock::queryBuilder()->Bool()->must($ancestorQuery, $langQuery)->minimum_number_should_match(1);

        $sherlock = $this->container->get('kunstmaan_search.searchprovider.sherlock');
        $request = $sherlock->getSherlock()->search();
        $request->query($query);
        $response = $this->search->search($this->indexName, $this->indexType, $request->toJSON(), true);

        foreach ($response['hits']['hits'] as $hit) {
            $nodetranslation_id = $hit['_source']['nodetranslation_id'];
            $childNodeTranslation = $this->em->getRepository('KunstmaanNodeBundle:NodeTranslation')->find($nodetranslation_id);
            $this->deleteNodeTranslation($childNodeTranslation);
        }

    }

    public function deleteIndex()
    {
        $this->search->deleteIndex($this->indexName);
    }
}
