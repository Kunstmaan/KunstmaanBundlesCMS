<?php

namespace Kunstmaan\NodeSearchBundle\Configuration;

use Doctrine\ORM\EntityManagerInterface;
use Elastica\Index;
use Elastica\Type\Mapping;
use Kunstmaan\AdminBundle\Helper\DomainConfigurationInterface;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\MaskBuilder;
use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Entity\NodeVersion;
use Kunstmaan\NodeBundle\Helper\PagesConfiguration;
use Kunstmaan\NodeSearchBundle\Helper\PageHelper;
use Kunstmaan\SearchBundle\Configuration\SearchConfigurationInterface;
use Kunstmaan\SearchBundle\Provider\SearchProviderInterface;
use Kunstmaan\SearchBundle\Search\AnalysisFactoryInterface;
use Kunstmaan\SearchBundle\Search\LanguageAnalysisFactory;
use Kunstmaan\UtilitiesBundle\Helper\ClassLookup;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Acl\Exception\AclNotFoundException;
use Symfony\Component\Security\Acl\Model\AclInterface;
use Symfony\Component\Security\Acl\Model\AclProviderInterface;
use Symfony\Component\Security\Acl\Model\AuditableEntryInterface;

/**
 * Class NodePagesConfiguration
 *
 * @package Kunstmaan\NodeSearchBundle\Configuration
 */
class NodePagesConfiguration implements SearchConfigurationInterface
{
    /** @var SearchProviderInterface */
    protected $searchProvider;

    /** @var PageHelper */
    private $pageHelper;

    /** @var PagesConfiguration */
    private $pagesConfiguration;

    /** @var DomainConfigurationInterface */
    protected $domainConfiguration;

    /** @var EntityManagerInterface */
    protected $em;

    /** @var LanguageAnalysisFactory */
    private $languageAnalysisFactory;

    /** @var array */
    protected $analyzerLanguages;

    /** @var string */
    protected $indexName;

    /** @var string */
    protected $indexType;

    /** @var array */
    protected $locales = [];

    /** @var array */
    protected $documents = [];

    /** @var AclProviderInterface */
    protected $aclProvider;

    /** @var LoggerInterface */
    protected $logger;

    /** @var array */
    protected $properties = [];

    /** @var integer */
    protected $numberOfShards;

    /** @var integer */
    protected $numberOfReplicas;

    /** @var Node */
    protected $currentTopNode;

    /** @var array */
    private $nodeRefs = [];

    /**
     * NodePagesConfiguration constructor.
     *
     * @param SearchProviderInterface      $searchProvider
     * @param PageHelper                   $pageHelper
     * @param PagesConfiguration           $pagesConfiguration
     * @param DomainConfigurationInterface $domainConfiguration
     * @param EntityManagerInterface       $em
     * @param LanguageAnalysisFactory      $languageAnalysisFactory
     * @param array                        $analyzerLanguages
     * @param string                       $name
     * @param string                       $type
     * @param int                          $numberOfShards
     * @param int                          $numberOfReplicas
     */
    public function __construct(
        SearchProviderInterface $searchProvider,
        PageHelper $pageHelper,
        PagesConfiguration $pagesConfiguration,
        DomainConfigurationInterface $domainConfiguration,
        EntityManagerInterface $em,
        LanguageAnalysisFactory $languageAnalysisFactory,
        $analyzerLanguages,
        $name,
        $type,
        $numberOfShards = 1,
        $numberOfReplicas = 0
    ) {
        $this->searchProvider = $searchProvider;
        $this->pageHelper = $pageHelper;
        $this->pagesConfiguration = $pagesConfiguration;
        $this->domainConfiguration = $domainConfiguration;
        $this->em = $em;
        $this->languageAnalysisFactory = $languageAnalysisFactory;
        $this->locales = $this->domainConfiguration->getBackendLocales();
        $this->analyzerLanguages = $analyzerLanguages;
        $this->indexType = $type;
        $this->indexName = $name;
        $this->numberOfShards = $numberOfShards;
        $this->numberOfReplicas = $numberOfReplicas;
    }

    /**
     * @param AclProviderInterface $aclProvider
     */
    public function setAclProvider(AclProviderInterface $aclProvider)
    {
        $this->aclProvider = $aclProvider;
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

            if (false === array_key_exists($locale, $this->analyzerLanguages)) {
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
        foreach ($this->locales as $locale) {
            // Multilanguage check
            if (preg_match('/[a-z]{2}_?+[a-zA-Z]{2}/', $locale)) {
                $locale = strtolower($locale);
            }

            // Build new index
            $index = $this->searchProvider->createIndex($this->indexName.'_'.$locale);

            if (array_key_exists($locale, $this->analyzerLanguages)) {
                $localeAnalysis = clone $this->languageAnalysisFactory;
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
        if (null === $publicNodeVersion) {
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
        return $this->pagesConfiguration->isIndexable($page);
    }

    /**
     * Remove the specified node translation from the index
     *
     * @param NodeTranslation $nodeTranslation
     */
    public function deleteNodeTranslation(NodeTranslation $nodeTranslation)
    {
        $uid = 'nodetranslation_'.$nodeTranslation->getId();
        $indexName = $this->indexName.'_'.$nodeTranslation->getLang();
        $this->searchProvider->deleteDocument($indexName, $this->indexType, $uid);
    }

    /**
     * Delete the specified index
     */
    public function deleteIndex()
    {
        foreach ($this->locales as $locale) {
            $this->searchProvider->deleteIndex($this->indexName.'_'.$locale);
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
            [
                'number_of_shards' => $this->numberOfShards,
                'number_of_replicas' => $this->numberOfReplicas,
                'analysis' => $analysis->build(),
            ]
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
    protected function setMapping(Index $index, $lang = 'en')
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
            $this->logger->info('Indexing document : '.implode(', ', $doc));
        }

        // Permissions
        $this->addPermissions($node, $doc);

        // Search type
        $this->addSearchType($page, $doc);

        // Parent and Ancestors
        $this->addParentAndAncestors($node, $doc);

        // Content
        $this->pageHelper->addPageContent($nodeTranslation, $page, $doc);

        // Add document to index
        $uid = 'nodetranslation_'.$nodeTranslation->getId();

        $this->pageHelper->addCustomData($page, $doc);

        $this->documents[] = $this->searchProvider->createDocument(
            $uid,
            $doc,
            $this->indexName.'_'.$nodeTranslation->getLang(),
            $this->indexType
        );
    }

    /**
     * Add view permissions to the index document
     *
     * @param Node  $node
     * @param array $doc
     */
    protected function addPermissions(Node $node, &$doc)
    {
        if (null !== $this->aclProvider) {
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
     * @param mixed $page
     * @param array $doc
     */
    protected function addSearchType($page, &$doc)
    {
        $doc['type'] = $this->pagesConfiguration->getSearchType($page);
    }

    /**
     * Add parent nodes to the index document
     *
     * @param Node  $node
     * @param array $doc
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
                    ($ace->getMask() & MaskBuilder::MASK_VIEW !== 0)
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
