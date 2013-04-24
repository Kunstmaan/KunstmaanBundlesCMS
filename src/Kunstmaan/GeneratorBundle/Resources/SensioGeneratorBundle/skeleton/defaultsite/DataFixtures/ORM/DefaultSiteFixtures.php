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
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Entity\PageInterface;
use Kunstmaan\PagePartBundle\Entity\TextPagePart;
use Kunstmaan\PagePartBundle\Entity\TocPagePart;
use Kunstmaan\PagePartBundle\Entity\HeaderPagePart;
use Kunstmaan\PagePartBundle\Entity\LinePagePart;
use Kunstmaan\PagePartBundle\Entity\LinkPagePart;
use Kunstmaan\PagePartBundle\Entity\RawHTMLPagePart;
use Kunstmaan\PagePartBundle\Entity\ToTopPagePart;
use Kunstmaan\FormBundle\Entity\PageParts\SubmitButtonPagePart;
use Kunstmaan\FormBundle\Entity\PageParts\MultiLineTextPagePart;
use Kunstmaan\FormBundle\Entity\PageParts\SingleLineTextPagePart;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\MaskBuilder;
use Kunstmaan\AdminBundle\Entity\DashboardConfiguration;

class DefaultSiteFixtures extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{

    private $PARAGRAPHTEXT="<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean auctor tempor nisl, eget mattis dolor malesuada non. In hac habitasse platea dictumst. Phasellus porttitor tempus neque nec fringilla. Aenean feugiat, nunc in scelerisque cursus, eros turpis condimentum justo, a tempor orci ligula pharetra velit. Vestibulum a purus interdum tellus eleifend semper. Integer eleifend adipiscing gravida. Phasellus dignissim, quam sagittis molestie sollicitudin, urna ligula pharetra diam, id consequat dui ante eget justo.</p>";
    private $SHORT_PARAGRAPHTEXT="<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean auctor tempor nisl, eget mattis dolor malesuada non. In hac habitasse platea dictumst. Phasellus porttitor tempus neque nec fringilla.</p>";
    private $RAW_HTML='<div class="row"><div class="six columns"><div class="panel"><h5>Lorem ipsum dolor sit amet.</h5><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean auctor tempor nisl, eget mattis dolor malesuada non. In hac habitasse platea dictumst. Phasellus porttitor tempus neque nec fringilla.</p></div></div><div class="six columns"><div class="panel callout radius"><h5>Aenean auctor tempor nisl.</h5><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean auctor tempor nisl, eget mattis dolor malesuada non. In hac habitasse platea dictumst. Phasellus porttitor tempus neque nec fringilla.</p></div></div></div>';

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

