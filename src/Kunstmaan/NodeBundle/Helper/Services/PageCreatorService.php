<?php

namespace Kunstmaan\NodeBundle\Helper\Services;

use Kunstmaan\NodeBundle\Entity\HasNodeInterface,
    Kunstmaan\NodeBundle\Entity\Node;

use Kunstmaan\NodeBundle\Repository\NodeRepository,
    Kunstmaan\NodeBundle\Helper\Services\ACLPermissionCreatorService;

use Symfony\Component\DependencyInjection\ContainerAwareInterface,
    Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Service to create new pages.
 */

class PageCreatorService Implements ContainerAwareInterface
{
    /**
     *
     * Automatically calls the ACL + sets the slugs to empty when the page is an Abstract node.
     *
     * @param $pageTypeInstance
     * @param array $translations array containing arrays. Sample:
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
     * @param array options -
     *      parent: type node, nodetransation or page.
     *      page_internal_name: string. name the page will have in the database.
     *      set_online: bool. if true the page will be set as online after creation.
     *      todo: creator: user?
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
        $em = $this->container->get('doctrine.orm.entity_manager');

        /* @var NodeRepository $nodeRepo */
        $nodeRepo = $em->getRepository('KunstmaanNodeBundle:Node');
        $userRepo = $em->getRepository('KunstmaanAdminBundle:User');
        $seoRepo = $em->getRepository('KunstmaanSeoBundle:Seo');

        // TODO: Get this from options.
        $creator = $userRepo->findOneBy(array('username' => 'pagecreator'));

        $parent = isset($options['parent']) ? $options['parent'] : null;

        $pageInternalName = isset($options['page_internal_name']) ? $options['page_internal_name'] : '';

        $setOnline = isset($options['set_online']) ? $options['set_online'] : false;

        $em->persist($pageTypeInstance);
        $em->flush();

        // We need to get the language of the first translation so we can create the rootnode.
        // This will also create a translationnode for that language attached to the rootnode.
        $first = true;
        $rootNode = null;

        /* @var \Kunstmaan\NodeBundle\Repository\NodeTranslationRepository $nodeTranslationRepo*/
        $nodeTranslationRepo = $em->getRepository('KunstmaanNodeBundle:NodeTranslation');

        foreach ($translations as $translation) {
            $language = $translation['language'];
            $callback = $translation['callback'];

            $translationNode = null;
            if ($first) {
                $first = false;
                // Fetch the translation instead of creating it.
                // This returns the rootnode.
                $rootNode = $nodeRepo->createNodeFor($pageTypeInstance, $language, $creator, $pageInternalName);
                if (!is_null($parent)) {
                    $rootNode->setParent($parent);
                }
                $em->persist($rootNode);
                $em->flush();

                $translationNode = $rootNode->getNodeTranslation($language, true);
            } else {
                // Create the translationnode.
                $translationNode = $nodeTranslationRepo->createNodeTranslationFor($pageTypeInstance, $language, $rootNode, $creator);
            }

            $em->persist($translationNode);
            $em->flush();

            // Make SEO.
            $seo = null;

            if (!is_null($seoRepo)) {
                $seo = $seoRepo->findOrCreateFor($pageTypeInstance);
            }

            $callback($pageTypeInstance, $translationNode, $seo);

            $translationNode->setOnline($setOnline);

            if (!is_null($seo)) {
                $em->persist($seo);
            }

            $em->persist($translationNode);
            $em->flush();
        }

        // ACL
        $aclService = new ACLPermissionCreatorService();
        $aclService->setContainer($this->container);
        $aclService->createPermission($rootNode);

        return $rootNode;
    }

    protected $container;
    /**
     * Sets the Container.
     *
     * @param ContainerInterface $container A ContainerInterface instance
     *
     * @api
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}
