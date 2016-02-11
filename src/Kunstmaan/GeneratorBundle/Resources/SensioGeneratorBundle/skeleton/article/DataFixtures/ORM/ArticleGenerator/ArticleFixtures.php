<?php

namespace {{ namespace }}\DataFixtures\ORM\ArticleGenerator;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Faker\Factory;
use Faker\Provider\Lorem;
use Faker\Provider\DateTime;
use Kunstmaan\NodeBundle\Helper\Services\PageCreatorService;
use Kunstmaan\UtilitiesBundle\Helper\Slugifier;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use {{ namespace }}\Entity\{{ entity_class }}Author;
use {{ namespace }}\Entity\Pages\{{ entity_class }}OverviewPage;
use {{ namespace }}\Entity\Pages\{{ entity_class }}Page;

/**
 * {{ entity_class }}ArticleFixtures
 */
class {{ entity_class }}ArticleFixtures extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
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
        if ($this->container->getParameter('multilanguage')) {
            $languages = explode('|', $this->container->getParameter('requiredlocales'));
        }
        if (!is_array($languages) || count($languages) < 1) {
            $languages = array('en');
        }

        $em = $this->container->get('doctrine.orm.entity_manager');

        $pageCreator = $this->container->get('kunstmaan_node.page_creator_service');
        $ppCreatorService = $this->container->get('kunstmaan_pageparts.pagepart_creator_service');

        // Create article overview page
        $nodeRepo = $em->getRepository('KunstmaanNodeBundle:Node');
        $homePage = $nodeRepo->findOneBy(array('internalName' => 'homepage'));

        $overviewPage = new {{ entity_class }}OverviewPage();
	$overviewPage->setTitle('{{ entity_class }}');

        $translations = array();
        foreach ($languages as $lang) {
	    $title = '{{ entity_class }}';
	    $translations[] = array('language' => $lang, 'callback' => function($page, $translation, $seo) use ($title) {
                $translation->setTitle($title);
                $translation->setWeight(30);
                $slugifier = $this->container->get('kunstmaan_utilities.slugifier');
                $translation->setSlug($slugifier->slugify($title));
            });
        }

        $options = array(
            'parent' => $homePage,
	    'page_internal_name' => '{{ entity_class|lower }}_overview_page',
            'set_online' => true,
	    'creator' => 'admin'
        );

        $pageCreator->createPage($overviewPage, $translations, $options);

	$fakerNL = Factory::create('nl_BE');
	$fakerEN = Factory::create('en_US');

        // Create articles
	for ($i=1; $i<=6; $i++) {

	    // Create author
	    $author = new {{ entity_class }}Author();
	    $author->setName($fakerNL->name);
	    $manager->persist($author);
	    $manager->flush();

            $articlePage = new {{ entity_class }}Page();
	    $articlePage->setTitle(Lorem::sentence(6));
            $articlePage->setAuthor($author);
            $articlePage->setDate(DateTime::dateTimeBetween('-'.($i+1).' days', '-'.$i.' days'));
            $articlePage->setSummary(Lorem::paragraph(5));

            $translations = array();
            foreach ($languages as $lang) {
                if ($lang == 'nl') {
		    $title = $fakerEN->sentence;
                } else {
		    $title = $fakerEN->sentence;
                }

		$translations[] = array('language' => $lang, 'callback' => function($page, $translation, $seo) use ($title, $i) {
                    $translation->setTitle($title);
                    $translation->setWeight(100 + $i);
                    $slugifier = $this->container->get('kunstmaan_utilities.slugifier');
                    $translation->setSlug($slugifier->slugify($title));
                });
            }

            $options = array(
                'parent' => $overviewPage,
                'set_online' => true,
                'hidden_from_nav' => true,
		'creator' => 'admin'
            );

            $articlePage = $pageCreator->createPage($articlePage, $translations, $options);

            foreach ($languages as $lang) {
                $pageparts = array(
                    'main' => array(
			$ppCreatorService->getCreatorArgumentsForPagePartAndProperties('{{ namespace }}\Entity\PageParts\TextPagePart',
			    array('setContent' => '<p>'.Lorem::paragraph(15).'</p>' . '<p>'.Lorem::paragraph(25).'</p>' .'<p>'.Lorem::paragraph(10).'</p>')
                        )
                    )
                );

                $ppCreatorService->addPagePartsToPage($articlePage, $pageparts, $lang);
            }
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
