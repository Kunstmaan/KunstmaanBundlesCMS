<?php

namespace Kunstmaan\GeneratorBundle\Generator;

use Kunstmaan\GeneratorBundle\Helper\GeneratorUtils;
use Kunstmaan\GeneratorBundle\Helper\CommandAssistant;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Generates an Article section
 */
class ArticleGenerator extends KunstmaanGenerator
{
    /**
     * @var BundleInterface
     */
    private $bundle;

    /**
     * @var string
     */
    private $entity;

    /**
     * @var array
     */
    private $parentPages = array();

    /**
     * ArticleGenerator constructor.
     *
     * @param Filesystem         $filesystem
     * @param RegistryInterface  $registry
     * @param string             $skeletonDir
     * @param array              $parentPages
     * @param CommandAssistant   $assistant
     * @param ContainerInterface $container
     */
    public function __construct(Filesystem $filesystem, RegistryInterface $registry, $skeletonDir, array $parentPages, CommandAssistant $assistant, ContainerInterface $container)
    {
        parent::__construct($filesystem, $registry, $skeletonDir, $assistant, $container);
        $this->parentPages = $parentPages;
    }

    /**
     * @param BundleInterface $bundle         The bundle
     * @param string          $entity
     * @param string          $prefix         The prefix
     * @param bool            $multilanguage
     * @param bool            $usesAuthor
     * @param bool            $usesCategories
     * @param bool            $usesTags
     * @param bool            $dummydata
     */
    public function generate(BundleInterface $bundle, $entity, $prefix, $multilanguage, $usesAuthor, $usesCategories, $usesTags, $bundleWithHomePage, $dummydata)
    {
        $this->bundle = $bundle;
        $this->entity = $entity;

        $parameters = array(
            'namespace' => $bundle->getNamespace(),
            'bundle' => $bundle,
            'prefix' => GeneratorUtils::cleanPrefix($prefix),
            'entity_class' => $entity,
            'uses_author' => $usesAuthor,
            'uses_category' => $usesCategories,
            'uses_tag' => $usesTags,
        );

        $this->generateEntities($parameters);
        $this->generateRepositories($parameters);
        $this->generateForm($parameters);
        $this->generateAdminList($parameters);
        $this->generateController($parameters);
        $this->generatePageTemplateConfigs($parameters);
        $this->generateTemplates($parameters, $bundleWithHomePage);
        $this->generateRouting($parameters, $multilanguage);
        $this->generateMenu($parameters);
        $this->generateServices($parameters);
        $this->updateParentPages();
        if ($dummydata) {
            $this->generateFixtures($parameters);
        }
    }

    /**
     * @param array $parameters The template parameters
     */
    public function generateServices(array $parameters)
    {
        $dirPath = sprintf('%s/Resources/config', $this->bundle->getPath());
        $skeletonDir = sprintf('%s/Resources/config', $this->skeletonDir);
        $this->setSkeletonDirs(array($skeletonDir));

        $routing = $this->render('/services.yml', $parameters);
        GeneratorUtils::append($routing, $dirPath . '/services.yml');

        $this->assistant->writeLine('Generating services : <info>OK</info>');
    }

    /**
     * @param array $parameters The template parameters
     */
    public function generateMenu(array $parameters)
    {
        $relPath = '/Helper/Menu/';
        $sourceDir = $this->skeletonDir.$relPath;
        $targetDir = $this->bundle->getPath().$relPath;

        $filename = 'MenuAdaptor.php';
        $this->renderSingleFile($sourceDir, $targetDir, $filename, $parameters, false, $this->entity . 'MenuAdaptor.php');

        $dirPath = sprintf('%s/Helper/Menu', $this->bundle->getPath());
        $skeletonDir = sprintf('%s/Helper/Menu', $this->skeletonDir);
        $this->setSkeletonDirs(array($skeletonDir));
        $partial = '';
        $twigParameters = $parameters;

        if ($parameters['uses_author']) {
            $twigParameters['type'] = 'Author';
            $partial .= $this->render('/MenuAdaptorPartial.php.twig', $twigParameters);
        }
        if ($parameters['uses_category']) {
            $twigParameters['type'] = 'Category';
            $partial .= $this->render('/MenuAdaptorPartial.php.twig', $twigParameters);
        }
        if ($parameters['uses_tag']) {
            $twigParameters['type'] = 'Tag';
            $partial .= $this->render('/MenuAdaptorPartial.php.twig', $twigParameters);
        }
        GeneratorUtils::replace('//%menuAdaptorPartial.php.twig%', $partial, $dirPath . '/' . $this->entity . $filename);

        $this->assistant->writeLine('Generating menu : <info>OK</info>');
    }

