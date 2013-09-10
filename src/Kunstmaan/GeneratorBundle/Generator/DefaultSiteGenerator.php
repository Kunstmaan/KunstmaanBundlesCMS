<?php

namespace Kunstmaan\GeneratorBundle\Generator;

use Kunstmaan\GeneratorBundle\Generator\AdminTestsGenerator;
use Kunstmaan\GeneratorBundle\Helper\GeneratorUtils;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

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


    private $rootDir;

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
     * Returns true if we detect ths site uses the locale.
     *
     * @return bool
     */
    private function isMultiLangEnvironment() {
        // This is a pretty silly implementation.
        // It just checks if it can find _locale in the routing.yml
        $routingFile = file_get_contents($this->rootDir . '/config/routing.yml');
        return preg_match('/_locale:/i', $routingFile);
    }

    /**
     * @param Bundle          $bundle  The bundle
     * @param string          $prefix  The prefix
     * @param string          $rootDir The root directory
     * @param OutputInterface $output
     */
    public function generate(Bundle $bundle, $prefix, $rootDir, OutputInterface $output)
    {
        $this->rootDir = $rootDir;

        $parameters = array(
            'namespace'         => $bundle->getNamespace(),
            'bundle'            => $bundle,
            'bundle_name'       => $bundle->getName(),
            'prefix'            => GeneratorUtils::cleanPrefix($prefix)
        );

        $this->generateControllers($bundle, $parameters, $output);
        if ($this->isMultiLangEnvironment()) {
            $this->generateDefaultLocaleFallbackCode($bundle, $parameters, $output);
            $this->addLanguageChooserRouting($rootDir);
            $this->addLanguageChooserConfig($bundle, $rootDir);
        }
        $this->generateEntities($bundle, $parameters, $output);
        $this->generateForm($bundle, $parameters, $output);
        $this->generateHelpers($bundle, $parameters, $output);
        $this->generateFixtures($bundle, $parameters, $output);
        $this->generateAssets($bundle, $output);

        // CAUTION : Following templates change the skeleton dir array
        // TODO Find a better way
        $this->generatePagepartConfigs($bundle, $parameters, $output);
        $this->generatePagetemplateConfigs($bundle, $parameters, $output);
        $this->generateTemplates($bundle, $parameters, $rootDir, $output);
        $this->generateAdminTests($bundle, $parameters, $output);
        $this->generateGruntFiles($bundle, $parameters, $rootDir, $output);
        $this->generateConfig($bundle, $parameters, $rootDir, $output);
        $this->generateRouting($bundle, $parameters, $rootDir, $output);
    }

    /**
     * Update the global routing.yml
     *
     * @param string $rootDir
     */
    public function addLanguageChooserRouting($rootDir)
    {
        $file = $rootDir.'/config/routing.yml';
        $ymlData = "\n\n# KunstmaanLanguageChooserBundle\n_languagechooser:\n    resource: .\n";
        file_put_contents($file, $ymlData, FILE_APPEND);
    }

    /**
     * Update the global config.yml
     *
     * @param Bundle $bundle
     * @param $rootDir
     */
    public function addLanguageChooserConfig(Bundle $bundle, $rootDir)
    {
        $params = Yaml::parse($rootDir.'/config/parameters.yml');

        if (is_array($params) || array_key_exists('parameters', $params) && is_array($params['parameters']) && array_key_exists('requiredlocales', $params['parameters']))  {
            $languages = explode('|', $params['parameters']['requiredlocales']);
        } else {
            $languages = array('en', 'nl', 'fr');
        }

        $file = $rootDir.'/config/config.yml';
        $ymlData = "\n\nkunstmaan_language_chooser:";
        $ymlData .= "\n    autodetectlanguage: false";
        $ymlData .= "\n    showlanguagechooser: true";
        $ymlData .= "\n    languagechoosertemplate: ".$bundle->getName().":Default:language-chooser.html.twig";
        $ymlData .= "\n    languagechooserlocales: [".implode(', ', $languages)."]\n";
        file_put_contents($file, $ymlData, FILE_APPEND);
    }

    /**
     * Update the global config.yml
     *
     * @param Bundle $bundle
     * @param array $parameters
     * @param $rootDir
     * @param OutputInterface $output
     */
    public function generateConfig(Bundle $bundle, array $parameters, $rootDir, OutputInterface $output)
    {
        $configFile = $rootDir.'/config/config.yml';

        $data = Yaml::parse($configFile);
        if (!array_key_exists('white_october_pagerfanta', $data)) {
            $ymlData = "\n\nwhite_october_pagerfanta:\n    default_view: twitter_bootstrap\n";
            file_put_contents($configFile, $ymlData, FILE_APPEND);
        }
    }

    public function generateGruntFiles(Bundle $bundle, array $parameters, $rootDir, OutputInterface $output)
    {
        $skeletonDir = sprintf("%s/grunt/", $this->fullSkeletonDir);
        $this->setSkeletonDirs(array($skeletonDir));
        $dirPath = sprintf("%s/Resources", $bundle->getPath());

        $this->filesystem->copy($skeletonDir . '/.gitignore', $dirPath . '/.gitignore', true);
        $this->renderFile('/Gruntfile.js.twig', $rootDir .'/../Gruntfile.js', $parameters);
        $this->renderFile('/package.json.twig', $rootDir .'/../package.json', $parameters);

        $output->writeln('Generating root files : <info>OK</info>');
    }

    public function generateAdminTests(Bundle $bundle, array $parameters, OutputInterface $output)
    {
        $adminTests = new AdminTestsGenerator($this->filesystem, '/admintests');
        $adminTests->generate($bundle,$output);

        $output->writeln('Generating Admin Tests : <info>OK</info>');
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
            GeneratorUtils::replace("~~~BUNDLE~~~", $bundle->getName(), $dirPath . '/Pages/HomePage/pagetemplate.html.twig');
            $this->filesystem->copy($skeletonDir . '/Pages/HomePage/slider.html.twig', $dirPath . '/Pages/HomePage/slider.html.twig', true);
        }

        { //SatelliteOverviewPage
            $this->filesystem->copy($skeletonDir . '/Pages/SatelliteOverviewPage/view.html.twig', $dirPath . '/Pages/SatelliteOverviewPage/view.html.twig', true);
            GeneratorUtils::prepend("{% extends '" . $bundle->getName() .":Page:layout.html.twig' %}\n", $dirPath . '/Pages/SatelliteOverviewPage/view.html.twig');
            $this->filesystem->copy($skeletonDir . '/Pages/SatelliteOverviewPage/pagetemplate.html.twig', $dirPath . '/Pages/SatelliteOverviewPage/pagetemplate.html.twig', true);
        }

        { //SlidePagePart
            $this->filesystem->copy($skeletonDir . '/PageParts/SlidePagePart/view.html.twig', $dirPath . '/PageParts/SlidePagePart/view.html.twig', true);
        }

        // LanguageChooser
        if ($this->isMultiLangEnvironment()) {
            $this->filesystem->copy($skeletonDir . '/Default/language-chooser.html.twig', $dirPath . '/Default/language-chooser.html.twig', true);
            GeneratorUtils::prepend("{% extends '" . $bundle->getName() .":Page:layout.html.twig' %}\n", $dirPath . '/Default/language-chooser.html.twig');
        }

        $this->filesystem->copy($skeletonDir  . '/Layout/layout.html.twig', $dirPath . '/Layout/layout.html.twig', true);
        GeneratorUtils::replace("~~~CSS~~~", "{% include '" . $bundle->getName() .":Layout:_css.html.twig' %}\n", $dirPath . '/Layout/layout.html.twig');
        GeneratorUtils::replace("~~~TOP_JS~~~", "{% include '" . $bundle->getName() .":Layout:_js_header.html.twig' %}\n", $dirPath . '/Layout/layout.html.twig');
        GeneratorUtils::replace("~~~FOOTER_JS~~~", "{% include '" . $bundle->getName() .":Layout:_js_footer.html.twig' %}\n", $dirPath . '/Layout/layout.html.twig');
        GeneratorUtils::replace("~~~BUNDLENAME~~~", $this->getBundleNameWithoutBundle($bundle), $dirPath . '/Layout/layout.html.twig');

        $this->filesystem->copy($skeletonDir  . '/Form/fields.html.twig', $dirPath . '/Form/fields.html.twig', true);

        $skeletonDir = sprintf("%s/app/KunstmaanSitemapBundle/views/SitemapPage/", $this->fullSkeletonDir);
        $dirPath = $rootDir .'/../app/Resources/KunstmaanSitemapBundle/views/SitemapPage/';
        $this->setSkeletonDirs(array($skeletonDir));

        $this->filesystem->copy($skeletonDir . '/view.html.twig', $dirPath . 'view.html.twig', true);
        GeneratorUtils::replace("~~~BUNDLENAME~~~", $bundle->getName(), $dirPath . 'view.html.twig');

        $output->writeln('Generating Twig Templates : <info>OK</info>');

        $this->generateErrorTemplates($bundle, $parameters, $rootDir, $output);

        // @todo: should be improved
        GeneratorUtils::replace("[ \"KunstmaanAdminBundle\"", "[ \"KunstmaanAdminBundle\", \"". $bundle->getName()  ."\"", $rootDir . '/config/config.yml');

        $output->writeln('Configure assetic : <info>OK</info>');
    }

    private function getBundleNameWithoutBundle(Bundle $bundle)
    {
        return preg_replace('/bundle$/i', '', strtolower($bundle->getName()));
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

        $assetsTypes = array(
            'files',
            'img',
            'js',
            'scss'
        );

        foreach ($assetsTypes as $type) {
            $this->generateAssetsForType($skeletonDir, $dirPath, $type);
        }

        $output->writeln('Generating Assets : <info>OK</info>');
    }

    /**
     * Generate the assets for assetsType
     *
     * @param $skeletonDir
     * @param $dirPath
     * @param $assetsType
     */
    public function generateAssetsForType($skeletonDir, $dirPath, $assetsType)
    {
        $this->filesystem->mirror(sprintf("%s/$assetsType/", $skeletonDir), sprintf("%s/$assetsType/", $dirPath));
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
            $this->generateSkeletonBasedClass($skeletonDir, $dirPath, 'SliderFixtures', $parameters);
            $this->generateSkeletonBasedClass($skeletonDir, $dirPath, 'SitemapFixtures', $parameters);
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
        $this->filesystem->copy($skeletonDir . '/pageparts/middle-column.yml', $dirPath . '/pageparts/middle-column.yml', true);
        $this->filesystem->copy($skeletonDir . '/pageparts/left-column.yml', $dirPath . '/pageparts/left-column.yml', true);
        $this->filesystem->copy($skeletonDir . '/pageparts/right-column.yml', $dirPath . '/pageparts/right-column.yml', true);
        $this->filesystem->copy($skeletonDir . '/pageparts/slider.yml', $dirPath . '/pageparts/slider.yml', true);
        GeneratorUtils::replace("~~~NAMESPACE~~~", $parameters['namespace'], $dirPath . '/pageparts/slider.yml');

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

        $this->filesystem->copy($skeletonDir . '/contentpage.yml', $dirPath . '/contentpage.yml', true);
        GeneratorUtils::replace("~~~BUNDLE~~~", $bundle->getName(), $dirPath . '/contentpage.yml');
        $this->filesystem->copy($skeletonDir . '/formpage-singlecolumn.yml', $dirPath . '/formpage-singlecolumn.yml', true);
        GeneratorUtils::replace("~~~BUNDLE~~~", $bundle->getName(), $dirPath . '/formpage-singlecolumn.yml');
        $this->filesystem->copy($skeletonDir . '/formpage.yml', $dirPath . '/formpage.yml', true);
        GeneratorUtils::replace("~~~BUNDLE~~~", $bundle->getName(), $dirPath . '/formpage.yml');
        $this->filesystem->copy($skeletonDir . '/homepage.yml', $dirPath . '/homepage.yml', true);
        GeneratorUtils::replace("~~~BUNDLE~~~", $bundle->getName(), $dirPath . '/homepage.yml');
        $this->filesystem->copy($skeletonDir . '/satelliteoverviewpage.yml', $dirPath . '/satelliteoverviewpage.yml', true);
        GeneratorUtils::replace("~~~BUNDLE~~~", $bundle->getName(), $dirPath . '/satelliteoverviewpage.yml');

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
        try {
            $this->generateSkeletonBasedClass($skeletonDir, $dirPath, 'SatelliteOverviewPageAdminType', $parameters);
        } catch (\Exception $error) {
            throw new \RuntimeException($error->getMessage());
        }

        $dirPath = $bundle->getPath() . '/Form/PageParts';
        $skeletonDir = $this->skeletonDir . '/Form/PageParts';

        try {
            $this->generateSkeletonBasedClass($skeletonDir, $dirPath, 'SlidePagePartAdminType', $parameters);
        } catch (\Exception $error) {
            throw new \RuntimeException($error->getMessage());
        }

        $dirPath = $bundle->getPath() . '/Form';
        $skeletonDir = $this->skeletonDir . '/Form';

        try {
            $this->generateSkeletonBasedClass($skeletonDir, $dirPath, 'SatelliteAdminType', $parameters);
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
    public function generateHelpers(Bundle $bundle, array $parameters, OutputInterface $output)
    {
        $dirPath = $bundle->getPath() . '/Helper/Menu';
        $skeletonDir = $this->skeletonDir . '/Helper/Menu';

        try {
            $this->generateSkeletonBasedClass($skeletonDir, $dirPath, 'AdminMenuAdaptor', $parameters);
        } catch (\Exception $error) {
            throw new \RuntimeException($error->getMessage());
        }

        $output->writeln('Generating helper classes : <info>OK</info>');
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
        try {
            $this->generateSkeletonBasedClass($skeletonDir, $dirPath, 'SatelliteOverviewPage', $parameters);
        } catch (\Exception $error) {
            throw new \RuntimeException($error->getMessage());
        }

        $dirPath = sprintf("%s/Entity/PageParts", $bundle->getPath());
        $skeletonDir = sprintf("%s/Entity/PageParts", $this->skeletonDir);

        try {
            $this->generateSkeletonBasedClass($skeletonDir, $dirPath, 'SlidePagePart', $parameters);
        } catch (\Exception $error) {
            throw new \RuntimeException($error->getMessage());
        }

        $dirPath = sprintf("%s/Entity", $bundle->getPath());
        $skeletonDir = sprintf("%s/Entity", $this->skeletonDir);

        try {
            $this->generateSkeletonBasedClass($skeletonDir, $dirPath, 'Satellite', $parameters);
        } catch (\Exception $error) {
            throw new \RuntimeException($error->getMessage());
        }

        $output->writeln('Generating entities : <info>OK</info>');
    }

    public function generateControllers(Bundle $bundle, array $parameters, OutputInterface $output)
    {
        $step = 'Generating controllers';

        try {
            $dirPath = sprintf("%s/Controller", $bundle->getPath());
            $skeletonDir = sprintf("%s/Controller", $this->skeletonDir);
            $this->generateSkeletonBasedClass($skeletonDir, $dirPath, 'DefaultController', $parameters, true);
            $this->generateSkeletonBasedClass($skeletonDir, $dirPath, 'SatelliteAdminListController', $parameters, true);
        } catch (\Exception $error) {
            $output->writeln($step . ' : <error>FAILED</error>');
            throw new \RuntimeException($error->getMessage());
        }

        $output->writeln($step . ' : <info>OK</info>');
    }

    /**
     * @param Bundle          $bundle     The bundle
     * @param array           $parameters The template parameters
     * @param OutputInterface $output
     *
     * @throws \RuntimeException
     */
    public function generateDefaultLocaleFallbackCode(Bundle $bundle, array $parameters, OutputInterface $output)
    {
        $step = 'Generating code for defaultlocale fallback';

        try {
            $dirPath = sprintf("%s/EventListener", $bundle->getPath());
            $skeletonDir = sprintf("%s/EventListener", $this->skeletonDir);
            $this->generateSkeletonBasedClass($skeletonDir, $dirPath, 'DefaultLocaleListener', $parameters);

            $dirPath = sprintf("%s/Resources/config", $bundle->getPath());
            $skeletonDir = sprintf("%s/Resources/config", $this->fullSkeletonDir);
            $this->filesystem->copy($skeletonDir . '/services.yml', $dirPath . '/services.yml', true);
            GeneratorUtils::replace("~~~APPNAME~~~", strtolower($bundle->getName()), $dirPath . '/services.yml');
            GeneratorUtils::replace("~~~NAMESPACE~~~", $parameters['namespace'], $dirPath . '/services.yml');
        } catch (\Exception $error) {
            $output->writeln($step . ' : <error>FAILED</error>');
            throw new \RuntimeException($error->getMessage());
        }

        $output->writeln($step . ' : <info>OK</info>');
    }

    /**
     * @param Bundle          $bundle     The bundle
     * @param array           $parameters The template parameters
     * @param OutputInterface $output
     *
     * @throws \RuntimeException
     */
    public function generateRouting(Bundle $bundle, array $parameters, OutputInterface $output)
    {
        $step = 'Generating routing';

        $dirPath = sprintf("%s/Resources/config", $bundle->getPath());
        $skeletonDir = sprintf("%s/Resources/config", $this->fullSkeletonDir);
        $this->filesystem->copy($skeletonDir . '/routing.yml', $dirPath . '/routing.yml', true);
        GeneratorUtils::replace("~~~BUNDLENAME~~~", $bundle->getName(), $dirPath . '/routing.yml');
        GeneratorUtils::replace("~~~BUNDLENAMELOWER~~~", strtolower($bundle->getName()), $dirPath . '/routing.yml');

        $output->writeln($step . ' : <info>OK</info>');
    }


    /**
     * @param string $skeletonDir The dir of the entity skeleton
     * @param string $dirPath     The full fir of where the entity should be created
     * @param string $className   The class name of the entity to create
     * @param array  $parameters  The template parameters
     *
     * @throws \RuntimeException
     */
    private function generateSkeletonBasedClass($skeletonDir, $dirPath, $className, array $parameters, $override = false)
    {
        $classPath = sprintf("%s/%s.php", $dirPath, $className);
        $skeletonPath = sprintf("%s/%s.php", $skeletonDir, $className);
        if (file_exists($classPath)) {
            if ($override) {
                unlink($classPath);
            } else {
                throw new \RuntimeException(sprintf('Unable to generate the %s class as it already exists under the %s file', $className, $classPath));
            }
        }
        $this->renderFile($skeletonPath, $classPath, $parameters);
    }

}
