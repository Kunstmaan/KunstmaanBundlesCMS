<?php

namespace {{ namespace }}\DataFixtures\ORM\DefaultSiteGenerator;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Kunstmaan\AdminBundle\Entity\DashboardConfiguration;
use Kunstmaan\MediaBundle\Entity\Media;
use Kunstmaan\MediaBundle\Helper\RemoteVideo\RemoteVideoHelper;
use Kunstmaan\MediaBundle\Helper\Services\MediaCreatorService;
use Kunstmaan\NodeBundle\Helper\Services\PageCreatorService;
use Kunstmaan\PagePartBundle\Helper\Services\PagePartCreatorService;
use Kunstmaan\TranslatorBundle\Entity\Translation;
use {{ namespace }}\Entity\Pages\ContentPage;
use {{ namespace }}\Entity\Pages\HomePage;
{% if demosite %}
use {{ namespace }}\Entity\Pages\FormPage;
use {{ namespace }}\Entity\Pages\SearchPage;
use {{ namespace }}\Entity\Bike;
{% endif %}

/**
 * DefaultSiteFixtures
 */
class DefaultSiteFixtures extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * Username that is used for creating pages
     */
    const ADMIN_USERNAME = 'admin';

    /**
     * @var ContainerInterface
     */
    private $container = null;

    /**
     * @var ObjectManager
     */
    private $manager;

    /**
     * @var PageCreatorService
     */
    private $pageCreator;

    /**
     * @var PagePartCreatorService
     */
    private $pagePartCreator;

    /**
     * @var MediaCreatorService
     */
    private $mediaCreator;

    /**
      * Defined locales during generation
      */
    private $requiredLocales;

    /**
     * Load data fixtures with the passed EntityManager.
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;

        $this->pageCreator = $this->container->get('kunstmaan_node.page_creator_service');
        $this->pagePartCreator = $this->container->get('kunstmaan_pageparts.pagepart_creator_service');
        $this->mediaCreator = $this->container->get('kunstmaan_media.media_creator_service');
        $this->requiredLocales = explode('|', $this->container->getParameter('requiredlocales'));

        $this->createTranslations();
        $this->createMedia();
        $this->createHomePage();
        $this->createContentPages();
{% if demosite %}
        $this->createAdminListPages();
        $this->createFormPage();
        $this->createSearchPage();
{% endif %}
        $this->createDashboard();
    }

    /**
     * Create the dashboard
     */
    private function createDashboard()
    {
        /** @var $dashboard DashboardConfiguration */
        $dashboard = $this->manager->getRepository("KunstmaanAdminBundle:DashboardConfiguration")->findOneBy(array());
        if (is_null($dashboard)) {
            $dashboard = new DashboardConfiguration();
        }
        $dashboard->setTitle("Dashboard");
        $dashboard->setContent('<div class="alert alert-info"><strong>Important: </strong>please change these items to the graphs of your own site!</div><iframe src="https://rpm.newrelic.com/public/charts/jjPIEE7OHz9" width="100%" height="300" scrolling="no" frameborder="no"></iframe><iframe src="https://rpm.newrelic.com/public/charts/hmDWR0eUNTo" width="100%" height="300" scrolling="no" frameborder="no"></iframe><iframe src="https://rpm.newrelic.com/public/charts/fv7IP1EmbVi" width="100%" height="300" scrolling="no" frameborder="no"></iframe>');
        $this->manager->persist($dashboard);
        $this->manager->flush();
    }

    /**
     * Create a Homepage
     */
    private function createHomePage()
    {
        $homePage = new HomePage();
        $homePage->setTitle('Home');

        $translations = array();
        foreach ($this->requiredLocales as $locale) {
            $translations[] = array(
                'language' => $locale,
                'callback' => function ($page, $translation, $seo) {
                    $translation->setTitle('Home');
                    $translation->setSlug('');
                }
            );
        }

        $options = array(
            'parent' => null,
            'page_internal_name' => 'homepage',
            'set_online' => true,
            'hidden_from_nav' => false,
            'creator' => self::ADMIN_USERNAME
        );

        $this->pageCreator->createPage($homePage, $translations, $options);

        {% if demosite %}
        foreach ($this->requiredLocales as $locale) {
            $pageparts = array();

            $folder = $this->manager->getRepository('KunstmaanMediaBundle:Folder')->findOneBy(array('rel' => 'image'));
            $imgDir = dirname(__FILE__).'/../../../Resources/ui/img/demosite/';

            $headerMedia = $this->mediaCreator->createFile($imgDir.'stocks/homepage__header.jpg', $folder->getId());
            $pageparts['header'][] = $this->pagePartCreator->getCreatorArgumentsForPagePartAndProperties(
                '{{ namespace }}\Entity\PageParts\PageBannerPagePart',
                array(
                    'setTitle' => $locale == 'nl' ? 'Wat doen we?' : 'What do we do?',
                    'setDescription' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Id cum corporis adipisci maiores nobis.',
                    'setBackgroundImage' => $headerMedia,
                    'setButtonUrl' => $locale == 'nl' ? '/nl/diensten' : '/' . $locale . '/services',
                    'setButtonText' => $locale == 'nl' ? 'Onze diensten' : 'Our services',
                )
            );

            $pageparts['section1'][] = $this->pagePartCreator->getCreatorArgumentsForPagePartAndProperties(
                '{{ namespace }}\Entity\PageParts\HeaderPagePart',
                array(
                    'setTitle' => $locale == 'nl' ? 'Wat doen we?' : 'What do we do?',
                    'setNiv' => 2
                )
            );
            $pageparts['section1'][] = $this->pagePartCreator->getCreatorArgumentsForPagePartAndProperties(
                '{{ namespace }}\Entity\PageParts\IntroTextPagePart',
                array(
                    'setContent' => '<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Molestias accusamus sint nostrum at, omnis ad quia ipsum fugit est magnam itaque error voluptates aliquam odio repellendus quis adipisci in. Alias!</p>'
                )
            );
            $pageparts['section1'][] = $this->pagePartCreator->getCreatorArgumentsForPagePartAndProperties(
                '{{ namespace }}\Entity\PageParts\LinkPagePart',
                array(
                    'setUrl' => $locale == 'nl' ? '/nl/diensten' : '/' . $locale . '/services',
                    'setText' => $locale == 'nl' ? 'Lees meer' : 'Read more'
                )
            );

            $buyBikeMedia = $this->mediaCreator->createFile($imgDir.'stocks/fixie1.png', $folder->getId());
            $pageparts['section2'][] = $this->pagePartCreator->getCreatorArgumentsForPagePartAndProperties(
                '{{ namespace }}\Entity\PageParts\ServicePagePart',
                array(
                    'setTitle' => $locale == 'nl' ? 'Onze fiets' : 'Our bycicles',
                    'setDescription' => '<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Molestias accusamus sint nostrum at, omnis ad quia ipsum fugit est magnam itaque error voluptates aliquam odio repellendus quis adipisci in. Alias!</p>',
                    'setLinkUrl' => $locale == 'nl' ? '/nl/diensten' : '/' . $locale . '/services',
                    'setLinkText' => $locale == 'nl' ? 'Ondek onze projecten' : 'Discover our projects',
                    'setImage' => $buyBikeMedia,
                    'setImagePosition' => 'right',
                )
            );

            $repairBikeMedia = $this->mediaCreator->createFile($imgDir.'stocks/homepage__maintenance.jpg', $folder->getId());
            $pageparts['section3'][] = $this->pagePartCreator->getCreatorArgumentsForPagePartAndProperties(
                '{{ namespace }}\Entity\PageParts\ServicePagePart',
                array(
                    'setTitle' => $locale == 'nl' ? 'Onderhoud' : 'Maintenance',
                    'setDescription' => '<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Molestias accusamus sint nostrum at, omnis ad quia ipsum fugit est magnam itaque error voluptates aliquam odio repellendus quis adipisci in. Alias!</p>',
                    'setLinkUrl' => $locale == 'nl' ? '/nl/diensten' : '/' . $locale . '/services',
                    'setLinkText' => $locale == 'nl' ? 'Bekijk lorem ipsum' : 'Read more',
                    'setImage' => $repairBikeMedia,
                    'setImagePosition' => 'left',
                )
            );

            $pageparts['section4'][] = $this->pagePartCreator->getCreatorArgumentsForPagePartAndProperties(
                '{{ namespace }}\Entity\PageParts\HeaderPagePart',
                array(
                    'setTitle' => $locale == 'nl' ? 'Waarom voor ons kiezen?' : 'Why choose us?',
                    'setNiv' => 2
                )
            );
            $items = new \Doctrine\Common\Collections\ArrayCollection();
            $item1Media = $this->mediaCreator->createFile($imgDir.'icons/icon--1.svg', $folder->getId());
            $item1 = new \{{ namespace }}\Entity\UspItem();
            $item1->setIcon($item1Media);
            $item1->setTitle('Fast repair');
            $item1->setDescription('Lorem ipsum dolor sit amet, consectetur adipisicing elit. Molestias accusamus sint nostrum at');
            $item1->setWeight(0);
            $items->add($item1);
            $item2Media = $this->mediaCreator->createFile($imgDir.'icons/icon--2.svg', $folder->getId());
            $item2 = new \{{ namespace }}\Entity\UspItem();
            $item2->setIcon($item2Media);
            $item2->setTitle('Personal service');
            $item2->setDescription('Lorem ipsum dolor sit amet, consectetur adipisicing elit. Molestias accusamus sint nostrum at');
            $item2->setWeight(1);
            $items->add($item2);
            $item3Media = $this->mediaCreator->createFile($imgDir.'icons/icon--3.svg', $folder->getId());
            $item3 = new \{{ namespace }}\Entity\UspItem();
            $item3->setIcon($item3Media);
            $item3->setTitle('10 years of experience');
            $item3->setDescription('Lorem ipsum dolor sit amet, consectetur adipisicing elit. Molestias accusamus sint nostrum at');
            $item3->setWeight(2);
            $items->add($item3);
            $pageparts['section4'][] = $this->pagePartCreator->getCreatorArgumentsForPagePartAndProperties(
                '{{ namespace }}\Entity\PageParts\UspPagePart',
                array(
                    'setItems' => $items,
                )
            );

            $pageparts['section5'][] = $this->pagePartCreator->getCreatorArgumentsForPagePartAndProperties(
                '{{ namespace }}\Entity\PageParts\HeaderPagePart',
                array(
                    'setTitle' => $locale == 'nl' ? 'Het team' : 'The Team',
                    'setNiv' => 2
                )
            );
            $teamMedia = $this->mediaCreator->createFile($imgDir.'stocks/team.jpg', $folder->getId());
            $pageparts['section5'][] = $this->pagePartCreator->getCreatorArgumentsForPagePartAndProperties(
                '{{ namespace }}\Entity\PageParts\ImagePagePart',
                array(
                    'setMedia' => $teamMedia
                )
            );

            $this->pagePartCreator->addPagePartsToPage('homepage', $pageparts, $locale);
        }
        {% endif %}
    }

    /**
     * Create a ContentPage
     */
    private function createContentPages()
    {
        $nodeRepo = $this->manager->getRepository('KunstmaanNodeBundle:Node');
        $homePage = $nodeRepo->findOneBy(array('internalName' => 'homepage'));

        $contentPage = new ContentPage();
        $contentPage->setTitle('Services');

        $translations = array();
        foreach ($this->requiredLocales as $locale) {
            $translations[] = array('language' => $locale, 'callback' => function($page, $translation, $seo) use ($locale) {
                $translation->setTitle($locale == 'nl' ? 'Diensten' : 'Services');
                $translation->setSlug($locale == 'nl' ? 'diensten' : 'services');
                $translation->setWeight(20);
            });
        }

        $options = array(
            'parent' => $homePage,
            'page_internal_name' => 'services',
            'set_online' => true,
            'hidden_from_nav' => false,
            'creator' => self::ADMIN_USERNAME
        );

        $this->pageCreator->createPage($contentPage, $translations, $options);

{% if demosite %}
        foreach ($this->requiredLocales as $locale) {
            // Add pageparts
            $pageparts = array();
            $pageparts['main'][] = $this->pagePartCreator->getCreatorArgumentsForPagePartAndProperties(
                '{{ namespace }}\Entity\PageParts\HeaderPagePart',
                array(
                    'setTitle' => $locale == 'nl' ? 'Onze diensten' : 'Our services',
                    'setNiv' => 2
                )
            );
            $pageparts['main'][] = $this->pagePartCreator->getCreatorArgumentsForPagePartAndProperties(
                '{{ namespace }}\Entity\PageParts\TextPagePart',
                array(
                    'setContent' => '<p>Inleiding ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi.</p>'
                )
            );

            $this->pagePartCreator->addPagePartsToPage('services', $pageparts, $locale);
            $this->pagePartCreator->setPageTemplate('services', $locale, 'Content page with submenu');
        }

        // Buy bikes page
        $servicesPage = $nodeRepo->findOneBy(array('internalName' => 'services'));
        $contentPage = new ContentPage();
        $contentPage->setTitle('Our bikes');

        $folder = $this->manager->getRepository('KunstmaanMediaBundle:Folder')->findOneBy(array('rel' => 'image'));
        $imgDir = dirname(__FILE__).'/../../../Resources/ui/img/demosite/';
        $menuMedia = $this->mediaCreator->createFile($imgDir.'stocks/stock1.jpg', $folder->getId());

        $translations = array();
        foreach ($this->requiredLocales as $locale) {
            $translations[] = array('language' => $locale, 'callback' => function($page, $translation, $seo) use ($locale, $menuMedia) {
                $translation->setTitle($locale == 'nl' ? 'Onze fietsen' : 'Our bikes');
                $translation->setSlug($locale == 'nl' ? 'fietsen' : 'bikes');
                $translation->setWeight(20);

                $page->setMenuDescription('Inleiding ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.');
                $page->setMenuImage($menuMedia);
            });
        }

        $options = array(
            'parent' => $servicesPage,
            'page_internal_name' => 'buy_bikes',
            'set_online' => true,
            'hidden_from_nav' => false,
            'creator' => self::ADMIN_USERNAME
        );

        $this->pageCreator->createPage($contentPage, $translations, $options);

        foreach ($this->requiredLocales as $locale) {
            // Add pageparts
            $pageparts = array();
            $pageparts['main'][] = $this->pagePartCreator->getCreatorArgumentsForPagePartAndProperties(
                '{{ namespace }}\Entity\PageParts\HeaderPagePart',
                array(
                    'setTitle' => $locale == 'nl' ? 'Onze fietsen' : 'Our bikes',
                    'setNiv' => 2
                )
            );
            $pageparts['main'][] = $this->pagePartCreator->getCreatorArgumentsForPagePartAndProperties(
                '{{ namespace }}\Entity\PageParts\TextPagePart',
                array(
                    'setContent' => '<p>Inleiding ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi.</p>'
                )
            );
            $pageparts['main'][] = $this->pagePartCreator->getCreatorArgumentsForPagePartAndProperties(
                '{{ namespace }}\Entity\PageParts\HeaderPagePart',
                array(
                    'setTitle' => $locale == 'nl' ? 'Prijslijst' : 'Pricelist',
                    'setNiv' => 3
                )
            );
            $pageparts['main'][] = $this->pagePartCreator->getCreatorArgumentsForPagePartAndProperties(
                '{{ namespace }}\Entity\PageParts\BikesListPagePart',
                array()
            );

            $this->pagePartCreator->addPagePartsToPage('buy_bikes', $pageparts, $locale);
        }

        // Repair bikes page
        $contentPage = new ContentPage();
        $contentPage->setTitle('Repair bikes');

        $menuMedia = $this->mediaCreator->createFile($imgDir.'stocks/stock2.jpg', $folder->getId());

        $translations = array();
        foreach ($this->requiredLocales as $locale) {
            $translations[] = array('language' => $locale, 'callback' => function($page, $translation, $seo) use ($locale, $menuMedia) {
                $translation->setTitle($locale == 'nl' ? 'Fietsen herstellen' : 'Repair bikes');
                $translation->setSlug($locale == 'nl' ? 'herstellen' : 'repair');
                $translation->setWeight(20);

                $page->setMenuDescription('Inleiding ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.');
                $page->setMenuImage($menuMedia);
            });
        }

        $options = array(
            'parent' => $servicesPage,
            'page_internal_name' => 'repair_bikes',
            'set_online' => true,
            'hidden_from_nav' => false,
            'creator' => self::ADMIN_USERNAME
        );

        $this->pageCreator->createPage($contentPage, $translations, $options);

        foreach ($this->requiredLocales as $locale) {
            // Add pageparts
            $pageparts = array();
            $pageparts['main'][] = $this->pagePartCreator->getCreatorArgumentsForPagePartAndProperties(
                '{{ namespace }}\Entity\PageParts\HeaderPagePart',
                array(
                    'setTitle' => $locale == 'nl' ? 'Fietsen herstellen' : 'Repair bikes',
                    'setNiv' => 2
                )
            );
            $pageparts['main'][] = $this->pagePartCreator->getCreatorArgumentsForPagePartAndProperties(
                '{{ namespace }}\Entity\PageParts\TextPagePart',
                array(
                    'setContent' => '<p>Inleiding ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi.</p>'
                )
            );

            $this->pagePartCreator->addPagePartsToPage('repair_bikes', $pageparts, $locale);
        }

        // Rent bikes page
        $contentPage = new ContentPage();
        $contentPage->setTitle('Rent bikes');

        $menuMedia = $this->mediaCreator->createFile($imgDir.'stocks/stock3.jpg', $folder->getId());

        $translations = array();
        foreach ($this->requiredLocales as $locale) {
            $translations[] = array('language' => $locale, 'callback' => function($page, $translation, $seo) use ($locale, $menuMedia) {
                $translation->setTitle($locale == 'nl' ? 'Fietsen verhuur' : 'Rent bikes');
                $translation->setSlug($locale == 'nl' ? 'verhuur' : 'rent');
                $translation->setWeight(20);

                $page->setMenuDescription('Inleiding ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.');
                $page->setMenuImage($menuMedia);
            });
        }

        $options = array(
            'parent' => $servicesPage,
            'page_internal_name' => 'rent_bikes',
            'set_online' => true,
            'hidden_from_nav' => false,
            'creator' => self::ADMIN_USERNAME
        );

        $this->pageCreator->createPage($contentPage, $translations, $options);

        foreach ($this->requiredLocales as $locale) {
            // Add pageparts
            $pageparts = array();
            $pageparts['main'][] = $this->pagePartCreator->getCreatorArgumentsForPagePartAndProperties(
                '{{ namespace }}\Entity\PageParts\HeaderPagePart',
                array(
                    'setTitle' => $locale == 'nl' ? 'Fietsen verhuur' : 'Rent bikes',
                    'setNiv' => 2
                )
            );
            $pageparts['main'][] = $this->pagePartCreator->getCreatorArgumentsForPagePartAndProperties(
                '{{ namespace }}\Entity\PageParts\TextPagePart',
                array(
                    'setContent' => '<p>Inleiding ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi.</p>'
                )
            );

            $this->pagePartCreator->addPagePartsToPage('rent_bikes', $pageparts, $locale);
        }

        // All pageparts page
        $contentPage = new ContentPage();
        $contentPage->setTitle('All pageparts');

        $translations = array();
        foreach ($this->requiredLocales as $locale) {
            $translations[] = array('language' => $locale, 'callback' => function($page, $translation, $seo) use ($locale, $menuMedia) {
                $translation->setTitle('All pageparts');
                $translation->setSlug('pageparts');
                $translation->setWeight(70);
            });
        }

        $options = array(
            'parent' => $homePage,
            'page_internal_name' => 'all_pageparts',
            'set_online' => true,
            'hidden_from_nav' => true,
            'creator' => self::ADMIN_USERNAME
        );

        $this->pageCreator->createPage($contentPage, $translations, $options);

        foreach ($this->requiredLocales as $locale) {
            $pageparts = array();

            // All pageparts listed below
            $pageparts['main'][] = $this->pagePartCreator->getCreatorArgumentsForPagePartAndProperties(
                '{{ namespace }}\Entity\PageParts\HeaderPagePart',
                array(
                    'setTitle' => 'Header h1',
                    'setNiv' => 1
                )
            );
            $pageparts['main'][] = $this->pagePartCreator->getCreatorArgumentsForPagePartAndProperties(
                '{{ namespace }}\Entity\PageParts\HeaderPagePart',
                array(
                    'setTitle' => 'Header h2',
                    'setNiv' => 2
                )
            );
            $pageparts['main'][] = $this->pagePartCreator->getCreatorArgumentsForPagePartAndProperties(
                '{{ namespace }}\Entity\PageParts\HeaderPagePart',
                array(
                    'setTitle' => 'Header h3',
                    'setNiv' => 3
                )
            );
            $pageparts['main'][] = $this->pagePartCreator->getCreatorArgumentsForPagePartAndProperties(
                '{{ namespace }}\Entity\PageParts\HeaderPagePart',
                array(
                    'setTitle' => 'Header h4',
                    'setNiv' => 4
                )
            );
            $pageparts['main'][] = $this->pagePartCreator->getCreatorArgumentsForPagePartAndProperties(
                '{{ namespace }}\Entity\PageParts\HeaderPagePart',
                array(
                    'setTitle' => 'Header h5',
                    'setNiv' => 5
                )
            );
            $pageparts['main'][] = $this->pagePartCreator->getCreatorArgumentsForPagePartAndProperties(
                '{{ namespace }}\Entity\PageParts\HeaderPagePart',
                array(
                    'setTitle' => 'Header h6',
                    'setNiv' => 6
                )
            );
            $pageparts['main'][] = $this->pagePartCreator->getCreatorArgumentsForPagePartAndProperties(
                '{{ namespace }}\Entity\PageParts\IntroTextPagePart',
                array(
                    'setcontent' => '<p>This is some intro text. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>'
                )
            );
            $pageparts['main'][] = $this->pagePartCreator->getCreatorArgumentsForPagePartAndProperties(
                '{{ namespace }}\Entity\PageParts\LinePagePart',
                array()
            );
            $pageparts['main'][] = $this->pagePartCreator->getCreatorArgumentsForPagePartAndProperties(
                '{{ namespace }}\Entity\PageParts\TextPagePart',
                array(
                    'setcontent' => '<p>This is a regular text pagepart. Lorem ipsum dolor sit amet, <a href="#">consectetur adipiscing</a> elit, sed do eiusmod tempor incididunt ut <strong>labore et dolore</strong> magna aliqua.</p>'
                )
            );
            $pageparts['main'][] = $this->pagePartCreator->getCreatorArgumentsForPagePartAndProperties(
                '{{ namespace }}\Entity\PageParts\TocPagePart',
                array()
            );
            $pageparts['main'][] = $this->pagePartCreator->getCreatorArgumentsForPagePartAndProperties(
                '{{ namespace }}\Entity\PageParts\ToTopPagePart',
                array()
            );
            $media = $this->mediaCreator->createFile($imgDir.'stocks/fixie1.png', $folder->getId());
            $pageparts['main'][] = $this->pagePartCreator->getCreatorArgumentsForPagePartAndProperties(
                '{{ namespace }}\Entity\PageParts\ImagePagePart',
                array(
                    'setMedia' => $media // TODO: add caption
                )
            );
            $pageparts['main'][] = $this->pagePartCreator->getCreatorArgumentsForPagePartAndProperties(
                '{{ namespace }}\Entity\PageParts\LinkPagePart',
                array(
                    'setUrl' => '/',
                    'setText' => 'Link text'
                )
            );
            $pageparts['main'][] = $this->pagePartCreator->getCreatorArgumentsForPagePartAndProperties(
                '{{ namespace }}\Entity\PageParts\ButtonPagePart',
                array(
                    'setLinkUrl' => '/',
                    'setLinkText' => 'Link text',
                    'setType' => 'primary',
                    'setSize' => 'default',
                    'setPosition' => 'left'
                )
            );
            $pageparts['main'][] = $this->pagePartCreator->getCreatorArgumentsForPagePartAndProperties(
                '{{ namespace }}\Entity\PageParts\ButtonPagePart',
                array(
                    'setLinkUrl' => '/',
                    'setLinkText' => 'Link text',
                    'setType' => 'secondary',
                    'setSize' => 'xl',
                    'setPosition' => 'center'
                )
            );
            $pageparts['main'][] = $this->pagePartCreator->getCreatorArgumentsForPagePartAndProperties(
                '{{ namespace }}\Entity\PageParts\ButtonPagePart',
                array(
                    'setLinkUrl' => '/',
                    'setLinkText' => 'Link text',
                    'setType' => 'tertiary',
                    'setSize' => 'lg',
                    'setPosition' => 'right'
                )
            );
            $pageparts['main'][] = $this->pagePartCreator->getCreatorArgumentsForPagePartAndProperties(
                '{{ namespace }}\Entity\PageParts\ButtonPagePart',
                array(
                    'setLinkUrl' => '/',
                    'setLinkText' => 'Link text',
                    'setType' => 'quaternary',
                    'setSize' => 'sm',
                    'setPosition' => 'block'
                )
            );
            $pageparts['main'][] = $this->pagePartCreator->getCreatorArgumentsForPagePartAndProperties(
                '{{ namespace }}\Entity\PageParts\ButtonPagePart',
                array(
                    'setLinkUrl' => '/',
                    'setLinkText' => 'Link text',
                    'setType' => 'primary',
                    'setSize' => 'xs',
                    'setPosition' => 'center'
                )
            );

            $this->pagePartCreator->addPagePartsToPage('all_pageparts', $pageparts, $locale);
        }
{% endif %}
    }

