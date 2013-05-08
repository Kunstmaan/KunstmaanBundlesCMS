<?php

namespace Kunstmaan\GeneratorBundle\Generator;

use Kunstmaan\GeneratorBundle\Helper\GeneratorUtils;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

// TODO: Add the Bundle to assetic:bundles configuration.

// TODO: Modify security.yml

/**
 * Generates a default website using several Kunstmaan bundles using default templates and assets
 */
class DefaultSiteGenerator extends \Sensio\Bundle\GeneratorBundle\Generator\Generator
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
        $this->fullSkeletonDir = GeneratorUtils::getFullSkeletonPath($skeletonDir);
    }

    /**
     * @param Bundle          $bundle  The bundle
     * @param string          $prefix  The prefix
     * @param string          $rootDir The root directory
     * @param OutputInterface $output
     */
    public function generate(Bundle $bundle, $prefix, $rootDir, OutputInterface $output)
    {
        $parameters = array(
            'namespace'         => $bundle->getNamespace(),
            'bundle'            => $bundle,
            'prefix'            => $prefix
        );

        $this->generateEntities($bundle, $parameters, $output);
        $this->generateForm($bundle, $parameters, $output);
        $this->generatePagepartConfigs($bundle, $parameters, $output);
        $this->generateFixtures($bundle, $parameters, $output);
        $this->generateAssets($bundle, $output);
        $this->generateTemplates($bundle, $parameters, $rootDir, $output);
        $this->generateBehatTests($bundle, $output);
        $this->generateUnitTests($bundle, $parameters, $output);
    }

    /**
     * @param Bundle          $bundle
     * @param array           $parameters The template parameters
     * @param OutputInterface $output
     */
    public function generateUnitTests(Bundle $bundle, array $parameters, OutputInterface $output)
    {
        $dirPath = sprintf("%s/Tests/Controller", $bundle->getPath());
        $skeletonDir = sprintf("%s/Tests/Controller", $this->fullSkeletonDir);
        $this->setSkeletonDirs(array($skeletonDir));

        $this->renderFile('/DefaultControllerTest.php', $dirPath . '/DefaultControllerTest.php', $parameters);

        $output->writeln('Generating Unit Tests : <info>OK</info>');
    }

    /**
     * @param Bundle          $bundle
     * @param OutputInterface $output
     */
    public function generateBehatTests(Bundle $bundle, OutputInterface $output)
    {
        $dirPath = sprintf("%s/Features", $bundle->getPath());
        $skeletonDir = sprintf("%s/Features", $this->fullSkeletonDir);

        $this->filesystem->copy($skeletonDir . '/homepage.feature', $dirPath . '/homepage.feature', true);

        $output->writeln('Generating Behat Tests : <info>OK</info>');
    }

    /**
     * @param Bundle          $bundle     The bundle
     * @param array           $parameters The template parameters
     * @param string          $rootDir    The root directory
     * @param OutputInterface $output
     */
    public function generateTemplates(Bundle $bundle, array $parameters, $rootDir, OutputInterface $output)
    {
        $dirPath = sprintf("%s/Resources/views", $bundle->getPath());
        $skeletonDir = sprintf("%s/Resources/views", $this->fullSkeletonDir);
        $this->setSkeletonDirs(array($skeletonDir));

        #var_dump($skeletonDir . '/Page/layout.html.twig');
        #var_dump(sprintf($skeletonDir . '/Page/layout.html.twig'));
        #die;

        $this->renderFile('/Page/layout.html.twig', $dirPath . '/Page/layout.html.twig', $parameters);
        $this->renderFile('/Layout/_css.html.twig', $dirPath . '/Layout/_css.html.twig', $parameters);
        $this->renderFile('/Layout/_js_footer.html.twig', $dirPath . '/Layout/_js_footer.html.twig', $parameters);
        $this->renderFile('/Layout/_js_header.html.twig', $dirPath . '/Layout/_js_header.html.twig', $parameters);


        $this->filesystem->copy($skeletonDir . '/Default/index.html.twig', $dirPath . '/Default/index.html.twig', true);
        GeneratorUtils::prepend("{% extends '" . $bundle->getName() .":Layout:layout.html.twig' %}\n", $dirPath . '/Default/index.html.twig');

        $this->filesystem->copy($skeletonDir  . '/Pages/ContentPage/view.html.twig', $dirPath . '/Pages/ContentPage/view.html.twig', true);
        GeneratorUtils::prepend("{% extends '" . $bundle->getName() .":Page:layout.html.twig' %}\n", $dirPath . '/Pages/ContentPage/view.html.twig');

        $this->filesystem->copy($skeletonDir  . '/Form/fields.html.twig', $dirPath . '/Form/fields.html.twig', true);

        $this->filesystem->copy($skeletonDir  . '/Pages/FormPage/view.html.twig', $dirPath . '/Pages/FormPage/view.html.twig', true);
        GeneratorUtils::prepend("{% extends '" . $bundle->getName() .":Page:layout.html.twig' %}\n", $dirPath . '/Pages/FormPage/view.html.twig');
        GeneratorUtils::replace("~~~BUNDLE~~~", $bundle->getName(), $dirPath . '/Pages/FormPage/view.html.twig');

        $this->filesystem->copy($skeletonDir  . '/Pages/HomePage/view.html.twig', $dirPath . '/Pages/HomePage/view.html.twig', true);
        GeneratorUtils::prepend("{% extends '" . $bundle->getName() .":Page:layout.html.twig' %}\n", $dirPath . '/Pages/HomePage/view.html.twig');

        $this->filesystem->copy($skeletonDir  . '/Layout/layout.html.twig', $dirPath . '/Layout/layout.html.twig', true);
        GeneratorUtils::replace("~~~CSS~~~", "{% include '" . $bundle->getName() .":Layout:_css.html.twig' %}\n", $dirPath . '/Layout/layout.html.twig');
        GeneratorUtils::replace("~~~TOP_JS~~~", "{% include '" . $bundle->getName() .":Layout:_js_header.html.twig' %}\n", $dirPath . '/Layout/layout.html.twig');
        GeneratorUtils::replace("~~~FOOTER_JS~~~", "{% include '" . $bundle->getName() .":Layout:_js_footer.html.twig' %}\n", $dirPath . '/Layout/layout.html.twig');



        $output->writeln('Generating Twig Templates : <info>OK</info>');

        $this->generateErrorTemplates($bundle, $parameters, $rootDir, $output);

        // @todo: should be improved
        GeneratorUtils::replace("[ \"KunstmaanAdminBundle\"", "[ \"KunstmaanAdminBundle\", \"". $bundle->getName()  ."\"", $rootDir . '/config/config.yml');

        $output->writeln('Configure assetic : <info>OK</info>');
    }

    /**
     * @param Bundle          $bundle     The bundle
     * @param array           $parameters The template parameters
     * @param string          $rootDir    The root directory
     * @param OutputInterface $output
     */
    public function generateErrorTemplates(Bundle $bundle, array $parameters, $rootDir, OutputInterface $output)
    {
        $dirPath = sprintf("%s/Resources/views/Error", $bundle->getPath());
        $skeletonDir = sprintf("%s/Resources/views/Error", $this->fullSkeletonDir);
        $this->setSkeletonDirs(array($skeletonDir));

        $this->renderFile('/error.html.twig', $rootDir . '/Resources/TwigBundle/views/Exception/error.html.twig', $parameters);
        $this->renderFile('/error404.html.twig', $rootDir . '/Resources/TwigBundle/views/Exception/error404.html.twig', $parameters);
        $this->renderFile('/error500.html.twig', $rootDir . '/Resources/TwigBundle/views/Exception/error500.html.twig', $parameters);
        $this->renderFile('/error503.html.twig', $rootDir . '/Resources/TwigBundle/views/Exception/error503.html.twig', $parameters);

        $output->writeln('Generating Error Twig Templates : <info>OK</info>');
    }

    /**
     * @param Bundle          $bundle
     * @param OutputInterface $output
     */
    public function generateAssets(Bundle $bundle, OutputInterface $output)
    {
        $dirPath = sprintf("%s/Resources/public", $bundle->getPath());
        $skeletonDir = sprintf("%s/Resources/public", $this->fullSkeletonDir);

        $assets = array(
            '/css/app.css',
            '/css/foundation.css',
            '/css/foundation.min.css',
            '/js/app.js',
            '/js/foundation.min.js',
            '/js/jquery.js',
            '/js/modernizr.foundation.js',
            '/img/favicon.ico'
        );

        # /Users/vincent/Development/bundles/GeneratorBundleTest/vendor/kunstmaan/generator-bundle/Kunstmaan/GeneratorBundle/Resources/SensioGeneratorBundle/skeleton/defaultsite/Resources/public/css/app.css
        foreach ($assets as $asset) {
            $this->filesystem->copy(sprintf("%s%s", $skeletonDir, $asset), sprintf("%s%s", $dirPath, $asset));
        }

        $output->writeln('Generating Assets : <info>OK</info>');
    }

    /**
     * @param Bundle          $bundle     The bundle
     * @param array           $parameters The template parameters
     * @param OutputInterface $output
     *
     * @throws \RuntimeException
     */
    public function generateFixtures(Bundle $bundle, array $parameters, OutputInterface $output)
    {
        $dirPath = $bundle->getPath() . '/DataFixtures/ORM';
        $skeletonDir = $this->skeletonDir . '/DataFixtures/ORM';

        try {
            $this->generateSkeletonBasedClass($skeletonDir, $dirPath, 'DefaultSiteFixtures', $parameters);
        } catch (\Exception $error) {
            throw new \RuntimeException($error->getMessage());
        }

        $output->writeln('Generating Fixtures : <info>OK</info>');
    }

    /**
     * @param Bundle          $bundle     The bundle
     * @param array           $parameters The template parameters
     * @param OutputInterface $output
     *
     * @throws \RuntimeException
     */
    public function generatePagepartConfigs(Bundle $bundle, array $parameters, OutputInterface $output)
    {
        $dirPath = $bundle->getPath() . '/PagePartAdmin';
        $skeletonDir = $this->skeletonDir . '/PagePartAdmin';

        try {
            $this->generateSkeletonBasedClass($skeletonDir, $dirPath, 'BannerPagePartAdminConfigurator', $parameters);
        } catch (\Exception $error) {
            throw new \RuntimeException($error->getMessage());
        }
        try {
            $this->generateSkeletonBasedClass($skeletonDir, $dirPath, 'ContentPagePagePartAdminConfigurator', $parameters);
        } catch (\Exception $error) {
            throw new \RuntimeException($error->getMessage());
        }
        try {
            $this->generateSkeletonBasedClass($skeletonDir, $dirPath, 'FormPagePagePartAdminConfigurator', $parameters);
        } catch (\Exception $error) {
            throw new \RuntimeException($error->getMessage());
        }

        try {
            $this->generateSkeletonBasedClass($skeletonDir, $dirPath, 'HomePagePagePartAdminConfigurator', $parameters);
        } catch (\Exception $error) {
            throw new \RuntimeException($error->getMessage());
        }

        $output->writeln('Generating PagePart Configurators : <info>OK</info>');
    }

    /**
     * @param Bundle          $bundle     The bundle
     * @param array           $parameters The template parameters
     * @param OutputInterface $output
     *
     * @throws \RuntimeException
     */
    public function generateForm(Bundle $bundle, array $parameters, OutputInterface $output)
    {
        $dirPath = $bundle->getPath() . '/Form/Pages';
        $skeletonDir = $this->skeletonDir . '/Form/Pages';

        try {
            $this->generateSkeletonBasedClass($skeletonDir, $dirPath, 'ContentPageAdminType', $parameters);
        } catch (\Exception $error) {
            throw new \RuntimeException($error->getMessage());
        }
        try {
            $this->generateSkeletonBasedClass($skeletonDir, $dirPath, 'FormPageAdminType', $parameters);
        } catch (\Exception $error) {
            throw new \RuntimeException($error->getMessage());
        }
        try {
            $this->generateSkeletonBasedClass($skeletonDir, $dirPath, 'HomePageAdminType', $parameters);
        } catch (\Exception $error) {
            throw new \RuntimeException($error->getMessage());
        }

        $output->writeln('Generating forms : <info>OK</info>');
    }

    /**
     * @param Bundle          $bundle     The bundle
     * @param array           $parameters The template parameters
     * @param OutputInterface $output
     *
     * @throws \RuntimeException
     */
    public function generateEntities(Bundle $bundle, array $parameters, OutputInterface $output)
    {
        $dirPath = sprintf("%s/Entity/Pages", $bundle->getPath());
        $skeletonDir = sprintf("%s/Entity/Pages", $this->skeletonDir);

        try {
            $this->generateSkeletonBasedClass($skeletonDir, $dirPath, 'ContentPage', $parameters);
        } catch (\Exception $error) {
            throw new \RuntimeException($error->getMessage());
        }
        try {
            $this->generateSkeletonBasedClass($skeletonDir, $dirPath, 'FormPage', $parameters);
        } catch (\Exception $error) {
            throw new \RuntimeException($error->getMessage());
        }
        try {
            $this->generateSkeletonBasedClass($skeletonDir, $dirPath, 'HomePage', $parameters);
        } catch (\Exception $error) {
            throw new \RuntimeException($error->getMessage());
        }

        $output->writeln('Generating entities : <info>OK</info>');
    }

    /**
     * @param string $skeletonDir The dir of the entity skeleton
     * @param string $dirPath     The full fir of where the entity should be created
     * @param string $className   The class name of the entity to create
     * @param array  $parameters  The template parameters
     *
     * @throws \RuntimeException
     */
    private function generateSkeletonBasedClass($skeletonDir, $dirPath, $className, array $parameters)
    {
        $classPath = sprintf("%s/%s.php", $dirPath, $className);
        $skeletonPath = sprintf("%s/%s.php", $skeletonDir, $className);
        if (file_exists($classPath)) {
            throw new \RuntimeException(sprintf('Unable to generate the %s class as it already exists under the %s file', $className, $classPath));
        }
        $this->renderFile($skeletonPath, $classPath, $parameters);
    }

}
