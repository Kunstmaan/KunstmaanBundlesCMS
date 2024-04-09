<?php

namespace {{ namespace }}\DataFixtures\ORM\SearchPageGenerator;

use {{ namespace }}\Entity\Pages\SearchPage;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Helper\Services\PageCreatorService;
use Kunstmaan\UtilitiesBundle\Helper\SlugifierInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;


class SearchFixtures extends AbstractFixture implements OrderedFixtureInterface, ORMFixtureInterface
{
    private EntityManagerInterface $em;
    private PageCreatorService $pageCreator;
    private SlugifierInterface $slugifier;
    private bool $isMultiLanguage;
    private string $requiredLocales;

    public function __construct(
        EntityManagerInterface $em,
        PageCreatorService $pageCreator,
        SlugifierInterface $slugifier,
        #[Autowire('%kunstmaan_admin.multi_language%')] bool $isMultiLanguage,
        #[Autowire('%kunstmaan_admin.required_locales%')] string $requiredLocales,
    ) {
        $this->em = $em;
        $this->pageCreator = $pageCreator;
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

        $searchPage = new SearchPage();
        $searchPage->setTitle('Search');

        $translations = array();
        foreach ($languages as $lang) {
            if ($lang == 'nl') {
                $title = 'Zoeken';
            } else {
                $title = 'Search';
            }

            $translations[] = array('language' => $lang, 'callback' => function($page, $translation, $seo) use($title) {
                $translation->setTitle($title);
                $translation->setWeight(50);
                $translation->setSlug($this->slugifier->slugify($title));
            });
        }

        $options = array(
            'parent' => $homePage,
            'page_internal_name' => 'search',
            'set_online' => true,
            'hidden_from_nav' => true,
            'creator' => 'Admin'
        );

        $this->pageCreator->createPage($searchPage, $translations, $options);
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
}