    /**
     * @param array $parameters    The template parameters
     * @param bool  $multilanguage
     */
    public function generateRouting(array $parameters, $multilanguage)
    {
        $dirPath = sprintf('%s/Resources/config', $this->bundle->getPath());
        $skeletonDir = sprintf('%s/Resources/config', $this->skeletonDir);
        $this->setSkeletonDirs(array($skeletonDir));

        $routingSource = $multilanguage ? 'routing_multilanguage' : 'routing_singlelanguage';
        $routing = $this->render('/' . $routingSource . '.yml', $parameters);

        GeneratorUtils::append($routing, $dirPath . '/routing.yml');

        $twigParameters = $parameters;

        if ($parameters['uses_author']) {
            $twigParameters['type'] = 'Author';
            $routing = $this->render('/routing_partial.yml', $twigParameters);
            GeneratorUtils::append($routing, $dirPath . '/routing.yml');
        }

        if ($parameters['uses_category']) {
            $twigParameters['type'] = 'Category';
            $routing = $this->render('/routing_partial.yml', $twigParameters);
            GeneratorUtils::append($routing, $dirPath . '/routing.yml');
        }
        if ($parameters['uses_tag']) {
            $twigParameters['type'] = 'Tag';
            $routing = $this->render('/routing_partial.yml', $twigParameters);
            GeneratorUtils::append($routing, $dirPath . '/routing.yml');
        }

        $this->assistant->writeLine('Generating routing : <info>OK</info>');
    }

    /**
     * @param array  $parameters         The template parameters
     * @param string $bundleWithHomePage
     */
    public function generateTemplates(array $parameters, $bundleWithHomePage)
    {
        $relPath = '/Resources/views/Pages/%sOverviewPage/';
        $sourceDir = $this->skeletonDir . sprintf($relPath, '');
        $targetDir = $this->bundle->getPath() . sprintf($relPath, $this->entity);
        $twigParameters = $parameters;
        $twigParameters['bundleWithHomePage'] = $bundleWithHomePage;

        $this->renderSingleFile($sourceDir, $targetDir, 'pagetemplate.html.twig', $twigParameters);
        $this->renderSingleFile($sourceDir, $targetDir, 'view.html.twig', $twigParameters);

        if ($twigParameters['uses_category']) {
            $this->renderSingleFile($sourceDir, $targetDir, '_filter-category.html.twig', $twigParameters);
            $this->renderSingleFile($sourceDir, $targetDir, '_list-category.html.twig', $twigParameters);
        }

        if ($twigParameters['uses_tag']) {
            $this->renderSingleFile($sourceDir, $targetDir, '_filter-tag.html.twig', $twigParameters);
            $this->renderSingleFile($sourceDir, $targetDir, '_list-tag.html.twig', $twigParameters);
        }

        $relPath = '/Resources/views/Pages/%sPage/';
        $sourceDir = $this->skeletonDir . sprintf($relPath, '');
        $targetDir = $this->bundle->getPath() . sprintf($relPath, $this->entity);

        $this->renderSingleFile($sourceDir, $targetDir, 'pagetemplate.html.twig', $twigParameters);
        $this->renderSingleFile($sourceDir, $targetDir, 'view.html.twig', $twigParameters);

        $relPath = '/Resources/views/AdminList/%sPageAdminList/';
        $sourceDir = $this->skeletonDir . sprintf($relPath, '');
        $targetDir = $this->bundle->getPath() . sprintf($relPath, $this->entity);

        $this->renderSingleFile($sourceDir, $targetDir, 'list.html.twig', $twigParameters);

        $this->assistant->writeLine('Generating twig templates : <info>OK</info>');
    }

