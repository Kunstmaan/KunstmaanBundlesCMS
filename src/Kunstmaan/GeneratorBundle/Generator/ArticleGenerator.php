<?php

namespace Kunstmaan\GeneratorBundle\Generator;

use Kunstmaan\GeneratorBundle\Helper\GeneratorUtils;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Generates an Article section
 */
class ArticleGenerator extends \Sensio\Bundle\GeneratorBundle\Generator\Generator
{

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var string
     */
    private $skeletonDir;

    private $fullSkeletonDir;

    /**
     * @param Filesystem $filesystem  The filesytem
     * @param string     $skeletonDir The skeleton directory

     */
    public function __construct(Filesystem $filesystem, $skeletonDir)
    {
        $this->filesystem = $filesystem;
        $this->skeletonDir = $skeletonDir;
        $this->fullSkeletonDir = __DIR__.'/../Resources/SensioGeneratorBundle/skeleton' . $skeletonDir;
    }

    /**
     * @param Bundle          $bundle  The bundle
     * @param string          $entity
     * @param string          $prefix  The prefix
     * @param OutputInterface $output
     */
    public function generate(Bundle $bundle, $entity, $prefix, OutputInterface $output)
    {
        $parameters = array(
            'namespace'         => $bundle->getNamespace(),
            'bundle'            => $bundle,
            'prefix'            => $prefix,
            'entity_class'      => $entity,
        );

        $this->generateEntities($bundle, $entity, $parameters, $output);
        $this->generateRepositories($bundle, $entity, $parameters, $output);
        $this->generateForm($bundle, $entity, $parameters, $output);
        $this->generateAdminList($bundle, $entity, $parameters, $output);
        $this->generateController($bundle, $entity, $parameters, $output);
        $this->generatePagepartConfigs($bundle, $entity, $parameters, $output);
        $this->generateTemplates($bundle, $entity, $parameters, $output);
        $this->generateRouting($bundle, $entity, $parameters, $output);
        $this->generateMenu($bundle, $entity, $parameters, $output);
        $this->generateServices($bundle, $entity, $parameters, $output);

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
        $routing = $this->render($skeletonDir . '/routing.yml', $parameters);
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

        $this->filesystem->copy($fullSkeletonDir . '/OverviewPage/view.html.twig', $dirPath . '/Pages/' . $entity . '/' . $entity . 'OverviewPage/view.html.twig', true);
        GeneratorUtils::prepend("{% extends '" . $bundle->getName() .":Layout:layout.html.twig' %}\n", $dirPath . '/Pages/' . $entity . '/view.html.twig');

        //$this->filesystem->copy($fullSkeletonDir . '/PageAdminList/list.html.twig', $dirPath . '/AdminList/' . $entity . 'PageAdminList/view.html.twig', true);
        $this->renderFile($skeletonDir . '/PageAdminList/list.html.twig', $dirPath . '/AdminList/' . $entity . 'PageAdminList/list.html.twig', $parameters );

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
        $dirPath = sprintf("%s/Controller/" . $entity, $bundle->getPath());
        $skeletonDir = sprintf("%s/Controller", $this->skeletonDir);

        try {
            $this->generateSkeletonBasedClass($skeletonDir, $entity, $dirPath, 'PageAdminListController', $parameters);
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
    public function generatePagepartConfigs(Bundle $bundle, $entity, array $parameters, OutputInterface $output)
    {
        $dirPath = sprintf("%s/PagePartAdmin/" . $entity, $bundle->getPath());
        $skeletonDir = sprintf("%s/PagePartAdmin", $this->skeletonDir);

        try {
            $this->generateSkeletonBasedClass($skeletonDir, $entity, $dirPath, 'OverviewPagePagePartAdminConfigurator', $parameters);
        } catch (\Exception $error) {
            throw new \RuntimeException($error->getMessage());
        }
        try {
            $this->generateSkeletonBasedClass($skeletonDir, $entity, $dirPath, 'PagePagePartAdminConfigurator', $parameters);
        } catch (\Exception $error) {
            throw new \RuntimeException($error->getMessage());
        }

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
        $dirPath = sprintf("%s/AdminList/" . $entity, $bundle->getPath());
        $skeletonDir = sprintf("%s/AdminList", $this->skeletonDir);

        try {
            $this->generateSkeletonBasedClass($skeletonDir, $entity, $dirPath, 'PageAdminListConfigurator', $parameters);
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
        $dirPath = sprintf("%s/Form/Pages/" . $entity, $bundle->getPath());
        $skeletonDir = sprintf("%s/Form", $this->skeletonDir);

        try {
            $this->generateSkeletonBasedClass($skeletonDir, $entity, $dirPath, 'PageAdminType', $parameters);
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
        $dirPath = sprintf("%s/Repository/Pages/" . $entity, $bundle->getPath());
        $skeletonDir = sprintf("%s/Repository", $this->skeletonDir);

        try {
            $this->generateSkeletonBasedClass($skeletonDir, $entity, $dirPath, 'PageRepository', $parameters);
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
        $dirPath = sprintf("%s/Entity/Pages/" . $entity, $bundle->getPath());
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

        $output->writeln('Generating entities : <info>OK</info>');
    }

    /**
     * @param string $skeletonDir     The full dir of the entity skeleton
     * @param string $entity
     * @param string $dirPath         The full fir of where the entity should be created
     * @param string $className       The class name of the entity to create
     * @param array  $parameters      The template parameters
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
