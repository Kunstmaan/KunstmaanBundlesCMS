<?php

namespace {{ namespace }}\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Acl\Exception\AclNotFoundException;
use Symfony\Component\Security\Acl\Model\MutableAclProviderInterface;
use Symfony\Component\Security\Acl\Model\ObjectIdentityRetrievalStrategyInterface;

use Kunstmaan\AdminBundle\Entity\DashboardConfiguration;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\MaskBuilder;
use Kunstmaan\FormBundle\Entity\PageParts\CheckboxPagePart;
use Kunstmaan\FormBundle\Entity\PageParts\ChoicePagePart;
use Kunstmaan\FormBundle\Entity\PageParts\EmailPagePart;
use Kunstmaan\FormBundle\Entity\PageParts\MultiLineTextPagePart;
use Kunstmaan\FormBundle\Entity\PageParts\SingleLineTextPagePart;
use Kunstmaan\FormBundle\Entity\PageParts\SubmitButtonPagePart;
use Kunstmaan\MediaBundle\Entity\Folder;
use Kunstmaan\MediaBundle\Entity\Media;
use Kunstmaan\MediaBundle\Helper\File\FileHelper;
use Kunstmaan\MediaBundle\Helper\RemoteVideo\RemoteVideoHelper;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Entity\PageInterface;
use Kunstmaan\TranslatorBundle\Entity\Translation;

use {{ namespace }}\Entity\Pages\ContentPage;
use {{ namespace }}\Entity\Pages\HomePage;

/**
 * DefaultSiteFixtures
 */