        // Homepage
        $homePage = $this->createHomePage($manager, "Home");
        // PageParts
        $this->createPagePartPage($manager, "Content PageParts", $homePage);
        // From PageParts
        $this->createFormPage($manager, "Form PageParts", $homePage);
        // Dashboard
        /** @var $dashboard DashboardConfiguration */
        $dashboard = $manager->getRepository("KunstmaanAdminBundle:DashboardConfiguration")->findOneBy(array());
        if (is_null($dashboard)) {
            $dashboard = new DashboardConfiguration();
        }
        $dashboard->setTitle("Dashboard");
        $dashboard->setContent('<div class="alert alert-block alert-error"><strong>Important: </strong>please change these items to the graphs of your own site!</div><iframe src="https://rpm.newrelic.com/public/charts/2h1YQ3W7j7Z" width="100%" height="300" scrolling="no" frameborder="no"></iframe><iframe src="https://rpm.newrelic.com/public/charts/1VNlg8JA5ed" width="100%" height="300" scrolling="no" frameborder="no"></iframe><iframe src="https://rpm.newrelic.com/public/charts/36A9KcMTMli" width="100%" height="300" scrolling="no" frameborder="no"></iframe>');
        $manager->persist($dashboard);
        $manager->flush();
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
     * @return PageInterface
     */
    private function createAndPersistPage(ObjectManager $manager, $class, $title, $parent = null, $slug = null, $internalName = null)
    {
        /* @var PageInterface $page */
        $page = new $class();
        $page->setTitle($title);
        if (!is_null($parent)) {
            $page->setParent($parent);
        }
        $manager->persist($page);
        $manager->flush();
        $node = $manager->getRepository('KunstmaanNodeBundle:Node')->createNodeFor($page, 'en', $this->adminuser, $internalName);
        /** @var $nodeTranslation NodeTranslation */
        $nodeTranslation = $node->getNodeTranslation('en', true);
        $nodeTranslation->setOnline(true);
        if (!is_null($slug)) {
            $nodeTranslation->setSlug($slug);
        }
        $manager->persist($nodeTranslation);
        $manager->flush();
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
        $homepage = $this->createAndPersistPage($manager, '{{ namespace }}\Entity\Pages\HomePage', $title, null, "", "homepage");
        {
            $headerpagepart = new HeaderPagePart();
            $headerpagepart->setNiv(1);
            $headerpagepart->setTitle("Welcome to your new website");
            $manager->persist($headerpagepart);
            $manager->flush();
            $manager->getRepository('KunstmaanPagePartBundle:PagePartRef')->addPagePart($homepage, $headerpagepart, 1);
        }
        {
            $textpagepart = new TextPagePart();
            $textpagepart->setContent("<p><strong>Success! It Works!</strong></p><p>This is a barebone frontend template, this can be, but most likely is not, the starting point of your website. This frontend template is built using <a href=\"http://foundation.zurb.com/\">Foundation 3</a>.</p>");
            $manager->persist($textpagepart);
            $manager->flush();
            $manager->getRepository('KunstmaanPagePartBundle:PagePartRef')->addPagePart($homepage, $textpagepart, 2);
        }

        return $homepage;
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
        return $this->createAndPersistPage($manager, '{{ namespace }}\Entity\Pages\ContentPage', $title, $parent);
    }

