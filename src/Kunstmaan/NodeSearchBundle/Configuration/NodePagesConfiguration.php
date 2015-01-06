<?php

namespace Kunstmaan\NodeSearchBundle\Configuration;

use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\MaskBuilder;
use Kunstmaan\NodeBundle\Entity\NodeVersion;
use Kunstmaan\NodeSearchBundle\Helper\IndexablePagePartsService;
use Kunstmaan\NodeSearchBundle\Helper\SearchBoostInterface;
use Kunstmaan\NodeSearchBundle\Helper\SearchViewTemplateInterface;
use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeSearchBundle\Helper\SearchTypeInterface;
use Kunstmaan\PagePartBundle\Helper\HasPagePartsInterface;
use Kunstmaan\SearchBundle\Configuration\SearchConfigurationInterface;
use Kunstmaan\SearchBundle\Helper\IndexableInterface;
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

class NodePagesConfiguration implements SearchConfigurationInterface
{
    /** @var string */
    private $indexName;

    /** @var string */
    private $indexType;

    /** @var SearchProviderInterface */
    private $searchProvider;

    /** @var array */
    private $locales = array();

    /** @var array */
    private $analyzerLanguages;

    /** @var EntityManager */
    private $em;

    /** @var array */
    private $documents = array();

    /** @var ContainerInterface */
    private $container;

    /** @var AclProviderInterface */
    private $aclProvider = null;

    /** @var LoggerInterface */
    private $logger = null;

    /** @var IndexablePagePartsService */
    private $indexablePagePartsService;

