<?php

namespace {{ namespace }}\DataFixtures\ORM\LegalGenerator;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Kunstmaan\CookieBundle\Entity\Cookie;
use Kunstmaan\CookieBundle\Entity\CookieType;
use Kunstmaan\MediaBundle\Helper\Services\MediaCreatorService;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\AdminBundle\Entity\User;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Helper\Services\PageCreatorService;
use Kunstmaan\PagePartBundle\Helper\Services\PagePartCreatorService;
use Kunstmaan\UtilitiesBundle\Helper\Slugifier;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Translation\TranslatorInterface;
use \Symfony\Component\HttpKernel\Kernel;
use Doctrine\ORM\EntityManagerInterface;
use Gedmo\Translatable\Entity\Repository\TranslationRepository;
use Gedmo\Translatable\Entity\Translation;
use {{ namespace }}\Entity\Pages\LegalFolderPage;
use {{ namespace }}\Entity\Pages\LegalPage;

/**
 * Class DefaultFixtures
 */
class DefaultFixtures extends Fixture implements OrderedFixtureInterface, ContainerAwareInterface, FixtureGroupInterface
{
    // Username that is used for creating pages
    const ADMIN_USERNAME = 'pagecreator';

    /** @var ContainerInterface */
    private $container;

    /** @var ObjectManager */
    private $manager;

    /** @var PageCreatorService */
    private $pageCreator;

    /** @var MediaCreatorService */
    private $mediaCreator;

    /** @var PagePartCreatorService */
    private $pagePartCreator;

    /** @var array */
    private $requiredLocales;

    /** @var Slugifier */
    private $slugifier;

    /** @var TranslatorInterface */
    private $translator;

    /** @var string */
    private $defaultLocale;

    /** @var EntityManagerInterface */
    private $em;

    /** @var TranslationRepository */
    private $translationRepo;

    /**
     * Load data fixtures with the passed EntityManager.
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;

        $this->pageCreator = $this->container->get('kunstmaan_node.page_creator_service');
        $this->mediaCreator = $this->container->get('kunstmaan_media.media_creator_service');
        $this->pagePartCreator = $this->container->get('kunstmaan_pageparts.pagepart_creator_service');
        $this->translator = $this->container->get('translator.default');
        $this->slugifier = $this->container->get('kunstmaan_utilities.slugifier');
        $this->requiredLocales = explode('|', $this->container->getParameter('kunstmaan_admin.required_locales'));
        $this->defaultLocale = $this->container->getParameter('kunstmaan_admin.default_locale');
        $this->em = $this->container->get('doctrine.orm.entity_manager');
        $this->translationRepo = $this->em->getRepository(Translation::class);

        $this->checkPageCreator();
        $this->createLegalPages();
        $this->createCookieTypes();
    }

    /**
     * Create legal pages
     */
    private function createLegalPages()
    {
        $legalFolderPage = new LegalFolderPage();
        $legalFolderPage->setTitle('Legal');

        $translations = [];
        foreach ($this->requiredLocales as $locale) {
            $translations[] = [
                'language' => $locale,
                'callback' => function (LegalFolderPage $page, NodeTranslation $translation, $seo) {
                    $translation->setTitle('Legal');

                    $seo->setMetaRobots('noindex,nofollow');
                },
            ];
        }

        $options = [
            'parent' => $this->manager->getRepository('KunstmaanNodeBundle:Node')->findOneBy(['internalName' => 'homepage']),
            'page_internal_name' => 'legal',
            'set_online' => false,
            'hidden_from_nav' => true,
            'creator' => self::ADMIN_USERNAME,
        ];

        $legalFolderNode = $this->pageCreator->createPage($legalFolderPage, $translations, $options);

        $node = $this->createLegalPage($legalFolderNode, 'kuma.cookie.fixtures.contact.title', 'legal_contact', 3);
        $this->addContactPageParts($node);

        $node = $this->createLegalPage($legalFolderNode, 'kuma.cookie.fixtures.cookie_preferences.title', 'legal_cookie_preferences', 2);
        $this->addCookiePreferencesPageParts($node);

        $node = $this->createLegalPage($legalFolderNode, 'kuma.cookie.fixtures.privacy_policy.title', 'legal_privacy_policy', 1);
        $this->addPrivacyPolicyPageParts($node);
    }