    /**
     * Create a ContentPage containing headers
     *
     * @param ObjectManager $manager The object manager
     * @param string        $title   The title
     * @param PageInterface $parent  The parent
     */
    private function createPagePartPage(ObjectManager $manager, $title, $parent)
    {
        $headerpage = $this->createContentPage($manager, $title, $parent);
        $position = 1;
        {
            $this->createTOCPagePart($headerpage, $position++, $manager);
        }
        {
            $this->createHeaderPagePart("1. Header (niv=1)", 1, $headerpage, $position++, $manager);
            $this->createTextPagePart($this->PARAGRAPHTEXT, $headerpage, $position++, $manager);
            {
                $this->createHeaderPagePart("1.1. Header (niv=2)", 2, $headerpage, $position++, $manager);
                $this->createTextPagePart($this->PARAGRAPHTEXT, $headerpage, $position++, $manager);
                {
                    $this->createHeaderPagePart("1.1.1. Header (niv=3)", 3, $headerpage, $position++, $manager);
                    $this->createTextPagePart($this->SHORT_PARAGRAPHTEXT, $headerpage, $position++, $manager);
                }
                {
                    $this->createHeaderPagePart("1.1.1.1. Header (niv=4)", 4, $headerpage, $position++, $manager);
                    $this->createTextPagePart($this->SHORT_PARAGRAPHTEXT, $headerpage, $position++, $manager);
                }
                {
                    $this->createHeaderPagePart("1.1.1.1.1. Header (niv=5)", 5, $headerpage, $position++, $manager);
                    $this->createTextPagePart($this->SHORT_PARAGRAPHTEXT, $headerpage, $position++, $manager);
                }
                {
                    $this->createHeaderPagePart("1.1.1.1.1.1. Header (niv=6)", 6, $headerpage, $position++, $manager);
                    $this->createTextPagePart($this->SHORT_PARAGRAPHTEXT, $headerpage, $position++, $manager);
                }
            }
            {
                $this->createHeaderPagePart("1.2. Header (niv=2)", 2, $headerpage, $position++, $manager);
                $this->createTextPagePart($this->PARAGRAPHTEXT, $headerpage, $position++, $manager);
            }
            {
                $this->createToTopPagePart($headerpage, $position++, $manager);
            }
            {
                $this->createLinePagePart($headerpage, $position++, $manager);
            }
        }
        {
            $this->createRawHTMLPagePart($this->RAW_HTML, $headerpage, $position++, $manager);
        }
        {
            $this->createHeaderPagePart("2. Header (niv=1)", 1, $headerpage, $position++, $manager);
            $this->createTextPagePart($this->PARAGRAPHTEXT, $headerpage, $position++, $manager);
            {
                $this->createHeaderPagePart("2.1. Header (niv=2)", 2, $headerpage, $position++, $manager);
                $this->createTextPagePart($this->PARAGRAPHTEXT, $headerpage, $position++, $manager);
            }
            {
                $this->createHeaderPagePart("2.2. Header (niv=2)", 2, $headerpage, $position++, $manager);
                $this->createTextPagePart($this->PARAGRAPHTEXT, $headerpage, $position++, $manager);
                {
                    $this->createHeaderPagePart("2.2.2. Header (niv=3)", 3, $headerpage, $position++, $manager);
                    $this->createTextPagePart($this->SHORT_PARAGRAPHTEXT, $headerpage, $position++, $manager);
                }
                {
                    $this->createHeaderPagePart("2.2.2.2. Header (niv=4)", 4, $headerpage, $position++, $manager);
                    $this->createTextPagePart($this->SHORT_PARAGRAPHTEXT, $headerpage, $position++, $manager);
                }
                {
                    $this->createHeaderPagePart("2.2.2.2.2. Header (niv=5)", 5, $headerpage, $position++, $manager);
                    $this->createTextPagePart($this->SHORT_PARAGRAPHTEXT, $headerpage, $position++, $manager);
                }
                {
                    $this->createHeaderPagePart("2.2.2.2.2.2. Header (niv=6)", 6, $headerpage, $position++, $manager);
                    $this->createTextPagePart($this->SHORT_PARAGRAPHTEXT, $headerpage, $position++, $manager);
                }
            }
            {
                $this->createToTopPagePart($headerpage, $position++, $manager);
            }
            {
                $this->createLinePagePart($headerpage, $position++, $manager);
            }
            {
                $this->createLinkPagePart("http://bundles.kunstmaan.be", "Kunstmaan Bundles site", true, $headerpage, $position++, $manager);
            }
        }
    }

    /**
     * Create a ToTopPagePart
     *
     * @param PageInterface $page     The page where the pagepart needs to be created
     * @param int           $position The position on the page
     * @param ObjectManager $manager  The object manager
     */
    private function createToTopPagePart($page, $position, $manager)
    {
        $totoppagepart = new ToTopPagePart();
        $manager->persist($totoppagepart);
        $manager->flush();
        $manager->getRepository('KunstmaanPagePartBundle:PagePartRef')->addPagePart($page, $totoppagepart, $position);
    }

    /**
     * Create a RawHTMLPagePart
     *
     * @param string        $content  The content of the pagepart
     * @param PageInterface $page     The page where the pagepart needs to be created
     * @param int           $position The position on the page
     * @param ObjectManager $manager  The object manager
     */
    private function createRawHTMLPagePart($content, $page, $position, $manager)
    {
        $rawhtmlpagepart = new RawHTMLPagePart();
        $rawhtmlpagepart->setContent($content);
        $manager->persist($rawhtmlpagepart);
        $manager->flush();
        $manager->getRepository('KunstmaanPagePartBundle:PagePartRef')->addPagePart($page, $rawhtmlpagepart, $position);
    }

