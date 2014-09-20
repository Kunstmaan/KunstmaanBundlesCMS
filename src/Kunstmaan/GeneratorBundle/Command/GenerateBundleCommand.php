<?php

namespace Kunstmaan\GeneratorBundle\Command;
use Kunstmaan\GeneratorBundle\Generator\BundleGenerator;
use Kunstmaan\GeneratorBundle\Helper\GeneratorUtils;
use Sensio\Bundle\GeneratorBundle\Command\GeneratorCommand;
use Sensio\Bundle\GeneratorBundle\Command\Helper\DialogHelper;
use Sensio\Bundle\GeneratorBundle\Command\Validators;
use Sensio\Bundle\GeneratorBundle\Manipulator\KernelManipulator;
use Sensio\Bundle\GeneratorBundle\Manipulator\RoutingManipulator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Generates bundles.
 */
class GenerateBundleCommand extends GeneratorCommand
{
    private $generator;

    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setDefinition(
                array(new InputOption('namespace', '', InputOption::VALUE_REQUIRED, 'The namespace of the bundle to create'),
                new InputOption('dir', '', InputOption::VALUE_REQUIRED, 'The directory where to create the bundle'),
                new InputOption('bundle-name', '', InputOption::VALUE_REQUIRED, 'The optional bundle name'),))
            ->setHelp(
                <<<EOT
            The <info>generate:bundle</info> command helps you generates new bundles.

By default, the command interacts with the developer to tweak the generation.
Any passed option will be used as a default value for the interaction
(<comment>--namespace</comment> is the only one needed if you follow the
conventions):

<info>php app/console kuma:generate:bundle --namespace=Acme/BlogBundle</info>

Note that you can use <comment>/</comment> instead of <comment>\\</comment> for the namespace delimiter to avoid any
problem.

If you want to disable any user interaction, use <comment>--no-interaction</comment> but don't forget to pass all needed options:

<info>php app/console kuma:generate:bundle --namespace=Acme/BlogBundle --dir=src [--bundle-name=...] --no-interaction</info>

Note that the bundle namespace must end with "Bundle".
EOT
            )
            ->setName('kuma:generate:bundle');
    }

    /**
     * Executes the command.
     *
     * @param InputInterface  $input  An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     *
     * @throws \RuntimeException
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getDialogHelper();

        if ($input->isInteractive()) {
            if (!$dialog->askConfirmation($output, $dialog->getQuestion('Do you confirm generation', 'yes', '?'), true)) {
                $output->writeln('<error>Command aborted</error>');

                return 1;
            }
        }

        GeneratorUtils::ensureOptionsProvided($input, array('namespace', 'dir'));

        $namespace = Validators::validateBundleNamespace($input->getOption('namespace'));
        if (!$bundle = $input->getOption('bundle-name')) {
            $bundle = strtr($namespace, array('\\' => ''));
        }
        $bundle = Validators::validateBundleName($bundle);
        $dir = Validators::validateTargetDir($input->getOption('dir'), $bundle, $namespace);
        $format = 'yml';

        $dialog->writeSection($output, 'Bundle generation');

        if (!$this
            ->getContainer()
            ->get('filesystem')
            ->isAbsolutePath($dir)
        ) {
            $dir = getcwd() . '/' . $dir;
        }

        $generator = $this->getGenerator($this->getApplication()->getKernel()->getBundle("KunstmaanGeneratorBundle"));
        $generator->generate($namespace, $bundle, $dir, $format);

        $output->writeln('Generating the bundle code: <info>OK</info>');

        $errors = array();
        $runner = $dialog->getRunner($output, $errors);

        // check that the namespace is already autoloaded
        $runner($this->checkAutoloader($output, $namespace, $bundle));

        // register the bundle in the Kernel class
        $runner($this->updateKernel($dialog, $input, $output, $this->getContainer()->get('kernel'), $namespace, $bundle));

        // routing
        $runner($this->updateRouting($dialog, $input, $output, $bundle, $format));

        $dialog->writeGeneratorSummary($output, $errors);
    }

    /**
     * Executes the command.
     *
     * @param InputInterface  $input  An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     *
     * @return void
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getDialogHelper();
        $dialog->writeSection($output, 'Welcome to the Kunstmaan bundle generator');

        // namespace
        $output
            ->writeln(
                array('', 'Your application code must be written in <comment>bundles</comment>. This command helps', 'you generate them easily.', '',
                'Each bundle is hosted under a namespace (like <comment>Acme/Bundle/BlogBundle</comment>).',
                'The namespace should begin with a "vendor" name like your company name, your', 'project name, or your client name, followed by one or more optional category',
                'sub-namespaces, and it should end with the bundle name itself', '(which must have <comment>Bundle</comment> as a suffix).', '',
                'See http://symfony.com/doc/current/cookbook/bundles/best_practices.html#index-1 for more', 'details on bundle naming conventions.', '',
                'Use <comment>/</comment> instead of <comment>\\ </comment>for the namespace delimiter to avoid any problems.', '',));

        $namespace = $dialog
            ->askAndValidate($output, $dialog->getQuestion('Bundle namespace', $input->getOption('namespace')),
                array('Sensio\Bundle\GeneratorBundle\Command\Validators', 'validateBundleNamespace'), false, $input->getOption('namespace'));
        $input->setOption('namespace', $namespace);

        // bundle name
        $bundle = $input->getOption('bundle-name') ? : strtr($namespace, array('\\Bundle\\' => '', '\\' => ''));
        $output
            ->writeln(
                array('', 'In your code, a bundle is often referenced by its name. It can be the', 'concatenation of all namespace parts but it\'s really up to you to come',
                'up with a unique name (a good practice is to start with the vendor name).', 'Based on the namespace, we suggest <comment>' . $bundle . '</comment>.', '',));
        $bundle = $dialog->askAndValidate($output, $dialog->getQuestion('Bundle name', $bundle), array('Sensio\Bundle\GeneratorBundle\Command\Validators', 'validateBundleName'), false, $bundle);
        $input->setOption('bundle-name', $bundle);

        // target dir
        $dir = $input->getOption('dir') ? : dirname($this
            ->getContainer()
            ->getParameter('kernel.root_dir')) . '/src';
        $output->writeln(array('', 'The bundle can be generated anywhere. The suggested default directory uses', 'the standard conventions.', '',));
        $dir = $dialog
            ->askAndValidate($output, $dialog->getQuestion('Target directory', $dir),
                function ($dir) use ($bundle, $namespace) {
                    return Validators::validateTargetDir($dir, $bundle, $namespace);
                }, false, $dir);
        $input->setOption('dir', $dir);

        // format
        $output->writeln(array('', 'Determine the format to use for the generated configuration.', '',));
        $output->writeln(array('', 'Determined \'yml\' to be used as the format for the generated configuration', '',));
        $format = 'yml';

        // summary
        $output
            ->writeln(
                array('', $this
                    ->getHelper('formatter')
                    ->formatBlock('Summary before generation', 'bg=blue;fg=white', true), '',
                    sprintf("You are going to generate a \"<info>%s\\%s</info>\" bundle\nin \"<info>%s</info>\" using the \"<info>%s</info>\" format.", $namespace, $bundle, $dir, $format),
                    '',));
    }

    /**
     * @param OutputInterface $output    The output
     * @param string          $namespace The namespace
     * @param string          $bundle    The bundle name
     *
     * @return array
     */
    protected function checkAutoloader(OutputInterface $output, $namespace, $bundle)
    {
        $output->write('Checking that the bundle is autoloaded: ');
        if (!class_exists($namespace . '\\' . $bundle)) {
            return array('- Edit the <comment>composer.json</comment> file and register the bundle', '  namespace in the "autoload" section:', '',);
        }
    }

    /**
     * @param DialogHelper    $dialog    The dialog helper
     * @param InputInterface  $input     The command input
     * @param OutputInterface $output    The command output
     * @param KernelInterface $kernel    The kernel
     * @param string          $namespace The namespace
     * @param string          $bundle    The bundle
     *
     * @return array
     */
    protected function updateKernel(DialogHelper $dialog, InputInterface $input, OutputInterface $output, KernelInterface $kernel, $namespace, $bundle)
    {
        $auto = true;
        if ($input->isInteractive()) {
            $auto = $dialog->askConfirmation($output, $dialog->getQuestion('Confirm automatic update of your Kernel', 'yes', '?'), true);
        }

        $output->write('Enabling the bundle inside the Kernel: ');
        $manip = new KernelManipulator($kernel);
        try {
            $ret = $auto ? $manip->addBundle($namespace . '\\' . $bundle) : false;

            if (!$ret) {
                $reflected = new \ReflectionObject($kernel);

                return array(sprintf('- Edit <comment>%s</comment>', $reflected->getFilename()), '  and add the following bundle in the <comment>AppKernel::registerBundles()</comment> method:', '',
                    sprintf('    <comment>new %s(),</comment>', $namespace . '\\' . $bundle), '',);
            }
        } catch (\RuntimeException $e) {
            return array(sprintf('Bundle <comment>%s</comment> is already defined in <comment>AppKernel::registerBundles()</comment>.', $namespace . '\\' . $bundle), '',);
        }
    }

    /**
     * @param DialogHelper    $dialog The dialog helper
     * @param InputInterface  $input  The command input
     * @param OutputInterface $output The command output
     * @param string          $bundle The bundle name
     * @param string          $format the format
     *
     * @return array
     */
    protected function updateRouting(DialogHelper $dialog, InputInterface $input, OutputInterface $output, $bundle, $format)
    {
        $auto = true;
        if ($input->isInteractive()) {
            $auto = $dialog->askConfirmation($output, $dialog->getQuestion('Confirm automatic update of the Routing', 'yes', '?'), true);
        }

        $output->write('Importing the bundle routing resource: ');
        $routing = new RoutingManipulator($this
            ->getContainer()
            ->getParameter('kernel.root_dir') . '/config/routing.yml');
        try {
            $ret = $auto ? $routing->addResource($bundle, $format) : false;
            if (!$ret) {
                $help = sprintf("        <comment>resource: \"@%s/Resources/config/routing.yml\"</comment>\n", $bundle);
                $help .= "        <comment>prefix:   /</comment>\n";

                return array('- Import the bundle\'s routing resource in the app main routing file:', '', sprintf('    <comment>%s:</comment>', $bundle), $help, '',);
            }
        } catch (\RuntimeException $e) {
            return array(sprintf('Bundle <comment>%s</comment> is already imported.', $bundle), '',);
        }
    }

    protected function createGenerator()
    {
        return new BundleGenerator($this->getContainer()->get('filesystem'));
    }
}
