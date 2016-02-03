<?php

namespace Kunstmaan\FixturesBundle\Builder;

use Doctrine\ORM\EntityManager;
use Kunstmaan\FixturesBundle\Loader\Fixture;
use Kunstmaan\FixturesBundle\Populator\Populator;
use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Entity\NodeVersion;
use Kunstmaan\NodeBundle\Entity\StructureNode;
use Kunstmaan\NodeBundle\Helper\Services\ACLPermissionCreatorService;
use Kunstmaan\UtilitiesBundle\Helper\Slugifier;
use Kunstmaan\UtilitiesBundle\Helper\ClassLookup;
use Kunstmaan\PagePartBundle\Entity\PageTemplateConfiguration;

class PageBuilder implements BuilderInterface
{
    private $manager;
    private $userRepo;
    private $nodeRepo;
    private $nodeTranslationRepo;
    private $aclPermissionCreatorService;
    private $populator;
    private $slugifier;

    public function __construct(
        EntityManager $em,
        ACLPermissionCreatorService $aclPermissionCreatorService,
        Populator $populator,
        Slugifier $slugifier
    ) {
        $this->manager = $em;
        $this->nodeRepo = $em->getRepository('KunstmaanNodeBundle:Node');
        $this->nodeTranslationRepo = $em->getRepository('KunstmaanNodeBundle:NodeTranslation');
        $this->userRepo = $em->getRepository('KunstmaanAdminBundle:User');
        $this->aclPermissionCreatorService = $aclPermissionCreatorService;
        $this->populator = $populator;
        $this->slugifier = $slugifier;
    }

    public function canBuild(Fixture $fixture)
    {
        if ($fixture->getEntity() instanceof HasNodeInterface) {
            return true;
        }

        return false;
    }

    public function preBuild(Fixture $fixture)
    {
        return;
    }

    public function postBuild(Fixture $fixture)
    {
        $entity = $fixture->getEntity();
        $fixtureParams = $fixture->getParameters();
        $translations = $fixture->getTranslations();
        if (empty($translations)) {
            throw new \Exception('No translations detected for page fixture ' . $fixture->getName() . ' (' . $fixture->getClass() . ')');
        }

        $internalName = array_key_exists('page_internal_name', $fixtureParams) ?
            $fixtureParams['page_internal_name'] : null;

        $rootNode = null;
        foreach ($fixture->getTranslations() as $language => $data) {
            if ($rootNode === null) {
                $page = $entity;
                $rootNode = $this->createRootNode($page, $language, $internalName, $fixtureParams);
                $this->manager->persist($rootNode);
            } else {
                $cloned = clone $entity;
                $page = $cloned;
                $this->manager->persist($page);
            }

            // Create the translationNode.
            $translationNode = $this->createTranslationNode($rootNode, $language, $page);
            if (!$page instanceof StructureNode) {
                $translationNode->setOnline(isset($fixtureParams['set_online']) ? $fixtureParams['set_online'] : true);
            }

            $fixture->addAdditional($fixture->getName() . '_' . $language, $page);
            $fixture->addAdditional('translationNode_' . $language, $translationNode);
            $fixture->addAdditional('nodeVersion_' . $language, $translationNode->getPublicNodeVersion());
            $fixture->addAdditional('rootNode', $rootNode);

            $this->populator->populate($translationNode, $data);
            $this->populator->populate($page, $data);
            if ($translationNode->getSlug() === null && $rootNode->getParent() !== null) {
                $translationNode->setSlug($this->slugifier->slugify($translationNode->getTitle()));
            }
            $this->ensureUniqueUrl($translationNode, $page);

            $this->manager->persist($translationNode);
            $rootNode->addNodeTranslation($translationNode);
        }

        $this->manager->flush();
        $this->aclPermissionCreatorService->createPermission($rootNode);
    }

