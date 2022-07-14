<?php

namespace {{ namespace }}\DataFixtures\ORM\DefaultSiteGenerator;

use {{ namespace }}\Entity\Pages\HomePage;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Kunstmaan\AdminBundle\Entity\DashboardConfiguration;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Helper\Services\PageCreatorService;
use Kunstmaan\TranslatorBundle\Entity\Translation;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DefaultSiteFixtures extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * Username that is used for creating pages.
     */
    private const ADMIN_USERNAME = 'admin';

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var ObjectManager
     */
    private $manager;

    /**
     * @var PageCreatorService
     */
    private $pageCreator;

    /**
     * Defined locales during generation.
     */
    private $requiredLocales;

    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;

        $this->pageCreator = $this->container->get('kunstmaan_node.page_creator_service');
        $this->requiredLocales = explode('|', $this->container->getParameter('requiredlocales'));

        $this->createTranslations(); //TODO: check if we should insert translation on another way and if needed still

        $this->createHomePage();
//        $this->createDashboard(); //TODO check if still needed
    }

    /**
     * Create the dashboard.
     */
    private function createDashboard()
    {
        //TODO: should we do this?
        /** @var $dashboard DashboardConfiguration */
        $dashboard = $this->manager->getRepository('KunstmaanAdminBundle:DashboardConfiguration')->findOneBy([]);
        if (null === $dashboard) {
            $dashboard = new DashboardConfiguration();
        }
        $dashboard->setTitle('Dashboard');
        $dashboard->setContent('<div class="alert alert-info"><strong>Important: </strong>please change these items to the graphs of your own site!</div><iframe src="https://rpm.newrelic.com/public/charts/jjPIEE7OHz9" width="100%" height="300" scrolling="no" frameborder="no"></iframe><iframe src="https://rpm.newrelic.com/public/charts/hmDWR0eUNTo" width="100%" height="300" scrolling="no" frameborder="no"></iframe><iframe src="https://rpm.newrelic.com/public/charts/fv7IP1EmbVi" width="100%" height="300" scrolling="no" frameborder="no"></iframe>');
        $this->manager->persist($dashboard);
        $this->manager->flush();
    }

    private function createHomePage()
    {
        $homePage = new HomePage();
        $homePage->setTitle('Home');

        $translations = [];
        foreach ($this->requiredLocales as $locale) {
            $translations[] = [
                'language' => $locale,
                'callback' => static function ($page, NodeTranslation $translation, $seo) {
                    $translation->setTitle('Home');
                    $translation->setSlug('');
                },
            ];
        }

        $options = [
            'parent' => null,
            'page_internal_name' => 'homepage',
            'set_online' => true,
            'hidden_from_nav' => false,
            'creator' => self::ADMIN_USERNAME,
        ];

        $this->pageCreator->createPage($homePage, $translations, $options);
    }

    /**
     * Insert all translations.
     */
    private function createTranslations()
    {
        //TODO: still needed?
        $trans = [];

        $trans['warning.outdated.title']['en'] = 'You are using an outdated browser.';
        $trans['warning.outdated.title']['nl'] = 'Uw browser is verouderd.';
        $trans['warning.outdated.title']['fr'] = 'Vous utilisez un navigateur internet dépassé.';
        $trans['warning.outdated.title']['de'] = 'Ihr Browser ist veraltet.';

        $trans['warning.outdated.subtitle']['en'] = 'Some page content will be lost or rendered incorrectly.';
        $trans['warning.outdated.subtitle']['nl'] = 'Sommige inhoud kan verloren gaan of zal niet correct weergegeven worden.';
        $trans['warning.outdated.subtitle']['fr'] = "Certain contenu pourrait être perdu ou ne pas s'afficher correctement";
        $trans['warning.outdated.subtitle']['de'] = 'Einige Inhalte können verloren gehen oder nicht richtig angezeigt werden.';

        $trans['warning.outdated.description']['en'] = 'Please install a more recent version of your browser.';
        $trans['warning.outdated.description']['nl'] = 'Gelieve een meer recente versie van uw browser te installeren.';
        $trans['warning.outdated.description']['fr'] = 'Nous vous conseillons de mettre votre navigateur à jour.';
        $trans['warning.outdated.description']['de'] = 'Bitte aktualisieren Sie Ihren Browser auf eine neuere Version.';

        $trans['warning.outdated.upgrade_browser']['en'] = 'Upgrade your browser';
        $trans['warning.outdated.upgrade_browser']['nl'] = 'Upgrade uw browser';
        $trans['warning.outdated.upgrade_browser']['fr'] = 'Mettez votre navigateur à jour';
        $trans['warning.outdated.upgrade_browser']['de'] = 'Aktualisieren Sie Ihren Browser';

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
            ++$translationId;
        }

        $this->manager->flush();
    }

    public function getOrder(): int
    {
        return 51;
    }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}