    /**
     * Create a TocPagePart
     *
     * @param PageInterface $page     The page where the pagepart needs to be created
     * @param int           $position The position on the page
     * @param ObjectManager $manager  The object manager
     */
    private function createTOCPagePart($page, $position, $manager)
    {
        $tocpagepart = new TocPagePart();
        $manager->persist($tocpagepart);
        $manager->flush();
        $manager->getRepository('KunstmaanPagePartBundle:PagePartRef')->addPagePart($page, $tocpagepart, $position);
    }

    /**
     * Creates a LinkPagePart (Not really useful in real life, more a proof of concept)
     *
     * @param string        $url             The link
     * @param string        $text            The label
     * @param boolean       $openinnewwindow Should the target be blank
     * @param PageInterface $page            The page where the pagepart needs to be created
     * @param int           $position        The position on the page
     * @param ObjectManager $manager         The object manager
     */
    private function createLinkPagePart($url, $text, $openinnewwindow, $page, $position, $manager)
    {
        $linkpagepart = new LinkPagePart();
        $linkpagepart->setUrl($url);
        $linkpagepart->setText($text);
        $linkpagepart->setOpenInNewWindow($openinnewwindow);
        $manager->persist($linkpagepart);
        $manager->flush();
        $manager->getRepository('KunstmaanPagePartBundle:PagePartRef')->addPagePart($page, $linkpagepart, $position);
    }

    /**
     * Create a LinePagePart
     *
     * @param PageInterface $page     The page where the pagepart needs to be created
     * @param int           $position The position on the page
     * @param ObjectManager $manager  The object manager
     */
    private function createLinePagePart($page, $position, $manager)
    {
        $linepagepart = new LinePagePart();
        $manager->persist($linepagepart);
        $manager->flush();
        $manager->getRepository('KunstmaanPagePartBundle:PagePartRef')->addPagePart($page, $linepagepart, $position);
    }

    /**
     * Create a HeaderPagePart
     *
     * @param string        $content  The content of the pagepart
     * @param int           $level    The header level
     * @param PageInterface $page     The page where the pagepart needs to be created
     * @param int           $position The position on the page
     * @param ObjectManager $manager  The object manager
     */
    private function createHeaderPagePart($content, $level, $page, $position, $manager)
    {
        $headerpagepart = new HeaderPagePart();
        $headerpagepart->setNiv($level);
        $headerpagepart->setTitle($content);
        $manager->persist($headerpagepart);
        $manager->flush();
        $manager->getRepository('KunstmaanPagePartBundle:PagePartRef')->addPagePart($page, $headerpagepart, $position);
    }

    /**
     * Create a TextPagePart
     *
     * @param string        $content  The content of the pagepart
     * @param PageInterface $page     The page where the pagepart needs to be created
     * @param int           $position The position on the page
     * @param ObjectManager $manager  The object manager
     */
    private function createTextPagePart($content, $page, $position, $manager)
    {
        $textpagepart = new TextPagePart();
        $textpagepart->setContent($content);
        $manager->persist($textpagepart);
        $manager->flush();
        $manager->getRepository('KunstmaanPagePartBundle:PagePartRef')->addPagePart($page, $textpagepart, $position);
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
        $page = $this->createAndPersistPage($manager, '{{ namespace }}\Entity\Pages\FormPage', $title, $parent);
        $page->setThanks("<p>We have received your submission.</p>");
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
//            {
//                $choicepagepart = new ChoicePagePart();
//                $choicepagepart->setLabel("Subject");
//                $choices = array("sub1" => "Subject 1", "sub2" => "Subject 2", "sub3" =>"Subject 3");
//                $choicepagepart->setChoices($choices);
//                $manager->persist($choicepagepart);
//                $manager->flush();
//                $manager->getRepository('KunstmaanPagePartBundle:PagePartRef')->addPagePart($page, $choicepagepart, $counter++, "main");
//            }
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
