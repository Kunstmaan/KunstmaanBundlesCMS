<?php

namespace Kunstmaan\NodeBundle\Helper\Services;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use Kunstmaan\AdminBundle\Repository\UserRepository;
use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Repository\NodeRepository;
use Kunstmaan\PagePartBundle\Helper\HasPagePartsInterface;
use Kunstmaan\SeoBundle\Repository\SeoRepository;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class PageCreatorService
 *
 * Service to create new pages.
 *
 * @package Kunstmaan\NodeBundle\Helper\Services
 */
class PageCreatorService
{
    /** @var EntityManagerInterface */
    protected $em;

    /** @var ACLPermissionCreatorService */
    protected $aclPermissionCreatorService;

    /** @var string */
    protected $userEntityClass;

    /**
     * PageCreatorService constructor.
     *
     * @param EntityManagerInterface|null      $em
     * @param ACLPermissionCreatorService|null $aclPermissionCreatorService
     * @param string|null                      $userEntityClass
     */
    public function __construct(
        EntityManagerInterface $em = null,
        ACLPermissionCreatorService $aclPermissionCreatorService = null,
        $userEntityClass = null
    ) {
        $this->em = $em;
        $this->aclPermissionCreatorService = $aclPermissionCreatorService;
        $this->userEntityClass = $userEntityClass;
    }

    /**
     * @param EntityManagerInterface $em
     */
    public function setEntityManager(EntityManagerInterface $em)
    {
        @trigger_error(
            'Setter injection is deprecated in KunstmaanNodeBundle 5.1 and will be removed in KunstmaanNodeBundle 6.0.',
            E_USER_DEPRECATED
        );
        $this->em = $em;
    }

    /**
     * @param ACLPermissionCreatorService $aclPermissionCreatorService
     */
    public function setAclPermissionCreatorService(ACLPermissionCreatorService $aclPermissionCreatorService)
    {
        @trigger_error(
            'Setter injection is deprecated in KunstmaanNodeBundle 5.1 and will be removed in KunstmaanNodeBundle 6.0.',
            E_USER_DEPRECATED
        );
        $this->aclPermissionCreatorService = $aclPermissionCreatorService;
    }

    /**
     * @param string $userEntityClass
     */
    public function setUserEntityClass(string $userEntityClass)
    {
        @trigger_error(
            'Setter injection is deprecated in KunstmaanNodeBundle 5.1 and will be removed in KunstmaanNodeBundle 6.0.',
            E_USER_DEPRECATED
        );
        $this->userEntityClass = $userEntityClass;
    }

    /**
     * Sets the Container. This is still here for backwards compatibility.
     *
     * The ContainerAwareInterface has been removed so the container won't be injected automatically.
     * This function is just there for code that calls it manually.
     *
     * @param ContainerInterface $container A ContainerInterface instance.
     *
     * @api
     */
    public function setContainer(ContainerInterface $container)
    {
        @trigger_error(
            'Container injection is deprecated in KunstmaanNodeBundle 5.1 and will be removed in KunstmaanNodeBundle 6.0.',
            E_USER_DEPRECATED
        );
        $this->setEntityManager($container->get(EntityManagerInterface::class));
        $this->setAclPermissionCreatorService($container->get(ACLPermissionCreatorService::class));
        $this->setUserEntityClass($container->getParameter('fos_user.model.user.class'));
    }