    /**
     * @param ContainerInterface      $container
     * @param SearchProviderInterface $searchProvider
     * @param string                  $name
     * @param string                  $type
     */
    public function __construct($container, $searchProvider, $name, $type)
    {
        $this->container         = $container;
        $this->indexName         = $name;
        $this->indexType         = $type;
        $this->searchProvider    = $searchProvider;
        $this->locales           = explode('|', $this->container->getParameter('requiredlocales'));
        $this->analyzerLanguages = $this->container->getParameter('analyzer_languages');
        $this->em                = $this->container->get('doctrine')->getManager();
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
        $analysis = $this->container->get('kunstmaan_search.search.factory.analysis');
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
     * Index all children of the specified node (only for the specified language)
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
        // Only index online NodeTranslations
        if (!$nodeTranslation->isOnline()) {
            return false;
        }

        // Retrieve the public NodeVersion
        $publicNodeVersion = $nodeTranslation->getPublicNodeVersion();
        if (is_null($publicNodeVersion)) {
            return false;
        }
        $node = $nodeTranslation->getNode();

        // Retrieve the referenced entity from the public NodeVersion
        $page = $publicNodeVersion->getRef($this->em);

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
     * Return if the page is indexable - by default all pages are indexable, you can override this by implementing
     * the IndexableInterface on your page entity and returning false in the isIndexable method.
     *
     * @param HasNodeInterface $page
     *
     * @return boolean
     */
    protected function isIndexable(HasNodeInterface $page)
    {
        // If the page doesn't implement IndexableInterface interface or it returns true on isIndexable, index the page
        return (!($page instanceof IndexableInterface) || $page->isIndexable());
    }

    /**
     * Remove the specified node translation from the index
     *
     * @param NodeTranslation $nodeTranslation
     */
    public function deleteNodeTranslation(NodeTranslation $nodeTranslation)
    {
        $uid = 'nodetranslation_' . $nodeTranslation->getId();
        $this->searchProvider->deleteDocument($this->indexName, $this->indexType, $uid);
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
     * @param \Elastica\Index          $index
     * @param AnalysisFactoryInterface $analysis
     */
    public function setAnalysis(\Elastica\Index $index, AnalysisFactoryInterface $analysis)
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
     * @param \Elastica\Index $index
     * @param string          $lang
     *
     * @return \Elastica\Type\Mapping
     */
    protected function getMapping(\Elastica\Index $index, $lang = 'en')
    {
        $mapping = new \Elastica\Type\Mapping();
        $mapping->setType($index->getType($this->indexType . '_' . $lang));
        $mapping->setParam('analyzer', 'index_analyzer_' . $lang);
        $mapping->setParam('_boost', array('name' => '_boost', 'null_value' => 1.0));

        $mapping->setProperties(
            array(
                'node_id'            => array(
                    'type'           => 'integer',
                    'include_in_all' => false,
                    'index'          => 'not_analyzed'
                ),
                'nodetranslation_id' => array(
                    'type'           => 'integer',
                    'include_in_all' => false,
                    'index'          => 'not_analyzed'
                ),
                'nodeversion_id'     => array(
                    'type'           => 'integer',
                    'include_in_all' => false,
                    'index'          => 'not_analyzed'
                ),
                'title'              => array(
                    'type'           => 'string',
                    'include_in_all' => true
                ),
                'lang'               => array(
                    'type'           => 'string',
                    'include_in_all' => true,
                    'index'          => 'not_analyzed'
                ),
                'slug'               => array(
                    'type'           => 'string',
                    'include_in_all' => false,
                    'index'          => 'not_analyzed'
                ),
                'type'               => array(
                    'type'           => 'string',
                    'include_in_all' => false,
                    'index'          => 'not_analyzed'
                ),
                'page_class'         => array(
                    'type'           => 'string',
                    'include_in_all' => false,
                    'index'          => 'not_analyzed'
                ),
                'contentanalyzer'    => array(
                    'type'           => 'string',
                    'include_in_all' => true,
                    'index'          => 'not_analyzed'
                ),
                'content'            => array(
                    'type'           => 'string',
                    'include_in_all' => true
                ),
                'created'            => array(
                    'type'           => 'date',
                    'include_in_all' => false,
                    'index'          => 'not_analyzed'
                ),
                'updated'            => array(
                    'type'           => 'date',
                    'include_in_all' => false,
                    'index'          => 'not_analyzed'
                ),
                'view_roles'         => array(
                    'type'           => 'string',
                    'include_in_all' => true,
                    'index'          => 'not_analyzed',
                    'index_name'     => 'view_role'
                ),
                '_boost'             => array(
                    'type'           => 'float',
                    'include_in_all' => false
                )
            )
        );

        return $mapping;
    }

    /**
     * Initialize the index with the default search fields mapping
     *
     * @param \Elastica\Index $index
     * @param string          $lang
     */
    protected function setMapping(\Elastica\Index $index, $lang = 'en')
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
        $doc = array(
            'node_id'             => $node->getId(),
            'node_translation_id' => $nodeTranslation->getId(),
            'node_version_id'     => $publicNodeVersion->getId(),
            'title'               => $nodeTranslation->getTitle(),
            'lang'                => $nodeTranslation->getLang(),
            'slug'                => $nodeTranslation->getFullSlug(),
            'page_class'          => ClassLookup::getClass($page),
            'created'             => $this->getUTCDateTime($nodeTranslation->getCreated())->format(\DateTime::ISO8601),
            'updated'             => $this->getUTCDateTime($nodeTranslation->getUpdated())->format(\DateTime::ISO8601)
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
        $roles = array();
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
        // Type
        $type = ClassLookup::getClassName($page);
        if ($page instanceof SearchTypeInterface) {
            $type = $page->getSearchType();
        }
        $doc['type'] = $type;
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
        $parent                = $node->getParent();
        $parentNodeTranslation = null;

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

            $major = Kernel::MAJOR_VERSION;
            $minor = Kernel::MINOR_VERSION;
            if ((int)$major > 2 || ((int)$major == 2 && (int)$minor >= 4)) {
                $requestStack = $this->container->get('request_stack');
                $requestStack->push($request);
            }

            $this->container->set('request', $request, 'request');
        }
    }

    /**
     * Render a custom search view
     *
     * @param NodeTranslation $nodeTranslation
     * @param                 $page
     * @param                 $renderer
     *
     * @return string
     */
    protected function renderCustomSearchView(NodeTranslation $nodeTranslation, $page, $renderer)
    {
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
     * Render default search view (all indexable pageparts in the main context of the page)
     *
     * @param NodeTranslation $nodeTranslation
     * @param                 $page
     * @param                 $renderer
     *
     * @return string
     */
    protected function renderDefaultSearchView(NodeTranslation $nodeTranslation, $page, $renderer)
    {
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
        $nodeSearch = $this->em->getRepository('KunstmaanNodeSearchBundle:NodeSearch')
            ->findOneByNode($node);
        if ($nodeSearch !== null) {
            $doc['_boost'] += $nodeSearch->getBoost();
        }
    }

    /**
     * Add custom data to index document (you can override to add custom fields to the search index)
     *
     * @param HasNodeInterface $page
     * @param array            $doc
     */
    protected function addCustomData(HasNodeInterface $page, &$doc)
    {
        // You can add custom data to be added to the document index array ($doc) here if you inherit from this class...
    }

    /**
     * Convert a DateTime to UTC equivalent...
     *
     * @param \DateTime $dateTime
     *
     * @return \DateTime
     */
    private function getUTCDateTime(\DateTime $dateTime)
    {
        $result = clone $dateTime;
        $result->setTimezone(new \DateTimeZone('UTC'));

        return $result;
    }

    /**
     * Removes all HTML markup & decode HTML entities
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
