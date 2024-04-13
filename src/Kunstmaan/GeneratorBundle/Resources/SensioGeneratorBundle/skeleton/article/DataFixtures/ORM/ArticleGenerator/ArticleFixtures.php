<?php

namespace {{ namespace }}\DataFixtures\ORM\ArticleGenerator;

use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Provider\DateTime;
use Faker\Provider\Lorem;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Helper\Services\PageCreatorService;
use Kunstmaan\PagePartBundle\Helper\Services\PagePartCreatorService;
use Kunstmaan\UtilitiesBundle\Helper\SlugifierInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use {{ namespace }}\Entity\Pages\{{ entity_class }}OverviewPage;
use {{ namespace }}\Entity\Pages\{{ entity_class }}Page;
use {{ namespace }}\Entity\{{ entity_class }}Author;

class {{ entity_class }}ArticleFixtures extends AbstractFixture implements OrderedFixtureInterface, ORMFixtureInterface
{
    private EntityManagerInterface $em;
    private PageCreatorService $pageCreator;
    private PagePartCreatorService $pagePartCreator;
    private SlugifierInterface $slugifier;
    private bool $isMultiLanguage;
    private string $requiredLocales;

    public function __construct(
        EntityManagerInterface $em,
        PageCreatorService $pageCreator,
        PagePartCreatorService $pagePartCreator,
        SlugifierInterface $slugifier,
        #[Autowire('%kunstmaan_admin.multi_language%')] bool $isMultiLanguage,
        #[Autowire('%kunstmaan_admin.required_locales%')] string $requiredLocales,
    ) {
        $this->em = $em;
        $this->pageCreator = $pageCreator;
        $this->pagePartCreator = $pagePartCreator;
        $this->slugifier = $slugifier;
        $this->isMultiLanguage = $isMultiLanguage;
        $this->requiredLocales = $requiredLocales;
    }

    /**
     * Load data fixtures with the passed EntityManager.
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        if ($this->isMultiLanguage) {
            $languages = explode('|', $this->requiredLocales);
        }
        if (!is_array($languages) || count($languages) < 1) {
            $languages = array('en');
        }

        // Create article overview page
        $nodeRepo = $this->em->getRepository(Node::class);
        $homePage = $nodeRepo->findOneBy(array('internalName' => 'homepage'));

        $overviewPage = new {{ entity_class }}OverviewPage();
        $overviewPage->setTitle('{{ entity_class }}');

        $translations = array();
        foreach ($languages as $lang) {
            $title = '{{ entity_class }}';
            $translations[] = array('language' => $lang, 'callback' => function($page, $translation, $seo) use ($title) {
                $translation->setTitle($title);
                $translation->setWeight(30);
                $translation->setSlug($this->slugifier->slugify($title));
            });
        }

        $options = array(
            'parent' => $homePage,
            'page_internal_name' => '{{ entity_class|lower }}_overview_page',
            'set_online' => true,
            'creator' => 'admin'
        );

        $this->pageCreator->createPage($overviewPage, $translations, $options);

        $fakerNL = Factory::create('nl_BE');
        $fakerEN = Factory::create('en_US');

        // Create articles
        for ($i=1; $i<=6; $i++) {
            {% if uses_author %}
            // Create author
            $author = new {{ entity_class }}Author();
            $author->setName($fakerNL->name);
            $manager->persist($author);
            $manager->flush();
            {% endif %}

            $articlePage = new {{ entity_class }}Page();
            $articlePage->setTitle(Lorem::sentence(6));
            {% if uses_author %}
            $articlePage->setAuthor($author);
            {% endif %}
            $articlePage->setDate(DateTime::dateTimeBetween('-'.($i+1).' days', '-'.$i.' days'));
            $articlePage->setSummary(Lorem::paragraph(5));

            $translations = array();
            foreach ($languages as $lang) {
                if ($lang == 'nl') {
                    $title = $fakerNL->sentence;
                } else {
                    $title = $fakerEN->sentence;
                }

                $translations[] = array('language' => $lang, 'callback' => function($page, $translation, $seo) use ($title, $i) {
                    $translation->setTitle($title);
                    $translation->setWeight(100 + $i);
                    $translation->setSlug($this->slugifier->slugify($title));
                });
            }

            $options = array(
                'parent' => $overviewPage,
                'set_online' => true,
                'hidden_from_nav' => true,
                'creator' => 'admin'
            );

            $articlePage = $this->pageCreator->createPage($articlePage, $translations, $options);

            foreach ($languages as $lang) {
                $pageparts = array(
                    'main' => array(
                        $this->pagePartCreator->getCreatorArgumentsForPagePartAndProperties('{{ namespace }}\Entity\PageParts\TextPagePart',
                            array('setContent' => '<p>'.Lorem::paragraph(15).'</p>' . '<p>'.Lorem::paragraph(25).'</p>' .'<p>'.Lorem::paragraph(10).'</p>')
                        )
                    )
                );

                $this->pagePartCreator->addPagePartsToPage($articlePage, $pageparts, $lang);
            }
        }
    }

    public function getOrder(): int
    {
        return 60;
    }
}