    /**
     * Creates the cookie types
     */
    public function createCookieTypes()
    {
        $cookieTypes = [
            'functional_cookie' => 3,
            'analyzing_cookie' => 1,
            'marketing_cookie' => 5,
        ];

        foreach ($cookieTypes as $cookieType => $count) {
            $type = new CookieType();
            $type->setInternalName(
                $this->translator->trans('kuma.cookie.fixtures.cookie_types.'.$cookieType.'.internal_name', [], null, $this->defaultLocale)
            );
            $type->setName($this->translator->trans('kuma.cookie.fixtures.cookie_types.'.$cookieType.'.name', [], null, $this->defaultLocale));
            $type->setShortDescription(
                $this->translator->trans('kuma.cookie.fixtures.cookie_types.'.$cookieType.'.short_description', [], null, $this->defaultLocale)
            );
            $type->setLongDescription(
                $this->translator->trans('kuma.cookie.fixtures.cookie_types.'.$cookieType.'.long_description', [], null, $this->defaultLocale)
            );
            if ($cookieType === 'functional_cookie') {
                $type->setAlwaysOn(true);
            }

            foreach ($this->requiredLocales as $locale) {
                if ($locale !== $this->defaultLocale) {
                    $this->translationRepo
                        ->translate(
                            $type,
                            'name',
                            $locale,
                            $this->translator->trans('kuma.cookie.fixtures.cookie_types.'.$cookieType.'.name', [], null, $locale)
                        )
                        ->translate(
                            $type,
                            'shortDescription',
                            $locale,
                            $this->translator->trans('kuma.cookie.fixtures.cookie_types.'.$cookieType.'.short_description', [], null, $locale)
                        )
                        ->translate(
                            $type,
                            'longDescription',
                            $locale,
                            $this->translator->trans('kuma.cookie.fixtures.cookie_types.'.$cookieType.'.long_description', [], null, $locale)
                        );
                }
            }

            $this->manager->persist($type);
            $this->createCookies($type, $count);
        }

        $this->manager->flush();
    }

    /**
     * @param CookieType $cookieType
     * @param CookieType $count
     */
    public function createCookies(CookieType $cookieType, $count)
    {
        for($i = 1; $i <= $count; $i++) {
            $cookie = new Cookie();
            $cookie->setName(
                $this->translator->trans('kuma.cookie.fixtures.cookies.'.$cookieType->getInternalName().'.'.$i.'.name', [], null, $this->defaultLocale)
            );
            $cookie->setDescription(
                $this->translator->trans(
                    'kuma.cookie.fixtures.cookies.'.$cookieType->getInternalName().'.'.$i.'.description',
                    [],
                    null,
                    $this->defaultLocale
                )
            );
            $cookie->setType($cookieType);

            foreach ($this->requiredLocales as $locale) {
                if ($locale !== $this->defaultLocale) {
                    $this->translationRepo
                        ->translate(
                            $cookie,
                            'name',
                            $locale,
                            $this->translator->trans('kuma.cookie.fixtures.cookies.'.$cookieType->getInternalName().'.'.$i.'.name', [], null, $locale)
                        )
                        ->translate(
                            $cookie,
                            'description',
                            $locale,
                            $this->translator->trans(
                                'kuma.cookie.fixtures.cookies.'.$cookieType->getInternalName().'.'.$i.'.description',
                                [],
                                null,
                                $locale
                            )
                        );
                }
            }
            $this->manager->persist($cookie);
        }
    }

    /**
     * @param Node $node
     */
    private function addPrivacyPolicyPageParts(Node $node)
    {
        foreach ($this->requiredLocales as $locale) {
            $pageparts = [];

            $pageparts['legal_header'][] = $this->createTextPagePart('kuma.cookie.fixtures.privacy_policy.text.1', $locale);

            $pageparts['legal_main'][] = $this->createHeaderPagePart('kuma.cookie.fixtures.privacy_policy.headers.1', $locale);
            $pageparts['legal_main'][] = $this->createTextPagePart('kuma.cookie.fixtures.privacy_policy.text.2', $locale);
            $pageparts['legal_main'][] = $this->createHeaderPagePart('kuma.cookie.fixtures.privacy_policy.headers.2', $locale);
            for ($i = 3; $i <= 7; $i ++) {
                $pageparts['legal_main'][] = $this->createHeaderPagePart('kuma.cookie.fixtures.privacy_policy.headers.' . $i, $locale, 3);
                $pageparts['legal_main'][] = $this->createTextPagePart('kuma.cookie.fixtures.privacy_policy.text.' . $i, $locale);
            }
            for ($i = 8; $i <= 11; $i ++) {
                $pageparts['legal_main'][] = $this->createHeaderPagePart('kuma.cookie.fixtures.privacy_policy.headers.' . $i, $locale);
                $pageparts['legal_main'][] = $this->createTextPagePart('kuma.cookie.fixtures.privacy_policy.text.' . $i, $locale);
            }

            $this->pagePartCreator->addPagePartsToPage($node, $pageparts, $locale);
        }
    }