    /**
     * @param array $parameters The template parameters
     */
    public function generateController(array $parameters)
    {
        $relPath = '/Controller/';
        $sourceDir = $this->skeletonDir.$relPath;
        $targetDir = $this->bundle->getPath().$relPath;

        $filename = 'PageAdminListController.php';
        $this->renderSingleFile($sourceDir, $targetDir, $filename, $parameters, false, $this->entity . $filename);

        if ($parameters['uses_author']) {
            $filename = 'AuthorAdminListController.php';
            $this->renderSingleFile($sourceDir, $targetDir, $filename, $parameters, false, $this->entity . $filename);
        }

        if ($parameters['uses_category']) {
            $filename = 'CategoryAdminListController.php';
            $this->renderSingleFile($sourceDir, $targetDir, $filename, $parameters, false, $this->entity . $filename);
        }

        if ($parameters['uses_tag']) {
            $filename = 'TagAdminListController.php';
            $this->renderSingleFile($sourceDir, $targetDir, $filename, $parameters, false, $this->entity . $filename);
        }

        $filename = 'ArticleController.php';
        $this->renderSingleFile($sourceDir, $targetDir, $filename, $parameters, false, $this->entity . $filename);

        $this->assistant->writeLine('Generating controllers : <info>OK</info>');
    }

    /**
     * @param array $parameters The template parameters
     */
    public function generatePageTemplateConfigs(array $parameters)
    {
        $relPath = '/Resources/config/pagetemplates/';
        $sourceDir = $this->skeletonDir.$relPath;
        $targetDir = $this->bundle->getPath().$relPath;

        $this->renderSingleFile($sourceDir, $targetDir, 'page.yml', $parameters, false, strtolower($this->entity) . 'page.yml');
        $this->renderSingleFile($sourceDir, $targetDir, 'overviewpage.yml', $parameters, false, strtolower($this->entity) . 'overviewpage.yml');

        $relPath = '/Resources/config/pageparts/';
        $sourceDir = $this->skeletonDir.$relPath;
        $targetDir = $this->bundle->getPath().$relPath;

        $this->renderSingleFile($sourceDir, $targetDir, 'main.yml', $parameters, false, strtolower($this->entity) . 'main.yml');

        $this->assistant->writeLine('Generating PagePart configurators : <info>OK</info>');
    }

    /**
     * @param array $parameters The template parameters
     */
    public function generateAdminList(array $parameters)
    {
        $relPath = '/AdminList/';
        $sourceDir = $this->skeletonDir.$relPath;
        $targetDir = $this->bundle->getPath().$relPath;

        $filename = 'PageAdminListConfigurator.php';
        $this->renderSingleFile($sourceDir, $targetDir, $filename, $parameters, false, $this->entity . $filename);

        if ($parameters['uses_author']) {
            $filename = 'AuthorAdminListConfigurator.php';
            $this->renderSingleFile($sourceDir, $targetDir, $filename, $parameters, false, $this->entity . $filename);
        }

        if ($parameters['uses_category']) {
            $filename = 'CategoryAdminListConfigurator.php';
            $this->renderSingleFile($sourceDir, $targetDir, $filename, $parameters, false, $this->entity . $filename);
        }

        if ($parameters['uses_tag']) {
            $filename = 'TagAdminListConfigurator.php';
            $this->renderSingleFile($sourceDir, $targetDir, $filename, $parameters, false, $this->entity . $filename);
        }

        $this->assistant->writeLine('Generating AdminList configurators : <info>OK</info>');
    }

