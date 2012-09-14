<?php

namespace Kunstmaan\GeneratorBundle\Generator;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\DependencyInjection\Container;

use Doctrine\ORM\Mapping\ClassMetadata;

class DefaultSiteGenerator extends \Sensio\Bundle\GeneratorBundle\Generator\Generator
{

    private $filesystem;
    private $skeletonDir;

    public function __construct(Filesystem $filesystem, $skeletonDir)
    {
        $this->filesystem = $filesystem;
        $this->skeletonDir = $skeletonDir;
    }

    public function generate($bundle, OutputInterface $output)
    {

        $parameters = array(
            'namespace'         => $bundle->getNamespace(),
            'bundle'            => $bundle,
        );

        /*
         *  ENTITIES
         */

        $dirPath = $bundle->getPath() . '/Entity';
        $fullSkeletonDir = $this->skeletonDir . '/entity';

        /* Content page */

        $classname = 'ContentPage';
        $classPath = $dirPath . '/ContentPage.php';
        if (file_exists($classPath)) {
            throw new \RuntimeException(sprintf('Unable to generate the %s class as it already exists under the %s file', $classname, $classPath));
        }
        $this->renderFile($fullSkeletonDir, 'ContentPage.php', $classPath, $parameters);

        /* Form page */

        $classname = 'FormPage';
        $classPath = $dirPath . '/FormPage.php';
        if (file_exists($classPath)) {
            throw new \RuntimeException(sprintf('Unable to generate the %s class as it already exists under the %s file', $classname, $classPath));
        }
        $this->renderFile($fullSkeletonDir, 'FormPage.php', $classPath, $parameters);

        /* Home page */

        $classname = 'HomePage';
        $classPath = $dirPath . '/HomePage.php';
        if (file_exists($classPath)) {
            throw new \RuntimeException(sprintf('Unable to generate the %s class as it already exists under the %s file', $classname, $classPath));
        }
        $this->renderFile($fullSkeletonDir, 'HomePage.php', $classPath, $parameters);

        $output->writeln('Generating entities : <info>OK</info>');

        /*
         * FORM
         */

        $dirPath = $bundle->getPath() . '/Form';
        $fullSkeletonDir = $this->skeletonDir . '/form';

        /* Content page */

        $classname = 'ContentPageAdminType';
        $classPath = $dirPath . '/ContentPageAdminType.php';
        if (file_exists($classPath)) {
            throw new \RuntimeException(sprintf('Unable to generate the %s class as it already exists under the %s file', $classname, $classPath));
        }
        $this->renderFile($fullSkeletonDir, 'ContentPageAdminType.php', $classPath, $parameters);

        /* Form page */

        $classname = 'FormPageAdminType';
        $classPath = $dirPath . '/FormPageAdminType.php';
        if (file_exists($classPath)) {
            throw new \RuntimeException(sprintf('Unable to generate the %s class as it already exists under the %s file', $classname, $classPath));
        }
        $this->renderFile($fullSkeletonDir, 'FormPageAdminType.php', $classPath, $parameters);

        /* Home page */

        $classname = 'HomePageAdminType';
        $classPath = $dirPath . '/HomePageAdminType.php';
        if (file_exists($classPath)) {
            throw new \RuntimeException(sprintf('Unable to generate the %s class as it already exists under the %s file', $classname, $classPath));
        }
        $this->renderFile($fullSkeletonDir, 'HomePageAdminType.php', $classPath, $parameters);

        $output->writeln('Generating forms : <info>OK</info>');
    }

}