class DefaultSiteFixtures extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * @var \Kunstmaan\MediaBundle\Entity\Media
     */
    private $image = null;

    /**
     * @var \Kunstmaan\MediaBundle\Entity\Media
     */
    private $file = null;

    /**
     * @var \Kunstmaan\MediaBundle\Entity\Media
     */
    private $video = null;

    /**
     * @var UserInterface
     */
    private $adminuser = null;

    /**
     * @var ContainerInterface
     */
    private $container = null;

    /**
     * @var string
     */
    private $rootDir;

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

        $this->rootDir = $this->container->get('kernel')->getRootDir();

        // Translations
        $this->createTranslations($manager);

        // Media
        $this->createMedia($manager);

        // Homepage
        $homePage = $this->createHomePage($manager);

        // ContentPages
        $this->createContentPages($manager, $homePage);

        // Styles
        $this->createStylePage($manager, $homePage);


        // From PageParts
        $this->createFormPage($manager, "Form PageParts", $homePage);

        // Dashboard
        /** @var $dashboard DashboardConfiguration */
        $dashboard = $manager->getRepository("KunstmaanAdminBundle:DashboardConfiguration")->findOneBy(array());
        if (is_null($dashboard)) {
            $dashboard = new DashboardConfiguration();
        }
        $dashboard->setTitle("Dashboard");
        $dashboard->setContent('<div class="alert alert-info"><strong>Important: </strong>please change these items to the graphs of your own site!</div><iframe src="https://rpm.newrelic.com/public/charts/2h1YQ3W7j7Z" width="100%" height="300" scrolling="no" frameborder="no"></iframe><iframe src="https://rpm.newrelic.com/public/charts/1VNlg8JA5ed" width="100%" height="300" scrolling="no" frameborder="no"></iframe><iframe src="https://rpm.newrelic.com/public/charts/36A9KcMTMli" width="100%" height="300" scrolling="no" frameborder="no"></iframe>');
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

        $securityIdentity = new RoleSecurityIdentity('IS_AUTHENTICATED_ANONYMOUSLY');
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
     * @param ObjectManager $manager      The object manager
     * @param string        $class        The class
     * @param string        $title        The title
     * @param PageInterface $parent       The parent
     * @param string        $slug         The slug
     * @param string        $internalName The internal name
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
     * @return HomePage
     */
    private function createHomePage(ObjectManager $manager)
    {
        $pageCreator = $this->container->get('kunstmaan_node.page_creator_service');
        $pageCreator->setContainer($this->container);

        $homePage = new HomePage();
        $homePage->setTitle('Home');

        $translations = array();
        $translations[] = array('language' => 'en', 'callback' => function($page, $translation, $seo) {
            $translation->setTitle('Home');
            $translation->setSlug('');
        });
        $translations[] = array('language' => 'nl', 'callback' => function($page, $translation, $seo) {
            $translation->setTitle('Home');
            $translation->setSlug('');
        });

        $options = array(
            'parent' => null,
            'page_internal_name' => 'homepage',
            'set_online' => true,
            'hidden_from_nav' => true,
            'creator' => 'Admin'
        );

        $pageCreator->createPage($homePage, $translations, $options);

        $ppCreatorService = $this->container->get('kunstmaan_pageparts.pagepart_creator_service');

        $pageparts = array();
        $pageparts['left_column'][] = $ppCreatorService->getCreatorArgumentsForPagePartAndProperties('Kunstmaan\PagePartBundle\Entity\HeaderPagePart',
            array(
                'setTitle' => 'First column heading',
                'setNiv'   => 1
            )
        );
        $pageparts['left_column'][] = $ppCreatorService->getCreatorArgumentsForPagePartAndProperties('Kunstmaan\PagePartBundle\Entity\TextPagePart',
            array(
                'setContent' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.'
            )
        );
        $pageparts['middle_column'][] = $ppCreatorService->getCreatorArgumentsForPagePartAndProperties('Kunstmaan\PagePartBundle\Entity\HeaderPagePart',
            array(
                'setTitle' => 'Second column heading',
                'setNiv'   => 1
            )
        );
        $pageparts['middle_column'][] = $ppCreatorService->getCreatorArgumentsForPagePartAndProperties('Kunstmaan\PagePartBundle\Entity\TextPagePart',
            array(
                'setContent' => 'There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don\'t look even slightly believable.'
            )
        );
        $pageparts['right_column'][] = $ppCreatorService->getCreatorArgumentsForPagePartAndProperties('Kunstmaan\PagePartBundle\Entity\HeaderPagePart',
            array(
                'setTitle' => 'Third column heading',
                'setNiv'   => 1
            )
        );
        $pageparts['right_column'][] = $ppCreatorService->getCreatorArgumentsForPagePartAndProperties('Kunstmaan\PagePartBundle\Entity\TextPagePart',
            array(
                'setContent' => 'The standard chunk of Lorem Ipsum used since the 1500s is reproduced below for those interested. Sections 1.10.32 and 1.10.33 from "de Finibus Bonorum et Malorum" by Cicero are also reproduced in their exact original form, accompanied by English versions from the 1914 translation by H. Rackham.'
            )
        );

        $ppCreatorService->addPagePartsToPage('homepage', $pageparts, 'en');

        $pageparts = array();
        $pageparts['left_column'][] = $ppCreatorService->getCreatorArgumentsForPagePartAndProperties('Kunstmaan\PagePartBundle\Entity\HeaderPagePart',
            array(
                'setTitle' => 'Eerste title',
                'setNiv'   => 1
            )
        );
        $pageparts['left_column'][] = $ppCreatorService->getCreatorArgumentsForPagePartAndProperties('Kunstmaan\PagePartBundle\Entity\TextPagePart',
            array(
                'setContent' => 'Lorem Ipsum is slechts een proeftekst uit het drukkerij- en zetterijwezen. Lorem Ipsum is de standaard proeftekst in deze bedrijfstak sinds de 16e eeuw, toen een onbekende drukker een zethaak met letters nam en ze door elkaar husselde om een font-catalogus te maken.'
            )
        );
        $pageparts['middle_column'][] = $ppCreatorService->getCreatorArgumentsForPagePartAndProperties('Kunstmaan\PagePartBundle\Entity\HeaderPagePart',
            array(
                'setTitle' => 'Tweede title',
                'setNiv'   => 1
            )
        );
        $pageparts['middle_column'][] = $ppCreatorService->getCreatorArgumentsForPagePartAndProperties('Kunstmaan\PagePartBundle\Entity\TextPagePart',
            array(
                'setContent' => 'Er zijn vele variaties van passages van Lorem Ipsum beschikbaar maar het merendeel heeft te lijden gehad van wijzigingen in een of andere vorm, door ingevoegde humor of willekeurig gekozen woorden die nog niet half geloofwaardig ogen.'
            )
        );
        $pageparts['right_column'][] = $ppCreatorService->getCreatorArgumentsForPagePartAndProperties('Kunstmaan\PagePartBundle\Entity\HeaderPagePart',
            array(
                'setTitle' => 'Derde titel',
                'setNiv'   => 1
            )
        );
        $pageparts['right_column'][] = $ppCreatorService->getCreatorArgumentsForPagePartAndProperties('Kunstmaan\PagePartBundle\Entity\TextPagePart',
            array(
                'setContent' => 'Het standaard stuk van Lorum Ipsum wat sinds de 16e eeuw wordt gebruikt is hieronder, voor wie er interesse in heeft, weergegeven. Secties 1.10.32 en 1.10.33 van "de Finibus Bonorum et Malorum" door Cicero zijn ook weergegeven in hun exacte originele vorm, vergezeld van engelse versies van de 1914 vertaling door H. Rackham.'
            )
        );

        $ppCreatorService->addPagePartsToPage('homepage', $pageparts, 'nl');

        return $homePage;
    }

    /**
     * Create a ContentPages
     *
     * @param ObjectManager $manager The object manager
     * @param PageInterface $parent  The parent
     */
    private function createContentPages(ObjectManager $manager, $parent)
    {
        $pageCreator = $this->container->get('kunstmaan_node.page_creator_service');
        $pageCreator->setContainer($this->container);

        $nodeRepo = $manager->getRepository('KunstmaanNodeBundle:Node');
        $homePage = $nodeRepo->findOneBy(array('internalName' => 'homepage'));

        $contentPage = new ContentPage();
        $contentPage->setTitle('Home');

        $translations = array();
        $translations[] = array('language' => 'en', 'callback' => function($page, $translation, $seo) {
            $translation->setTitle('Satellite');
            $translation->setSlug('satellite');
        });
        $translations[] = array('language' => 'nl', 'callback' => function($page, $translation, $seo) {
            $translation->setTitle('Satelliet');
            $translation->setSlug('satelliet');
        });

        $options = array(
            'parent' => $homePage,
            'page_internal_name' => 'satellite',
            'set_online' => true,
            'hidden_from_nav' => false,
            'creator' => 'Admin'
        );

        $pageCreator->createPage($contentPage, $translations, $options);

        $ppCreatorService = $this->container->get('kunstmaan_pageparts.pagepart_creator_service');

        $pageparts = array();
        $pageparts['main'][] = $ppCreatorService->getCreatorArgumentsForPagePartAndProperties('Kunstmaan\PagePartBundle\Entity\HeaderPagePart',
            array(
                'setTitle' => 'Satellite (artificial)',
                'setNiv'   => 1
            )
        );
        $pageparts['main'][] = $ppCreatorService->getCreatorArgumentsForPagePartAndProperties('Kunstmaan\PagePartBundle\Entity\TextPagePart',
            array(
                'setContent' => '<p>A <b>satellite</b> is an object that orbits another object. In space, satellites may be made by man, or they may be natural. The moon is a natural satellite that orbits the Earth. Most man-made satellites also orbit the Earth, but some orbit other planets, such as Saturn, Venus or Mars, or the moon.</p>'
            )
        );
        $pageparts['main'][] = $ppCreatorService->getCreatorArgumentsForPagePartAndProperties('Kunstmaan\PagePartBundle\Entity\HeaderPagePart',
            array(
                'setTitle' => 'History',
                'setNiv'   => 2
            )
        );
        $pageparts['main'][] = $ppCreatorService->getCreatorArgumentsForPagePartAndProperties('Kunstmaan\PagePartBundle\Entity\TextPagePart',
            array(
                'setContent' => "<p>The idea of a man-made satellite has been around for a long time. When Isaac Newton was thinking about gravity, he came up with the thought experiment called Newton's cannonball. He wondered what would happen if a cannonball was shot from a tall mountain. If fired at just the right speed (and ignoring the friction of air), he realized it would orbit the Earth. Later, Jules Verne wrote about a satellite in 1879 in a book called Begum's Fortune.</p>
                                 <p>In 1903, Konstantin Tsiolkovsky wrote Means of Reaction Devices (in Russian: Исследование мировых пространств реактивными приборами), which was the first serious study on how to use rockets to launch spacecraft. He calculated the speed needed to reach orbit around the Earth (at 8 km/s). He also wrote that a multi-stage rocket, using liquid fuel could reach that speed. He recommended liquid hydrogen and liquid oxygen, though other fuels could be used. He was correct on all of these points.</p>
                                 <p>The English science fiction writer Arthur C. Clarke is given the credit of coming up with the idea of the communication satellite in 1945. He described in detail the possible use of satellites for mass communication, how to launch satellites, what orbits they could use, and the benefits of having a network of world-circling satellites.</p>
                                 <p>The world's first artificial satellite, the Sputnik 1, was launched by the Soviet Union on October 4, 1957. This surprised the world, and the United States quickly worked to launch their own satellite, starting the space race. Sputnik 2 was launched on November 3, 1957 and carried the first living passenger into orbit, a dog named Laika. The United States launched their first satellite, called Explorer 1 on January 31, 1958. The UK launched its first satellite in 1962.</p>
                                 <p>Since then, thousands of satellites have been launched into orbit around the Earth. Some satellites, notably space stations, have been launched in parts and assembled in orbit.</p>"
            )
        );
        $pageparts['main'][] = $ppCreatorService->getCreatorArgumentsForPagePartAndProperties('Kunstmaan\PagePartBundle\Entity\HeaderPagePart',
            array(
                'setTitle' => 'Satellites orbiting now',
                'setNiv'   => 2
            )
        );
        $pageparts['main'][] = $ppCreatorService->getCreatorArgumentsForPagePartAndProperties('Kunstmaan\PagePartBundle\Entity\TextPagePart',
            array(
                'setContent' => "<p>Artificial satellites come from more than 50 countries and have used the satellite launching capabilities of ten nations. A few hundred satellites are currently working, but thousands of unused satellites and satellite fragments orbit the Earth as space debris. The largest satellite is the International Space Station, which was put together in space from sections made by several different countries (including the organizations of NASA, ESA, JAXA and RKA). It usually has a crew of six astronauts or cosmonauts living on board. It is permanently occupied, but the crews change. The Hubble Space Telescope has been repaired and updated by astronauts in space several times.</p>
                                 <p>There are also man-made satellites orbiting something other than the Earth. The Mars Reconnaissance Orbiter is orbiting Mars. Cassini-Huygens is orbiting Saturn. Venus Express, run by the ESA, is orbiting Venus. Two GRAIL satellites orbited the moon until December 2012. There are plans to launch a satellite in 2017 called the Solar Orbiter (SolO) that will orbit the sun.</p>
                                 <p>Man-made satellites have several main uses:</p>
                                 <ul>
                                 <li>Scientific Investigation</li>
                                 <li>Earth Observation - including weather forecasting and tracking storms and pollution</li>
                                 <li>Communications - including satellite television and telephone calls</li>
                                 <li>Navigation - including Global Positioning System (GPS)</li>
                                 <li>Military - including spy photography and communications (nuclear weapons are not allowed in space)</li>
                                 </ul>"
            )
        );
        $pageparts['main'][] = $ppCreatorService->getCreatorArgumentsForPagePartAndProperties('Kunstmaan\PagePartBundle\Entity\HeaderPagePart',
            array(
                'setTitle' => 'Orbits',
                'setNiv'   => 2
            )
        );
        $pageparts['main'][] = $ppCreatorService->getCreatorArgumentsForPagePartAndProperties('Kunstmaan\PagePartBundle\Entity\TextPagePart',
            array(
                'setContent' => "<p>Most of the man-made satellites are in a low Earth orbit (LEO) or a geostationary orbit. To stay in orbit, the satellite's sideways speed must balance the force of gravity. Closer to the Earth, in LEO, the satellites must move faster to stay in orbit. Low orbits work well for satellites that take pictures of the Earth. It is easier to put a satellite in low Earth orbit, but the satellite appears to move when viewed from Earth. This means a satellite dish (a type of antenna) must be always moving in order to send or receive communications with that satellite. This works well for GPS satellites - receivers on Earth use the satellite's changing position and precise time (and a type of antenna that does not have to be pointed) to find where on Earth the receiver is. But constantly changing positions does not work for satellite TV and other types of satellites that send and receive a lot of information. Those need to be in geostationary orbit.</p>
                                 <p>A satellite in a geostationary orbit moves around the Earth as fast as the Earth spins, so from the ground it looks like it is stationary (not moving). To move this way, the satellite must be straight above the equator, and 35,786 kilometers (22,236 miles) above the ground. Satellites in low Earth orbit are often less than one thousand kilometers above the ground. They move much faster. Many are in tilted orbits (they swing above and below the equator), so they can communicate, or see what is happening in other areas, depending on what they are used for.</p>"
            )
        );
        $pageparts['main'][] = $ppCreatorService->getCreatorArgumentsForPagePartAndProperties('Kunstmaan\PagePartBundle\Entity\HeaderPagePart',
            array(
                'setTitle' => 'References',
                'setNiv'   => 2
            )
        );
        $pageparts['main'][] = $ppCreatorService->getCreatorArgumentsForPagePartAndProperties('Kunstmaan\PagePartBundle\Entity\TextPagePart',
            array(
                'setContent' => '<p><a href="http://simple.wikipedia.org/wiki/Satellite_(artificial)">Wikipedia</a></p>'
            )
        );

        $ppCreatorService->addPagePartsToPage('satellite', $pageparts, 'en');

        $pageparts = array();
        $pageparts['main'][] = $ppCreatorService->getCreatorArgumentsForPagePartAndProperties('Kunstmaan\PagePartBundle\Entity\HeaderPagePart',
            array(
                'setTitle' => 'Kunstmaan (satelliet)',
                'setNiv'   => 1
            )
        );
        $pageparts['main'][] = $ppCreatorService->getCreatorArgumentsForPagePartAndProperties('Kunstmaan\PagePartBundle\Entity\TextPagePart',
            array(
                'setContent' => '<p>Een <b>kunstmaan</b> of <b>satelliet</b> is een door mensen gemaakt object in een baan om een hemellichaam. Kunstmanen zijn onbemande toestellen die door de mens in een baan zijn gebracht. Natuurlijke manen zijn meestal objecten met de structuur van een kleine planeet of planetoïde die door de zwaartekracht van de planeet in hun baan worden gehouden.</p>'
            )
        );
        $pageparts['main'][] = $ppCreatorService->getCreatorArgumentsForPagePartAndProperties('Kunstmaan\PagePartBundle\Entity\HeaderPagePart',
            array(
                'setTitle' => 'Historie',
                'setNiv'   => 2
            )
        );
        $pageparts['main'][] = $ppCreatorService->getCreatorArgumentsForPagePartAndProperties('Kunstmaan\PagePartBundle\Entity\TextPagePart',
            array(
                'setContent' => '<p>De eerste succesvol in een baan om de aarde gebrachte satelliet is de Spoetnik 1 van de Sovjet-Unie op 4 oktober 1957. Vaak wordt deze datum gezien als het begin van het ruimtevaarttijdperk. De eerste Amerikaanse satelliet die in een baan om te aarde gebracht werd was de Explorer 1.</p>
                                 <p>De eerste satelliet in een baan rond Mars was de Amerikaanse Mariner 9 op 13 november 1971, slechts enkele weken later gevolgd door de Mars 2 en de Mars 3 (27 november en 2 december 1971) van de Sovjet-Unie.</p>'
            )
        );
        $pageparts['main'][] = $ppCreatorService->getCreatorArgumentsForPagePartAndProperties('Kunstmaan\PagePartBundle\Entity\HeaderPagePart',
            array(
                'setTitle' => 'Classificatie',
                'setNiv'   => 2
            )
        );
        $pageparts['main'][] = $ppCreatorService->getCreatorArgumentsForPagePartAndProperties('Kunstmaan\PagePartBundle\Entity\TextPagePart',
            array(
                'setContent' => '<p>Afhankelijk van de toepassing kunnen satellieten als volgt worden geclassificeerd:</p>
                                <ul>
                                <li>Communicatiesatelliet: verzorging van telefoon, radio, televisie, internet over lange afstanden zoals Artemis</li>
                                <li>Navigatiesatelliet: voor plaatsbepaling op aarde met bijvoorbeeld GPS of Galileo</li>
                                <li>Observatiesatelliet: observatie van bijvoorbeeld milieuverontreiniging, maken van landkaarten en observeren van het heelal, bijvoorbeeld Envisat of ANS</li>
                                <li>Onderzoekssatelliet: voor wetenschappelijk onderzoek bijvoorbeeld naar gewichtloosheid, Sloshsat-FLEVO</li>
                                <li>Spionagesatelliet: veelal militaire toepassingen, bv. CORONA (Satelliet)</li>
                                <li>Weersatelliet: toegepast bij het doen van weersvoorspellingen, bijvoorbeeld Meteosat</li>
                                </ul>
                                <p>Een aparte categorie vormen de ruimtestations die in zekere zin ook satellieten zijn.</p>
                                <p>Satellieten worden ook geclassificeerd naar massa (inclusief brandstof):</p>
                                <ul>
                                <li>Minisatelliet of gewoon "kleine satelliet": 100 tot 500 kg</li>
                                <li>Microsatelliet: 10 tot 100 kg</li>
                                <li>Nanosatelliet: 1 tot 10 kg</li>
                                <li>Picosatelliet: 100 g tot 1 kg</li>
                                <li>Femtosatelliet: 10 tot 100 g - bevinden zich in de testfase</li>
                                </ul>
                                <p>Een of meer (zeer) kleine satellieten worden soms aanvullend, met dezelfde draagraket, gelanceerd bij de lancering van een gewone satelliet (meeliften, piggyback ride), zie bijvoorbeeld de eerste lancering van de Antares raket. Verder is bijvoorbeeld in ontwikkeling raket LauncherOne die eerst met de White Knight Two op 15 km hoogte wordt gebracht en vandaar gelanceerd wordt (zie ook hieronder); afhankelijk van de baan waarin een satelliet moet worden gebracht, kan deze een satelliet van 100 tot 250 kg lanceren. Ook in ontwikkeling is de SWORDS, een kleine raket die vanaf de grond een satelliet van 25 kg kan lanceren.</p>'
            )
        );
        $pageparts['main'][] = $ppCreatorService->getCreatorArgumentsForPagePartAndProperties('Kunstmaan\PagePartBundle\Entity\HeaderPagePart',
            array(
                'setTitle' => 'Lancering',
                'setNiv'   => 2
            )
        );
        $pageparts['main'][] = $ppCreatorService->getCreatorArgumentsForPagePartAndProperties('Kunstmaan\PagePartBundle\Entity\TextPagePart',
            array(
                'setContent' => '<p>De traditionele manier om een satelliet in een baan om de aarde te brengen is door middel van een lanceerraket, zoals de Europese Ariane-raket. Afhankelijk van de voortstuwingskracht van de raket en van het gewicht van de satellieten, kunnen soms meerdere satellieten tegelijk gelanceerd worden. Na de lancering komt een satelliet meestal in een tijdelijke overgangsbaan, om daarna door zijn eigen motor naar de gewenste definitieve baan te worden gestuwd.</p>
                                 <p>Een andere manier om satellieten in de ruimte te brengen, is ze aan boord van een ruimteveer mee te nemen en in de ruimte uit te zetten, zoals met de Hubble-ruimtetelescoop is gebeurd.</p>
                                 <p>Een raket kan ook vanaf een vliegtuig gelanceerd worden, dat de raket tot op een grote hoogte (ongeveer 12 kilometer) brengt en daar lanceert. Dit heeft als voordeel dat de raket zelf kleiner, en dus goedkoper, kan zijn, omdat ze slechts een deel van de zwaartekracht van de aarde moet overwinnen. De commerciële ruimtevaartfirma Orbital voert dergelijke lanceringen uit met de Pegasusraket die vanaf een Lockheed L-1011 TriStar wordt gelanceerd.</p>'
            )
        );
        $pageparts['main'][] = $ppCreatorService->getCreatorArgumentsForPagePartAndProperties('Kunstmaan\PagePartBundle\Entity\HeaderPagePart',
            array(
                'setTitle' => 'Plaatsing',
                'setNiv'   => 2
            )
        );
        $pageparts['main'][] = $ppCreatorService->getCreatorArgumentsForPagePartAndProperties('Kunstmaan\PagePartBundle\Entity\TextPagePart',
            array(
                'setContent' => '<p>Een satelliet kan in een geostationaire of niet geostationaire baan om de aarde worden gebracht. Een geostationair geplaatste satelliet hangt op een hoogte van ongeveer 36.000 km op een vast punt boven de evenaar. Op die hoogte is de omlooptijd van de satelliet namelijk exact gelijk aan de rotatiesnelheid van de aarde om haar eigen as (ongeveer 24 uur). Het idee van geostationaire kunstmanen werd oorspronkelijk door de sciencefictionschrijver Arthur C. Clarke geopperd. Geostationaire satellieten zijn bij uitstek geschikt voor observatie en telefoon- en andere communicatieverbindingen, omdat antennes op aarde naar een vast punt gericht kunnen blijven. Wel is de vertraging in de communicatie iets groter (ongeveer 0,25 seconde) dan voor een satelliet in een lagere baan. Ook staat op zeer hoge breedtegraden (dicht bij de polen) de satelliet nauwelijks boven de horizon.</p>
                                 <p>Een niet-geostationair geplaatste satelliet beweegt met een bepaalde snelheid ten opzichte van het aardoppervlak. Dit komt doordat de hoeksnelheid van de kunstmaan groter (op lage hoogte) of kleiner (op grote hoogte) is dan de hoeksnelheid van de aardrotatie. Voor elke cirkelbeweging van een kunstmaan dient de middelpuntzoekende kracht gelijk te zijn aan de zwaartekracht. Naarmate de baan hoger is, is de zwaartekracht lager. Als gevolg daarvan is in hogere banen de baansnelheid lager.</p>
                                 <p>Satellietbanen kunnen cirkelvormig of elliptisch zijn, met de aarde in een brandpunt van de ellips. In een cirkelvormige baan blijft de satelliet altijd even hoog boven het aardoppervlak; een ellipsvormige baan wordt gekenmerkt door de laagste hoogte (het perigeum) en de grootste hoogte (het apogeum). De omlooptijd van de satelliet is de tijd nodig om één volledige baan uit te voeren; hierbij geldt dat hoe hoger de satelliet zich boven het aardoppervlak bevindt, hoe langer de omlooptijd is.</p>
                                 <p>Daarnaast wordt een satellietbaan gekenmerkt door de inclinatie, dat wil zeggen, de hoek ervan met de evenaar. Een polaire baan staat loodrecht op de evenaar (inclinatie 90°) en loopt dus over de twee polen; dit heeft als voordeel, dat de satelliet het volledige aardoppervlak kan overvliegen en observeren. Dit is onder meer het geval voor de commerciële satelliet IKONOS die gedetailleerde beelden van elk deel van de aarde kan maken. Geostationaire satellieten hebben een inclinatie van 0° (ze blijven boven de evenaar).</p>'
            )
        );
        $pageparts['main'][] = $ppCreatorService->getCreatorArgumentsForPagePartAndProperties('Kunstmaan\PagePartBundle\Entity\HeaderPagePart',
            array(
                'setTitle' => 'Referenties',
                'setNiv'   => 2
            )
        );
        $pageparts['main'][] = $ppCreatorService->getCreatorArgumentsForPagePartAndProperties('Kunstmaan\PagePartBundle\Entity\TextPagePart',
            array(
                'setContent' => '<p><a href="http://nl.wikipedia.org/wiki/Kunstmaan">Wikipedia</a></p>'
            )
        );

        $ppCreatorService->addPagePartsToPage('satellite', $pageparts, 'nl');
    }

    /**
     * Create a ContentPage containing headers
     *
     * @param ObjectManager $manager The object manager
     * @param PageInterface $parent  The parent
     */
    private function createStylePage(ObjectManager $manager, $parent)
    {
        $pageCreator = $this->container->get('kunstmaan_node.page_creator_service');
        $pageCreator->setContainer($this->container);

        $nodeRepo = $manager->getRepository('KunstmaanNodeBundle:Node');
        $homePage = $nodeRepo->findOneBy(array('internalName' => 'homepage'));

        $contentPage = new ContentPage();
        $contentPage->setTitle('Home');

        $translations = array();
        $translations[] = array('language' => 'en', 'callback' => function($page, $translation, $seo) {
            $translation->setTitle('Styles');
            $translation->setSlug('styles');
        });
        $translations[] = array('language' => 'nl', 'callback' => function($page, $translation, $seo) {
            $translation->setTitle('Styles');
            $translation->setSlug('styles');
        });

        $options = array(
            'parent' => $homePage,
            'page_internal_name' => 'styles',
            'set_online' => true,
            'hidden_from_nav' => false,
            'creator' => 'Admin'
        );

        $pageCreator->createPage($contentPage, $translations, $options);

        $ppCreatorService = $this->container->get('kunstmaan_pageparts.pagepart_creator_service');

        $pageparts = array();
        $pageparts['main'][] = $ppCreatorService->getCreatorArgumentsForPagePartAndProperties('Kunstmaan\PagePartBundle\Entity\HeaderPagePart',
            array(
                'setTitle' => 'Buttons',
                'setNiv'   => 1
            )
        );
        $pageparts['main'][] = $ppCreatorService->getCreatorArgumentsForPagePartAndProperties('Kunstmaan\PagePartBundle\Entity\HeaderPagePart',
            array(
                'setTitle' => 'Sizes',
                'setNiv'   => 2
            )
        );
        $pageparts['main'][] = $ppCreatorService->getCreatorArgumentsForPagePartAndProperties('Kunstmaan\PagePartBundle\Entity\RawHTMLPagePart',
            array(
                'setContent' => '<p>
                                 <button class="btn btn-mini">Mini button</button>
                                 <button class="btn btn-small">Small button</button>
                                 <button class="btn">Normal</button>
                                 <button class="btn btn-large">Large button</button>
                                 </p>'
            )
        );
        $pageparts['main'][] = $ppCreatorService->getCreatorArgumentsForPagePartAndProperties('Kunstmaan\PagePartBundle\Entity\HeaderPagePart',
            array(
                'setTitle' => 'Styles',
                'setNiv'   => 2
            )
        );
        $pageparts['main'][] = $ppCreatorService->getCreatorArgumentsForPagePartAndProperties('Kunstmaan\PagePartBundle\Entity\RawHTMLPagePart',
            array(
                'setContent' => '<p>
                                 <button class="btn btn-large">Normal</button>
                                 <button class="btn btn-large btn-primary">Primary</button>
                                 <button class="btn btn-large btn-info">Info</button>
                                 <button class="btn btn-large btn-success">Success</button>
                                 <button class="btn btn-large btn-danger">Danger</button>
                                 <button class="btn btn-large btn-warning">Warning</button>
                                 <button class="btn btn-large btn-inverse">Inverse</button>
                                 <button class="btn btn-large btn-link">Link</button>
                                 </p>'
            )
        );

        $ppCreatorService->addPagePartsToPage('styles', $pageparts, 'en');
        $ppCreatorService->addPagePartsToPage('styles', $pageparts, 'nl');
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
                $pagepart = new EmailPagePart();
                $pagepart->setLabel("E-mail");
                $pagepart->setRequired(true);
                $pagepart->setErrormessageRequired("Required");
                $manager->persist($pagepart);
                $manager->flush();
                $manager->getRepository('KunstmaanPagePartBundle:PagePartRef')->addPagePart($page, $pagepart, $counter++, "main");
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
                $choicepagepart = new ChoicePagePart();
                $choicepagepart->setLabel("Choice");
                $choices = "Subject 1 \n Subject 2 \n Subject 3";
                $choicepagepart->setChoices($choices);
                $manager->persist($choicepagepart);
                $manager->flush();
                $manager->getRepository('KunstmaanPagePartBundle:PagePartRef')->addPagePart($page, $choicepagepart, $counter++, "main");
            }
            {
                $choicepagepart = new ChoicePagePart();
                $choicepagepart->setLabel("Expanded Choice");
                $choicepagepart->setExpanded(true);
                $choices = "Subject 1 \n Subject 2 \n Subject 3";
                $choicepagepart->setChoices($choices);
                $manager->persist($choicepagepart);
                $manager->flush();
                $manager->getRepository('KunstmaanPagePartBundle:PagePartRef')->addPagePart($page, $choicepagepart, $counter++, "main");
            }
            {
                $choicepagepart = new ChoicePagePart();
                $choicepagepart->setLabel("Multiple");
                $choicepagepart->setMultiple(true);
                $choices = "Subject 1 \n Subject 2 \n Subject 3";
                $choicepagepart->setChoices($choices);
                $manager->persist($choicepagepart);
                $manager->flush();
                $manager->getRepository('KunstmaanPagePartBundle:PagePartRef')->addPagePart($page, $choicepagepart, $counter++, "main");
            }
            {
                $choicepagepart = new ChoicePagePart();
                $choicepagepart->setLabel("Expanded Multiple Choice");
                $choicepagepart->setExpanded(true);
                $choicepagepart->setMultiple(true);
                $choices = "Subject 1 \n Subject 2 \n Subject 3";
                $choicepagepart->setChoices($choices);
                $manager->persist($choicepagepart);
                $manager->flush();
                $manager->getRepository('KunstmaanPagePartBundle:PagePartRef')->addPagePart($page, $choicepagepart, $counter++, "main");
            }
            {
                $multilinetextpagepart = new MultiLineTextPagePart();
                $multilinetextpagepart->setLabel("Description");
                $manager->persist($multilinetextpagepart);
                $manager->flush();
                $manager->getRepository('KunstmaanPagePartBundle:PagePartRef')->addPagePart($page, $multilinetextpagepart, $counter++, "main");
            }
            {
                $pagepart = new CheckboxPagePart();
                $pagepart->setLabel("Checkbox");
                $pagepart->setRequired(true);
                $manager->persist($pagepart);
                $manager->flush();
                $manager->getRepository('KunstmaanPagePartBundle:PagePartRef')->addPagePart($page, $pagepart, $counter++, "main");
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
     * @param ObjectManager $manager
     */
    public function createTranslations(ObjectManager $manager)
    {
        // Splashpage
        $trans['lang_chooser.welcome']['en'] = 'Welcome, continue in English';
        $trans['lang_chooser.welcome']['fr'] = 'Bienvenu, continuer en Français';
        $trans['lang_chooser.welcome']['nl'] = 'Welkom, ga verder in het Nederlands';
        $trans['lang_chooser.welcome']['de'] = 'Willkommen, gehe weiter in Deutsch';

        // AdminList page with satellites
        $trans['satellite.name']['en'] = 'name';
        $trans['satellite.launched']['en'] = 'launched';
        $trans['satellite.weight']['en'] = 'launch mass';
        $trans['satellite.communication']['en'] = 'Communication satellites';
        $trans['satellite.climate_research']['en'] = 'Climate research satellites';
        $trans['satellite.name']['nl'] = 'naam';
        $trans['satellite.launched']['nl'] = 'lanceringsdatum';
        $trans['satellite.weight']['nl'] = 'gewicht';
        $trans['satellite.communication']['nl'] = 'Communicatie satellieten';
        $trans['satellite.climate_research']['nl'] = 'Klimatologische onderzoekssatellieten';

        foreach ($trans as $key => $array) {
            foreach ($array as $lang => $value) {
                $t = new Translation;
                $t->setKeyword($key);
                $t->setLocale($lang);
                $t->setText($value);
                $t->setDomain('messages');
                $t->setCreatedAt(new \DateTime());
                $t->setFlag(Translation::FLAG_NEW);

                $manager->persist($t);
            }
        }

        $manager->flush();
    }

    /**
     * @param ObjectManager $manager
     */
    public function createMedia(ObjectManager $manager)
    {
        // Create dummy image folder and add dummy images
        {
            $imageFolder = $manager->getRepository('KunstmaanMediaBundle:Folder')->findOneBy(array('rel' => 'image'));

            $folder = new Folder();
            $folder->setName('dummy');
            $folder->setRel('image');
            $folder->setParent($imageFolder);
            $folder->setInternalName('dummy_images');
            $manager->persist($folder);
            $manager->flush();

            $path = $this->rootDir . '/../src/{{ namespace|replace({"\\" : "/"}) }}/Resources/public/img/general/logo.png';
            $this->image = $this->createMediaFile($manager, basename($path), $path, $folder);
        }

        // Create dummy file folder and add dummy files
        {
            $filesFolder = $manager->getRepository('KunstmaanMediaBundle:Folder')->findOneBy(array('rel' => 'files'));

            $folder = new Folder();
            $folder->setName('dummy');
            $folder->setRel('files');
            $folder->setParent($filesFolder);
            $folder->setInternalName('dummy_files');
            $manager->persist($folder);
            $manager->flush();

            $path = $this->rootDir . '/../src/{{ namespace|replace({"\\" : "/"}) }}/Resources/public/files/dummy/sample.pdf';
            $this->file = $this->createMediaFile($manager, basename($path), $path, $folder);
        }

        // Create dummy video folder and add dummy videos
        {
            $videoFolder = $manager->getRepository('KunstmaanMediaBundle:Folder')->findOneBy(array('rel' => 'video'));

            $folder = new Folder();
            $folder->setName('dummy');
            $folder->setRel('videos');
            $folder->setParent($videoFolder);
            $folder->setInternalName('dummy_videos');
            $manager->persist($folder);
            $manager->flush();

            $this->video = $this->createVideoFile($manager, 'Kunstmaan', 'WPx-Oe2WrUE', $folder);
        }
    }

    private function createMediaFile($manager, $name, $path, $folder)
    {
        $file = new UploadedFile(
            $path,
            $name,
            mime_content_type($path), // DEPRECATED - just used as quick hack!
            filesize($path),
            null
        );

        // Hack for media bundle issue
        $dir = dirname($this->rootDir);
        chdir($dir . '/web');
        $media = new Media();
        $media->setFolder($folder);
        $helper = new FileHelper($media);
        $helper->setFile($file);
        $manager->getRepository('KunstmaanMediaBundle:Media')->save($media);
        chdir($dir);

        return $media;
    }

    private function createVideoFile($manager, $name, $code, $folder)
    {
        // Hack for media bundle issue
        $dir = dirname($this->rootDir);
        chdir($dir . '/web');
        $media = new Media();
        $media->setFolder($folder);
        $media->setName($name);
        $helper = new RemoteVideoHelper($media);
        $helper->setCode($code);
        $helper->setType('youtube');
        $manager->getRepository('KunstmaanMediaBundle:Media')->save($media);
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
