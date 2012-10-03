<?php

namespace {{ namespace }}\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Acl\Exception\AclNotFoundException;

use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\MaskBuilder;
use Kunstmaan\NodeBundle\Entity\Node;

use {{ namespace }}\Entity\ContentPage;
use {{ namespace }}\Entity\FormPage;
use {{ namespace }}\Entity\HomePage;

class DefaultSiteFixtures extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    private $adminuser = null;
    private $container = null;

    /**
     * Load data fixtures with the passed EntityManager.
     *
     * @param Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $this->adminuser = $manager
            ->getRepository('KunstmaanAdminBundle:User')
            ->findOneBy(array('username' => 'Admin'));

        $homepage = $this->createHomePage($manager, "Home");

        $pagepartspage = $this->createContentPage($manager, "PageParts", $homepage);

        $headerspage = $this->createHeaderPage($manager, "Headers", $pagepartspage);

        $textpage = $this->createTextPage($manager, "Text", $pagepartspage);

        $tocpage = $this->createTocPage($manager, "Toc", $pagepartspage);

        $formpage = $this->createFormPage($manager, "Form", $pagepartspage);
    }

    /**
     * Initialize the permissions for the given Node
     *
     * @param Kunstmaan\NodeBundle\Entity\Node $node
     */
    private function initPermissions(Node $node)
    {
        $aclProvider = $this->container->get('security.acl.provider');
        $oidStrategy = $this->container->get('security.acl.object_identity_retrieval_strategy');
        $objectIdentity = $oidStrategy->getObjectIdentity($node);
        try {
            $acl = $aclProvider->findAcl($objectIdentity);
            $aclProvider->deleteAcl($objectIdentity);
        } catch (AclNotFoundException $e) {
            // Do nothing
        }
        $acl = $aclProvider->createAcl($objectIdentity);

        $securityIdentity = new RoleSecurityIdentity('ROLE_GUEST');
        $acl->insertObjectAce($securityIdentity, MaskBuilder::MASK_VIEW);

        $securityIdentity = new RoleSecurityIdentity('ROLE_ADMIN');
        $acl->insertObjectAce(
            $securityIdentity,
            MaskBuilder::MASK_VIEW | MaskBuilder::MASK_EDIT | MaskBuilder::MASK_PUBLISH | MaskBuilder::MASK_UNPUBLISH
        );

        $securityIdentity = new RoleSecurityIdentity('ROLE_SUPER_ADMIN');
        $acl->insertObjectAce($securityIdentity, MaskBuilder::MASK_IDDQD);
        $aclProvider->updateAcl($acl);
    }

    /**
     * Create a Homepage
     *
     * @param Doctrine\Common\Persistence\ObjectManager $manager
     * @param string                                    $title
     *
     * @return HomePage
     */
    private function createHomePage(ObjectManager $manager, string $title)
    {
        $homepage = new HomePage();
        $homepage->setTitle($title);
        $manager->persist($homepage);
        $manager->flush();
        $node = $manager
            ->getRepository('KunstmaanNodeBundle:Node')
            ->createNodeFor($homepage, 'en', $this->adminuser);
        $this->initPermissions($node);

        return $homepage;
    }

    /**
     * Create a ContentPage
     *
     * @param Doctrine\Common\Persistence\ObjectManager $manager
     * @param string                                    $title
     * @param                                           $parent
     *
     * @return ContentPage
     */
    private function createContentPage(ObjectManager $manager, string $title, $parent)
    {
        $page = new ContentPage();
        $page->setParent($parent);
        $page->setTitle('PageParts');
        $manager->persist($page);
        $manager->flush();
        $node = $manager
            ->getRepository('KunstmaanNodeBundle:Node')
            ->createNodeFor($page, 'en', $this->adminuser);
        $this->initPermissions($node);
        return $page;
    }

    /**
     * Create a ContentPage containing headers
     *
     * @param Doctrine\Common\Persistence\ObjectManager $manager
     * @param string                                    $title
     * @param                                           $parent
     */
    private function createHeaderPage(ObjectManager $manager, string $title, $parent)
    {
        $headerpage = new ContentPage();
        $headerpage->setParent($parent);
        $headerpage->setTitle($title);
        $manager->persist($headerpage);
        $manager->flush();
        $node = $manager
            ->getRepository('KunstmaanNodeBundle:Node')
            ->createNodeFor($headerpage, 'en', $this->adminuser);
        $this->initPermissions($node);
        for ($i = 1; $i <= 6; $i++) {
            $headerpagepart = new \Kunstmaan\PagePartBundle\Entity\HeaderPagePart();
            $headerpagepart->setNiv($i);
            $headerpagepart->setTitle("Header " . $i);
            $manager->persist($headerpagepart);
            $manager->flush();
            $manager
                ->getRepository('KunstmaanPagePartBundle:PagePartRef')
                ->addPagePart($headerpage, $headerpagepart, $i);
        }
    }

    /**
     * Create a ContentPage containing headers and text
     *
     * @param Doctrine\Common\Persistence\ObjectManager $manager
     * @param string                                    $title
     * @param                                           $parent
     */
    private function createTextPage(ObjectManager $manager, string $title, $parent)
    {
        $textpage = new ContentPage();
        $textpage->setParent($parent);
        $textpage->setTitle($title);
        $manager->persist($textpage);
        $manager->flush();
        $node = $manager
            ->getRepository('KunstmaanNodeBundle:Node')
            ->createNodeFor($textpage, 'en', $this->adminuser);
        $this->initPermissions($node);
        {
            $headerpagepart = new \Kunstmaan\PagePartBundle\Entity\HeaderPagePart();
            $headerpagepart->setNiv(1);
            $headerpagepart->setTitle($title);
            $manager->persist($headerpagepart);
            $manager->flush();
            $manager
                ->getRepository('KunstmaanPagePartBundle:PagePartRef')
                ->addPagePart($textpage, $headerpagepart, 1);
        }
        {
            $textpagepart = new \Kunstmaan\PagePartBundle\Entity\TextPagePart();
            $textpagepart->setContent("<strong>Lorem ipsum dolor sit amet</strong>, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci <a href=\"#\">textlink</a> tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. Euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim.</p>");
            $manager->persist($textpagepart);
            $manager->flush();
            $manager
                ->getRepository('KunstmaanPagePartBundle:PagePartRef')
                ->addPagePart($textpage, $textpagepart, 2);
        }
    }

    /**
     * Create a ContentPage containing a table of content, headers and text
     *
     * @param Doctrine\Common\Persistence\ObjectManager $manager
     * @param string                                    $title
     * @param                                           $parent
     */
    private function createTocPage(ObjectManager $manager, string $title, $parent)
    {
        $textpage = new ContentPage();
        $textpage->setParent($parent);
        $textpage->setTitle($title);
        $manager->persist($textpage);
        $manager->flush();
        $node = $manager
            ->getRepository('KunstmaanNodeBundle:Node')
            ->createNodeFor($textpage, 'en', $this->adminuser);
        $this->initPermissions($node);
        {
            $headerpagepart = new \Kunstmaan\PagePartBundle\Entity\HeaderPagePart();
            $headerpagepart->setNiv(1);
            $headerpagepart->setTitle($title);
            $manager->persist($headerpagepart);
            $manager->flush();
            $manager
                ->getRepository('KunstmaanPagePartBundle:PagePartRef')
                ->addPagePart($textpage, $headerpagepart, 1);
        }
        {
            $headerpagepart = new \Kunstmaan\PagePartBundle\Entity\TocPagePart();
            $manager->persist($headerpagepart);
            $manager->flush();
            $manager
                ->getRepository('KunstmaanPagePartBundle:PagePartRef')
                ->addPagePart($textpage, $headerpagepart, 2);
        }
        {
            $headerpagepart = new \Kunstmaan\PagePartBundle\Entity\HeaderPagePart();
            $headerpagepart->setNiv(2);
            $headerpagepart->setTitle("Title A");
            $manager->persist($headerpagepart);
            $manager->flush();
            $manager
                ->getRepository('KunstmaanPagePartBundle:PagePartRef')
                ->addPagePart($textpage, $headerpagepart, 3);
        }
        {
            $textpagepart = new \Kunstmaan\PagePartBundle\Entity\TextPagePart();
            $textpagepart->setContent("<strong>Lorem ipsum dolor sit amet</strong>, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci <a href=\"#\">textlink</a> tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. Euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim.</p>");
            $manager->persist($textpagepart);
            $manager->flush();
            $manager
                ->getRepository('KunstmaanPagePartBundle:PagePartRef')
                ->addPagePart($textpage, $textpagepart, 4);
        }
        {
            $headerpagepart = new \Kunstmaan\PagePartBundle\Entity\HeaderPagePart();
            $headerpagepart->setNiv(2);
            $headerpagepart->setTitle("Title B");
            $manager->persist($headerpagepart);
            $manager->flush();
            $manager
                ->getRepository('KunstmaanPagePartBundle:PagePartRef')
                ->addPagePart($textpage, $headerpagepart, 5);
        }
        {
            $textpagepart = new \Kunstmaan\PagePartBundle\Entity\TextPagePart();
            $textpagepart->setContent("<strong>Lorem ipsum dolor sit amet</strong>, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci <a href=\"#\">textlink</a> tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. Euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim.</p>");
            $manager->persist($textpagepart);
            $manager->flush();
            $manager
                ->getRepository('KunstmaanPagePartBundle:PagePartRef')
                ->addPagePart($textpage, $textpagepart, 6);
        }
    }

    /**
     * Create a FormPage
     *
     * @param Doctrine\Common\Persistence\ObjectManager $manager
     * @param string                                    $title
     * @param                                           $parent
     *
     * @return FormPage
     */
    private function createFormPage(ObjectManager $manager, string $title, $parent)
    {
        $page = new FormPage();
        $page->setParent($parent);
        $page->setTitle($title);
        $page->setThanks("<p>We have received your submissions.</p>");
        $manager->persist($page);
        $manager->flush();
        $manager->refresh($page);
        $node = $manager
            ->getRepository('KunstmaanNodeBundle:Node')
            ->createNodeFor($page, 'en', $this->adminuser);
        $manager->persist($node);
        $manager->flush();
        $manager->refresh($node);
        { // Text page
            $counter = 1;
            {
                $singlelinetextpagepart = new \Kunstmaan\FormBundle\Entity\PageParts\SingleLineTextPagePart();
                $singlelinetextpagepart->setLabel("Firstname");
                $singlelinetextpagepart->setRequired(true);
                $singlelinetextpagepart->setErrormessageRequired("Required");
                $manager->persist($singlelinetextpagepart);
                $manager->flush();
                $manager
                    ->getRepository('KunstmaanPagePartBundle:PagePartRef')
                    ->addPagePart($page, $singlelinetextpagepart, $counter++, "main");
            }
            {
                $singlelinetextpagepart = new \Kunstmaan\FormBundle\Entity\PageParts\SingleLineTextPagePart();
                $singlelinetextpagepart->setLabel("Lastname");
                $singlelinetextpagepart->setRequired(true);
                $singlelinetextpagepart->setErrormessageRequired("Required");
                $manager->persist($singlelinetextpagepart);
                $manager->flush();
                $manager
                    ->getRepository('KunstmaanPagePartBundle:PagePartRef')
                    ->addPagePart($page, $singlelinetextpagepart, $counter++, "main");
            }
            {
                $singlelinetextpagepart = new \Kunstmaan\FormBundle\Entity\PageParts\SingleLineTextPagePart();
                $singlelinetextpagepart->setLabel("Postal code");
                $singlelinetextpagepart->setRegex("[0-9]{4}");
                $singlelinetextpagepart->setErrormessageRegex("This is not a valid postal code");
                $manager->persist($singlelinetextpagepart);
                $manager->flush();
                $manager
                    ->getRepository('KunstmaanPagePartBundle:PagePartRef')
                    ->addPagePart($page, $singlelinetextpagepart, $counter++, "main");
            }
            {
                $multilinetextpagepart = new \Kunstmaan\FormBundle\Entity\PageParts\MultiLineTextPagePart();
                $multilinetextpagepart->setLabel("Description");
                $manager->persist($multilinetextpagepart);
                $manager->flush();
                $manager
                    ->getRepository('KunstmaanPagePartBundle:PagePartRef')
                    ->addPagePart($page, $multilinetextpagepart, $counter++, "main");
            }
            {
                $submitbuttonpagepart = new \Kunstmaan\FormBundle\Entity\PageParts\SubmitButtonPagePart();
                $submitbuttonpagepart->setLabel("Send");
                $manager->persist($submitbuttonpagepart);
                $manager->flush();
                $manager
                    ->getRepository('KunstmaanPagePartBundle:PagePartRef')
                    ->addPagePart($page, $submitbuttonpagepart, $counter++, "main");
            }
        }
        $this->initPermissions($node);
        return $page;
    }

    /**
     * Get the order of this fixture
     *
     * @return int
     */
    public function getOrder()
    {
        return 51;
    }

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