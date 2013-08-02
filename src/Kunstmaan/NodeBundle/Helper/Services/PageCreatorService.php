<?php

namespace Kunstmaan\NodeBundle\Helper\Services;

use Doctrine\ORM\EntityManager;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Kunstmaan\AdminBundle\Repository\UserRepository;

use Kunstmaan\NodeBundle\Entity\HasNodeInterface,
    Kunstmaan\NodeBundle\Entity\Node,
    Kunstmaan\NodeBundle\Repository\NodeRepository,
    Kunstmaan\NodeBundle\Helper\Services\ACLPermissionCreatorService;

use Kunstmaan\PagePartBundle\Helper\HasPagePartsInterface;

use Kunstmaan\SeoBundle\Repository\SeoRepository;



/**
 * Service to create new pages.
 *
 */
class PageCreatorService
{
    /** @var EntityManager */
    protected $entityManager;
    public function setEntityManager($entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /** @var ACLPermissionCreatorService */
    protected $aclPermissionCreatorService;
    public function setACLPermissionCreatorService($aclPermissionCreatorService)
    {
        $this->aclPermissionCreatorService = $aclPermissionCreatorService;
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
    public function setContainer(ContainerInterface $container = null)
    {
        $this->setEntityManager($container->get('doctrine.orm.entity_manager'));
        $this->setACLPermissionCreatorService($container->get('kunstmaan_node.acl_permission_creator_service'));
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
     *      creator: username
     *
     * Automatically calls the ACL + sets the slugs to empty when the page is an Abstract node.
     *
     * @return Node The new node for the page.
     *
     * @throws \InvalidArgumentException
     */
    public function createPage(HasNodeInterface $pageTypeInstance, array $translations, array $options = array())
    {
        if (is_null($options)) {
            $options = array();
        }

        if (is_null($translations) or (count($translations) == 0)) {
            throw new \InvalidArgumentException('Needs at least 1 translation in the translations array');
        }

        // TODO: Wrap it all in a transaction.
        $em = $this->entityManager;

        /** @var NodeRepository $nodeRepo */
        $nodeRepo = $em->getRepository('KunstmaanNodeBundle:Node');
        /** @var $userRepo UserRepository */
        $userRepo = $em->getRepository('KunstmaanAdminBundle:User');
        /** @var $seoRepo SeoRepository */
        $seoRepo = $em->getRepository('KunstmaanSeoBundle:Seo');

        $pagecreator = array_key_exists('creator', $options) ? $options['creator'] : 'pagecreator';
        $creator     = $userRepo->findOneBy(array('username' => $pagecreator));

        $parent = isset($options['parent']) ? $options['parent'] : null;

        $pageInternalName = isset($options['page_internal_name']) ? $options['page_internal_name'] : '';

        $setOnline = isset($options['set_online']) ? $options['set_online'] : false;

        // We need to get the language of the first translation so we can create the rootnode.
        // This will also create a translationnode for that language attached to the rootnode.
        $first    = true;
        $rootNode = null;

        /* @var \Kunstmaan\NodeBundle\Repository\NodeTranslationRepository $nodeTranslationRepo*/
        $nodeTranslationRepo = $em->getRepository('KunstmaanNodeBundle:NodeTranslation');

        foreach ($translations as $translation) {
            $language = $translation['language'];
            $callback = $translation['callback'];

            $translationNode = null;
            if ($first) {
                $first = false;

                $em->persist($pageTypeInstance);
                $em->flush();

                // Fetch the translation instead of creating it.
                // This returns the rootnode.
                $rootNode = $nodeRepo->createNodeFor($pageTypeInstance, $language, $creator, $pageInternalName);

                if (!is_null($parent)) {
                    if ($parent instanceof HasPagePartsInterface) {
                        $parent = $nodeRepo->getNodeFor($parent);
                    }
                    $rootNode->setParent($parent);
                }
                $em->persist($rootNode);
                $em->flush();

                $translationNode = $rootNode->getNodeTranslation($language, true);
            } else {
                // Clone the $pageTypeInstance.
                $pageTypeInstance = clone $pageTypeInstance;

                $em->persist($pageTypeInstance);
                $em->flush();

                // Create the translationnode.
                $translationNode = $nodeTranslationRepo->createNodeTranslationFor($pageTypeInstance, $language, $rootNode, $creator);
            }

            // Make SEO.
            $seo = null;

            if (!is_null($seoRepo)) {
                $seo = $seoRepo->findOrCreateFor($pageTypeInstance);
            }

            $callback($pageTypeInstance, $translationNode, $seo);

            $em->persist($translationNode);
            $em->flush();

            $translationNode->setOnline($setOnline);

            if (!is_null($seo)) {
                $em->persist($seo);
            }

            $em->persist($translationNode);
            $em->flush();
        }

        // ACL
        $this->aclPermissionCreatorService->createPermission($rootNode);

        return $rootNode;
    }

}
