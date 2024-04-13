<?php

namespace {{ namespace }}\DataFixtures\ORM\DefaultSiteGenerator;

use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Helper\Services\PageCreatorService;
use Kunstmaan\SitemapBundle\Entity\SitemapPage;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class SitemapFixtures extends AbstractFixture implements OrderedFixtureInterface, ORMFixtureInterface
{
    private EntityManagerInterface $em;
    private PageCreatorService $pageCreatorService;
    private string $requiredLocales;

    public function __construct(EntityManagerInterface $em, PageCreatorService $pageCreatorService, #[Autowire('%requiredlocales%')] string $requiredLocales)
    {
        $this->em = $em;
        $this->pageCreatorService = $pageCreatorService;
        $this->requiredLocales = $requiredLocales;
    }

    /**
     * Load data fixtures with the passed EntityManager.
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $nodeRepo = $this->em->getRepository(Node::class);
        $homePage = $nodeRepo->findOneBy(array('internalName' => 'homepage'));

        $sitemapPage = new SitemapPage();
        $sitemapPage->setTitle('Sitemap');

	    $locales = explode('|', $this->requiredLocales);
        $translations = array();
        foreach ($locales as $locale) {
            $translations[] = array('language' => $locale, 'callback' => function($page, $translation, $seo) {
            $translation->setTitle('Sitemap');
            $translation->setSlug('sitemap');
            $translation->setWeight(100);
            });
        }

        $options = array(
            'parent' => $homePage,
            'page_internal_name' => 'sitemap',
            'set_online' => true,
            'hidden_from_nav' => true,
            'creator' => 'admin'
        );

        $this->pageCreatorService->createPage($sitemapPage, $translations, $options);
    }

    public function getOrder(): int
    {
        return 90;
    }
}
