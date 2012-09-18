<?php

namespace {{ namespace }}\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use {{ namespace }}\Entity\HomePage;
use {{ namespace }}\Entity\ContentPage;
use {{ namespace }}\Entity\FormPage;
use Kunstmaan\ViewBundle\Entity\SearchPage;
use Kunstmaan\AdminNodeBundle\Entity\Node;
use Kunstmaan\AdminBundle\Entity\Permission;
use Kunstmaan\AdminBundle\Modules\ClassLookup;
use Doctrine\Common\Persistence\ObjectManager;

class DefaultSiteFixtures extends AbstractFixture implements OrderedFixtureInterface
{
    private $adminuser = null;

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

        $search = $this->createSearchPage($manager, "Search", $homepage);
    }

    private function initPermissions($manager, Node $node)
    {
        $superadminGroup = $this->getReference('superadministrators-group');
        $adminGroup = $manager
            ->getRepository('KunstmaanAdminBundle:Group')
            ->findOneBy(array('name' => 'Administrators'));
        $guestGroup = $manager
            ->getRepository('KunstmaanAdminBundle:Group')
            ->findOneBy(array('name' => 'Guests'));

        //Page 1
        //----------------------------------
        $page1Permission1 = new Permission();
        $page1Permission1->setRefId($node->getId());
        $page1Permission1->setRefEntityname(ClassLookup::getClass($node));
        $page1Permission1->setRefGroup($superadminGroup);
        $page1Permission1->setPermissions('|read:1|write:1|delete:1|');
        $manager->persist($page1Permission1);
        $manager->flush();

        $page1Permission2 = new Permission();
        $page1Permission2->setRefId($node->getId());
        $page1Permission2->setRefEntityname(ClassLookup::getClass($node));
        $page1Permission2->setRefGroup($adminGroup);
        $page1Permission2->setPermissions('|read:1|write:1|delete:1|');
        $manager->persist($page1Permission2);
        $manager->flush();

        $page1Permission3 = new Permission();
        $page1Permission3->setRefId($node->getId());
        $page1Permission3->setRefEntityname(ClassLookup::getClass($node));
        $page1Permission3->setRefGroup($guestGroup);
        $page1Permission3->setPermissions('|read:1|write:0|delete:0|');
        $manager->persist($page1Permission3);
        $manager->flush();
    }

    private function createHomePage($manager, $title)
    {
        $homepage = new HomePage();
        $homepage->setTitle($title);
        $manager->persist($homepage);
        $manager->flush();
        $node = $manager
            ->getRepository('KunstmaanAdminNodeBundle:Node')
            ->createNodeFor($homepage, 'en', $this->adminuser);
        $this->initPermissions($manager, $node);

        return $homepage;
    }

    private function createContentPage($manager, $title, $parent)
    {
        $page = new ContentPage();
        $page->setParent($parent);
        $page->setTitle('PageParts');
        $manager->persist($page);
        $manager->flush();
        $node = $manager
            ->getRepository('KunstmaanAdminNodeBundle:Node')
            ->createNodeFor($page, 'en', $this->adminuser);
        $this->initPermissions($manager, $node);
        return $page;
    }

    private function createHeaderPage($manager, $title, $parent)
    {
        $headerpage = new ContentPage();
        $headerpage->setParent($parent);
        $headerpage->setTitle($title);
        $manager->persist($headerpage);
        $manager->flush();
        $node = $manager
            ->getRepository('KunstmaanAdminNodeBundle:Node')
            ->createNodeFor($headerpage, 'en', $this->adminuser);
        $this->initPermissions($manager, $node);
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

    private function createTextPage($manager, $title, $parent)
    {
        $textpage = new ContentPage();
        $textpage->setParent($parent);
        $textpage->setTitle($title);
        $manager->persist($textpage);
        $manager->flush();
        $node = $manager
            ->getRepository('KunstmaanAdminNodeBundle:Node')
            ->createNodeFor($textpage, 'en', $this->adminuser);
        $this->initPermissions($manager, $node);
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

    private function createTocPage($manager, $title, $parent)
    {
        $textpage = new ContentPage();
        $textpage->setParent($parent);
        $textpage->setTitle($title);
        $manager->persist($textpage);
        $manager->flush();
        $node = $manager
            ->getRepository('KunstmaanAdminNodeBundle:Node')
            ->createNodeFor($textpage, 'en', $this->adminuser);
        $this->initPermissions($manager, $node);
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

    private function createFormPage($manager, $title, $parent)
    {
        $page = FormPage();
        $page->setParent($parent);
        $page->setTitle($title);
        $page->setThanks("<p>We have received your submissions.</p>");
        $manager->persist($page);
        $manager->flush();
        $manager->refresh($page);
        $node = $manager
            ->getRepository('KunstmaanAdminNodeBundle:Node')
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
        $this->initPermissions($manager, $node);
        return $page;
    }

    private function createSearchPage($manager, $title, $parent)
    {
        $page = new SearchPage();
        $page->setParent($parent);
        $page->setTitle($title);
        $manager->persist($page);
        $manager->flush();
        $node = $manager
            ->getRepository('KunstmaanAdminNodeBundle:Node')
            ->createNodeFor($page, 'en', $this->adminuser);
        $this->initPermissions($manager, $node);

        return $page;
    }

    public function getOrder()
    {
        return 51;
    }

}