    /**
     * @param array $parameters The template parameters
     */
    public function generateForm(array $parameters)
    {
        $relPath = '/Form/Pages/';
        $sourceDir = $this->skeletonDir.$relPath;
        $targetDir = $this->bundle->getPath().$relPath;

        $filename = 'OverviewPageAdminType.php';
        $this->renderSingleFile($sourceDir, $targetDir, $filename, $parameters, false, $this->entity . $filename);

        $filename = 'PageAdminType.php';
        $this->renderSingleFile($sourceDir, $targetDir, $filename, $parameters, false, $this->entity . $filename);

        $relPath = '/Form/';
        $sourceDir = $this->skeletonDir.$relPath;
        $targetDir = $this->bundle->getPath().$relPath;

        if ($parameters['uses_author']) {
            $filename = 'AuthorAdminType.php';
            $this->renderSingleFile($sourceDir, $targetDir, $filename, $parameters, false, $this->entity . $filename);
        }

        if ($parameters['uses_category']) {
            $filename = 'CategoryAdminType.php';
            $this->renderSingleFile($sourceDir, $targetDir, $filename, $parameters, false, $this->entity . $filename);
        }

        if ($parameters['uses_tag']) {
            $filename = 'TagAdminType.php';
            $this->renderSingleFile($sourceDir, $targetDir, $filename, $parameters, false, $this->entity . $filename);
        }

        $dirPath = sprintf('%s/Form/Pages', $this->bundle->getPath());
        $skeletonDir = sprintf('%s/Form/Pages', $this->skeletonDir);
        $this->setSkeletonDirs(array($skeletonDir));
        $partial = '';
        $twigParameters = $parameters;

        if ($parameters['uses_author']) {
            $twigParameters['type'] = 'Author';
            $twigParameters['pluralType'] = 'authors';
            $partial .= $this->render('/PageAdminTypePartial.php.twig', $twigParameters);
        }

        if ($parameters['uses_category']) {
            $twigParameters['type'] = 'Category';
            $twigParameters['pluralType'] = 'categories';
            $partial .= $this->render('/PageAdminTypePartial.php.twig', $twigParameters);
        }

        if ($parameters['uses_tag']) {
            $twigParameters['type'] = 'Tag';
            $twigParameters['pluralType'] = 'tags';
            $partial .= $this->render('/PageAdminTypePartial.php.twig', $twigParameters);
        }
        GeneratorUtils::replace('//%PageAdminTypePartial.php.twig%', $partial, $dirPath . '/' . $this->entity . 'PageAdminType.php');

        $this->assistant->writeLine('Generating forms : <info>OK</info>');
    }

    /**
     * @param array $parameters The template parameters
     */
    public function generateRepositories(array $parameters)
    {
        $relPath = '/Repository/';
        $sourceDir = $this->skeletonDir.$relPath;
        $targetDir = $this->bundle->getPath().$relPath;

        $filename = 'PageRepository.php';
        $this->renderSingleFile($sourceDir, $targetDir, $filename, $parameters, false, $this->entity . $filename);

        $filename = 'OverviewPageRepository.php';
        $this->renderSingleFile($sourceDir, $targetDir, $filename, $parameters, false, $this->entity . $filename);

        $dirPath = sprintf('%s/Repository', $this->bundle->getPath());
        $skeletonDir = sprintf('%s/Repository', $this->skeletonDir);
        $this->setSkeletonDirs(array($skeletonDir));

        $repository = $this->render('/PageRepositoryPartial.php.twig', $parameters);
        GeneratorUtils::replace('%PageRepository.php%', $repository, $dirPath . '/' . $this->entity . 'PageRepository.php');

        $this->assistant->writeLine('Generating repositories : <info>OK</info>');
    }