    /**
     * @param HasNodeInterface $pageTypeInstance The page.
     * @param array            $translations     Containing arrays. Sample:
     *                                           [
     *                                           [   "language" => "nl",
     *                                           "callback" => function($page, $translation) {
     *                                           $translation->setTitle('NL titel');
     *                                           }
     *                                           ],
     *                                           [   "language" => "fr",
     *                                           "callback" => function($page, $translation) {
     *                                           $translation->setTitle('FR titel');
     *                                           }
     *                                           ]
     *                                           ]
     *                                           Perhaps it's cleaner when you create one array and append another array for each language.
     *
     * @param array            $options          Possible options:
     *                                           parent: type node, nodetransation or page.
     *                                           page_internal_name: string. name the page will have in the database.
     *                                           set_online: bool. if true the page will be set as online after creation.
     *                                           hidden_from_nav: bool. if true the page will not be show in the navigation
     *                                           creator: username
     *
     * Automatically calls the ACL + sets the slugs to empty when the page is an Abstract node.
     *
     * @return Node The new node for the page.
     *
     * @throws \InvalidArgumentException
     */
    public function createPage(HasNodeInterface $pageTypeInstance, array $translations, array $options = [])
    {
        if (is_null($options)) {
            $options = [];
        }

        if (is_null($translations) || (count($translations) == 0)) {
            throw new \InvalidArgumentException('There has to be at least 1 translation in the translations array');
        }

        $em = $this->em;

        /** @var NodeRepository $nodeRepo */
        $nodeRepo = $em->getRepository('KunstmaanNodeBundle:Node');
        /** @var $userRepo UserRepository */
        $userRepo = $em->getRepository($this->userEntityClass);
        /** @var $seoRepo SeoRepository */
        try {
            $seoRepo = $em->getRepository('KunstmaanSeoBundle:Seo');
        } catch (ORMException $e) {
            $seoRepo = null;
        }

        $pagecreator = array_key_exists('creator', $options) ? $options['creator'] : 'pagecreator';
        $creator = $userRepo->findOneBy(['username' => $pagecreator]);

        $parent = isset($options['parent']) ? $options['parent'] : null;

        $pageInternalName = isset($options['page_internal_name']) ? $options['page_internal_name'] : null;

        $setOnline = isset($options['set_online']) ? $options['set_online'] : false;

        // We need to get the language of the first translation so we can create the rootnode.
        // This will also create a translationnode for that language attached to the rootnode.
        $first = true;
        $rootNode = null;

        /* @var \Kunstmaan\NodeBundle\Repository\NodeTranslationRepository $nodeTranslationRepo */
        $nodeTranslationRepo = $em->getRepository('KunstmaanNodeBundle:NodeTranslation');

        foreach ($translations as $translation) {
            $language = $translation['language'];
            $callback = $translation['callback'];

            $translationNode = null;
            if ($first) {
                $first = false;

                $em->persist($pageTypeInstance);
                $em->flush($pageTypeInstance);

                // Fetch the translation instead of creating it.
                // This returns the rootnode.
                $rootNode = $nodeRepo->createNodeFor($pageTypeInstance, $language, $creator, $pageInternalName);

                if (array_key_exists('hidden_from_nav', $options)) {
                    $rootNode->setHiddenFromNav($options['hidden_from_nav']);
                }

                if (!is_null($parent)) {
                    if ($parent instanceof HasPagePartsInterface) {
                        $parent = $nodeRepo->getNodeFor($parent);
                    }
                    $rootNode->setParent($parent);
                }

                $em->persist($rootNode);
                $em->flush($rootNode);

                $translationNode = $rootNode->getNodeTranslation($language, true);
            } else {
                // Clone the $pageTypeInstance.
                $pageTypeInstance = clone $pageTypeInstance;

                $em->persist($pageTypeInstance);
                $em->flush($pageTypeInstance);

                // Create the translationnode.
                $translationNode = $nodeTranslationRepo->createNodeTranslationFor($pageTypeInstance, $language, $rootNode, $creator);
            }

            // Make SEO.
            $seo = null;

            if (!is_null($seoRepo)) {
                $seo = $seoRepo->findOrCreateFor($pageTypeInstance);
            }

            $callback($pageTypeInstance, $translationNode, $seo);

            // Overwrite the page title with the translated title
            $pageTypeInstance->setTitle($translationNode->getTitle());
            $em->persist($pageTypeInstance);
            $em->persist($translationNode);
            $em->flush($pageTypeInstance);
            $em->flush($translationNode);

            $translationNode->setOnline($setOnline);

            if (!is_null($seo)) {
                $em->persist($seo);
                $em->flush($seo);
            }

            $em->persist($translationNode);
            $em->flush($translationNode);
        }

        // ACL
        $this->aclPermissionCreatorService->createPermission($rootNode);

        return $rootNode;
    }

}
