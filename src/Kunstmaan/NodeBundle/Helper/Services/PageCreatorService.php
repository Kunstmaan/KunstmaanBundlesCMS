<?php

namespace Kunstmaan\NodeBundle\Helper\Services;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Kunstmaan\AdminBundle\Entity\User;
use Kunstmaan\AdminBundle\Repository\UserRepository;
use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Repository\NodeRepository;
use Kunstmaan\NodeBundle\Repository\NodeTranslationRepository;
use Kunstmaan\PagePartBundle\Helper\HasPagePartsInterface;
use Kunstmaan\SeoBundle\Repository\SeoRepository;
use Webmozart\Assert\Assert;

/**
 * Class PageCreatorService
 * @package Kunstmaan\NodeBundle\Helper\Services
 */
class PageCreatorService
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var ACLPermissionCreatorService $aclPermissionCreatorService
     */
    protected $aclPermissionCreatorService;

    /**
     * @var string
     */
    protected $userEntityClass;

    /**
     * @var HasNodeInterface $pageTypeInstance
     */
    private $pageTypeInstance;

    /**
     * @var Node
     */
    private $rootNode = null;

    /**
     * @var \Kunstmaan\SeoBundle\Entity\Seo|null $seo
     */
    private $seo;

    /**
     * @var User $creator
     */
    private $creator;

    /**
     * @var NodeRepository $nodeRepo
     */
    private $nodeRepo;

    /**
     * @var NodeTranslationRepository $translationsRepo
     */
    private $translationsRepo;

    /**
     * @var array $options
     */
    private $options;

    /**
     * @var mixed|null $parent
     */
    private $parent;

    /**
     * PageCreatorService constructor.
     * @param EntityManagerInterface $em
     * @param ACLPermissionCreatorService $aclPermissionCreatorService
     * @param $userClass
     */
    public function __construct(
        EntityManagerInterface $em,
        ACLPermissionCreatorService $aclPermissionCreatorService,
        $userClass)
    {
        $this->entityManager = $em;
        $this->aclPermissionCreatorService = $aclPermissionCreatorService;
        $this->userEntityClass = $userClass;
        $this->nodeRepo = $em->getRepository(Node::class);
        $this->translationsRepo = $em->getRepository(NodeTranslation::class);
    }

    /**
     * @param HasNodeInterface $pageTypeInstance The page.
     * @param array            $translations     Containing arrays. Sample:
     * [
     *  [   "language" => "nl",
     *      "callback" => function($page, $translation) {
     *          $translation->setTitle('NL titel');
     *      }
     *  ],
     *  [   "language" => "fr",
     *      "callback" => function($page, $translation) {
     *          $translation->setTitle('FR titel');
     *      }
     *  ]
     * ]
     * Perhaps it's cleaner when you create one array and append another array for each language.
     *
     * @param array            $options          Possible options:
     *      parent: type node, nodetransation or page.
     *      page_internal_name: string. name the page will have in the database.
     *      set_online: bool. if true the page will be set as online after creation.
     *      hidden_from_nav: bool. if true the page will not be show in the navigation
     *      creator: username
     *
     * Automatically calls the ACL + sets the slugs to empty when the page is an Abstract node.
     *
     * @return Node The new node for the page.
     */
    public function createPage(HasNodeInterface $pageTypeInstance, array $translations, array $options = [])
    {
        Assert::notEmpty($translations, 'There has to be at least 1 translation in the translations array');
        $this->pageTypeInstance = $pageTypeInstance;
        $this->options = (null === $options) ? [] : $options;
        $this->seo = $this->getSeo();
        $this->creator = $this->getCreator();
        $this->parent = $this->getOption('parent');

        $firstTranslation = array_shift($translations);
        $this->handleTranslation($firstTranslation, true);

        foreach ($translations as $translation) {
            $this->handleTranslation($translation);
        }

        $this->aclPermissionCreatorService->createPermission($this->rootNode);
        return $this->rootNode;
    }

    /**
     * @return UserRepository
     */
    private function getUserRepo()
    {
        /** @var UserRepository $userRepo */
        $userRepo = $this->entityManager->getRepository($this->userEntityClass);
        return $userRepo;
    }

    /**
     * @return \Kunstmaan\SeoBundle\Entity\Seo|null
     */
    private function getSeo()
    {
        $seo = null;
        /** @var $seoRepo SeoRepository */
        try {
            $seoRepo = $this->entityManager->getRepository('KunstmaanSeoBundle:Seo');
            $seo = $seoRepo->findOrCreateFor($this->pageTypeInstance);
        } catch (Exception $e) {
            $seoRepo = null;
        }
        return $seo;
    }

    /**
     * @return object|User
     */
    private function getCreator()
    {
        $pagecreator = array_key_exists('creator', $this->options) ? $this->options['creator'] : 'pagecreator';

        $userRepo = $this->getUserRepo();

        return $userRepo->findOneBy(['username' => $pagecreator]);
    }

    /**
     * @return bool
     */
    private function isOnline()
    {
        return $this->getOption('set_online') ?: false;
    }

    /**
     * @param $key
     * @return mixed|null
     */
    private function getOption($key)
    {
        return isset($this->options[$key]) ? $this->options[$key] : null;
    }

    /**
     * This is where the action happens!
     *
     * @param array $translation
     * @param bool $first
     */
    private function handleTranslation(array $translation, $first = false)
    {
        $language = $translation['language'];
        $callback = $translation['callback'];

        $translationNode = null;
        $pageTypeInstance = $this->getPageTypeInstance($first);
        $this->entityManager->persist($pageTypeInstance);
        $this->entityManager->flush($pageTypeInstance);


        if ($first === true) {
            $this->setRootNode($language);
        }

        $translationNode = $this->getTranslationNode($first, $language);

        $callback($pageTypeInstance, $translationNode, $this->seo);

        // Overwrite the page title with the translated title
        $pageTypeInstance->setTitle($translationNode->getTitle());
        $this->entityManager->persist($translationNode);

        $translationNode->setOnline($this->isOnline());

        if (!is_null($this->seo)) {
            $this->entityManager->persist($this->seo);
        }

        $this->entityManager->flush();
    }

    /**
     * @param bool $first
     * @return HasNodeInterface
     */
    private function getPageTypeInstance($first = false)
    {
        return $first ? $this->pageTypeInstance : clone $this->pageTypeInstance;
    }

    /**
     * @param $language
     */
    private function setRootNode($language)
    {
        $this->rootNode = $this->nodeRepo->createNodeFor($this->pageTypeInstance, $language, $this->creator, $this->getOption('page_internal_name'));

        if (array_key_exists('hidden_from_nav', $this->options)) {
            $this->rootNode->setHiddenFromNav($this->options['hidden_from_nav']);
        }

        if (!is_null($this->parent)) {
            if ($this->parent instanceof HasPagePartsInterface) {
                $this->parent = $this->nodeRepo->getNodeFor($this->parent);
            }
            $this->rootNode->setParent($this->parent);
        }

        $this->entityManager->persist($this->rootNode);
        $this->entityManager->flush($this->rootNode);
    }

    /**
     * @param $first
     * @return \Kunstmaan\NodeBundle\Entity\NodeTranslation|null
     */
    private function getTranslationNode($first, $language)
    {
        if ($first === true) {
            $translationNode = $this->rootNode->getNodeTranslation($language, true);
        } else {
            $translationNode = $this->translationsRepo->createNodeTranslationFor($this->pageTypeInstance, $language, $this->rootNode, $this->creator);
        }
        return $translationNode;
    }
}
