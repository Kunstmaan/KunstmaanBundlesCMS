<?php

namespace Kunstmaan\GeneratorBundle\Generator;

use Kunstmaan\GeneratorBundle\Helper\GeneratorUtils;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\DependencyInjection\Container;

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
     * @param Bundle          $bundle  The bundle
     * @param string          $prefix  The prefix
     * @param string          $rootDir The root directory
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
        $this->generateFixtures($bundle, $parameters);
        $this->generateAssets($bundle);
        $this->generateTemplates($bundle, $parameters, $rootDir);
    }

    /**
     * @param Bundle          $bundle     The bundle
     * @param array           $parameters The template parameters
     * @param string          $rootDir    The root directory
     */
    public function generateTemplates(Bundle $bundle, array $parameters, $rootDir)
    {
        $dirPath = $bundle->getPath();
        $fullSkeletonDir = $this->skeletonDir . '/Resources/views';

        $this->filesystem->copy($fullSkeletonDir . '/Default/index.html.twig', $dirPath . '/Resources/views/Default/index.html.twig');
        GeneratorUtils::prepend("{% extends '" . $bundle->getName() .":Layout:layout.html.twig' %}\n", $dirPath . '/Resources/views/Default/index.html.twig');

        $this->renderFile($fullSkeletonDir, '/Page/layout.html.twig', $dirPath . '/Resources/views/Page/layout.html.twig', $parameters);

        $this->filesystem->copy($fullSkeletonDir . '/ContentPage/view.html.twig', $dirPath . '/Resources/views/ContentPage/view.html.twig');
        GeneratorUtils::prepend("{% extends '" . $bundle->getName() .":Page:layout.html.twig' %}\n", $dirPath . '/Resources/views/ContentPage/view.html.twig');

        $this->filesystem->copy($fullSkeletonDir . '/Form/fields.html.twig', $dirPath . '/Resources/views/Form/fields.html.twig');

        $this->filesystem->copy($fullSkeletonDir . '/FormPage/view.html.twig', $dirPath . '/Resources/views/FormPage/view.html.twig');
        GeneratorUtils::prepend("{% extends '" . $bundle->getName() .":Page:layout.html.twig' %}\n", $dirPath . '/Resources/views/FormPage/view.html.twig');
        GeneratorUtils::replace("~~~BUNDLE~~~", $bundle->getName(), $dirPath . '/Resources/views/FormPage/view.html.twig');

        $this->filesystem->copy($fullSkeletonDir . '/HomePage/view.html.twig', $dirPath . '/Resources/views/HomePage/view.html.twig');
        GeneratorUtils::prepend("{% extends '" . $bundle->getName() .":Page:layout.html.twig' %}\n", $dirPath . '/Resources/views/HomePage/view.html.twig');

        $this->filesystem->copy($fullSkeletonDir . '/Layout/layout.html.twig', $dirPath . '/Resources/views/Layout/layout.html.twig');
        GeneratorUtils::replace("~~~CSS~~~", "{% include '" . $bundle->getName() .":Layout:_css.html.twig' %}\n", $dirPath . '/Resources/views/Layout/layout.html.twig');
        GeneratorUtils::replace("~~~TOP_JS~~~", "{% include '" . $bundle->getName() .":Layout:_js_header.html.twig' %}\n", $dirPath . '/Resources/views/Layout/layout.html.twig');
        GeneratorUtils::replace("~~~FOOTER_JS~~~", "{% include '" . $bundle->getName() .":Layout:_js_footer.html.twig' %}\n", $dirPath . '/Resources/views/Layout/layout.html.twig');

        $this->renderFile($fullSkeletonDir, '/Layout/_css.html.twig', $dirPath . '/Resources/views/Layout/_css.html.twig', $parameters);
        $this->renderFile($fullSkeletonDir, '/Layout/_js_footer.html.twig', $dirPath . '/Resources/views/Layout/_js_footer.html.twig', $parameters);
        $this->renderFile($fullSkeletonDir, '/Layout/_js_header.html.twig', $dirPath . '/Resources/views/Layout/_js_header.html.twig', $parameters);

        $this->output->writeln('Generating Twig Templates : <info>OK</info>');

        // @todo: should be improved
        GeneratorUtils::replace("[ \"KunstmaanAdminBundle\"", "[ \"KunstmaanAdminBundle\", \"". $bundle->getName()  ."\"", $rootDir . '/config/config.yml');

        $this->output->writeln('Configure assetic : <info>OK</info>');
    }

    /**
     * @param Bundle $bundle
     */
    public function generateAssets(Bundle $bundle)
    {
        $dirPath = $bundle->getPath();
        $fullSkeletonDir = $this->skeletonDir . '/Resources/public';

        $assets = array(
            '/css/style.css',
            '/js/script.js',
            '/js/libs/boxsizing.htc',
            '/js/libs/jquery-1.8.1.min.js',
            '/js/libs/modernizr-2.6.2.min.js',
            '/js/libs/respond.min.js',
            '/sass/_480.scss',
            '/sass/_768.scss',
            '/sass/_1024.scss',
            '/sass/_debug.scss',
            '/sass/_fallbacks.scss',
            '/sass/_main.scss',
            '/sass/_mixins.scss',
            '/sass/_normalizer.scss',
            '/sass/_print.scss',
            '/sass/style.scss',
            '/apple-touch-icon-57x57-precomposed.png',
            '/apple-touch-icon-72x72-precomposed.png',
            '/apple-touch-icon-114x114-precomposed.png',
            '/apple-touch-icon-precomposed.png',
            '/apple-touch-icon.png',
            '/favicon.ico'
        );

        foreach ($assets as $asset) {
            $this->filesystem->copy(sprintf("%s%s", $fullSkeletonDir, $asset), sprintf("%s/Resources/public%s", $dirPath, $asset));
        }

        $this->output->writeln('Generating Assets : <info>OK</info>');
    }

    /**
     * @param Bundle          $bundle     The bundle
     * @param array           $parameters The template parameters
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
     * @param Bundle          $bundle     The bundle
     * @param array           $parameters The template parameters
     *
     * @throws \RuntimeException
     */
    public function generatePagepartConfigs(Bundle $bundle, array $parameters)
    {
        $dirPath = $bundle->getPath() . '/PagePartAdmin';
        $fullSkeletonDir = $this->skeletonDir . '/PagePartAdmin';

        try {
            $this->generateSkeletonBasedClass($fullSkeletonDir, $dirPath, 'BannerPagePartAdminConfigurator', $parameters);
        } catch (\Exception $error) {
            $this->output->writeln($this->dialog->getHelperSet()->get('formatter')->formatBlock($error->getMessage(), 'error'));
        }
        try {
            $this->generateSkeletonBasedClass($fullSkeletonDir, $dirPath, 'ContentPagePagePartAdminConfigurator', $parameters);
        } catch (\Exception $error) {
            $this->output->writeln($this->dialog->getHelperSet()->get('formatter')->formatBlock($error->getMessage(), 'error'));
        }
        try {
            $this->generateSkeletonBasedClass($fullSkeletonDir, $dirPath, 'FormPagePagePartAdminConfigurator', $parameters);
        } catch (\Exception $error) {
            $this->output->writeln($this->dialog->getHelperSet()->get('formatter')->formatBlock($error->getMessage(), 'error'));
        }

        $this->output->writeln('Generating forms : <info>OK</info>');

        try {
            $this->generateSkeletonBasedClass($fullSkeletonDir, $dirPath, 'HomePagePagePartAdminConfigurator', $parameters);
        } catch (\Exception $error) {
            $this->output->writeln($this->dialog->getHelperSet()->get('formatter')->formatBlock($error->getMessage(), 'error'));
        }

        $this->output->writeln('Generating PagePart Configurators : <info>OK</info>');
    }

    /**
     * @param Bundle          $bundle     The bundle
     * @param array           $parameters The template parameters
     *
     * @throws \RuntimeException
     */
    public function generateForm(Bundle $bundle, array $parameters)
    {
        $dirPath = $bundle->getPath() . '/Form';
        $fullSkeletonDir = $this->skeletonDir . '/Form';

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
     * @param Bundle          $bundle     The bundle
     * @param array           $parameters The template parameters
     */
    public function generateEntities(Bundle $bundle, array $parameters)
    {
        $dirPath = sprintf("%s/Entity", $bundle->getPath());
        $fullSkeletonDir = sprintf("%s/Entity", $this->skeletonDir);

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