    public function postFlushBuild(Fixture $fixture)
    {
        $entities = $fixture->getAdditionalEntities();
        $fixtureParams = $fixture->getParameters();

        foreach ($fixture->getTranslations() as $language => $data) {
            /** @var HasNodeInterface $page */
            $page = $entities[$fixture->getName() . '_' . $language];
            /** @var NodeTranslation $translationNode */
            $translationNode = $entities['translationNode_' . $language];

            $pagecreator = array_key_exists('creator', $fixtureParams) ? $fixtureParams['creator'] : 'pagecreator';
            $creator = $this->userRepo->findOneBy(array('username' => $pagecreator));

            $nodeVersion = new NodeVersion();
            $nodeVersion->setNodeTranslation($translationNode);
            $nodeVersion->setType('public');
            $nodeVersion->setOwner($creator);
            $nodeVersion->setRef($page);

            $translationNode->setPublicNodeVersion($nodeVersion);

            if (isset($fixtureParams['template'])) {
                $pageTemplateConfiguration = new PageTemplateConfiguration();
                $pageTemplateConfiguration->setPageId($page->getId());
                $pageTemplateConfiguration->setPageEntityName(ClassLookup::getClass($page));
                $pageTemplateConfiguration->setPageTemplate($fixtureParams['template']);
                $this->manager->persist($pageTemplateConfiguration);
            }

            $this->manager->persist($nodeVersion);
            $this->manager->persist($translationNode);
        }
        $this->manager->flush();
    }

    private function getParentNode($params, $language)
    {
        if (!isset($params['parent'])) {
            return;
        }

        $parent = $params['parent'];
        if ($parent instanceof Fixture) {
            $additionals = $parent->getAdditionalEntities();
            $parent = $additionals['rootNode'];
        } elseif (is_string($parent)) {
            $nodes = $this->nodeRepo->getNodesByInternalName($parent, $language, false, true);
            if (count($nodes) > 0) {
                $parent = $nodes[0];
            }
        }

        return $parent;
    }

    private function createRootNode($page, $language, $internalName, $fixtureParams)
    {
        $rootNode = new Node();
        $rootNode->setRef($page);
        $rootNode->setDeleted(false);
        $rootNode->setInternalName($internalName);
        $rootNode->setHiddenFromNav(
            isset($fixtureParams['hidden_from_nav']) ? $fixtureParams['hidden_from_nav'] : false
        );
        $parent = $this->getParentNode($fixtureParams, $language);
        if ($parent instanceof Node) {
            $rootNode->setParent($parent);
        }

        return $rootNode;
    }

    private function createTranslationNode(Node $rootNode, $language, HasNodeInterface $page)
    {
        $translationNode = new NodeTranslation();
        $translationNode
            ->setNode($rootNode)
            ->setLang($language)
            ->setTitle($page->getTitle())
            ->setOnline(false)
            ->setWeight(0);

        return $translationNode;
    }

    private function ensureUniqueUrl(NodeTranslation $translation, HasNodeInterface $page)
    {
        if ($page instanceof StructureNode) {
            $translation->setSlug('');
            $translation->setUrl($translation->getFullSlug());

            return $translation;
        }

        $translation->setUrl($translation->getFullSlug());

        // Find all translations with this new URL, whose nodes are not deleted.
        $translationWithSameUrl = $this->nodeTranslationRepo->getNodeTranslationForUrl($translation->getUrl(), $translation->getLang(), false, $translation);

        if ($translationWithSameUrl instanceof NodeTranslation) {
            $translation->setSlug($this->slugifier->slugify($this->incrementString($translation->getSlug())));
            $this->ensureUniqueUrl($translation, $page);
        }

        return $translation;
    }

    private static function incrementString($string, $append = '-v')
    {
        $finalDigitGrabberRegex = '/\d+$/';
        $matches = array();

        preg_match($finalDigitGrabberRegex, $string, $matches);

        if (count($matches) > 0) {
            $digit = (int) $matches[0];
            ++$digit;

            // Replace the integer with the new digit.
            return preg_replace($finalDigitGrabberRegex, $digit, $string);
        } else {
            return $string . $append . '1';
        }
    }
}