    /**
     * @param Node $node
     */
    private function addCookiePreferencesPageParts(Node $node)
    {
        foreach ($this->requiredLocales as $locale) {
            $pageparts = [];

            $folder = $this->manager->getRepository('KunstmaanMediaBundle:Folder')->findOneBy(['rel' => 'image']);

            if (Kernel::VERSION_ID < 40000 ) {
                $imgDir = __DIR__ . '/../../../Resources/ui/img/legal/';
            } else {
                $imgDir = $this->container->getParameter('kernel.project_dir').'/assets/ui/img/legal/';
            }

            $icon = $this->mediaCreator->createFile($imgDir.'cookie.svg', $folder->getId());
            $pageparts['legal_header'][] = $this->pagePartCreator->getCreatorArgumentsForPagePartAndProperties(
                '{{ namespace }}\Entity\PageParts\LegalCenteredIconPagePart',
                [
                    'setIcon' => $icon,
                ]
            );
            $pageparts['legal_header'][] = $this->createTextPagePart('kuma.cookie.fixtures.cookie_preferences.text.1', $locale);
            $pageparts['legal_main'][] = $this->createHeaderPagePart('kuma.cookie.fixtures.cookie_preferences.headers.1', $locale);
            $pageparts['legal_main'][] = $this->createTextPagePart('kuma.cookie.fixtures.cookie_preferences.text.2', $locale);
            $pageparts['legal_main'][] = $this->createTipPagePart('kuma.cookie.fixtures.cookie_preferences.tip.1', $locale);
            $pageparts['legal_main'][] = $this->createIconCenteredPagePart(
                'kuma.cookie.fixtures.cookie_preferences.icon_text.1.title',
                'kuma.cookie.fixtures.cookie_preferences.icon_text.1.subtitle',
                'kuma.cookie.fixtures.cookie_preferences.icon_text.1.text',
                $this->mediaCreator->createFile($imgDir.'label.svg', $folder->getId()),
                $locale
            );
            $pageparts['legal_main'][] = $this->createHeaderPagePart('kuma.cookie.fixtures.cookie_preferences.headers.2', $locale);
            $pageparts['legal_main'][] = $this->pagePartCreator->getCreatorArgumentsForPagePartAndProperties(
                '{{ namespace }}\Entity\PageParts\LegalCookiesPagePart',
                []
            );
            $pageparts['legal_main'][] = $this->createIconCenteredPagePart(
                'kuma.cookie.fixtures.cookie_preferences.icon_text.2.title',
                'kuma.cookie.fixtures.cookie_preferences.icon_text.2.subtitle',
                'kuma.cookie.fixtures.cookie_preferences.icon_text.2.text',
                $this->mediaCreator->createFile($imgDir.'cookie_monster.svg', $folder->getId()),
                $locale
            );
            $pageparts['legal_main'][] = $this->createHeaderPagePart('kuma.cookie.fixtures.cookie_preferences.headers.3', $locale);
            $pageparts['legal_main'][] = $this->createTextPagePart('kuma.cookie.fixtures.cookie_preferences.text.3', $locale);
            $pageparts['legal_main'][] = $this->createHeaderPagePart('kuma.cookie.fixtures.cookie_preferences.headers.4', $locale);
            $pageparts['legal_main'][] = $this->createTextPagePart('kuma.cookie.fixtures.cookie_preferences.text.4', $locale);
            $pageparts['legal_main'][] = $this->createHeaderPagePart('kuma.cookie.fixtures.cookie_preferences.headers.5', $locale);
            $pageparts['legal_main'][] = $this->createTextPagePart('kuma.cookie.fixtures.cookie_preferences.text.5', $locale);
            $pageparts['legal_main'][] = $this->createTipPagePart('kuma.cookie.fixtures.cookie_preferences.tip.2', $locale);
            $pageparts['legal_main'][] = $this->createHeaderPagePart('kuma.cookie.fixtures.cookie_preferences.headers.6', $locale);
            $pageparts['legal_main'][] = $this->createTextPagePart('kuma.cookie.fixtures.cookie_preferences.text.6', $locale);

            $this->pagePartCreator->addPagePartsToPage($node, $pageparts, $locale);
        }
    }

    /**
     * @param Node $node
     */
    private function addContactPageParts(Node $node)
    {
        foreach ($this->requiredLocales as $locale) {
            $pageparts = [];

            $pageparts['legal_header'][] = $this->pagePartCreator->getCreatorArgumentsForPagePartAndProperties(
                '{{ namespace }}\Entity\PageParts\TextPagePart',
                [
                    'setContent' => $this->translator->trans('kuma.cookie.fixtures.contact.text.1', [], null, $locale),
                ]
            );
            $pageparts['legal_header'][] = $this->pagePartCreator->getCreatorArgumentsForPagePartAndProperties(
                '{{ namespace }}\Entity\PageParts\TextPagePart',
                [
                    'setContent' => $this->translator->trans('kuma.cookie.fixtures.contact.text.2', [], null, $locale),
                ]
            );
            $pageparts['legal_header'][] = $this->pagePartCreator->getCreatorArgumentsForPagePartAndProperties(
                '{{ namespace }}\Entity\PageParts\TextPagePart',
                [
                    'setContent' => $this->translator->trans('kuma.cookie.fixtures.contact.text.3', [], null, $locale),
                ]
            );

            $this->pagePartCreator->addPagePartsToPage($node, $pageparts, $locale);
        }
    }

