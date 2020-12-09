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
use Kunstmaan\NodeSearchBundle\Services\SearchViewRenderer;
use Kunstmaan\PagePartBundle\Helper\HasPagePartsInterface;
use Kunstmaan\SearchBundle\Configuration\SearchConfigurationInterface;
use Kunstmaan\SearchBundle\Provider\SearchProviderInterface;
use Kunstmaan\SearchBundle\Search\AnalysisFactoryInterface;
use Kunstmaan\UtilitiesBundle\Helper\ClassLookup;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\LegacyEventDispatcherProxy;
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

    /** @var int */
    protected $numberOfShards;

    /** @var int */
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
        $this->container = $container;
        $this->indexName = $name;
        $this->indexType = $type;
        $this->searchProvider = $searchProvider;
        $this->domainConfiguration = $this->container->get('kunstmaan_admin.domain_configuration');
        $this->locales = $this->domainConfiguration->getBackendLocales();
        $this->analyzerLanguages = $this->container->getParameter('analyzer_languages');
        $this->em = $this->container->get('doctrine')->getManager();
        $this->numberOfShards = $numberOfShards;
        $this->numberOfReplicas = $numberOfReplicas;
    }

    public function setAclProvider(AclProviderInterface $aclProvider)
    {
        $this->aclProvider = $aclProvider;
    }

    public function setIndexablePagePartsService(IndexablePagePartsService $indexablePagePartsService)
    {
        $this->indexablePagePartsService = $indexablePagePartsService;
    }

    public function setDefaultProperties(array $properties)
    {
        $this->properties = array_merge($this->properties, $properties);
    }

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

            if (false === \array_key_exists($locale, $this->analyzerLanguages)) {
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

            if (\array_key_exists($locale, $this->analyzerLanguages)) {
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
        $nodeRepository = $this->em->getRepository(Node::class);
        $nodes = $nodeRepository->getAllTopNodes();

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
     * @param string $lang
     */
    public function createNodeDocuments(Node $node, $lang)
    {
        $nodeTranslation = $node->getNodeTranslation($lang, true);
        if ($nodeTranslation && $this->indexNodeTranslation($nodeTranslation)) {
            $this->indexChildren($node, $lang);
        }
    }

    /**
     * Index all children of the specified node (only for the specified
     * language)
     *
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
     * @param bool $add Add node immediately to index?
     *
     * @return bool Return true if the document has been indexed
     */
    public function indexNodeTranslation(NodeTranslation $nodeTranslation, $add = false)
    {
        // Retrieve the public NodeVersion
        $publicNodeVersion = $nodeTranslation->getPublicNodeVersion();
        if (\is_null($publicNodeVersion)) {
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
     * @return bool
     */
    protected function isIndexable(HasNodeInterface $page)
    {
        return $this->container->get('kunstmaan_node.pages_configuration')->isIndexable($page);
    }

    /**
     * Remove the specified node translation from the index
     */
    public function deleteNodeTranslation(NodeTranslation $nodeTranslation)
    {
        $uid = 'nodetranslation_' . $nodeTranslation->getId();
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
     */
    public function setAnalysis(Index $index, AnalysisFactoryInterface $analysis)
    {
        $analysers = $analysis->build();
        $args = [
            'number_of_shards' => $this->numberOfShards,
            'number_of_replicas' => $this->numberOfReplicas,
            'analysis' => $analysers,
        ];

        if (class_exists(\Elastica\Mapping::class)) {
            $args = [
                'settings' => [
                    'number_of_shards' => $this->numberOfShards,
                    'number_of_replicas' => $this->numberOfReplicas,
                    'analysis' => $analysers,
                ],
            ];

            $ngramDiff = 1;
            if (isset($analysers['tokenizer']) && count($analysers['tokenizer']) > 0) {
                foreach ($analysers['tokenizer'] as $tokenizer) {
                    if ($tokenizer['type'] === 'nGram') {
                        $diff = $tokenizer['max_gram'] - $tokenizer['min_gram'];

                        $ngramDiff = $diff > $ngramDiff ? $diff : $ngramDiff;
                    }
                }
            }

            if ($ngramDiff > 1) {
                $args['settings']['max_ngram_diff'] = $ngramDiff;
            }
        }

        $index->create($args);
    }

    /**
     * Return default search fields mapping for node translations
     *
     * @param string $lang
     *
     * @return Mapping|\Elastica\Mapping
     */
    protected function createDefaultSearchFieldsMapping(Index $index, $lang = 'en')
    {
        if (class_exists(\Elastica\Type\Mapping::class)) {
            $mapping = new Mapping();
            $mapping->setType($index->getType($this->indexType));
        } else {
            $mapping = new \Elastica\Mapping();
        }

        $mapping->setProperties($this->properties);

        return $mapping;
    }

    /**
     * Initialize the index with the default search fields mapping
     *
     * @param string $lang
     */
    protected function setMapping(Index $index, $lang = 'en')
    {
        $mapping = $this->createDefaultSearchFieldsMapping($index, $lang);
        if (class_exists(\Elastica\Mapping::class)) {
            $mapping->send($index);
        } else {
            $mapping->send();
        }
        $index->refresh();
    }

    /**
     * Create a search document for a page
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
            $rootNode = $this->em->getRepository(Node::class)->getRootNodeFor(
                $node,
                $nodeTranslation->getLang()
            );
        }

        $doc = [
            'root_id' => $rootNode->getId(),
            'node_id' => $node->getId(),
            'node_translation_id' => $nodeTranslation->getId(),
            'node_version_id' => $publicNodeVersion->getId(),
            'title' => $nodeTranslation->getTitle(),
            'slug' => $nodeTranslation->getFullSlug(),
            'page_class' => ClassLookup::getClass($page),
            'created' => $this->getUTCDateTime(
                $nodeTranslation->getCreated()
            )->format(\DateTime::ISO8601),
            'updated' => $this->getUTCDateTime(
                $nodeTranslation->getUpdated()
            )->format(\DateTime::ISO8601),
        ];
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
     * @param array $doc
     *
     * @return array
     */
    protected function addPermissions(Node $node, &$doc)
    {
        if (!\is_null($this->aclProvider)) {
            $roles = $this->getAclPermissions($node);
        } else {
            // Fallback when no ACL available / assume everything is accessible...
            $roles = ['IS_AUTHENTICATED_ANONYMOUSLY'];
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
            $ancestors = [];
            do {
                $ancestors[] = $parent->getId();
                $parent = $parent->getParent();
            } while ($parent);
            $doc['ancestors'] = $ancestors;
        }
    }

    /**
     * Add page content to the index document
     *
     * @param HasNodeInterface $page
     * @param array            $doc
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
                    \get_class($page),
                    $page->getId(),
                    $nodeTranslation->getNode()->getId()
                )
            );
        }

        $doc['content'] = '';
        if ($page instanceof SearchViewTemplateInterface) {
            if ($this->isMethodOverridden('renderCustomSearchView')) {
                @trigger_error(sprintf('Overriding the "%s" method is deprecated since KunstmaanNodeSearchBundle 5.7 and will be removed in KunstmaanNodeSearchBundle 6.0. Override the "renderCustomSearchView" method of the "%s" service instead.', __METHOD__, SearchViewRenderer::class), E_USER_DEPRECATED);

                $doc['content'] = $this->renderCustomSearchView($nodeTranslation, $page, $this->container->get('templating'));
            } else {
                $searchViewRenderer = $this->container->get('kunstmaan_node_search.service.search_view_renderer');

                $doc['content'] = $searchViewRenderer->renderCustomSearchView($nodeTranslation, $page, $this->container);
            }

            return null;
        }

        if ($page instanceof HasPagePartsInterface) {
            if ($this->isMethodOverridden('renderDefaultSearchView')) {
                @trigger_error(sprintf('Overriding the "%s" method is deprecated since KunstmaanNodeSearchBundle 5.7 and will be removed in KunstmaanNodeSearchBundle 6.0. Override the "renderDefaultSearchView" method of the "%s" service instead.', __METHOD__, SearchViewRenderer::class), E_USER_DEPRECATED);

                $doc['content'] = $this->renderDefaultSearchView($nodeTranslation, $page, $this->container->get('templating'));
            } else {
                $searchViewRenderer = $this->container->get('kunstmaan_node_search.service.search_view_renderer');

                $doc['content'] = $searchViewRenderer->renderDefaultSearchView($nodeTranslation, $page);
            }

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
        $locale = null;
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
     * @deprecated This method is deprecated since KunstmaanNodeSearchBundle 5.7 and will be removed in KunstmaanNodeSearchBundle 6.0. Use the "renderCustomSearchView" method of the "Kunstmaan\NodeSearchBundle\Services\SearchViewRenderer" instead.
     *
     * @return string
     */
    protected function renderCustomSearchView(
        NodeTranslation $nodeTranslation,
        SearchViewTemplateInterface $page,
        EngineInterface $renderer
    ) {
        @trigger_error(sprintf('The "%s" method is deprecated since KunstmaanNodeSearchBundle 5.7 and will be removed in KunstmaanNodeSearchBundle 6.0. Use the "%s" service with method "renderCustomSearchView" instead.', __METHOD__, SearchViewRenderer::class), E_USER_DEPRECATED);

        $view = $page->getSearchView();
        $renderContext = new RenderContext([
            'locale' => $nodeTranslation->getLang(),
            'page' => $page,
            'indexMode' => true,
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
     * @deprecated This method is deprecated since KunstmaanNodeSearchBundle 5.7 and will be removed in KunstmaanNodeSearchBundle 6.0. Use the "renderDefaultSearchView" method of the "Kunstmaan\NodeSearchBundle\Services\SearchViewRenderer" instead.
     *
     * @return string
     */
    protected function renderDefaultSearchView(
        NodeTranslation $nodeTranslation,
        HasPagePartsInterface $page,
        EngineInterface $renderer
    ) {
        @trigger_error(sprintf('The "%s" method is deprecated since KunstmaanNodeSearchBundle 5.7 and will be removed in KunstmaanNodeSearchBundle 6.0. Use the "%s" service with method "renderDefaultSearchView" instead.', __METHOD__, SearchViewRenderer::class), E_USER_DEPRECATED);

        $pageparts = $this->indexablePagePartsService->getIndexablePageParts($page);
        $view = '@KunstmaanNodeSearch/PagePart/view.html.twig';
        $content = $this->removeHtml(
            $renderer->render(
                $view,
                [
                    'locale' => $nodeTranslation->getLang(),
                    'page' => $page,
                    'pageparts' => $pageparts,
                    'indexMode' => true,
                ]
            )
        );

        return $content;
    }

    /**
     * Add custom data to index document (you can override to add custom fields
     * to the search index)
     *
     * @param array $doc
     */
    protected function addCustomData(HasNodeInterface $page, &$doc)
    {
        $event = new IndexNodeEvent($page, $doc);
        $this->dispatch($event, IndexNodeEvent::EVENT_INDEX_NODE);

        $doc = $event->doc;

        if ($page instanceof HasCustomSearchDataInterface) {
            $doc += $page->getCustomSearchData($doc);
        }
    }

    /**
     * Convert a DateTime to UTC equivalent...
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
     * @deprecated This method is deprecated since KunstmaanNodeSearchBundle 5.7 and will be removed in KunstmaanNodeSearchBundle 6.0. Use the "removeHtml" method of the "Kunstmaan\NodeSearchBundle\Services\SearchViewRenderer" instead.
     *
     * @param $text
     *
     * @return string
     */
    protected function removeHtml($text)
    {
        @trigger_error(sprintf('The "%s" method is deprecated since KunstmaanNodeSearchBundle 5.7 and will be removed in KunstmaanNodeSearchBundle 6.0. Use the "removeHtml" method of the "%s" service instead.', __METHOD__, SearchViewRenderer::class), E_USER_DEPRECATED);

        $searchViewRenderer = $this->container->get('kunstmaan_node_search.service.search_view_renderer');

        return $searchViewRenderer->removeHtml($text);
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
            $acl = $this->aclProvider->findAcl($objectIdentity);
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
            $roles = ['IS_AUTHENTICATED_ANONYMOUSLY'];
        }

        return $roles;
    }

    /**
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

    /**
     * @param object $event
     *
     * @return object
     */
    private function dispatch($event, string $eventName)
    {
        $eventDispatcher = $this->container->get('event_dispatcher');
        if (class_exists(LegacyEventDispatcherProxy::class)) {
            $eventDispatcher = LegacyEventDispatcherProxy::decorate($eventDispatcher);

            return $eventDispatcher->dispatch($event, $eventName);
        }

        return $eventDispatcher->dispatch($eventName, $event);
    }

    private function isMethodOverridden(string $method)
    {
        $reflector = new \ReflectionMethod($this, $method);

        return $reflector->getDeclaringClass()->getName() !== __CLASS__;
    }
}
