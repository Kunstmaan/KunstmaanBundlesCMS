<?php

namespace {{ namespace }}\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

use Faker\Provider\Lorem;
use Faker\Provider\DateTime;

use Kunstmaan\NodeBundle\Helper\Services\PageCreatorService;
use Kunstmaan\PagePartBundle\Helper\Services\PagePartCreatorService;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use {{ namespace }}\Entity\{{ entity_class }}\{{ entity_class }}Author;
use {{ namespace }}\Entity\{{ entity_class }}\{{ entity_class }}OverviewPage;
use {{ namespace }}\Entity\{{ entity_class }}\{{ entity_class }}Page;

/**
 * ArticleFixtures
 */
class ArticleFixtures extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
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

        $pageCreator = new PageCreatorService();
        $pageCreator->setContainer($this->container);

        $ppCreatorService = new PagePartCreatorService($em);

        // Create article overview page
        $nodeRepo = $em->getRepository('KunstmaanNodeBundle:Node');
        $homePage = $nodeRepo->findOneBy(array('internalName' => 'homepage'));

        $overviewPage = new {{ entity_class }}OverviewPage();
        $overviewPage->setTitle('Article overview page');

        $translations = [];
        $translations[] = ['language' =>  'en', 'callback' => function($page, $translation, $seo) {
            $translation->setTitle('Article overview page');
            $translation->setSlug('article-overview');
        }];

        $options = [
            'parent' => $homePage,
            'page_internal_name' => 'article_overview_page',
            'set_online' => true,
            'creator' => 'Admin'
        ];

        $pageCreator->createPage($overviewPage, $translations, $options);

        // Create author
        $author = new {{ entity_class }}Author();
        $author->setName('John Doe');
        $manager->persist($author);
        $manager->flush();

        // Create articles
        for($i=1; $i<=rand(13,18); $i++) {
            $articlePage = new {{ entity_class }}Page();
            $articlePage->setTitle('Article title '.$i);
            $articlePage->setAuthor($author);
            $articlePage->setDate(DateTime::dateTimeBetween('-'.($i+1).' days', '-'.$i.' days'));
            $articlePage->setSummary(Lorem::paragraph(5));

            $translations = [];
            $translations[] = ['language' =>  'en', 'callback' => function($page, $translation, $seo) use($i) {
                $translation->setTitle('Article title '.$i);
                $translation->setSlug('article-1'.$i);
            }];

            $options = [
                'parent' => $overviewPage,
                'set_online' => true,
                'creator' => 'Admin'
            ];

            $articlePage = $pageCreator->createPage($articlePage, $translations, $options);

            $pageparts = array(
                'main' => array(
                    $ppCreatorService->getCreatorArgumentsForPagePartAndProperties('Kunstmaan\PagePartBundle\Entity\TextPagePart',
                        array('setContent' => '<p>'.Lorem::paragraph(15).'</p>')
                    )
                )
            );

            $ppCreatorService->addPagePartsToPage($articlePage, $pageparts, 'en');
        }
    }

    /**
     * Get the order of this fixture
     *
     * @return int
     */
    public function getOrder()
    {
        return 60;
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