    /**
     * @param array $parameters The template parameters
     */
    public function generateEntities(array $parameters)
    {
        $relPath = '/Entity/Pages/';
        $sourceDir = $this->skeletonDir.$relPath;
        $targetDir = $this->bundle->getPath().$relPath;

        $filename = 'OverviewPage.php';
        $this->renderSingleFile($sourceDir, $targetDir, $filename, $parameters, false, $this->entity . $filename);

        $filename = 'Page.php';
        $this->renderSingleFile($sourceDir, $targetDir, $filename, $parameters, false, $this->entity . $filename);

        $relPath = '/Entity/';
        $sourceDir = $this->skeletonDir.$relPath;
        $targetDir = $this->bundle->getPath().$relPath;

        if ($parameters['uses_author']) {
            $filename = 'Author.php';
            $this->renderSingleFile($sourceDir, $targetDir, $filename, $parameters, false, $this->entity . $filename);
        }

        if ($parameters['uses_category']) {
            $filename = 'Category.php';
            $this->renderSingleFile($sourceDir, $targetDir, $filename, $parameters, false, $this->entity . $filename);
        }

        if ($parameters['uses_tag']) {
            $filename = 'Tag.php';
            $this->renderSingleFile($sourceDir, $targetDir, $filename, $parameters, false, $this->entity . $filename);
        }

        $dirPath = sprintf('%s/Entity/Pages', $this->bundle->getPath());
        $skeletonDir = sprintf('%s/Entity/Pages', $this->skeletonDir);
        $this->setSkeletonDirs(array($skeletonDir));
        $partial = '';
        $partialFunctions = '';
        $constructor = '';
        $twigParameters = $parameters;

        if ($parameters['uses_author']) {
            $twigParameters['type'] = 'Author';
            $twigParameters['pluralType'] = 'authors';
            $partial .= $this->render('/PagePartial.php.twig', $twigParameters);
            $partialFunctions .= $this->render('/PagePartialFunctions.php.twig', $twigParameters);
        }

        if ($parameters['uses_category']) {
            $twigParameters['type'] = 'Category';
            $twigParameters['pluralType'] = 'categories';
            $partial .= $this->render('/PagePartial.php.twig', $twigParameters);
            $partialFunctions .= $this->render('/PagePartialFunctions.php.twig', $twigParameters);
            $constructor .= '$this->categories = new ArrayCollection();' . "\n";
        }

        if ($parameters['uses_tag']) {
            $twigParameters['type'] = 'Tag';
            $twigParameters['pluralType'] = 'tags';
            $partial .= $this->render('/PagePartial.php.twig', $twigParameters);
            $partialFunctions .= $this->render('/PagePartialFunctions.php.twig', $twigParameters);
            $constructor .= '$this->tags = new ArrayCollection();' . "\n";
        }
        GeneratorUtils::replace('//%PagePartial.php.twig%', $partial, $dirPath . '/' . $this->entity . 'Page.php');
        GeneratorUtils::replace('//%PagePartialFunctions.php.twig%', $partialFunctions, $dirPath . '/' . $this->entity . 'Page.php');
        GeneratorUtils::replace('//%constructor%', $constructor, $dirPath . '/' . $this->entity . 'Page.php');

        $this->assistant->writeLine('Generating entities : <info>OK</info>');
    }

    /**
     * @param array $parameters The template parameters
     */
    public function generateFixtures(array $parameters)
    {
        $relPath = '/DataFixtures/ORM/ArticleGenerator/';
        $sourceDir = $this->skeletonDir.$relPath;
        $targetDir = $this->bundle->getPath().$relPath;

        $filename = 'ArticleFixtures.php';
        $this->renderSingleFile($sourceDir, $targetDir, $filename, $parameters, false, $this->entity . $filename);

        $this->assistant->writeLine('Generating fixtures : <info>OK</info>');
    }

    /**
     * Update the getPossibleChildTypes function of the parent Page classes
     */
    public function updateParentPages()
    {
        $phpCode = "            array(\n";
        $phpCode .= "                'name' => '" . $this->entity . "OverviewPage',\n";
        $phpCode .= "                'class'=> '" . $this->bundle->getNamespace() . '\\Entity\\Pages\\' . $this->entity . "OverviewPage'\n";
        $phpCode .= '            ),';

        // When there is a BehatTestPage, we should also allow the new page as sub page
        $behatTestPage = $this->bundle->getPath() . '/Entity/Pages/BehatTestPage.php';
        if (file_exists($behatTestPage)) {
            $this->parentPages[] = $behatTestPage;
        }

        foreach ($this->parentPages as $file) {
            $data = file_get_contents($file);
            $data = preg_replace(
                '/(function\s*getPossibleChildTypes\s*\(\)\s*\{\s*return\s*array\s*\()/',
                "$1\n$phpCode",
                $data
            );
            file_put_contents($file, $data);
        }
    }
}