    /**
     * @param string $content
     * @param string $locale
     *
     * @return callable
     */
    private function createTextPagePart($content, $locale)
    {
        return $this->pagePartCreator->getCreatorArgumentsForPagePartAndProperties(
            '{{ namespace }}\Entity\PageParts\TextPagePart',
            [
                'setContent' => $this->translator->trans($content, [], null, $locale),
            ]
        );
    }

    /**
     * @param string $content
     * @param string $locale
     * @param int    $niv
     *
     * @return callable
     */
    private function createHeaderPagePart($content, $locale, $niv = 2)
    {
        return $this->pagePartCreator->getCreatorArgumentsForPagePartAndProperties(
            '{{ namespace }}\Entity\PageParts\HeaderPagePart',
            [
                'setNiv' => $niv,
                'setTitle' => $this->translator->trans($content, [], null, $locale),
            ]
        );
    }

    /**
     * @param string $content
     * @param string $locale
     * @param int    $niv
     *
     * @return callable
     */
    private function createTipPagePart($content, $locale)
    {
        return $this->pagePartCreator->getCreatorArgumentsForPagePartAndProperties(
            '{{ namespace }}\Entity\PageParts\LegalTipPagePart',
            [
                'setContent' => $this->translator->trans($content, [], null, $locale),
            ]
        );
    }

    /**
     * @param string $title
     * @param string $subtitle
     * @param string $content
     * @param string $icon
     * @param string $locale
     *
     * @return callable
     */
    private function createIconCenteredPagePart($title, $subtitle, $content, $icon, $locale)
    {
        return $this->pagePartCreator->getCreatorArgumentsForPagePartAndProperties(
            '{{ namespace }}\Entity\PageParts\LegalIconTextPagePart',
            [
                'setTitle' => $this->translator->trans($title, [], null, $locale),
                'setSubtitle' => $this->translator->trans($subtitle, [], null, $locale),
                'setContent' => $this->translator->trans($content, [], null, $locale),
                'setIcon' => $icon,
            ]
        );
    }

    /**
     * @param Node   $parent
     * @param string $title
     * @param string $internalName
     * @param int    $weight
     *
     * @return Node
     */
    private function createLegalPage(Node $parent, $title, $internalName, $weight)
    {
        $legalPage = new LegalPage();
        $legalPage->setTitle($title);

        $translations = [];
        foreach ($this->requiredLocales as $locale) {
            $translations[] = [
                'language' => $locale,
                'callback' => function (LegalPage $page, NodeTranslation $translation, $seo) use ($title, $weight, $locale) {
                    $translatedTitle = $this->translator->trans($title, [], null, $locale);
                    $translation->setTitle($translatedTitle);
                    $translation->setSlug($this->slugifier->slugify($translatedTitle));
                    $translation->setWeight($weight);
                },
            ];
        }

        $options = [
            'parent' => $parent,
            'page_internal_name' => $internalName,
            'set_online' => true,
            'hidden_from_nav' => false,
            'creator' => self::ADMIN_USERNAME,
        ];

        return $this->pageCreator->createPage($legalPage, $translations, $options);
    }

    /**
     * Get the order of this fixture
     *
     * @return int
     */
    public function getOrder()
    {
        return 52;
    }

    /**
     * @param ContainerInterface|null $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Check if we already have a page creator user, if not create one.
     */
    private function checkPageCreator()
    {
        $creator = $this->em->getRepository('KunstmaanAdminBundle:User')->findOneBy(['username' => self::ADMIN_USERNAME]);

        if (null === $creator) {
            $user = new User();
            $user->setEmail(sprintf('%s@admin.com', self::ADMIN_USERNAME));
            $user->setUsername(self::ADMIN_USERNAME);
            $user->setEnabled(1);
            $user->setPlainPassword($this->container->getParameter('kernel.secret'));

            $this->container->get('kunstmaan_admin.user_manager')->updateUser($user);

            $user->setPasswordChanged(true);

            $this->em->persist($user);
            $this->em->flush();
        }
    }

    public static function getGroups(): array
    {
        return ['cookie-bundle'];
    }
}
