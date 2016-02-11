<?php

namespace Kunstmaan\GeneratorBundle\Generator;

use Kunstmaan\GeneratorBundle\Helper\GeneratorUtils;
use Sensio\Bundle\GeneratorBundle\Generator\Generator;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Generates an Article section
 */
class ArticleGenerator extends Generator
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var string
     */
    private $skeletonDir;

    /**
     * @var string
     */
    private $fullSkeletonDir;

    /**
     * @var bool
     */
    private $multilanguage;

    /**
     * @param Filesystem $filesystem    The filesytem
     * @param string     $skeletonDir   The skeleton directory
     * @param bool       $multilanguage If the site is multilanguage
     */
    public function __construct(Filesystem $filesystem, $skeletonDir, $multilanguage)
    {
        $this->filesystem = $filesystem;
        $this->skeletonDir = $skeletonDir;
        $this->fullSkeletonDir = __DIR__.'/../Resources/SensioGeneratorBundle/skeleton' . $skeletonDir;
        $this->multilanguage = $multilanguage;
    }

    /**
     * @param Bundle          $bundle The bundle
     * @param string          $entity
     * @param string          $prefix The prefix
     * @param bool            $dummydata
     * @param OutputInterface $output
     */
    public function generate(Bundle $bundle, $entity, $prefix, $dummydata, OutputInterface $output)
    {
        $parameters = array(
            'namespace'         => $bundle->getNamespace(),
            'bundle'            => $bundle,
            'prefix'            => GeneratorUtils::cleanPrefix($prefix),
            'entity_class'      => $entity,
        );

        $this->generateEntities($bundle, $entity, $parameters, $output);
        $this->generateRepositories($bundle, $entity, $parameters, $output);
        $this->generateForm($bundle, $entity, $parameters, $output);
        $this->generateAdminList($bundle, $entity, $parameters, $output);
        $this->generateController($bundle, $entity, $parameters, $output);
	$this->generatePageTemplateConfigs($bundle, $entity, $parameters, $output);
        $this->generateTemplates($bundle, $entity, $parameters, $output);
        $this->generateRouting($bundle, $entity, $parameters, $output);
        $this->generateMenu($bundle, $entity, $parameters, $output);
        $this->generateServices($bundle, $entity, $parameters, $output);
        if ($dummydata) {
            $this->generateFixtures($bundle, $entity, $parameters, $output);
        }
    }

    /**
     * @param Bundle          $bundle     The bundle
     * @param string          $entity     The name of the entity
     * @param array           $parameters The template parameters
     * @param OutputInterface $output
     */
    public function generateServices(Bundle $bundle, $entity, array $parameters, OutputInterface $output)
    {
        $dirPath = sprintf("%s/Resources/config", $bundle->getPath());
        $skeletonDir = sprintf("%s/Resources/config", $this->skeletonDir);
        $routing = $this->render($skeletonDir . '/services.yml', $parameters);
        GeneratorUtils::append($routing, $dirPath . '/services.yml');

        $output->writeln('Generating services : <info>OK</info>');
    }

    /**
     * @param Bundle          $bundle     The bundle
     * @param string          $entity     The name of the entity
     * @param array           $parameters The template parameters
     * @param OutputInterface $output
     *
     * @throws \RuntimeException
     */
    public function generateMenu(Bundle $bundle, $entity, array $parameters, OutputInterface $output)
    {
        $dirPath = sprintf("%s/Helper/Menu", $bundle->getPath());
        $skeletonDir = sprintf("%s/Helper/Menu", $this->skeletonDir);

        try {
            $this->generateSkeletonBasedClass($skeletonDir, $entity, $dirPath, 'MenuAdaptor', $parameters);
        } catch (\Exception $error) {
            throw new \RuntimeException($error->getMessage());
        }

        $output->writeln('Generating menu : <info>OK</info>');
    }

    /**
     * @param Bundle          $bundle     The bundle
     * @param string          $entity     The name of the entity
     * @param array           $parameters The template parameters
     * @param OutputInterface $output
     */
    public function generateRouting(Bundle $bundle, $entity, array $parameters, OutputInterface $output)
    {
        $dirPath = sprintf("%s/Resources/config", $bundle->getPath());
        $skeletonDir = sprintf("%s/Resources/config", $this->skeletonDir);
	if ($this->multilanguage) {
            $routing = $this->render($skeletonDir . '/routing_multilanguage.yml', $parameters);
        } else {
            $routing = $this->render($skeletonDir . '/routing_singlelanguage.yml', $parameters);
        }
        GeneratorUtils::append($routing, $dirPath . '/routing.yml');

        $output->writeln('Generating routing : <info>OK</info>');
    }

    /**
     * @param Bundle          $bundle     The bundle
     * @param string          $entity     The name of the entity
     * @param array           $parameters The template parameters
     * @param OutputInterface $output
     */
    public function generateTemplates(Bundle $bundle, $entity, array $parameters, OutputInterface $output)
    {
        $dirPath = sprintf("%s/Resources/views", $bundle->getPath());
        $skeletonDir = sprintf("%s/Resources/views", $this->skeletonDir);
        $fullSkeletonDir = sprintf("%s/Resources/views", $this->fullSkeletonDir);

	$this->filesystem->copy($fullSkeletonDir . '/OverviewPage/view.html.twig', $dirPath . '/Pages/' . $entity . 'OverviewPage/view.html.twig', true);
	GeneratorUtils::prepend("{% extends '" . $bundle->getName() .":Layout:layout.html.twig' %}\n", $dirPath . '/Pages/' . $entity . 'OverviewPage/view.html.twig');
	$this->filesystem->copy($fullSkeletonDir . '/OverviewPage/pagetemplate.html.twig', $dirPath . '/Pages/' . $entity . 'OverviewPage/pagetemplate.html.twig', true);

	$this->filesystem->copy($fullSkeletonDir . '/Page/view.html.twig', $dirPath . '/Pages/' . $entity . 'Page/view.html.twig', true);
	GeneratorUtils::prepend("{% extends '" . $bundle->getName() .":Layout:layout.html.twig' %}\n", $dirPath . '/Pages/' . $entity . 'Page/view.html.twig');
	$this->filesystem->copy($fullSkeletonDir . '/Page/pagetemplate.html.twig', $dirPath . '/Pages/' . $entity . 'Page/pagetemplate.html.twig', true);

	$this->renderFile($skeletonDir . '/PageAdminList/list.html.twig', $dirPath . '/AdminList/' . '/' . $entity . 'PageAdminList/list.html.twig', $parameters);

        $output->writeln('Generating twig templates : <info>OK</info>');
    }

    /**
     * @param Bundle          $bundle     The bundle
     * @param string          $entity     The name of the entity
     * @param array           $parameters The template parameters
     * @param OutputInterface $output
     *
     * @throws \RuntimeException
     */
    public function generateController(Bundle $bundle, $entity, array $parameters, OutputInterface $output)
    {
	$dirPath = sprintf('%s/Controller', $bundle->getPath());
	$skeletonDir = sprintf('%s/Controller', $this->skeletonDir);

        try {
            $this->generateSkeletonBasedClass($skeletonDir, $entity, $dirPath, 'PageAdminListController', $parameters);
        } catch (\Exception $error) {
            throw new \RuntimeException($error->getMessage());
        }

        try {
            $this->generateSkeletonBasedClass($skeletonDir, $entity, $dirPath, 'AuthorAdminListController', $parameters);
        } catch (\Exception $error) {
            throw new \RuntimeException($error->getMessage());
        }

        $output->writeln('Generating controllers : <info>OK</info>');
    }

    /**
     * @param Bundle          $bundle     The bundle
     * @param string          $entity     The name of the entity
     * @param array           $parameters The template parameters
     * @param OutputInterface $output
     *
     * @throws \RuntimeException
     */
    public function generatePageTemplateConfigs(Bundle $bundle, $entity, array $parameters, OutputInterface $output)
    {
	$dirPath = sprintf('%s/Resources/config/pagetemplates', $bundle->getPath());
	$skeletonDir = sprintf('%s/Resources/config/pagetemplates', $this->skeletonDir);

	$this->renderFile($skeletonDir . '/page.yml', $dirPath . '/'.strtolower($entity).'page.yml', $parameters);
	$this->renderFile($skeletonDir . '/overviewpage.yml', $dirPath . '/'.strtolower($entity).'overviewpage.yml', $parameters);

        $output->writeln('Generating PagePart configurators : <info>OK</info>');
    }

    /**
     * @param Bundle          $bundle     The bundle
     * @param string          $entity     The name of the entity
     * @param array           $parameters The template parameters
     * @param OutputInterface $output
     *
     * @throws \RuntimeException
     */
    public function generateAdminList(Bundle $bundle, $entity, array $parameters, OutputInterface $output)
    {
	$dirPath = sprintf("%s/AdminList/", $bundle->getPath());
        $skeletonDir = sprintf("%s/AdminList", $this->skeletonDir);

        try {
            $this->generateSkeletonBasedClass($skeletonDir, $entity, $dirPath, 'PageAdminListConfigurator', $parameters);
        } catch (\Exception $error) {
            throw new \RuntimeException($error->getMessage());
        }

        try {
            $this->generateSkeletonBasedClass($skeletonDir, $entity, $dirPath, 'AuthorAdminListConfigurator', $parameters);
        } catch (\Exception $error) {
            throw new \RuntimeException($error->getMessage());
        }

        $output->writeln('Generating AdminList configurators : <info>OK</info>');
    }

    /**
     * @param Bundle          $bundle     The bundle
     * @param string          $entity     The name of the entity
     * @param array           $parameters The template parameters
     * @param OutputInterface $output
     *
     * @throws \RuntimeException
     */
    public function generateForm(Bundle $bundle, $entity, array $parameters, OutputInterface $output)
    {
	$dirPath = sprintf("%s/Form/Pages/", $bundle->getPath());
        $skeletonDir = sprintf("%s/Form", $this->skeletonDir);

        try {
            $this->generateSkeletonBasedClass($skeletonDir, $entity, $dirPath, 'OverviewPageAdminType', $parameters);
        } catch (\Exception $error) {
            throw new \RuntimeException($error->getMessage());
        }

        try {
            $this->generateSkeletonBasedClass($skeletonDir, $entity, $dirPath, 'PageAdminType', $parameters);
        } catch (\Exception $error) {
            throw new \RuntimeException($error->getMessage());
        }

	$dirPath = sprintf("%s/Form/", $bundle->getPath());

        try {
            $this->generateSkeletonBasedClass($skeletonDir, $entity, $dirPath, 'AuthorAdminType', $parameters);
        } catch (\Exception $error) {
            throw new \RuntimeException($error->getMessage());
        }

        $output->writeln('Generating forms : <info>OK</info>');
    }

    /**
     * @param Bundle          $bundle     The bundle
     * @param string          $entity     The name of the entity
     * @param array           $parameters The template parameters
     * @param OutputInterface $output
     *
     * @throws \RuntimeException
     */
    public function generateRepositories(Bundle $bundle, $entity, array $parameters, OutputInterface $output)
    {
	$dirPath = sprintf("%s/Repository/", $bundle->getPath());
        $skeletonDir = sprintf("%s/Repository", $this->skeletonDir);

        try {
            $this->generateSkeletonBasedClass($skeletonDir, $entity, $dirPath, 'PageRepository', $parameters);
        } catch (\Exception $error) {
            throw new \RuntimeException($error->getMessage());
        }

        try {
	    $this->generateSkeletonBasedClass($skeletonDir, $entity, $dirPath, 'OverviewPageRepository', $parameters);
        } catch (\Exception $error) {
            throw new \RuntimeException($error->getMessage());
        }

        $output->writeln('Generating repositories : <info>OK</info>');
    }

    /**
     * @param Bundle          $bundle     The bundle
     * @param string          $entity     The name of the entity
     * @param array           $parameters The template parameters
     * @param OutputInterface $output
     *
     * @throws \RuntimeException
     */
    public function generateEntities(Bundle $bundle, $entity, array $parameters, OutputInterface $output)
    {
	$dirPath = sprintf("%s/Entity/Pages", $bundle->getPath());
        $skeletonDir = sprintf("%s/Entity", $this->skeletonDir);

        try {
            $this->generateSkeletonBasedClass($skeletonDir, $entity, $dirPath, 'OverviewPage', $parameters);
        } catch (\Exception $error) {
            throw new \RuntimeException($error->getMessage());
        }
        try {
            $this->generateSkeletonBasedClass($skeletonDir, $entity, $dirPath, 'Page', $parameters);
        } catch (\Exception $error) {
            throw new \RuntimeException($error->getMessage());
        }

	$dirPath = sprintf("%s/Entity", $bundle->getPath());

        try {
            $this->generateSkeletonBasedClass($skeletonDir, $entity, $dirPath, 'Author', $parameters);
        } catch (\Exception $error) {
            throw new \RuntimeException($error->getMessage());
        }

        $output->writeln('Generating entities : <info>OK</info>');
    }

    /**
     * @param Bundle          $bundle     The bundle
     * @param string          $entity     The name of the entity
     * @param array           $parameters The template parameters
     * @param OutputInterface $output
     *
     * @throws \RuntimeException
     */
    public function generateFixtures(Bundle $bundle, $entity, array $parameters, OutputInterface $output)
    {
        $dirPath = $bundle->getPath() . '/DataFixtures/ORM/ArticleGenerator';
        $skeletonDir = $this->skeletonDir . '/DataFixtures/ORM/ArticleGenerator';

        try {
            $this->generateSkeletonBasedClass($skeletonDir, $entity, $dirPath, 'ArticleFixtures', $parameters);
        } catch (\Exception $error) {
            throw new \RuntimeException($error->getMessage());
        }

        $output->writeln('Generating fixtures : <info>OK</info>');
    }

    /**
     * @param string $skeletonDir The full dir of the entity skeleton
     * @param string $entity
     * @param string $dirPath     The full fir of where the entity should be created
     * @param string $className   The class name of the entity to create
     * @param array  $parameters  The template parameters
     *
     * @throws \RuntimeException
     */
    private function generateSkeletonBasedClass($skeletonDir, $entity, $dirPath, $className, array $parameters)
    {
        $classPath = sprintf("%s/%s.php", $dirPath, $entity . $className);
        $skeletonPath = sprintf("%s/%s.php", $skeletonDir, $className);
        if (file_exists($classPath)) {
            throw new \RuntimeException(sprintf('Unable to generate the %s class as it already exists under the %s file', $className, $classPath));
        }
        $this->renderFile($skeletonPath, $classPath, $parameters);
    }
}
