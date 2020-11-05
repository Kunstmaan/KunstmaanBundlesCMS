<?php

namespace Kunstmaan\GeneratorBundle\Generator;

use Kunstmaan\GeneratorBundle\Helper\GeneratorUtils;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Generates a SearchPage using KunstmaanSearchBundle and
 * KunstmaanNodeSearchBundle
 */
class SearchPageGenerator extends \Sensio\Bundle\GeneratorBundle\Generator\Generator
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
    private $rootDir;

    /**
     * @param Filesystem $filesystem  The filesytem
     * @param string     $skeletonDir The skeleton directory
     */
    public function __construct(Filesystem $filesystem, $skeletonDir, $rootDir = null)
    {
        $this->filesystem = $filesystem;
        $this->skeletonDir = $skeletonDir;
        $this->rootDir = $rootDir;
    }

    /**
     * @param BundleInterface $bundle     The bundle
     * @param string          $prefix     The prefix
     * @param string          $rootDir    The root directory
     * @param string          $createPage Create data fixtures or not
     */
    public function generate(
        BundleInterface $bundle,
        $prefix,
        $rootDir,
        $createPage,
        OutputInterface $output
    ) {
        $parameters = [
            'namespace' => $bundle->getNamespace(),
            'bundle' => $bundle,
            'prefix' => GeneratorUtils::cleanPrefix($prefix),
            'isV4' => Kernel::VERSION_ID >= 40000,
        ];

        $this->generateEntities($bundle, $parameters, $output);
        $this->generateTemplates($bundle, $parameters, $rootDir, $output);
        if ($createPage) {
            $this->generateFixtures($bundle, $parameters, $output);
        }
    }

    /**
     * @param BundleInterface $bundle     The bundle
     * @param array           $parameters The template parameters
     * @param string          $rootDir    The root directory
     */
    public function generateTemplates(
        BundleInterface $bundle,
        array $parameters,
        $rootDir,
        OutputInterface $output
    ) {
        $dirPath = Kernel::VERSION_ID >= 40000 ? $this->rootDir . '/templates' : $bundle->getPath() . '/Resources/views';
        $fullSkeletonDir = $this->skeletonDir . '/Resources/views';

        $this->filesystem->copy(
            __DIR__ . '/../Resources/SensioGeneratorBundle/skeleton' . $fullSkeletonDir . '/Pages/SearchPage/view.html.twig',
            $dirPath . '/Pages/SearchPage/view.html.twig',
            true
        );

        $twigFile = Kernel::VERSION_ID >= 40000 ? "{% extends 'Page/layout.html.twig' %}\n" : "{% extends '" . $bundle->getName() . ":Page:layout.html.twig' %}\n";
        GeneratorUtils::prepend($twigFile, $dirPath . '/Pages/SearchPage/view.html.twig');

        $output->writeln('Generating Twig Templates : <info>OK</info>');
    }

    /**
     * @param BundleInterface $bundle     The bundle
     * @param array           $parameters The template parameters
     *
     * @throws \RuntimeException
     */
    public function generateEntities(
        BundleInterface $bundle,
        array $parameters,
        OutputInterface $output
    ) {
        $dirPath = sprintf(
            '%s/Entity/Pages/',
            $bundle->getPath()
        );
        $fullSkeletonDir = sprintf(
            '%s/Entity/Pages/',
            $this->skeletonDir
        );

        try {
            $this->generateSkeletonBasedClass(
                $fullSkeletonDir,
                $dirPath,
                'SearchPage',
                $parameters
            );
        } catch (\Exception $error) {
            throw new \RuntimeException($error->getMessage());
        }

        $output->writeln('Generating entities : <info>OK</info>');
    }

    /**
     * @param BundleInterface $bundle     The bundle
     * @param array           $parameters The template parameters
     *
     * @throws \RuntimeException
     */
    public function generateFixtures(
        BundleInterface $bundle,
        array $parameters,
        OutputInterface $output
    ) {
        $dirPath = $bundle->getPath() . '/DataFixtures/ORM/SearchPageGenerator/';
        $skeletonDir = $this->skeletonDir . '/DataFixtures/ORM/SearchPageGenerator/';

        try {
            $this->generateSkeletonBasedClass(
                $skeletonDir,
                $dirPath,
                'SearchFixtures',
                $parameters
            );
        } catch (\Exception $error) {
            throw new \RuntimeException($error->getMessage());
        }

        $output->writeln('Generating fixtures : <info>OK</info>');
    }

    /**
     * @param string $fullSkeletonDir The full dir of the entity skeleton
     * @param string $dirPath         The full fir of where the entity should
     *                                be created
     * @param string $className       The class name of the entity to create
     * @param array  $parameters      The template parameters
     *
     * @throws \RuntimeException
     */
    private function generateSkeletonBasedClass(
        $fullSkeletonDir,
        $dirPath,
        $className,
        array $parameters
    ) {
        $classPath = sprintf('%s/%s.php', $dirPath, $className);
        if (file_exists($classPath)) {
            throw new \RuntimeException(sprintf('Unable to generate the %s class as it already exists under the %s file', $className, $classPath));
        }
        $this->renderFile(
            $fullSkeletonDir . $className . '.php',
            $classPath,
            $parameters
        );
    }
}
