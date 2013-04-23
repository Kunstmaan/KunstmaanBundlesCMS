<?php

namespace Kunstmaan\GeneratorBundle\Generator;

use Kunstmaan\GeneratorBundle\Helper\GeneratorUtils;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

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
     * @param Filesystem $filesystem  The filesytem
     * @param string     $skeletonDir The skeleton directory

     */
    public function __construct(Filesystem $filesystem, $skeletonDir)
    {
        $this->filesystem = $filesystem;
        $this->skeletonDir = $skeletonDir . '/defaultsite';
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
        $dirPath = $bundle->getPath();
        $fullSkeletonDir = $this->skeletonDir . '/Tests';

        $this->renderFile($fullSkeletonDir, '/Controller/DefaultControllerTest.php', $dirPath . '/Tests/Controller/DefaultControllerTest.php', $parameters);

        $output->writeln('Generating Unit Tests : <info>OK</info>');
    }

    /**
     * @param Bundle          $bundle
     * @param OutputInterface $output
     */
    public function generateBehatTests(Bundle $bundle, OutputInterface $output)
    {
        $dirPath = $bundle->getPath();
        $fullSkeletonDir = $this->skeletonDir . '/Features';

        $this->filesystem->copy($fullSkeletonDir . '/homepage.feature', $dirPath . '/Features/homepage.feature', true);

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
        $dirPath = $bundle->getPath();
        $fullSkeletonDir = $this->skeletonDir . '/Resources/views';

        $this->filesystem->copy($fullSkeletonDir . '/Default/index.html.twig', $dirPath . '/Resources/views/Default/index.html.twig', true);
        GeneratorUtils::prepend("{% extends '" . $bundle->getName() .":Layout:layout.html.twig' %}\n", $dirPath . '/Resources/views/Default/index.html.twig');

        $this->renderFile($fullSkeletonDir, '/Page/layout.html.twig', $dirPath . '/Resources/views/Page/layout.html.twig', $parameters);

        $this->filesystem->copy($fullSkeletonDir . '/Pages/ContentPage/view.html.twig', $dirPath . '/Resources/views/Pages/ContentPage/view.html.twig', true);
        GeneratorUtils::prepend("{% extends '" . $bundle->getName() .":Page:layout.html.twig' %}\n", $dirPath . '/Resources/views/Pages/ContentPage/view.html.twig');

        $this->filesystem->copy($fullSkeletonDir . '/Form/fields.html.twig', $dirPath . '/Resources/views/Form/fields.html.twig', true);

        $this->filesystem->copy($fullSkeletonDir . '/Pages/FormPage/view.html.twig', $dirPath . '/Resources/views/Pages/FormPage/view.html.twig', true);
        GeneratorUtils::prepend("{% extends '" . $bundle->getName() .":Page:layout.html.twig' %}\n", $dirPath . '/Resources/views/Pages/FormPage/view.html.twig');
        GeneratorUtils::replace("~~~BUNDLE~~~", $bundle->getName(), $dirPath . '/Resources/views/Pages/FormPage/view.html.twig');

        $this->filesystem->copy($fullSkeletonDir . '/Pages/HomePage/view.html.twig', $dirPath . '/Resources/views/Pages/HomePage/view.html.twig', true);
        GeneratorUtils::prepend("{% extends '" . $bundle->getName() .":Page:layout.html.twig' %}\n", $dirPath . '/Resources/views/Pages/HomePage/view.html.twig');

        $this->filesystem->copy($fullSkeletonDir . '/Layout/layout.html.twig', $dirPath . '/Resources/views/Layout/layout.html.twig', true);
        GeneratorUtils::replace("~~~CSS~~~", "{% include '" . $bundle->getName() .":Layout:_css.html.twig' %}\n", $dirPath . '/Resources/views/Layout/layout.html.twig');
        GeneratorUtils::replace("~~~TOP_JS~~~", "{% include '" . $bundle->getName() .":Layout:_js_header.html.twig' %}\n", $dirPath . '/Resources/views/Layout/layout.html.twig');
        GeneratorUtils::replace("~~~FOOTER_JS~~~", "{% include '" . $bundle->getName() .":Layout:_js_footer.html.twig' %}\n", $dirPath . '/Resources/views/Layout/layout.html.twig');

        $this->renderFile($fullSkeletonDir, '/Layout/_css.html.twig', $dirPath . '/Resources/views/Layout/_css.html.twig', $parameters);
        $this->renderFile($fullSkeletonDir, '/Layout/_js_footer.html.twig', $dirPath . '/Resources/views/Layout/_js_footer.html.twig', $parameters);
        $this->renderFile($fullSkeletonDir, '/Layout/_js_header.html.twig', $dirPath . '/Resources/views/Layout/_js_header.html.twig', $parameters);

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
        $dirPath = $bundle->getPath();
        $fullSkeletonDir = $this->skeletonDir . '/Resources/views/Error';

        $this->renderFile($fullSkeletonDir, '/error.html.twig', $rootDir . '/Resources/TwigBundle/views/Exception/error.html.twig', $parameters);
        $this->renderFile($fullSkeletonDir, '/error404.html.twig', $rootDir . '/Resources/TwigBundle/views/Exception/error404.html.twig', $parameters);
        $this->renderFile($fullSkeletonDir, '/error500.html.twig', $rootDir . '/Resources/TwigBundle/views/Exception/error500.html.twig', $parameters);
        $this->renderFile($fullSkeletonDir, '/error503.html.twig', $rootDir . '/Resources/TwigBundle/views/Exception/error503.html.twig', $parameters);

        $output->writeln('Generating Error Twig Templates : <info>OK</info>');
    }

    /**
     * @param Bundle          $bundle
     * @param OutputInterface $output
     */
    public function generateAssets(Bundle $bundle, OutputInterface $output)
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
        $fullSkeletonDir = $this->skeletonDir . '/DataFixtures/ORM';

        try {
            $this->generateSkeletonBasedClass($fullSkeletonDir, $dirPath, 'DefaultSiteFixtures', $parameters);
        } catch (\Exception $error) {
            $output->writeln($this->dialog->getHelperSet()->get('formatter')->formatBlock($error->getMessage(), 'error'));
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
        $fullSkeletonDir = $this->skeletonDir . '/PagePartAdmin';

        try {
            $this->generateSkeletonBasedClass($fullSkeletonDir, $dirPath, 'BannerPagePartAdminConfigurator', $parameters);
        } catch (\Exception $error) {
            $output->writeln($this->dialog->getHelperSet()->get('formatter')->formatBlock($error->getMessage(), 'error'));
        }
        try {
            $this->generateSkeletonBasedClass($fullSkeletonDir, $dirPath, 'ContentPagePagePartAdminConfigurator', $parameters);
        } catch (\Exception $error) {
            $output->writeln($this->dialog->getHelperSet()->get('formatter')->formatBlock($error->getMessage(), 'error'));
        }
        try {
            $this->generateSkeletonBasedClass($fullSkeletonDir, $dirPath, 'FormPagePagePartAdminConfigurator', $parameters);
        } catch (\Exception $error) {
            $output->writeln($this->dialog->getHelperSet()->get('formatter')->formatBlock($error->getMessage(), 'error'));
        }

        try {
            $this->generateSkeletonBasedClass($fullSkeletonDir, $dirPath, 'HomePagePagePartAdminConfigurator', $parameters);
        } catch (\Exception $error) {
            $output->writeln($this->dialog->getHelperSet()->get('formatter')->formatBlock($error->getMessage(), 'error'));
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
        $fullSkeletonDir = $this->skeletonDir . '/Form/Pages';

        try {
            $this->generateSkeletonBasedClass($fullSkeletonDir, $dirPath, 'ContentPageAdminType', $parameters);
        } catch (\Exception $error) {
            $output->writeln($this->dialog->getHelperSet()->get('formatter')->formatBlock($error->getMessage(), 'error'));
        }
        try {
            $this->generateSkeletonBasedClass($fullSkeletonDir, $dirPath, 'FormPageAdminType', $parameters);
        } catch (\Exception $error) {
            $output->writeln($this->dialog->getHelperSet()->get('formatter')->formatBlock($error->getMessage(), 'error'));
        }
        try {
            $this->generateSkeletonBasedClass($fullSkeletonDir, $dirPath, 'HomePageAdminType', $parameters);
        } catch (\Exception $error) {
            $output->writeln($this->dialog->getHelperSet()->get('formatter')->formatBlock($error->getMessage(), 'error'));
        }

        $output->writeln('Generating forms : <info>OK</info>');
    }

    /**
     * @param Bundle          $bundle     The bundle
     * @param array           $parameters The template parameters
     * @param OutputInterface $output
     */
    public function generateEntities(Bundle $bundle, array $parameters, OutputInterface $output)
    {
        $dirPath = sprintf("%s/Entity/Pages", $bundle->getPath());
        $fullSkeletonDir = sprintf("%s/Entity/Pages", $this->skeletonDir);

        try {
            $this->generateSkeletonBasedClass($fullSkeletonDir, $dirPath, 'ContentPage', $parameters);
        } catch (\Exception $error) {
            $output->writeln($this->dialog->getHelperSet()->get('formatter')->formatBlock($error->getMessage(), 'error'));
        }
        try {
            $this->generateSkeletonBasedClass($fullSkeletonDir, $dirPath, 'FormPage', $parameters);
        } catch (\Exception $error) {
            $output->writeln($this->dialog->getHelperSet()->get('formatter')->formatBlock($error->getMessage(), 'error'));
        }
        try {
            $this->generateSkeletonBasedClass($fullSkeletonDir, $dirPath, 'HomePage', $parameters);
        } catch (\Exception $error) {
            $output->writeln($this->dialog->getHelperSet()->get('formatter')->formatBlock($error->getMessage(), 'error'));
        }

        $output->writeln('Generating entities : <info>OK</info>');
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
