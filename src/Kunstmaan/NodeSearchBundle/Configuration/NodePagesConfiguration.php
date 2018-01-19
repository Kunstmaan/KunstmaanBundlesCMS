<?php

namespace Kunstmaan\NodeSearchBundle\Configuration;

use Doctrine\ORM\EntityManager;
use Elastica\Index;
use Elastica\Type\Mapping;
use Kunstmaan\AdminBundle\Helper\DomainConfigurationInterface;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\MaskBuilder;
use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Entity\NodeVersion;
use Kunstmaan\NodeBundle\Entity\PageInterface;
use Kunstmaan\NodeBundle\Helper\RenderContext;
use Kunstmaan\NodeSearchBundle\Event\IndexNodeEvent;
use Kunstmaan\NodeSearchBundle\Helper\IndexablePagePartsService;
use Kunstmaan\NodeSearchBundle\Helper\SearchViewTemplateInterface;
use Kunstmaan\PagePartBundle\Helper\HasPagePartsInterface;
use Kunstmaan\SearchBundle\Configuration\SearchConfigurationInterface;
use Kunstmaan\SearchBundle\Provider\SearchProviderInterface;
use Kunstmaan\SearchBundle\Search\AnalysisFactoryInterface;
use Kunstmaan\UtilitiesBundle\Helper\ClassLookup;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Request;
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
    protected $locales = [];

    /** @var array */
    protected $analyzerLanguages;

    /** @var EntityManager */
    protected $em;

    /** @var array */
    protected $documents = [];

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

    /** @var array */
    protected $properties = [];
    
    /** @var integer */
    protected $numberOfShards;

    /** @var integer */
    protected $numberOfReplicas;

    /** @var Node */
    protected $currentTopNode = null;

    /** @var array */
    protected $nodeRefs = [];

    /**
     * @param ContainerInterface      $container
     * @param SearchProviderInterface $searchProvider
     * @param string                  $name
     * @param string                  $type
     */
    public function __construct($container, $searchProvider, $name, $type, $numberOfShards = 1, $numberOfReplicas = 0)
    {
        $this->container           = $container;
        $this->indexName           = $name;
        $this->indexType           = $type;
        $this->searchProvider      = $searchProvider;
        $this->domainConfiguration = $this->container->get('kunstmaan_admin.domain_configuration');
        $this->locales             = $this->domainConfiguration->getBackendLocales();
        $this->analyzerLanguages   = $this->container->getParameter('analyzer_languages');
        $this->em                  = $this->container->get('doctrine')->getManager();
        $this->numberOfShards      = $numberOfShards;
        $this->numberOfReplicas    = $numberOfReplicas;
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
     * @return array
     */
    public function getLanguagesNotAnalyzed()
    {
        $notAnalyzed = [];
        foreach ($this->locales as $locale) {
            if (preg_match('/[a-z]{2}_?+[a-zA-Z]{2}/', $locale)) {
                $locale = strtolower($locale);
            }

            if ( false === array_key_exists($locale, $this->analyzerLanguages) ) {
                $notAnalyzed[] = $locale;
            }
        }

        return $notAnalyzed;
    }

    /**
     * Create node index
     */
    public function createIndex()
    {
        //create analysis
        $analysis = $this->container->get(
            'kunstmaan_search.search.factory.analysis'
        );

        foreach ($this->locales as $locale) {
            // Multilanguage check
            if (preg_match('/[a-z]{2}_?+[a-zA-Z]{2}/', $locale)) {
                $locale = strtolower($locale);
            }

            // Build new index
            $index = $this->searchProvider->createIndex($this->indexName . '_' . $locale);

            if (array_key_exists($locale, $this->analyzerLanguages)) {
                $localeAnalysis = clone $analysis;
                $language = $this->analyzerLanguages[$locale]['analyzer'];

                // Create index with analysis
                $this->setAnalysis($index, $localeAnalysis->setupLanguage($language));
            } else {
                $index->create();
            }

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
            $this->documents = [];
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
            $this->documents = [];
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
        $nodeTranslation = $node->getNodeTranslation($lang, true);
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

        $refPage = $this->getNodeRefPage($publicNodeVersion);
        if ($refPage->isStructureNode()) {
            return true;
        }

        // Only index online NodeTranslations
        if (!$nodeTranslation->isOnline()) {
            return false;
        }

        $node = $nodeTranslation->getNode();
        if ($this->isIndexable($refPage)) {
            // Retrieve the referenced entity from the public NodeVersion
            $page = $publicNodeVersion->getRef($this->em);

            $this->addPageToIndex($nodeTranslation, $node, $publicNodeVersion, $page);
            if ($add) {
                $this->searchProvider->addDocuments($this->documents);
                $this->documents = [];
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
        $indexName = $this->indexName . '_' . $nodeTranslation->getLang();
        $this->searchProvider->deleteDocument($indexName, $this->indexType, $uid);
    }

    /**
     * Delete the specified index
     */
    public function deleteIndex()
    {
        foreach ($this->locales as $locale) {
            $this->searchProvider->deleteIndex($this->indexName . '_' . $locale);
        }
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
                'number_of_shards'   => $this->numberOfShards,
                'number_of_replicas' => $this->numberOfReplicas,
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
    protected function createDefaultSearchFieldsMapping(Index $index, $lang = 'en')
    {
        $mapping = new Mapping();
        $mapping->setType($index->getType($this->indexType));

        $mapping->setProperties($this->properties);

        return $mapping;
    }

    /**
     * Initialize the index with the default search fields mapping
     *
     * @param Index  $index
     * @param string $lang
     */
    protected function setMapping(Index $index, $lang='en')
    {
        $mapping = $this->createDefaultSearchFieldsMapping($index, $lang);
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

        // Parent and Ancestors
        $this->addParentAndAncestors($node, $doc);

        // Content
        $this->addPageContent($nodeTranslation, $page, $doc);

        // Add document to index
        $uid = 'nodetranslation_' . $nodeTranslation->getId();

        $this->addCustomData($page, $doc);

        $this->documents[] = $this->searchProvider->createDocument(
            $uid,
            $doc,
            $this->indexName . '_' . $nodeTranslation->getLang(),
            $this->indexType
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
            $ancestors     = [];
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
     * @return null
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

            return null;
        }

        if ($page instanceof HasPagePartsInterface) {
            $doc['content'] = $this->renderDefaultSearchView($nodeTranslation, $page, $renderer);

            return null;
        }
    }

    /**
     * Enter request scope if it is not active yet...
     *
     * @param string $lang
     */
    protected function enterRequestScope($lang)
    {
        $requestStack = $this->container->get('request_stack');
        // If there already is a request, get the locale from it.
        if ($requestStack->getCurrentRequest()) {
            $locale = $requestStack->getCurrentRequest()->getLocale();
        }
        // If we don't have a request or the current request locale is different from the node langauge
        if (!$requestStack->getCurrentRequest() || ($locale && $locale !== $lang)) {
            $request = new Request();
            $request->setLocale($lang);

            $context = $this->container->get('router')->getContext();
            $context->setParameter('_locale', $lang);

            $requestStack->push($request);
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
        $view = $page->getSearchView();
        $renderContext = new RenderContext([
            'locale'          => $nodeTranslation->getLang(),
            'page'            => $page,
            'indexMode'       => true,
            'nodetranslation' => $nodeTranslation,
        ]);

        if ($page instanceof PageInterface) {
            $request = $this->container->get('request_stack')->getCurrentRequest();
            $page->service($this->container, $request, $renderContext);
        }

        $content = $this->removeHtml(
            $renderer->render(
                $view,
                $renderContext->getArrayCopy()
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
        if (!trim($text)) {
            return '';
        }
        
        // Remove Styles and Scripts
        $crawler = new Crawler($text);
        $crawler->filter('style, script')->each(function (Crawler $crawler) {
            foreach ($crawler as $node) {
                $node->parentNode->removeChild($node);
            }
        });
        $text = $crawler->html();

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
        $roles = [];
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

    /**
     * @param $publicNodeVersion
     *
     * @return mixed
     */
    private function getNodeRefPage(NodeVersion $publicNodeVersion)
    {
        $refEntityName = $publicNodeVersion->getRefEntityName();

        if (!isset($this->nodeRefs[$refEntityName])) {
            $this->nodeRefs[$refEntityName] = new $refEntityName();
        }

        return $this->nodeRefs[$refEntityName];
    }
}
