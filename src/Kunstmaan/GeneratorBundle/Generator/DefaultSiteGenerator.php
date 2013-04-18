<?php

namespace Kunstmaan\GeneratorBundle\Generator;

use Kunstmaan\GeneratorBundle\Helper\GeneratorUtils;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

use Sensio\Bundle\GeneratorBundle\Command\Helper\DialogHelper;

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

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var DialogHelper
     */
    private $dialog;

    /**
     * @param Filesystem      $filesystem  The filesytem
     * @param string          $skeletonDir The skeleton directory
     * @param OutputInterface $output      The output
     * @param DialogHelper    $dialog      The dialog
     */
    public function __construct(Filesystem $filesystem, $skeletonDir, OutputInterface $output, DialogHelper $dialog)
    {
        $this->filesystem = $filesystem;
        $this->skeletonDir = $skeletonDir;
        $this->output = $output;
        $this->dialog = $dialog;
    }

    /**
     * @param Bundle $bundle  The bundle
     * @param string $prefix  The prefix
     * @param string $rootDir The root directory
     */
    public function generate(Bundle $bundle, $prefix, $rootDir)
    {
        $parameters = array(
            'namespace'         => $bundle->getNamespace(),
            'bundle'            => $bundle,
            'prefix'            => $prefix
        );

        $this->generateEntities($bundle, $parameters);
        $this->generateForm($bundle, $parameters);
        $this->generatePagepartConfigs($bundle, $parameters);
        $this->generatePagetemplateConfigs($bundle, $parameters);
        $this->generateFixtures($bundle, $parameters);
        $this->generateAssets($bundle);
        $this->generateTemplates($bundle, $parameters, $rootDir);
        $this->generateBehatTests($bundle);
        $this->generateUnitTests($bundle, $parameters);
    }

    /**
     * @param Bundle $bundle     The bundle name
     * @param array  $parameters The template parameters
     */
    public function generateUnitTests(Bundle $bundle, array $parameters)
    {
        $dirPath = $bundle->getPath();
        $fullSkeletonDir = $this->skeletonDir . '/Tests';

        $this->renderFile($fullSkeletonDir, '/Controller/DefaultControllerTest.php', $dirPath . '/Tests/Controller/DefaultControllerTest.php', $parameters);

        $this->output->writeln('Generating Unit Tests : <info>OK</info>');
    }

    /**
     * @param Bundle $bundle
     */
    public function generateBehatTests(Bundle $bundle)
    {
        $dirPath = $bundle->getPath();
        $fullSkeletonDir = $this->skeletonDir . '/Features';

        $this->filesystem->copy($fullSkeletonDir . '/homepage.feature', $dirPath . '/Features/homepage.feature', true);

        $this->output->writeln('Generating Behat Tests : <info>OK</info>');
    }

    /**
     * @param Bundle $bundle     The bundle
     * @param array  $parameters The template parameters
     * @param string $rootDir    The root directory
     */
    public function generateTemplates(Bundle $bundle, array $parameters, $rootDir)
    {
        $dirPath = $bundle->getPath();
        $fullSkeletonDir = $this->skeletonDir . '/Resources/views';

        $this->filesystem->copy($fullSkeletonDir . '/Default/index.html.twig', $dirPath . '/Resources/views/Default/index.html.twig', true);
        GeneratorUtils::prepend("{% extends '" . $bundle->getName() .":Layout:layout.html.twig' %}\n", $dirPath . '/Resources/views/Default/index.html.twig');

        $this->renderFile($fullSkeletonDir, '/Page/layout.html.twig', $dirPath . '/Resources/views/Page/layout.html.twig', $parameters);

        $this->filesystem->copy($fullSkeletonDir . '/Form/fields.html.twig', $dirPath . '/Resources/views/Form/fields.html.twig', true);

        { //ContentPage
            $this->filesystem->copy($fullSkeletonDir . '/Pages/ContentPage/view.html.twig', $dirPath . '/Resources/views/Pages/ContentPage/view.html.twig', true);
            GeneratorUtils::prepend("{% extends '" . $bundle->getName() .":Page:layout.html.twig' %}\n", $dirPath . '/Resources/views/Pages/ContentPage/view.html.twig');
            $this->filesystem->copy($fullSkeletonDir . '/Pages/ContentPage/pagetemplate.html.twig', $dirPath . '/Resources/views/Pages/ContentPage/pagetemplate.html.twig', true);
            $this->filesystem->copy($fullSkeletonDir . '/Pages/ContentPage/pagetemplate-singlecolumn.html.twig', $dirPath . '/Resources/views/Pages/ContentPage/pagetemplate-singlecolumn.html.twig', true);
        }

        { //FormPage
            $this->filesystem->copy($fullSkeletonDir . '/Pages/FormPage/view.html.twig', $dirPath . '/Resources/views/Pages/FormPage/view.html.twig', true);
            GeneratorUtils::prepend("{% extends '" . $bundle->getName() .":Page:layout.html.twig' %}\n", $dirPath . '/Resources/views/Pages/FormPage/view.html.twig');
            $this->filesystem->copy($fullSkeletonDir . '/Pages/FormPage/pagetemplate.html.twig', $dirPath . '/Resources/views/Pages/FormPage/pagetemplate.html.twig', true);
            GeneratorUtils::replace("~~~BUNDLE~~~", $bundle->getName(), $dirPath . '/Resources/views/Pages/FormPage/pagetemplate.html.twig');
            $this->filesystem->copy($fullSkeletonDir . '/Pages/FormPage/pagetemplate-singlecolumn.html.twig', $dirPath . '/Resources/views/Pages/FormPage/pagetemplate-singlecolumn.html.twig', true);
            GeneratorUtils::replace("~~~BUNDLE~~~", $bundle->getName(), $dirPath . '/Resources/views/Pages/FormPage/pagetemplate-singlecolumn.html.twig');
        }

        { //HomePage
            $this->filesystem->copy($fullSkeletonDir . '/Pages/HomePage/view.html.twig', $dirPath . '/Resources/views/Pages/HomePage/view.html.twig', true);
            GeneratorUtils::prepend("{% extends '" . $bundle->getName() .":Page:layout.html.twig' %}\n", $dirPath . '/Resources/views/Pages/HomePage/view.html.twig');
            $this->filesystem->copy($fullSkeletonDir . '/Pages/HomePage/pagetemplate.html.twig', $dirPath . '/Resources/views/Pages/HomePage/pagetemplate.html.twig', true);
        }


        $this->filesystem->copy($fullSkeletonDir . '/Layout/layout.html.twig', $dirPath . '/Resources/views/Layout/layout.html.twig', true);
        GeneratorUtils::replace("~~~CSS~~~", "{% include '" . $bundle->getName() .":Layout:_css.html.twig' %}\n", $dirPath . '/Resources/views/Layout/layout.html.twig');
        GeneratorUtils::replace("~~~TOP_JS~~~", "{% include '" . $bundle->getName() .":Layout:_js_header.html.twig' %}\n", $dirPath . '/Resources/views/Layout/layout.html.twig');
        GeneratorUtils::replace("~~~FOOTER_JS~~~", "{% include '" . $bundle->getName() .":Layout:_js_footer.html.twig' %}\n", $dirPath . '/Resources/views/Layout/layout.html.twig');

        $this->renderFile($fullSkeletonDir, '/Layout/_css.html.twig', $dirPath . '/Resources/views/Layout/_css.html.twig', $parameters);
        $this->renderFile($fullSkeletonDir, '/Layout/_js_footer.html.twig', $dirPath . '/Resources/views/Layout/_js_footer.html.twig', $parameters);
        $this->renderFile($fullSkeletonDir, '/Layout/_js_header.html.twig', $dirPath . '/Resources/views/Layout/_js_header.html.twig', $parameters);

        $this->output->writeln('Generating Twig Templates : <info>OK</info>');

        $this->generateErrorTemplates($bundle, $parameters, $rootDir);

        // @todo: should be improved
        GeneratorUtils::replace("[ \"KunstmaanAdminBundle\"", "[ \"KunstmaanAdminBundle\", \"". $bundle->getName()  ."\"", $rootDir . '/config/config.yml');

        $this->output->writeln('Configure assetic : <info>OK</info>');
    }

    /**
     * @param Bundle $bundle     The bundle
     * @param array  $parameters The template parameters
     * @param string $rootDir    The root directory
     */
    public function generateErrorTemplates(Bundle $bundle, array $parameters, $rootDir)
    {
        $dirPath = $bundle->getPath();
        $fullSkeletonDir = $this->skeletonDir . '/Resources/views/Error';

        $this->renderFile($fullSkeletonDir, '/error.html.twig', $rootDir . '/Resources/TwigBundle/views/Exception/error.html.twig', $parameters);
        $this->renderFile($fullSkeletonDir, '/error404.html.twig', $rootDir . '/Resources/TwigBundle/views/Exception/error404.html.twig', $parameters);
        $this->renderFile($fullSkeletonDir, '/error500.html.twig', $rootDir . '/Resources/TwigBundle/views/Exception/error500.html.twig', $parameters);
        $this->renderFile($fullSkeletonDir, '/error503.html.twig', $rootDir . '/Resources/TwigBundle/views/Exception/error503.html.twig', $parameters);

        $this->output->writeln('Generating Error Twig Templates : <info>OK</info>');
    }

    /**
     * @param Bundle $bundle
     */
    public function generateAssets(Bundle $bundle)
    {
        $dirPath = $bundle->getPath();
        $fullSkeletonDir = $this->skeletonDir . '/Resources/public';

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
            $this->filesystem->copy(sprintf("%s%s", $fullSkeletonDir, $asset), sprintf("%s/Resources/public%s", $dirPath, $asset));
        }

        $this->output->writeln('Generating Assets : <info>OK</info>');
    }

    /**
     * @param Bundle $bundle     The bundle
     * @param array  $parameters The template parameters
     *
     * @throws \RuntimeException
     */
    public function generateFixtures(Bundle $bundle, array $parameters)
    {
        $dirPath = $bundle->getPath() . '/DataFixtures/ORM';
        $fullSkeletonDir = $this->skeletonDir . '/DataFixtures/ORM';

        try {
            $this->generateSkeletonBasedClass($fullSkeletonDir, $dirPath, 'DefaultSiteFixtures', $parameters);
        } catch (\Exception $error) {
            $this->output->writeln($this->dialog->getHelperSet()->get('formatter')->formatBlock($error->getMessage(), 'error'));
        }

        $this->output->writeln('Generating Fixtures : <info>OK</info>');
    }

    /**
     * @param Bundle $bundle     The bundle
     * @param array  $parameters The template parameters
     *
     * @throws \RuntimeException
     */
    public function generatePagepartConfigs(Bundle $bundle, array $parameters)
    {
        $dirPath = $bundle->getPath();
        $fullSkeletonDir = $this->skeletonDir . '/Resources/config';

        $this->filesystem->copy($fullSkeletonDir . '/pageparts/banners.yml', $dirPath . '/Resources/config/pageparts/banners.yml', true);
        $this->filesystem->copy($fullSkeletonDir . '/pageparts/form.yml', $dirPath . '/Resources/config/pageparts/form.yml', true);
        $this->filesystem->copy($fullSkeletonDir . '/pageparts/home.yml', $dirPath . '/Resources/config/pageparts/home.yml', true);
        $this->filesystem->copy($fullSkeletonDir . '/pageparts/main.yml', $dirPath . '/Resources/config/pageparts/main.yml', true);
        $this->filesystem->copy($fullSkeletonDir . '/pageparts/footer.yml', $dirPath . '/Resources/config/pageparts/footer.yml', true);

        $this->output->writeln('Generating PagePart Configurators : <info>OK</info>');
    }

    /**
     * @param Bundle $bundle     The bundle
     * @param array  $parameters The template parameters
     *
     * @throws \RuntimeException
     */
    public function generatePagetemplateConfigs(Bundle $bundle, array $parameters)
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

        $this->output->writeln('Generating PageTemplate Configurators : <info>OK</info>');
    }

    /**
     * @param Bundle $bundle     The bundle
     * @param array  $parameters The template parameters
     *
     * @throws \RuntimeException
     */
    public function generateForm(Bundle $bundle, array $parameters)
    {
        $dirPath = $bundle->getPath() . '/Form/Pages';
        $fullSkeletonDir = $this->skeletonDir . '/Form/Pages';

        try {
            $this->generateSkeletonBasedClass($fullSkeletonDir, $dirPath, 'ContentPageAdminType', $parameters);
        } catch (\Exception $error) {
            $this->output->writeln($this->dialog->getHelperSet()->get('formatter')->formatBlock($error->getMessage(), 'error'));
        }
        try {
            $this->generateSkeletonBasedClass($fullSkeletonDir, $dirPath, 'FormPageAdminType', $parameters);
        } catch (\Exception $error) {
            $this->output->writeln($this->dialog->getHelperSet()->get('formatter')->formatBlock($error->getMessage(), 'error'));
        }
        try {
            $this->generateSkeletonBasedClass($fullSkeletonDir, $dirPath, 'HomePageAdminType', $parameters);
        } catch (\Exception $error) {
            $this->output->writeln($this->dialog->getHelperSet()->get('formatter')->formatBlock($error->getMessage(), 'error'));
        }

        $this->output->writeln('Generating forms : <info>OK</info>');
    }

    /**
     * @param Bundle $bundle     The bundle
     * @param array  $parameters The template parameters
     */
    public function generateEntities(Bundle $bundle, array $parameters)
    {
        $dirPath = sprintf("%s/Entity/Pages", $bundle->getPath());
        $fullSkeletonDir = sprintf("%s/Entity/Pages", $this->skeletonDir);

        try {
            $this->generateSkeletonBasedClass($fullSkeletonDir, $dirPath, 'ContentPage', $parameters);
        } catch (\Exception $error) {
            $this->output->writeln($this->dialog->getHelperSet()->get('formatter')->formatBlock($error->getMessage(), 'error'));
        }
        try {
            $this->generateSkeletonBasedClass($fullSkeletonDir, $dirPath, 'FormPage', $parameters);
        } catch (\Exception $error) {
            $this->output->writeln($this->dialog->getHelperSet()->get('formatter')->formatBlock($error->getMessage(), 'error'));
        }
        try {
            $this->generateSkeletonBasedClass($fullSkeletonDir, $dirPath, 'HomePage', $parameters);
        } catch (\Exception $error) {
            $this->output->writeln($this->dialog->getHelperSet()->get('formatter')->formatBlock($error->getMessage(), 'error'));
        }

        $this->output->writeln('Generating entities : <info>OK</info>');
    }

    /**
     * @param string $fullSkeletonDir The full dir of the entity skeleton
     * @param string $dirPath         The full fir of where the entity should be created
     * @param string $className       The class name of the entity to create
     * @param array  $parameters      The template parameters
     *
     * @throws \RuntimeException
     */
    private function generateSkeletonBasedClass($fullSkeletonDir, $dirPath, $className, array $parameters)
    {
        $classPath = sprintf("%s/%s.php", $dirPath, $className);
        if (file_exists($classPath)) {
            throw new \RuntimeException(sprintf('Unable to generate the %s class as it already exists under the %s file', $className, $classPath));
        }
        $this->renderFile($fullSkeletonDir, $className . '.php', $classPath, $parameters);
    }

}