{% if demosite %}
    /**
     * Create a ContentPages based on an admin list
     */
    private function createAdminListPages()
    {
        $list = array(
            array(Bike::TYPE_CITY_BIKE, 'Gazelle', 'CityZen C7', 1020),
            array(Bike::TYPE_RACING_BIKE, 'Eddy Merckx', 'EMX-525', 2300),
            array(Bike::TYPE_RACING_BIKE, 'Specialized', 'S-WORKS TARMAC DURA-ACE', 2100),
            array(Bike::TYPE_MOUNTAIN_BIKE, 'BMC', 'Teamelite TE01 29', 1600),
            array(Bike::TYPE_MOUNTAIN_BIKE, 'Trek', 'Superfly', 1450),
        );
        foreach ($list as $info) {
            $bike = new Bike();
            $bike->setType($info[0]);
            $bike->setBrand($info[1]);
            $bike->setModel($info[2]);
            $bike->setPrice($info[3]);

            $this->manager->persist($bike);
        }

        $this->manager->flush();
    }

    /**
     * Create a FormPage
     */
    private function createFormPage()
    {
        $nodeRepo = $this->manager->getRepository('KunstmaanNodeBundle:Node');
        $homePage = $nodeRepo->findOneBy(array('internalName' => 'homepage'));

        $formPage = new FormPage();
        $formPage->setTitle('Contact form');

        $translations = array();
        foreach ($this->requiredLocales as $locale) {
            $translations[] = array(
                'language' => $locale,
                'callback' => function ($page, $translation, $seo) use ($locale) {
                    $translation->setTitle('Contact');
                    $translation->setSlug('contact');
                    $translation->setWeight(60);

                    $page->setThanks($locale == 'nl' ? '<p>Bedankt, we hebben je bericht succesvol ontvangen.</p>' : '<p>We have received your submission.</p>');
                }
            );
        }

        $options = array(
            'parent' => $homePage,
            'page_internal_name' => 'contact',
            'set_online' => true,
            'hidden_from_nav' => false,
            'creator' => self::ADMIN_USERNAME
        );

        $this->pageCreator->createPage($formPage, $translations, $options);

        foreach ($this->requiredLocales as $locale) {
            $pageparts = array();
            $pageparts['main'][] = $this->pagePartCreator->getCreatorArgumentsForPagePartAndProperties(
                'Kunstmaan\FormBundle\Entity\PageParts\SingleLineTextPagePart',
                array(
                    'setLabel' => $locale == 'nl' ? 'Naam' : 'Name',
                    'setRequired' => true,
                    'setErrorMessageRequired' => $locale == 'nl' ? 'Naam is verplicht' :'Name is required'
                )
            );
            $pageparts['main'][] = $this->pagePartCreator->getCreatorArgumentsForPagePartAndProperties(
                'Kunstmaan\FormBundle\Entity\PageParts\EmailPagePart',
                array(
                    'setLabel' => 'E-mail',
                    'setRequired' => true,
                    'setErrorMessageRequired' => $locale == 'nl' ? 'Email is verplicht' :'E-mail is required',
                    'setErrorMessageInvalid' => $locale == 'nl' ? 'Vul een geldig e-mail adres in' :'Fill in a valid e-mail address'
                )
            );
            $pageparts['main'][] = $this->pagePartCreator->getCreatorArgumentsForPagePartAndProperties(
                'Kunstmaan\FormBundle\Entity\PageParts\ChoicePagePart',
                array(
                    'setLabel' => $locale == 'nl' ? 'Onderwerp' :'Subject',
                    'setRequired' => true,
                    'setErrorMessageRequired' => $locale == 'nl' ? 'Onderwerp is verplicht' :'Subject is required',
                    'setChoices' => $locale == 'nl' ?
                        "Ik wil een website maken met de Kunstmaan bundles \n Ik ben een website aan het testen \n Ik wil dat Kunstmaan een website voor mij maakt" :
                        "I want to make a website with the Kunstmaan bundles \n I'm testing the website \n I want to get a quote for a website built by Kunstmaan"
                )
            );
            $pageparts['main'][] = $this->pagePartCreator->getCreatorArgumentsForPagePartAndProperties(
                'Kunstmaan\FormBundle\Entity\PageParts\MultiLineTextPagePart',
                array(
                    'setLabel' => $locale == 'nl' ? 'Bericht' : 'Message',
                    'setRequired' => true,
                    'setErrorMessageRequired' => $locale == 'nl' ? 'Bericht is verplicht' : 'Message is required'
                )
            );
            $pageparts['main'][] = $this->pagePartCreator->getCreatorArgumentsForPagePartAndProperties(
                'Kunstmaan\FormBundle\Entity\PageParts\SubmitButtonPagePart',
                array(
                    'setLabel' => $locale == 'nl' ? 'Verzenden' : 'Send'
                )
            );

            $this->pagePartCreator->addPagePartsToPage('contact', $pageparts, $locale);
        }

        $this->manager->flush();
    }

    /**
     * Create a FormPage
     */
    private function createSearchPage()
    {
        $nodeRepo = $this->manager->getRepository('KunstmaanNodeBundle:Node');
        $homePage = $nodeRepo->findOneBy(array('internalName' => 'homepage'));

        $searchPage = new SearchPage();
        $searchPage->setTitle('Search');

        $translations = array();
        foreach ($this->requiredLocales as $locale) {
            $translations[] = array(
                'language' => $locale,
                'callback' => function ($page, $translation, $seo) use ($locale) {
                    $translation->setTitle($locale == 'nl' ? 'Zoeken' : 'Search');
                    $translation->setSlug($locale == 'nl' ? 'zoeken' : 'search');
                    $translation->setWeight(50);
                }
            );
        }

        $options = array(
            'parent' => $homePage,
            'page_internal_name' => 'search',
            'set_online' => true,
            'hidden_from_nav' => true,
            'creator' => self::ADMIN_USERNAME
        );

        $this->pageCreator->createPage($searchPage, $translations, $options);

        $this->manager->flush();
    }
{% endif %}

    /**
     * Insert all translations
     */
    private function createTranslations()
    {
        $trans = array();
{% if demosite %}
        $trans['bike.type']['en'] = 'Type';
        $trans['bike.type']['nl'] = 'Type';
        $trans['bike.brand']['en'] = 'Brand';
        $trans['bike.brand']['nl'] = 'Merk';
        $trans['bike.model']['en'] = 'Model';
        $trans['bike.model']['nl'] = 'Model';
        $trans['bike.price']['en'] = 'Price';
        $trans['bike.price']['nl'] = 'Prijs';

        $trans['bike.city_bike']['en'] = 'City bike';
        $trans['bike.city_bike']['nl'] = 'Stadsfiets';
        $trans['bike.mountain_bike']['en'] = 'Mountain bike';
        $trans['bike.mountain_bike']['nl'] = 'Mountainbike';
        $trans['bike.racing_bike']['en'] = 'Racing bike';
        $trans['bike.racing_bike']['nl'] = 'Koersfiets';

        $trans['pagerfanta.prev']['en'] = 'Previous';
        $trans['pagerfanta.prev']['nl'] = 'Vorige';
        $trans['pagerfanta.next']['en'] = 'Next';
        $trans['pagerfanta.next']['nl'] = 'Volgende';

        $trans['top']['en'] = 'Top';
        $trans['top']['nl'] = 'Top';

        $trans['article.readmore']['en'] = 'Read more';
        $trans['article.readmore']['nl'] = 'Lees meer';

        $trans['search.results']['en'] = 'Results';
        $trans['search.results']['nl'] = 'Resultaten';
        $trans['search.result']['en'] = 'Result';
        $trans['search.result']['nl'] = 'Resultaat';
        $trans['search.for']['en'] = 'for';
        $trans['search.for']['nl'] = 'voor';
        $trans['search.search']['en'] = 'search';
        $trans['search.search']['nl'] = 'zoeken';
        $trans['search.no_results']['en'] = 'No results found';
        $trans['search.no_results']['nl'] = 'Geen resultaten gevonden';

        $trans['search.filter']['en'] = 'Filter';
        $trans['search.filter']['nl'] = 'Filter';
{% endif %}

        $translationId = $this->manager->getRepository('KunstmaanTranslatorBundle:Translation')->getUniqueTranslationId();
        foreach ($trans as $key => $array) {
            foreach ($array as $lang => $value) {
                $t = new Translation();
                $t->setKeyword($key);
                $t->setLocale($lang);
                $t->setText($value);
                $t->setDomain('messages');
                $t->setCreatedAt(new \DateTime());
                $t->setFlag(Translation::FLAG_NEW);
                $t->setTranslationId($translationId);

                $this->manager->persist($t);
            }
            $translationId++;
        }

        $this->manager->flush();
    }

    /**
     * Create some dummy media files
     */
    private function createMedia()
    {
        // Add images to database
        $imageFolder = $this->manager->getRepository('KunstmaanMediaBundle:Folder')->findOneBy(array('rel' => 'image'));
        $filesFolder = $this->manager->getRepository('KunstmaanMediaBundle:Folder')->findOneBy(array('rel' => 'files'));
        $publicDir = dirname(__FILE__).'/../../../Resources/ui/';
        $this->mediaCreator->createFile($publicDir.'img/general/logo-kunstmaan.svg', $imageFolder->getId());
        $this->mediaCreator->createFile($publicDir.'img/demosite/logo-thecrew.svg', $imageFolder->getId());
        $this->mediaCreator->createFile($publicDir.'files/dummy/sample.pdf', $filesFolder->getId());

        // Create dummy video folder and add dummy videos
        $videoFolder = $this->manager->getRepository('KunstmaanMediaBundle:Folder')->findOneBy(array('rel' => 'video'));
        $this->createVideoFile('Kunstmaan', 'WPx-Oe2WrUE', $videoFolder);
    }

    /**
     * Create a video file record in the database.
     *
     * @param $name
     * @param $code
     * @param $folder
     * @return Media
     */
    private function createVideoFile($name, $code, $folder)
    {
        // Hack for media bundle issue
        $dir = dirname($this->container->get('kernel')->getRootDir());
        chdir($dir . '/web');
        $media = new Media();
        $media->setFolder($folder);
        $media->setName($name);
        $helper = new RemoteVideoHelper($media);
        $helper->setCode($code);
        $helper->setType('youtube');
        $this->manager->getRepository('KunstmaanMediaBundle:Media')->save($media);
        chdir($dir);

        return $media;
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
