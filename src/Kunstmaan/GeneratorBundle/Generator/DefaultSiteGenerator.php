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
        $classPath = $dirPath . '/' . $classname . '.php';
        if (file_exists($classPath)) {
            throw new \RuntimeException(sprintf('Unable to generate the %s class as it already exists under the %s file', $classname, $classPath));
        }
        $this->renderFile($fullSkeletonDir, $classname . '.php', $classPath, $parameters);

        /* Form page */

        $classname = 'FormPage';
        $classPath = $dirPath . '/' . $classname . '.php';
        if (file_exists($classPath)) {
            throw new \RuntimeException(sprintf('Unable to generate the %s class as it already exists under the %s file', $classname, $classPath));
        }
        $this->renderFile($fullSkeletonDir, $classname . '.php', $classPath, $parameters);

        /* Home page */

        $classname = 'HomePage';
        $classPath = $dirPath . '/' . $classname . '.php';
        if (file_exists($classPath)) {
            throw new \RuntimeException(sprintf('Unable to generate the %s class as it already exists under the %s file', $classname, $classPath));
        }
        $this->renderFile($fullSkeletonDir, $classname . '.php', $classPath, $parameters);

        $output->writeln('Generating entities : <info>OK</info>');

        /*
         * FORM
         */

        $dirPath = $bundle->getPath() . '/Form';
        $fullSkeletonDir = $this->skeletonDir . '/form';

        /* Content page */

        $classname = 'ContentPageAdminType';
        $classPath = $dirPath . '/' . $classname . '.php';
        if (file_exists($classPath)) {
            throw new \RuntimeException(sprintf('Unable to generate the %s class as it already exists under the %s file', $classname, $classPath));
        }
        $this->renderFile($fullSkeletonDir, $classname . '.php', $classPath, $parameters);

        /* Form page */

        $classname = 'FormPageAdminType';
        $classPath = $dirPath . '/' . $classname . '.php';
        if (file_exists($classPath)) {
            throw new \RuntimeException(sprintf('Unable to generate the %s class as it already exists under the %s file', $classname, $classPath));
        }
        $this->renderFile($fullSkeletonDir, $classname . '.php', $classPath, $parameters);

        /* Home page */

        $classname = 'HomePageAdminType';
        $classPath = $dirPath . '/' . $classname . '.php';
        if (file_exists($classPath)) {
            throw new \RuntimeException(sprintf('Unable to generate the %s class as it already exists under the %s file', $classname, $classPath));
        }
        $this->renderFile($fullSkeletonDir, $classname . '.php', $classPath, $parameters);

        $output->writeln('Generating forms : <info>OK</info>');

        /*
         * PagePart Configurators
         */

        $dirPath = $bundle->getPath() . '/PagePartAdmin';
        $fullSkeletonDir = $this->skeletonDir . '/pagepartadmin';

        /* Banner */

        $classname = 'BannerPagePartAdminConfigurator';
        $classPath = $dirPath . '/' . $classname . '.php';
        if (file_exists($classPath)) {
            throw new \RuntimeException(sprintf('Unable to generate the %s class as it already exists under the %s file', $classname, $classPath));
        }
        $this->renderFile($fullSkeletonDir, $classname . '.php', $classPath, $parameters);

        /* Content page */

        $classname = 'ContentPagePagePartAdminConfigurator';
        $classPath = $dirPath . '/' . $classname . '.php';
        if (file_exists($classPath)) {
            throw new \RuntimeException(sprintf('Unable to generate the %s class as it already exists under the %s file', $classname, $classPath));
        }
        $this->renderFile($fullSkeletonDir, $classname . '.php', $classPath, $parameters);

        /* Form page */

        $classname = 'FormPagePagePartAdminConfigurator';
        $classPath = $dirPath . '/' . $classname . '.php';
        if (file_exists($classPath)) {
            throw new \RuntimeException(sprintf('Unable to generate the %s class as it already exists under the %s file', $classname, $classPath));
        }
        $this->renderFile($fullSkeletonDir, $classname . '.php', $classPath, $parameters);

        $output->writeln('Generating forms : <info>OK</info>');

        /* Home page */

        $classname = 'HomePagePagePartAdminConfigurator';
        $classPath = $dirPath . '/' . $classname . '.php';
        if (file_exists($classPath)) {
            throw new \RuntimeException(sprintf('Unable to generate the %s class as it already exists under the %s file', $classname, $classPath));
        }
        $this->renderFile($fullSkeletonDir, $classname . '.php', $classPath, $parameters);

        $output->writeln('Generating PagePart Configurators : <info>OK</info>');

        /*
         * Fixtures
         */

        $dirPath = $bundle->getPath() . '/DataFixtures/ORM';
        $fullSkeletonDir = $this->skeletonDir . '/datafixtures/orm';

        /* Default Site Fixtures */

        $classname = 'DefaultSiteFixtures';
        $classPath = $dirPath . '/' . $classname . '.php';
        if (file_exists($classPath)) {
            throw new \RuntimeException(sprintf('Unable to generate the %s class as it already exists under the %s file', $classname, $classPath));
        }
        $this->renderFile($fullSkeletonDir, $classname . '.php', $classPath, $parameters);

        /* Group Fixtures */

        $classname = 'GroupFixtures';
        $classPath = $dirPath . '/' . $classname . '.php';
        if (file_exists($classPath)) {
            throw new \RuntimeException(sprintf('Unable to generate the %s class as it already exists under the %s file', $classname, $classPath));
        }
        $this->renderFile($fullSkeletonDir, $classname . '.php', $classPath, $parameters);

        $output->writeln('Generating Fixtures : <info>OK</info>');

    }

}