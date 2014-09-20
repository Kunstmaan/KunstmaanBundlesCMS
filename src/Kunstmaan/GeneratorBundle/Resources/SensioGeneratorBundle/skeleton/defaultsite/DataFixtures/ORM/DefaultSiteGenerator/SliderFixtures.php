<?php

namespace {{ namespace }}\DataFixtures\ORM\DefaultSiteGenerator;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Provider\Lorem;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * SliderFixtures
 */
class SliderFixtures extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
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
        $em = $this->container->get('doctrine.orm.entity_manager');

        // Add slider images to database
        $mediaCreatorService = $this->container->get('kunstmaan_media.media_creator_service');

        $folder = $em->getRepository('KunstmaanMediaBundle:Folder')->findOneBy(array('rel' => 'image'));

        $sliderDir = dirname(__FILE__).'/../../../Resources/public/files/slider/';
        $allFiles = glob($sliderDir.'slide*');
        $mediaImages = array();
        foreach ($allFiles as $file) {
            $mediaImages[] = $mediaCreatorService->createFile($file, $folder->getId());
        }

        // Create slide page parts
        $nodeRepo = $em->getRepository('KunstmaanNodeBundle:Node');
        $homePage = $nodeRepo->findOneBy(array('internalName' => 'homepage'));

        $ppCreatorService = $this->container->get('kunstmaan_pageparts.pagepart_creator_service');

        $pageparts = array('slider' => array());
        foreach ($mediaImages as $key => $media) {
            $pageparts['slider'][] = $ppCreatorService->getCreatorArgumentsForPagePartAndProperties('{{ namespace }}\Entity\PageParts\SlidePagePart',
                array(
                    'setTitle'           => 'Title '.($key+1),
                    'setDescription'     => Lorem::paragraph(2),
                    'setTickText'        => 'thick text '.($key+1),
                    'setButtonText'      => 'Click me!',
                    'setButtonUrl'       => 'http://www.kunstmaan.be',
                    'setButtonNewWindow' => true,
                    'setImage'           => $media
                )
            );
        }

        $ppCreatorService->addPagePartsToPage($homePage, $pageparts, 'en');

        $pageparts = array('slider' => array());
        foreach ($mediaImages as $key => $media) {
            $pageparts['slider'][] = $ppCreatorService->getCreatorArgumentsForPagePartAndProperties('{{ namespace }}\Entity\PageParts\SlidePagePart',
                array(
                    'setTitle'           => 'Titel '.($key+1),
                    'setDescription'     => Lorem::paragraph(2),
                    'setTickText'        => 'thick tekst '.($key+1),
                    'setButtonText'      => 'Klik hier!',
                    'setButtonUrl'       => 'http://www.kunstmaan.be',
                    'setButtonNewWindow' => true,
                    'setImage'           => $media
                )
            );
        }

        $ppCreatorService->addPagePartsToPage($homePage, $pageparts, 'nl');
    }

    /**
     * Get the order of this fixture
     *
     * @return int
     */
    public function getOrder()
    {
        return 70;
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
