<?php

namespace Kunstmaan\NodeSearchBundle\Configuration;

use Doctrine\ORM\EntityManager;
use Elastica\Index;
use Elastica\Type\Mapping;
use Kunstmaan\AdminBundle\Helper\DomainConfigurationInterface;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\MaskBuilder;
use Kunstmaan\NodeBundle\Entity\NodeVersion;
use Kunstmaan\NodeSearchBundle\Event\IndexNodeEvent;
use Kunstmaan\NodeSearchBundle\Helper\IndexablePagePartsService;
use Kunstmaan\NodeSearchBundle\Helper\SearchBoostInterface;
use Kunstmaan\NodeSearchBundle\Helper\SearchViewTemplateInterface;
use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\PagePartBundle\Helper\HasPagePartsInterface;
use Kunstmaan\SearchBundle\Configuration\SearchConfigurationInterface;
use Kunstmaan\SearchBundle\Provider\SearchProviderInterface;
use Kunstmaan\SearchBundle\Search\AnalysisFactoryInterface;
use Kunstmaan\UtilitiesBundle\Helper\ClassLookup;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Acl\Exception\AclNotFoundException;
use Symfony\Component\Security\Acl\Model\AclInterface;
use Symfony\Component\Security\Acl\Model\AclProviderInterface;
use Symfony\Component\Security\Acl\Model\AuditableEntryInterface;
use Symfony\Component\Templating\EngineInterface;

class NodePagesConfiguration implements SearchConfigurationInterface
{
    /** @var string */
    protected $indexName;

    /** @var string */
    protected $indexType;

    /** @var SearchProviderInterface */
    protected $searchProvider;

    /** @var array */
    protected $locales = array();

    /** @var array */
    protected $analyzerLanguages;

    /** @var EntityManager */
    protected $em;

    /** @var array */
    protected $documents = array();

    /** @var ContainerInterface */
    protected $container;

    /** @var AclProviderInterface */
    protected $aclProvider = null;

    /** @var LoggerInterface */
    protected $logger = null;

    /** @var IndexablePagePartsService */
    protected $indexablePagePartsService;

    /** @var DomainConfigurationInterface */
    protected $domainConfiguration;

    private $properties = [];

    /** @var Node */
    private $currentTopNode = null;

    /**
     * @param ContainerInterface      $container
     * @param SearchProviderInterface $searchProvider
     * @param string                  $name
     * @param string                  $type
     */
    public function __construct($container, $searchProvider, $name, $type)
    {
        $this->container           = $container;
        $this->indexName           = $name;
        $this->indexType           = $type;
        $this->searchProvider      = $searchProvider;
        $this->domainConfiguration = $this->container->get('kunstmaan_admin.domain_configuration');
        $this->locales             = $this->domainConfiguration->getBackendLocales();
        $this->analyzerLanguages   = $this->container->getParameter('analyzer_languages');
        $this->em                  = $this->container->get('doctrine')->getManager();
    }

    /**
     * @param AclProviderInterface $aclProvider
     */
    public function setAclProvider(AclProviderInterface $aclProvider)
    {
        $this->aclProvider = $aclProvider;
    }

    /**
     * @param IndexablePagePartsService $indexablePagePartsService
     */
    public function setIndexablePagePartsService(IndexablePagePartsService $indexablePagePartsService)
    {
        $this->indexablePagePartsService = $indexablePagePartsService;
    }

