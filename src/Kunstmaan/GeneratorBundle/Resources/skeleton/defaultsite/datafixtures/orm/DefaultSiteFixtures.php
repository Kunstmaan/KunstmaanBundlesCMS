<?php

namespace {{ namespace }}\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Acl\Exception\AclNotFoundException;
use Symfony\Component\Security\Acl\Model\MutableAclProviderInterface;
use Symfony\Component\Security\Acl\Model\ObjectIdentityRetrievalStrategyInterface;

use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Entity\PageInterface;
use Kunstmaan\PagePartBundle\Entity\TextPagePart;
use Kunstmaan\PagePartBundle\Entity\TocPagePart;
use Kunstmaan\PagePartBundle\Entity\HeaderPagePart;
use Kunstmaan\FormBundle\Entity\PageParts\SubmitButtonPagePart;
use Kunstmaan\FormBundle\Entity\PageParts\MultiLineTextPagePart;
use Kunstmaan\FormBundle\Entity\PageParts\SingleLineTextPagePart;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\MaskBuilder;

class DefaultSiteFixtures extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{

    /**
     * @var UserInterface
     */
    private $adminuser = null;

    /**
     * @var ContainerInterface
     */
    private $container = null;

    /**
     * Load data fixtures with the passed EntityManager.
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $this->adminuser = $manager
            ->getRepository('KunstmaanAdminBundle:User')
            ->findOneBy(array('username' => 'Admin'));

        $homePage = $this->createHomePage($manager, "Home");
        $pagePartsPage = $this->createContentPage($manager, "PageParts", $homePage);
        $this->createHeaderPage($manager, "Headers", $pagePartsPage);
        $this->createTextPage($manager, "Text", $pagePartsPage);
        $this->createTocPage($manager, "Toc", $pagePartsPage);
        $this->createFormPage($manager, "Form", $pagePartsPage);
    }

    /**
     * Initialize the permissions for the given Node
     *
     * @param Node $node
     */
    private function initPermissions(Node $node)
    {
        /* @var MutableAclProviderInterface $aclProvider */
        $aclProvider = $this->container->get('security.acl.provider');
        /* @var ObjectIdentityRetrievalStrategyInterface $oidStrategy */
        $oidStrategy = $this->container->get('security.acl.object_identity_retrieval_strategy');
        $objectIdentity = $oidStrategy->getObjectIdentity($node);
        try {
            $acl = $aclProvider->findAcl($objectIdentity);
            $aclProvider->deleteAcl($objectIdentity);
        } catch (AclNotFoundException $e) {
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
     * @param ObjectManager $manager The object manager
     * @param string        $class   The class
     * @param string        $title   The title
     * @param PageInterface $parent  The parent
     *
     * @return Kunstmaan\NodeBundle\Entity\PageInterface
     */
    private function createAndPersistPage(ObjectManager $manager, $class, $title, $parent = null)
    {
        /* @var PageInterface $page */
        $page = new $class();
        $page->setTitle($title);
        if (!is_null($parent)) {
            $page->setParent($parent);
        }
        $manager->persist($page);
        $manager->flush();
        $node = $manager->getRepository('KunstmaanNodeBundle:Node')->createNodeFor($page, 'en', $this->adminuser);
        $this->initPermissions($node);

        return $page;
    }

    /**
     * Create a Homepage
     *
     * @param ObjectManager $manager The object manager
     * @param string        $title   The title
     *
     * @return HomePage
     */
    private function createHomePage(ObjectManager $manager, $title)
    {
        return $this->createAndPersistPage($manager, '{{ namespace }}\Entity\HomePage', $title);
    }

    /**
     * Create a ContentPage
     *
     * @param ObjectManager $manager The object manager
     * @param string        $title   The title
     * @param PageInterface $parent  The parent
     *
     * @return ContentPage
     */
    private function createContentPage(ObjectManager $manager, $title, $parent)
    {
        return $this->createAndPersistPage($manager, '{{ namespace }}\Entity\ContentPage', $title, $parent);
    }

    /**
     * Create a ContentPage containing headers
     *
     * @param ObjectManager $manager The object manager
     * @param string        $title   The title
     * @param PageInterface $parent  The parent
     */
    private function createHeaderPage(ObjectManager $manager, $title, $parent)
    {
        $headerpage = $this->createContentPage($manager, $title, $parent);

        for ($i = 1; $i <= 6; $i++) {
            $headerpagepart = new HeaderPagePart();
            $headerpagepart->setNiv($i);
            $headerpagepart->setTitle("Header " . $i);
            $manager->persist($headerpagepart);
            $manager->flush();
            $manager->getRepository('KunstmaanPagePartBundle:PagePartRef')->addPagePart($headerpage, $headerpagepart, $i);
        }
    }

    /**
     * Create a ContentPage containing headers and text
     *
     * @param ObjectManager $manager The object manager
     * @param string        $title   The title
     * @param PageInterface $parent  The parent
     */
    private function createTextPage(ObjectManager $manager, $title, $parent)
    {
        $textpage = $this->createContentPage($manager, $title, $parent);
        {
            $headerpagepart = new HeaderPagePart();
            $headerpagepart->setNiv(1);
            $headerpagepart->setTitle($title);
            $manager->persist($headerpagepart);
            $manager->flush();
            $manager
                ->getRepository('KunstmaanPagePartBundle:PagePartRef')
                ->addPagePart($textpage, $headerpagepart, 1);
        }
        {
            $textpagepart = new TextPagePart();
            $textpagepart->setContent("<strong>Lorem ipsum dolor sit amet</strong>, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci <a href=\"#\">textlink</a> tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. Euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim.</p>");
            $manager->persist($textpagepart);
            $manager->flush();
            $manager->getRepository('KunstmaanPagePartBundle:PagePartRef')->addPagePart($textpage, $textpagepart, 2);
        }
    }

    /**
     * Create a ContentPage containing a table of content, headers and text
     *
     * @param Doctrine\Common\Persistence\ObjectManager $manager
     * @param string                                    $title
     * @param                                           $parent
     */
    private function createTocPage(ObjectManager $manager, $title, $parent)
    {
        $textpage = $this->createContentPage($manager, $title, $parent);
        {
            $headerpagepart = new HeaderPagePart();
            $headerpagepart->setNiv(1);
            $headerpagepart->setTitle($title);
            $manager->persist($headerpagepart);
            $manager->flush();
            $manager->getRepository('KunstmaanPagePartBundle:PagePartRef')->addPagePart($textpage, $headerpagepart, 1);
        }
        {
            $headerpagepart = new TocPagePart();
            $manager->persist($headerpagepart);
            $manager->flush();
            $manager->getRepository('KunstmaanPagePartBundle:PagePartRef')->addPagePart($textpage, $headerpagepart, 2);
        }
        {
            $headerpagepart = new HeaderPagePart();
            $headerpagepart->setNiv(2);
            $headerpagepart->setTitle("Title A");
            $manager->persist($headerpagepart);
            $manager->flush();
            $manager->getRepository('KunstmaanPagePartBundle:PagePartRef')->addPagePart($textpage, $headerpagepart, 3);
        }
        {
            $textpagepart = new TextPagePart();
            $textpagepart->setContent("<strong>Lorem ipsum dolor sit amet</strong>, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci <a href=\"#\">textlink</a> tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. Euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim.</p>");
            $manager->persist($textpagepart);
            $manager->flush();
            $manager->getRepository('KunstmaanPagePartBundle:PagePartRef')->addPagePart($textpage, $textpagepart, 4);
        }
        {
            $headerpagepart = new HeaderPagePart();
            $headerpagepart->setNiv(2);
            $headerpagepart->setTitle("Title B");
            $manager->persist($headerpagepart);
            $manager->flush();
            $manager->getRepository('KunstmaanPagePartBundle:PagePartRef')->addPagePart($textpage, $headerpagepart, 5);
        }
        {
            $textpagepart = new TextPagePart();
            $textpagepart->setContent("<strong>Lorem ipsum dolor sit amet</strong>, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci <a href=\"#\">textlink</a> tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. Euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim.</p>");
            $manager->persist($textpagepart);
            $manager->flush();
            $manager->getRepository('KunstmaanPagePartBundle:PagePartRef')->addPagePart($textpage, $textpagepart, 6);
        }
    }

    /**
     * Create a FormPage
     *
     * @param ObjectManager $manager The object manager
     * @param string        $title   The title
     * @param PageInterface $parent  The parent
     *
     * @return FormPage
     */
    private function createFormPage(ObjectManager $manager, $title, $parent)
    {
        $page = $this->createAndPersistPage($manager, '{{ namespace }}\Entity\FormPage', $title, $parent);
        $page->setThanks("<p>We have received your submissions.</p>");
        $manager->persist($page);
        $manager->flush();
        $manager->refresh($page);
        { // Text page
            $counter = 1;
            {
                $singlelinetextpagepart = new SingleLineTextPagePart();
                $singlelinetextpagepart->setLabel("Firstname");
                $singlelinetextpagepart->setRequired(true);
                $singlelinetextpagepart->setErrormessageRequired("Required");
                $manager->persist($singlelinetextpagepart);
                $manager->flush();
                $manager->getRepository('KunstmaanPagePartBundle:PagePartRef')->addPagePart($page, $singlelinetextpagepart, $counter++, "main");
            }
            {
                $singlelinetextpagepart = new SingleLineTextPagePart();
                $singlelinetextpagepart->setLabel("Lastname");
                $singlelinetextpagepart->setRequired(true);
                $singlelinetextpagepart->setErrormessageRequired("Required");
                $manager->persist($singlelinetextpagepart);
                $manager->flush();
                $manager->getRepository('KunstmaanPagePartBundle:PagePartRef')->addPagePart($page, $singlelinetextpagepart, $counter++, "main");
            }
            {
                $singlelinetextpagepart = new SingleLineTextPagePart();
                $singlelinetextpagepart->setLabel("Postal code");
                $singlelinetextpagepart->setRegex("[0-9]{4}");
                $singlelinetextpagepart->setErrormessageRegex("This is not a valid postal code");
                $manager->persist($singlelinetextpagepart);
                $manager->flush();
                $manager->getRepository('KunstmaanPagePartBundle:PagePartRef')->addPagePart($page, $singlelinetextpagepart, $counter++, "main");
            }
            {
                $multilinetextpagepart = new MultiLineTextPagePart();
                $multilinetextpagepart->setLabel("Description");
                $manager->persist($multilinetextpagepart);
                $manager->flush();
                $manager->getRepository('KunstmaanPagePartBundle:PagePartRef')->addPagePart($page, $multilinetextpagepart, $counter++, "main");
            }
            {
                $submitbuttonpagepart = new SubmitButtonPagePart();
                $submitbuttonpagepart->setLabel("Send");
                $manager->persist($submitbuttonpagepart);
                $manager->flush();
                $manager->getRepository('KunstmaanPagePartBundle:PagePartRef')->addPagePart($page, $submitbuttonpagepart, $counter++, "main");
            }
        }

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