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
        $this->generatePagetemplateConfigs($bundle, $parameters, $ouput);
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

        $this->renderFile('/Page/layout.html.twig', $dirPath . '/Page/layout.html.twig', $parameters);
        $this->renderFile('/Layout/_css.html.twig', $dirPath . '/Layout/_css.html.twig', $parameters);
        $this->renderFile('/Layout/_js_footer.html.twig', $dirPath . '/Layout/_js_footer.html.twig', $parameters);
        $this->renderFile('/Layout/_js_header.html.twig', $dirPath . '/Layout/_js_header.html.twig', $parameters);

        { //ContentPage
            $this->filesystem->copy($skeletonDir . '/Pages/ContentPage/view.html.twig', $dirPath . '/Resources/views/Pages/ContentPage/view.html.twig', true);
            GeneratorUtils::prepend("{% extends '" . $bundle->getName() .":Page:layout.html.twig' %}\n", $dirPath . '/Resources/views/Pages/ContentPage/view.html.twig');
            $this->filesystem->copy($skeletonDir . '/Pages/ContentPage/pagetemplate.html.twig', $dirPath . '/Resources/views/Pages/ContentPage/pagetemplate.html.twig', true);
            $this->filesystem->copy($skeletonDir . '/Pages/ContentPage/pagetemplate-singlecolumn.html.twig', $dirPath . '/Resources/views/Pages/ContentPage/pagetemplate-singlecolumn.html.twig', true);
        }

        { //FormPage
            $this->filesystem->copy($skeletonDir . '/Pages/FormPage/view.html.twig', $dirPath . '/Resources/views/Pages/FormPage/view.html.twig', true);
            GeneratorUtils::prepend("{% extends '" . $bundle->getName() .":Page:layout.html.twig' %}\n", $dirPath . '/Resources/views/Pages/FormPage/view.html.twig');
            $this->filesystem->copy($skeletonDir . '/Pages/FormPage/pagetemplate.html.twig', $dirPath . '/Resources/views/Pages/FormPage/pagetemplate.html.twig', true);
            GeneratorUtils::replace("~~~BUNDLE~~~", $bundle->getName(), $dirPath . '/Resources/views/Pages/FormPage/pagetemplate.html.twig');
            $this->filesystem->copy($skeletonDir . '/Pages/FormPage/pagetemplate-singlecolumn.html.twig', $dirPath . '/Resources/views/Pages/FormPage/pagetemplate-singlecolumn.html.twig', true);
            GeneratorUtils::replace("~~~BUNDLE~~~", $bundle->getName(), $dirPath . '/Resources/views/Pages/FormPage/pagetemplate-singlecolumn.html.twig');
        }

        { //HomePage
            $this->filesystem->copy($skeletonDir . '/Pages/HomePage/view.html.twig', $dirPath . '/Resources/views/Pages/HomePage/view.html.twig', true);
            GeneratorUtils::prepend("{% extends '" . $bundle->getName() .":Page:layout.html.twig' %}\n", $dirPath . '/Resources/views/Pages/HomePage/view.html.twig');
            $this->filesystem->copy($skeletonDir . '/Pages/HomePage/pagetemplate.html.twig', $dirPath . '/Resources/views/Pages/HomePage/pagetemplate.html.twig', true);
        }

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
     * @param Bundle                                            $bundle
     * @param OutputInterface                                   $output
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
        $dirPath = $bundle->getPath();
        $fullSkeletonDir = $this->skeletonDir . '/Resources/config';

        $this->filesystem->copy($fullSkeletonDir . '/pageparts/banners.yml', $dirPath . '/Resources/config/pageparts/banners.yml', true);
        $this->filesystem->copy($fullSkeletonDir . '/pageparts/form.yml', $dirPath . '/Resources/config/pageparts/form.yml', true);
        $this->filesystem->copy($fullSkeletonDir . '/pageparts/home.yml', $dirPath . '/Resources/config/pageparts/home.yml', true);
        $this->filesystem->copy($fullSkeletonDir . '/pageparts/main.yml', $dirPath . '/Resources/config/pageparts/main.yml', true);
        $this->filesystem->copy($fullSkeletonDir . '/pageparts/footer.yml', $dirPath . '/Resources/config/pageparts/footer.yml', true);

        $output->writeln('Generating PagePart Configurators : <info>OK</info>');
    }

    /**
     * @param Bundle $bundle     The bundle
     * @param array  $parameters The template parameters
     * @param OutputInterface $output
     *
     * @throws \RuntimeException
     */
    public function generatePagetemplateConfigs(Bundle $bundle, array $parameters, OutputInterface $output)
    {
        $dirPath = $bundle->getPath();
        $fullSkeletonDir = $this->skeletonDir . '/Resources/config';

        $this->filesystem->copy($fullSkeletonDir . '/pagetemplates/contentpage-singlecolumn.yml', $dirPath . '/Resources/config/pagetemplates/contentpage-singlecolumn.yml', true);
        GeneratorUtils::replace("~~~BUNDLE~~~", $bundle->getName(), $dirPath . '/Resources/config/pagetemplates/contentpage-singlecolumn.yml');
        $this->filesystem->copy($fullSkeletonDir . '/pagetemplates/contentpage.yml', $dirPath . '/Resources/config/pagetemplates/contentpage.yml', true);
        GeneratorUtils::replace("~~~BUNDLE~~~", $bundle->getName(), $dirPath . '/Resources/config/pagetemplates/contentpage.yml');
        $this->filesystem->copy($fullSkeletonDir . '/pagetemplates/formpage-singlecolumn.yml', $dirPath . '/Resources/config/pagetemplates/formpage-singlecolumn.yml', true);
        GeneratorUtils::replace("~~~BUNDLE~~~", $bundle->getName(), $dirPath . '/Resources/config/pagetemplates/formpage-singlecolumn.yml');
        $this->filesystem->copy($fullSkeletonDir . '/pagetemplates/formpage.yml', $dirPath . '/Resources/config/pagetemplates/formpage.yml', true);
        GeneratorUtils::replace("~~~BUNDLE~~~", $bundle->getName(), $dirPath . '/Resources/config/pagetemplates/formpage.yml');
        $this->filesystem->copy($fullSkeletonDir . '/pagetemplates/homepage.yml', $dirPath . '/Resources/config/pagetemplates/homepage.yml', true);
        GeneratorUtils::replace("~~~BUNDLE~~~", $bundle->getName(), $dirPath . '/Resources/config/pagetemplates/homepage.yml');

        $output->writeln('Generating PageTemplate Configurators : <info>OK</info>');
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
