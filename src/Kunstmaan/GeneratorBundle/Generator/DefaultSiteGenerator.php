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
        $this->generateFixtures($bundle, $parameters, $output);
        $this->generateAssets($bundle, $output);
        // CAUTION : Following templates change the skeleton dir array
        // TODO Find a better way
        $this->generatePagepartConfigs($bundle, $parameters, $output);
        $this->generatePagetemplateConfigs($bundle, $parameters, $output);
        $this->generateTemplates($bundle, $parameters, $rootDir, $output);
        $this->generateBehatTests($bundle, $output);
        $this->generateUnitTests($bundle, $parameters, $output);
        $this->generateGruntFiles($bundle, $parameters, $output);
    }

    public function generateGruntFiles(Bundle $bundle, array $parameters, OutputInterface $output)
    {
        $dirPath = sprintf("%s/Resources", $bundle->getPath());
        $skeletonDir = sprintf("%s/Resources", $this->fullSkeletonDir);
        $this->setSkeletonDirs(array($skeletonDir));

        $this->filesystem->copy($skeletonDir . '/Gruntfile.js', $dirPath . '/Gruntfile.js', true);
        $this->renderFile('/package.json', $dirPath . '/package.json', $parameters);

        $output->writeln('Generating root files : <info>OK</info>');
    }

    /**
     * @param Bundle          $bundle
     * @param array           $parameters The template parameters
     * @param OutputInterface $output
     */
    public function generateUnitTests(Bundle $bundle, array $parameters, OutputInterface $output)
    {
        $dirPath = sprintf("%s/Tests/Controlapp/console", $bundle->getPath());
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
        $this->setSkeletonDirs(array($skeletonDir));

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
            $this->filesystem->copy($skeletonDir . '/Pages/ContentPage/view.html.twig', $dirPath . '/Pages/ContentPage/view.html.twig', true);
            GeneratorUtils::prepend("{% extends '" . $bundle->getName() .":Page:layout.html.twig' %}\n", $dirPath . '/Pages/ContentPage/view.html.twig');
            $this->filesystem->copy($skeletonDir . '/Pages/ContentPage/pagetemplate.html.twig', $dirPath . '/Pages/ContentPage/pagetemplate.html.twig', true);
            $this->filesystem->copy($skeletonDir . '/Pages/ContentPage/pagetemplate-singlecolumn.html.twig', $dirPath . '/Pages/ContentPage/pagetemplate-singlecolumn.html.twig', true);
        }

        { //FormPage
            $this->filesystem->copy($skeletonDir . '/Pages/FormPage/view.html.twig', $dirPath . '/Pages/FormPage/view.html.twig', true);
            GeneratorUtils::prepend("{% extends '" . $bundle->getName() .":Page:layout.html.twig' %}\n", $dirPath . '/Pages/FormPage/view.html.twig');
            $this->filesystem->copy($skeletonDir . '/Pages/FormPage/pagetemplate.html.twig', $dirPath . '/Pages/FormPage/pagetemplate.html.twig', true);
            GeneratorUtils::replace("~~~BUNDLE~~~", $bundle->getName(), $dirPath . '/Pages/FormPage/pagetemplate.html.twig');
            $this->filesystem->copy($skeletonDir . '/Pages/FormPage/pagetemplate-singlecolumn.html.twig', $dirPath . '/Pages/FormPage/pagetemplate-singlecolumn.html.twig', true);
            GeneratorUtils::replace("~~~BUNDLE~~~", $bundle->getName(), $dirPath . '/Pages/FormPage/pagetemplate-singlecolumn.html.twig');
        }

        { //HomePage
            $this->filesystem->copy($skeletonDir . '/Pages/HomePage/view.html.twig', $dirPath . '/Pages/HomePage/view.html.twig', true);
            GeneratorUtils::prepend("{% extends '" . $bundle->getName() .":Page:layout.html.twig' %}\n", $dirPath . '/Pages/HomePage/view.html.twig');
            $this->filesystem->copy($skeletonDir . '/Pages/HomePage/pagetemplate.html.twig', $dirPath . '/Pages/HomePage/pagetemplate.html.twig', true);
        }

        $this->filesystem->copy($skeletonDir  . '/Layout/layout.html.twig', $dirPath . '/Layout/layout.html.twig', true);
        GeneratorUtils::replace("~~~CSS~~~", "{% include '" . $bundle->getName() .":Layout:_css.html.twig' %}\n", $dirPath . '/Layout/layout.html.twig');
        GeneratorUtils::replace("~~~TOP_JS~~~", "{% include '" . $bundle->getName() .":Layout:_js_header.html.twig' %}\n", $dirPath . '/Layout/layout.html.twig');
        GeneratorUtils::replace("~~~FOOTER_JS~~~", "{% include '" . $bundle->getName() .":Layout:_js_footer.html.twig' %}\n", $dirPath . '/Layout/layout.html.twig');

        $this->filesystem->copy($skeletonDir  . '/Form/fields.html.twig', $dirPath . '/Form/fields.html.twig', true);

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
        $this->setSkeletonDirs(array($skeletonDir));

        $this->GenerateImageAssets($skeletonDir, $dirPath);
        $this->GenerateJavascriptAssets($skeletonDir, $dirPath);
        $this->GenerateStyleSheetAssets($skeletonDir, $dirPath);

        $output->writeln('Generating Assets : <info>OK</info>');
    }

    /**
     * Generate the image assets
     *
     * @param $skeletonDir
     * @param $dirPath
     */
    public function generateImageAssets($skeletonDir, $dirPath)
    {
        $assets = array(
            'backgrounds/.gitkeep',
            'buttons/.gitkeep',
            'dummy/.gitkeep',
            'general/.gitkeep',
            'general/logo.png',
            'icons/.gitkeep',
            'icons/favicon.ico'
        );

        foreach ($assets as $asset) {
            $this->filesystem->copy(sprintf("%s/img/%s", $skeletonDir, $asset), sprintf("%s/img/%s", $dirPath, $asset));
        }
    }

    /**
     * Generate Javascript assets
     *
     * @param $skeletonDir
     * @param $dirPath
     */
    public function generateJavascriptAssets($skeletonDir, $dirPath)
    {
        $assets = array(
            '.gitkeep',
            'script.js',
        );

        foreach ($assets as $asset) {
            $this->filesystem->copy(sprintf("%s/js/%s", $skeletonDir, $asset), sprintf("%s/js/%s", $dirPath, $asset));
        }
    }

    /**
     * Generate Stylesheet assets
     *
     * @param $skeletonDir
     * @param $dirPath
     */
    public function generateStylesheetAssets($skeletonDir, $dirPath)
    {
        $assets = array(
            '_base.scss',
            'style.scss',
            'style-old-ie.scss',
            'components/.gitkeep',
            'config/.gitkeep',
            'config/_base.scss',
            'config/_bootstrap-imports.scss',
            'config/_buttons.scss',
            'config/_colors.scss',
            'config/_config.scss',
            'config/_dropdowns.scss',
            'config/_forms.scss',
            'config/_grid.scss',
            'config/_hero-unit.scss',
            'config/_navbar.scss',
            'config/_pagination.scss',
            'config/_paths.scss',
            'config/_tables.scss',
            'config/_tooltips-popover.scss',
            'config/_typography.scss',
            'config/_z-index.scss',
            'helpers/.gitkeep',
            'legacy/.gitkeep',
            'legacy/_fallbacks.scss',
            'legacy/_ie.scss',
            'theme/.gitkeep',
            'theme/_grid.scss'
        );

        foreach ($assets as $asset) {
            $this->filesystem->copy(sprintf("%s/scss/%s", $skeletonDir, $asset), sprintf("%s/scss/%s", $dirPath, $asset));
        }
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
        $dirPath = sprintf("%s/Resources/config", $bundle->getPath());
        $skeletonDir = sprintf("%s/Resources/config", $this->fullSkeletonDir);
        $this->setSkeletonDirs(array($skeletonDir));

        $this->filesystem->copy($skeletonDir . '/pageparts/banners.yml', $dirPath . '/pageparts/banners.yml', true);
        $this->filesystem->copy($skeletonDir . '/pageparts/form.yml', $dirPath . '/pageparts/form.yml', true);
        $this->filesystem->copy($skeletonDir . '/pageparts/home.yml', $dirPath . '/pageparts/home.yml', true);
        $this->filesystem->copy($skeletonDir . '/pageparts/main.yml', $dirPath . '/pageparts/main.yml', true);
        $this->filesystem->copy($skeletonDir . '/pageparts/footer.yml', $dirPath . '/pageparts/footer.yml', true);

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
        $dirPath = sprintf("%s/Resources/config/pagetemplates", $bundle->getPath());
        $skeletonDir = sprintf("%s/Resources/config/pagetemplates", $this->fullSkeletonDir);
        $this->setSkeletonDirs(array($skeletonDir));

        $this->filesystem->copy($skeletonDir . '/contentpage-singlecolumn.yml', $dirPath . '/contentpage-singlecolumn.yml', true);
        GeneratorUtils::replace("~~~BUNDLE~~~", $bundle->getName(), $dirPath . '/contentpage-singlecolumn.yml');
        $this->filesystem->copy($skeletonDir . '/contentpage.yml', $dirPath . '/contentpage.yml', true);
        GeneratorUtils::replace("~~~BUNDLE~~~", $bundle->getName(), $dirPath . '/contentpage.yml');
        $this->filesystem->copy($skeletonDir . '/formpage-singlecolumn.yml', $dirPath . '/formpage-singlecolumn.yml', true);
        GeneratorUtils::replace("~~~BUNDLE~~~", $bundle->getName(), $dirPath . '/formpage-singlecolumn.yml');
        $this->filesystem->copy($skeletonDir . '/formpage.yml', $dirPath . '/formpage.yml', true);
        GeneratorUtils::replace("~~~BUNDLE~~~", $bundle->getName(), $dirPath . '/formpage.yml');
        $this->filesystem->copy($skeletonDir . '/homepage.yml', $dirPath . '/homepage.yml', true);
        GeneratorUtils::replace("~~~BUNDLE~~~", $bundle->getName(), $dirPath . '/homepage.yml');

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
