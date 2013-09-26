<?php

namespace {{ namespace }}\DataFixtures\ORM\DefaultSiteGenerator;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

use Kunstmaan\NodeBundle\Helper\Services\PageCreatorService;
use Kunstmaan\SitemapBundle\Entity\SitemapPage;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * SitemapFixtures
 */
class SitemapFixtures extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
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

        $pageCreator = $this->container->get('kunstmaan_node.page_creator_service');

        $nodeRepo = $em->getRepository('KunstmaanNodeBundle:Node');
        $homePage = $nodeRepo->findOneBy(array('internalName' => 'homepage'));

        $sitemapPage = new SitemapPage();
        $sitemapPage->setTitle('Sitemap');

        $translations = array();
        $translations[] = array('language' => 'en', 'callback' => function($page, $translation, $seo) {
            $translation->setTitle('Sitemap');
            $translation->setSlug('sitemap');
            $translation->setWeight(100);
        });
        $translations[] = array('language' => 'nl', 'callback' => function($page, $translation, $seo) {
            $translation->setTitle('Sitemap');
            $translation->setSlug('sitemap');
            $translation->setWeight(100);
        });

        $options = array(
            'parent' => $homePage,
            'page_internal_name' => 'sitemap',
            'set_online' => true,
            'hidden_from_nav' => true,
            'creator' => 'Admin'
        );

        $pageCreator->createPage($sitemapPage, $translations, $options);
    }

    /**
     * Get the order of this fixture
     *
     * @return int
     */
    public function getOrder()
    {
        return 90;
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
