<?php

namespace kumaBundles\WebsiteBundle\DataFixtures\ORM\DefaultSiteGenerator;

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
use kumaBundles\WebsiteBundle\Entity\Pages\ContentPage;
use kumaBundles\WebsiteBundle\Entity\Pages\HomePage;

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

    }


    /**
     * Insert all translations
     */
    private function createTranslations()
    {
	$trans = array();

	$trans['warning.outdated.title']['en'] = 'You are using an outdated browser.';
	$trans['warning.outdated.title']['nl'] = 'Uw browser is verouderd.';
	$trans['warning.outdated.title']['fr'] = 'Vous utilisez un navigateur internet dépassé.';
	$trans['warning.outdated.title']['de'] = 'Ihr Browser ist veraltet.';

	$trans['warning.outdated.subtitle']['en'] = 'Some page content will be lost or rendered incorrectly.';
	$trans['warning.outdated.subtitle']['nl'] = 'Sommige inhoud kan verloren gaan of zal niet correct weergegeven worden.';
	$trans['warning.outdated.subtitle']['fr'] = "Certain contenu pourrait être perdu ou ne pas s'afficher correctement";
	$trans['warning.outdated.subtitle']['de'] = "Einige Inhalte können verloren gehen oder nicht richtig angezeigt werden.";

	$trans['warning.outdated.description']['en'] = 'Please install a more recent version of your browser.';
	$trans['warning.outdated.description']['nl'] = 'Gelieve een meer recente versie van uw browser te installeren.';
	$trans['warning.outdated.description']['fr'] = 'Nous vous conseillons de mettre votre navigateur à jour.';
	$trans['warning.outdated.description']['de'] = 'Bitte aktualisieren Sie Ihren Browser auf eine neuere Version.';

	$trans['warning.outdated.upgrade_browser']['en'] = 'Upgrade your browser';
	$trans['warning.outdated.upgrade_browser']['nl'] = 'Upgrade uw browser';
	$trans['warning.outdated.upgrade_browser']['fr'] = 'Mettez votre navigateur à jour';
	$trans['warning.outdated.upgrade_browser']['de'] = 'Aktualisieren Sie Ihren Browser';

	$trans['cookieconsent.description']['en'] = 'This website uses cookies to enhance your browsing experience. <a href="#">More information</a>.';
	$trans['cookieconsent.description']['nl'] = 'Deze website gebruikt cookies om uw surfervaring makkelijker te maken. <a href="#">Meer informatie</a>.';
	$trans['cookieconsent.description']['fr'] = 'Ce site web utilise des cookies pour faciliter votre navigation. <a href="#">Plus d\'info</a>.';
	$trans['cookieconsent.description']['de'] = 'Diese Website verwendet Cookies, um Ihren Besuch effizienter zu machen. <a href="#">Weitere angaben</a>';

	$trans['cookieconsent.confirm']['en'] = 'Proceed';
	$trans['cookieconsent.confirm']['nl'] = 'Doorgaan';
	$trans['cookieconsent.confirm']['fr'] = 'Continuer';
	$trans['cookieconsent.confirm']['de'] = 'Weitergehen';

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