    /**
     * @param array $properties
     */
    public function setDefaultProperties(array $properties)
    {
        $this->properties = array_merge($this->properties, $properties);
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Create node index
     */
    public function createIndex()
    {
        //build new index
        $index = $this->searchProvider->createIndex($this->indexName);

        //create analysis
        $analysis = $this->container->get(
            'kunstmaan_search.search.factory.analysis'
        );
        foreach ($this->locales as $locale) {
            $analysis
                ->addIndexAnalyzer($locale)
                ->addSuggestionAnalyzer($locale);
        }

        //create index with analysis
        $this->setAnalysis($index, $analysis);

        //create mapping
        foreach ($this->locales as $locale) {
            $this->setMapping($index, $locale);
        }
    }

    /**
     * Populate node index
     */
    public function populateIndex()
    {
        $nodeRepository = $this->em->getRepository('KunstmaanNodeBundle:Node');
        $nodes          = $nodeRepository->getAllTopNodes();

        foreach ($nodes as $node) {
            $this->currentTopNode = $node;
            foreach ($this->locales as $lang) {
                $this->createNodeDocuments($node, $lang);
            }
        }

        if (!empty($this->documents)) {
            $this->searchProvider->addDocuments($this->documents);
            $this->documents = array();
        }
    }

    /**
     * Index a node (including its children) - for the specified language only
     *
     * @param Node   $node
     * @param string $lang
     */
    public function indexNode(Node $node, $lang)
    {
        $this->createNodeDocuments($node, $lang);

        if (!empty($this->documents)) {
            $this->searchProvider->addDocuments($this->documents);
            $this->documents = array();
        }
    }

    /**
     * Add documents for the node translation (and children) to the index
     *
     * @param Node   $node
     * @param string $lang
     */
    public function createNodeDocuments(Node $node, $lang)
    {
        $nodeTranslation = $node->getNodeTranslation($lang);
        if ($nodeTranslation) {
            if ($this->indexNodeTranslation($nodeTranslation)) {
                $this->indexChildren($node, $lang);
            }
        }
    }

    /**
     * Index all children of the specified node (only for the specified
     * language)
     *
     * @param Node   $node
     * @param string $lang
     */
    public function indexChildren(Node $node, $lang)
    {
        foreach ($node->getChildren() as $childNode) {
            $this->indexNode($childNode, $lang);
        }
    }

    /**
     * Index a node translation
     *
     * @param NodeTranslation $nodeTranslation
     * @param bool            $add Add node immediately to index?
     *
     * @return bool Return true if the document has been indexed
     */
    public function indexNodeTranslation(NodeTranslation $nodeTranslation, $add = false)
    {
        // Retrieve the public NodeVersion
        $publicNodeVersion = $nodeTranslation->getPublicNodeVersion();
        if (is_null($publicNodeVersion)) {
            return false;
        }

        // Retrieve the referenced entity from the public NodeVersion
        $page = $publicNodeVersion->getRef($this->em);

        if ($page->isStructureNode()) {
            return true;
        }

        // Only index online NodeTranslations
        if (!$nodeTranslation->isOnline()) {
            return false;
        }

        $node = $nodeTranslation->getNode();
        if ($this->isIndexable($page)) {
            $this->addPageToIndex($nodeTranslation, $node, $publicNodeVersion, $page);
            if ($add) {
                $this->searchProvider->addDocuments($this->documents);
                $this->documents = array();
            }
        }

        return true; // return true even if the page itself should not be indexed. This makes sure its children are being processed (i.e. structured nodes)
    }

    /**
     * Return if the page is indexable - by default all pages are indexable,
     * you can override this by implementing the IndexableInterface on your
     * page entity and returning false in the isIndexable method.
     *
     * @param HasNodeInterface $page
     *
     * @return boolean
     */
    protected function isIndexable(HasNodeInterface $page)
    {
        return $this->container->get('kunstmaan_node.pages_configuration')->isIndexable($page);
    }

    /**
     * Remove the specified node translation from the index
     *
     * @param NodeTranslation $nodeTranslation
     */
    public function deleteNodeTranslation(NodeTranslation $nodeTranslation)
    {
        $uid       = 'nodetranslation_' . $nodeTranslation->getId();
        $indexType = $this->indexType . '_' . $nodeTranslation->getLang();
        $this->searchProvider->deleteDocument($this->indexName, $indexType, $uid);
    }

    /**
     * Delete the specified index
     */
    public function deleteIndex()
    {
        $this->searchProvider->deleteIndex($this->indexName);
    }

    /**
     * Apply the analysis factory to the index
     *
     * @param Index                    $index
     * @param AnalysisFactoryInterface $analysis
     */
    public function setAnalysis(Index $index, AnalysisFactoryInterface $analysis)
    {
        $index->create(
            array(
                'number_of_shards'   => 4,
                'number_of_replicas' => 1,
                'analysis'           => $analysis->build()
            )
        );
    }

    /**
     * Return default search fields mapping for node translations
     *
     * @param Index  $index
     * @param string $lang
     *
     * @return Mapping
     */
    protected function getMapping(Index $index, $lang = 'en')
    {
        $mapping = new Mapping();
        $mapping->setType($index->getType($this->indexType . '_' . $lang));
        $mapping->setParam('analyzer', 'index_analyzer_' . $lang);
        $mapping->setParam(
            '_boost',
            array('name' => '_boost', 'null_value' => 1.0)
        );

        $mapping->setProperties($this->properties);

        return $mapping;
    }

    /**
     * Initialize the index with the default search fields mapping
     *
     * @param Index  $index
     * @param string $lang
     */
    protected function setMapping(Index $index, $lang = 'en')
    {
        $mapping = $this->getMapping($index, $lang);
        $mapping->send();
        $index->refresh();
    }

    /**
     * Create a search document for a page
     *
     * @param NodeTranslation  $nodeTranslation
     * @param Node             $node
     * @param NodeVersion      $publicNodeVersion
     * @param HasNodeInterface $page
     */
    protected function addPageToIndex(
        NodeTranslation $nodeTranslation,
        Node $node,
        NodeVersion $publicNodeVersion,
        HasNodeInterface $page
    ) {
        $rootNode = $this->currentTopNode;
        if (!$rootNode) {
            // Fetch main parent of current node...
            $rootNode = $this->em->getRepository('KunstmaanNodeBundle:Node')->getRootNodeFor(
                $node,
                $nodeTranslation->getLang()
            );
        }

        $doc = array(
            'root_id'             => $rootNode->getId(),
            'node_id'             => $node->getId(),
            'node_translation_id' => $nodeTranslation->getId(),
            'node_version_id'     => $publicNodeVersion->getId(),
            'title'               => $nodeTranslation->getTitle(),
            'lang'                => $nodeTranslation->getLang(),
            'slug'                => $nodeTranslation->getFullSlug(),
            'page_class'          => ClassLookup::getClass($page),
            'created'             => $this->getUTCDateTime(
                $nodeTranslation->getCreated()
            )->format(\DateTime::ISO8601),
            'updated'             => $this->getUTCDateTime(
                $nodeTranslation->getUpdated()
            )->format(\DateTime::ISO8601)
        );
        if ($this->logger) {
            $this->logger->info('Indexing document : ' . implode(', ', $doc));
        }

        // Permissions
        $this->addPermissions($node, $doc);

        // Search type
        $this->addSearchType($page, $doc);

        // Analyzer field
        $this->addAnalyzer($nodeTranslation, $doc);

        // Parent and Ancestors
        $this->addParentAndAncestors($node, $doc);

        // Content
        $this->addPageContent($nodeTranslation, $page, $doc);

        // Add document to index
        $uid = 'nodetranslation_' . $nodeTranslation->getId();

        $this->addBoost($node, $page, $doc);
        $this->addCustomData($page, $doc);

        $this->documents[] = $this->searchProvider->createDocument(
            $uid,
            $doc,
            $this->indexName,
            $this->indexType . '_' . $nodeTranslation->getLang()
        );
    }

    /**
     * Add view permissions to the index document
     *
     * @param Node  $node
     * @param array $doc
     *
     * @return array
     */
    protected function addPermissions(Node $node, &$doc)
    {
        if (!is_null($this->aclProvider)) {
            $roles = $this->getAclPermissions($node);
        } else {
            // Fallback when no ACL available / assume everything is accessible...
            $roles = array('IS_AUTHENTICATED_ANONYMOUSLY');
        }
        $doc['view_roles'] = $roles;
    }

    /**
     * Add type to the index document
     *
     * @param object $page
     * @param array  $doc
     *
     * @return array
     */
    protected function addSearchType($page, &$doc)
    {
        $doc['type'] = $this->container->get('kunstmaan_node.pages_configuration')->getSearchType($page);
    }

    /**
     * Add content analyzer to the index document
     *
     * @param NodeTranslation $nodeTranslation
     * @param array           $doc
     *
     * @return array
     */
    protected function addAnalyzer(NodeTranslation $nodeTranslation, &$doc)
    {
        $language               = $this->analyzerLanguages[$nodeTranslation->getLang()]['analyzer'];
        $doc['contentanalyzer'] = $language;
    }

    /**
     * Add parent nodes to the index document
     *
     * @param Node  $node
     * @param array $doc
     *
     * @return array
     */
    protected function addParentAndAncestors($node, &$doc)
    {
        $parent = $node->getParent();

        if ($parent) {
            $doc['parent'] = $parent->getId();
            $ancestors     = array();
            do {
                $ancestors[] = $parent->getId();
                $parent      = $parent->getParent();
            } while ($parent);
            $doc['ancestors'] = $ancestors;
        }
    }

    /**
     * Add page content to the index document
     *
     * @param NodeTranslation  $nodeTranslation
     * @param HasNodeInterface $page
     * @param array            $doc
     *
     * @return array
     */
    protected function addPageContent(NodeTranslation $nodeTranslation, $page, &$doc)
    {
        $this->enterRequestScope($nodeTranslation->getLang());
        if ($this->logger) {
            $this->logger->debug(
                sprintf(
                    'Indexing page "%s" / lang : %s / type : %s / id : %d / node id : %d',
                    $page->getTitle(),
                    $nodeTranslation->getLang(),
                    get_class($page),
                    $page->getId(),
                    $nodeTranslation->getNode()->getId()
                )
            );
        }

        $renderer       = $this->container->get('templating');
        $doc['content'] = '';

        if ($page instanceof SearchViewTemplateInterface) {
            $doc['content'] = $this->renderCustomSearchView($nodeTranslation, $page, $renderer);

            return;
        }

        if ($page instanceof HasPagePartsInterface) {
            $doc['content'] = $this->renderDefaultSearchView($nodeTranslation, $page, $renderer);

            return;
        }
    }

    /**
     * Enter request scope if it is not active yet...
     *
     * @param string $lang
     */
    protected function enterRequestScope($lang)
    {
        if (!$this->container->isScopeActive('request')) {
            $this->container->enterScope('request');
            $request = new Request();
            $request->setLocale($lang);

            $context = $this->container->get('router')->getContext();
            $context->setParameter('_locale', $lang);

            $major = Kernel::MAJOR_VERSION;
            $minor = Kernel::MINOR_VERSION;
            if ((int) $major > 2 || ((int) $major == 2 && (int) $minor >= 4)) {
                $requestStack = $this->container->get('request_stack');
                $requestStack->push($request);
            }

            $this->container->set('request', $request, 'request');
        }
    }

    /**
     * Render a custom search view
     *
     * @param NodeTranslation             $nodeTranslation
     * @param SearchViewTemplateInterface $page
     * @param EngineInterface             $renderer
     *
     * @return string
     */
    protected function renderCustomSearchView(
        NodeTranslation $nodeTranslation,
        SearchViewTemplateInterface $page,
        EngineInterface $renderer
    ) {
        $view    = $page->getSearchView();
        $content = $this->removeHtml(
            $renderer->render(
                $view,
                array(
                    'locale'    => $nodeTranslation->getLang(),
                    'page'      => $page,
                    'indexMode' => true
                )
            )
        );

        return $content;
    }

    /**
     * Render default search view (all indexable pageparts in the main context
     * of the page)
     *
     * @param NodeTranslation       $nodeTranslation
     * @param HasPagePartsInterface $page
     * @param EngineInterface       $renderer
     *
     * @return string
     */
    protected function renderDefaultSearchView(
        NodeTranslation $nodeTranslation,
        HasPagePartsInterface $page,
        EngineInterface $renderer
    ) {
        $pageparts = $this->indexablePagePartsService->getIndexablePageParts($page);
        $view      = 'KunstmaanNodeSearchBundle:PagePart:view.html.twig';
        $content   = $this->removeHtml(
            $renderer->render(
                $view,
                array(
                    'locale'    => $nodeTranslation->getLang(),
                    'page'      => $page,
                    'pageparts' => $pageparts,
                    'indexMode' => true
                )
            )
        );

        return $content;
    }

    /**
     * Add boost to the index document
     *
     * @param Node             $node
     * @param HasNodeInterface $page
     * @param array            $doc
     */
    protected function addBoost($node, HasNodeInterface $page, &$doc)
    {
        // Check page type boost
        $doc['_boost'] = 1.0;
        if ($page instanceof SearchBoostInterface) {
            $doc['_boost'] += $page->getSearchBoost();
        }

        // Check if page is boosted
        $nodeSearch = $this->em->getRepository('KunstmaanNodeSearchBundle:NodeSearch')->findOneByNode($node);
        if ($nodeSearch !== null) {
            $doc['_boost'] += $nodeSearch->getBoost();
        }
    }

    /**
     * Add custom data to index document (you can override to add custom fields
     * to the search index)
     *
     * @param HasNodeInterface $page
     * @param array            $doc
     */
    protected function addCustomData(HasNodeInterface $page, &$doc)
    {
        $event = new IndexNodeEvent($page, $doc);
        $this->container->get('event_dispatcher')->dispatch(IndexNodeEvent::EVENT_INDEX_NODE, $event);

        $doc = $event->doc;

        if ($page instanceof HasCustomSearchDataInterface) {
            $doc += $page->getCustomSearchData($doc);
        }
    }

    /**
     * Convert a DateTime to UTC equivalent...
     *
     * @param \DateTime $dateTime
     *
     * @return \DateTime
     */
    protected function getUTCDateTime(\DateTime $dateTime)
    {
        $result = clone $dateTime;
        $result->setTimezone(new \DateTimeZone('UTC'));

        return $result;
    }

    /**
     * Removes all HTML markup & decode HTML entities
     *
     * @param $text
     *
     * @return string
     */
    protected function removeHtml($text)
    {
        // Remove HTML markup
        $result = strip_tags($text);

        // Decode HTML entities
        $result = trim(html_entity_decode($result, ENT_QUOTES));

        return $result;
    }

    /**
     * Fetch ACL permissions for the specified entity
     *
     * @param object $object
     *
     * @return array
     */
    protected function getAclPermissions($object)
    {
        $roles = array();
        try {
            $objectIdentity = ObjectIdentity::fromDomainObject($object);

            /* @var AclInterface $acl */
            $acl        = $this->aclProvider->findAcl($objectIdentity);
            $objectAces = $acl->getObjectAces();

            /* @var AuditableEntryInterface $ace */
            foreach ($objectAces as $ace) {
                $securityIdentity = $ace->getSecurityIdentity();
                if (
                    $securityIdentity instanceof RoleSecurityIdentity &&
                    ($ace->getMask() & MaskBuilder::MASK_VIEW != 0)
                ) {
                    $roles[] = $securityIdentity->getRole();
                }
            }
        } catch (AclNotFoundException $e) {
            // No ACL found... assume default
            $roles = array('IS_AUTHENTICATED_ANONYMOUSLY');
        }

        return $roles;
    }
}
