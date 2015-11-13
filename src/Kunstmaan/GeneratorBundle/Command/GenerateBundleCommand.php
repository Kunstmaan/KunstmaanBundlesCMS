<?php
namespace Kunstmaan\GeneratorBundle\Command;

use Kunstmaan\GeneratorBundle\Helper\GeneratorUtils;
use Sensio\Bundle\GeneratorBundle\Command\GeneratorCommand;
use Kunstmaan\GeneratorBundle\Generator\BundleGenerator;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Sensio\Bundle\GeneratorBundle\Command\Validators;
use Sensio\Bundle\GeneratorBundle\Manipulator\KernelManipulator;
use Sensio\Bundle\GeneratorBundle\Manipulator\RoutingManipulator;
use Sensio\Bundle\GeneratorBundle\Command\Helper\QuestionHelper;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ConfirmationQuestion;

/**
 * Generates bundles.
 */
class GenerateBundleCommand extends GeneratorCommand
{
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
     * @param InputInterface $input An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     *
     * @throws \RuntimeException
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $questionHelper = $this->getQuestionHelper();

        if ($input->isInteractive()) {
            $confirmationQuestion = new ConfirmationQuestion($questionHelper->getQuestion('Do you confirm generation', 'yes', '?'), true);
            if (!$questionHelper->ask($input, $output, $confirmationQuestion)) {
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
        $dir = $this::validateTargetDir($input->getOption('dir'), $bundle, $namespace);
        $format = 'yml';

        $questionHelper->writeSection($output, 'Bundle generation');

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
        $runner = $questionHelper->getRunner($output, $errors);

        // check that the namespace is already autoloaded
        $runner($this->checkAutoloader($output, $namespace, $bundle));

        // register the bundle in the Kernel class
        $runner($this->updateKernel($questionHelper, $input, $output, $this->getContainer()->get('kernel'), $namespace, $bundle));

        // routing
        $runner($this->updateRouting($questionHelper, $input, $output, $bundle, $format));

        $questionHelper->writeGeneratorSummary($output, $errors);
    }

    /**
     * Executes the command.
     *
     * @param InputInterface $input An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     *
     * @return void
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $questionHelper = $this->getQuestionHelper();
        $questionHelper->writeSection($output, 'Welcome to the Kunstmaan bundle generator');

        // namespace
        $output
            ->writeln(
                array('', 'Your application code must be written in <comment>bundles</comment>. This command helps', 'you generate them easily.', '',
                    'Each bundle is hosted under a namespace (like <comment>Acme/Bundle/BlogBundle</comment>).',
                    'The namespace should begin with a "vendor" name like your company name, your', 'project name, or your client name, followed by one or more optional category',
                    'sub-namespaces, and it should end with the bundle name itself', '(which must have <comment>Bundle</comment> as a suffix).', '',
                    'See http://symfony.com/doc/current/cookbook/bundles/best_practices.html#index-1 for more', 'details on bundle naming conventions.', '',
                    'Use <comment>/</comment> instead of <comment>\\ </comment>for the namespace delimiter to avoid any problems.', '',));

        $question = new Question($questionHelper->getQuestion('Bundle namespace', $input->getOption('namespace')), $input->getOption('namespace'));
        $question->setValidator(array('Sensio\Bundle\GeneratorBundle\Command\Validators', 'validateBundleNamespace'));
        $namespace = $questionHelper->ask($input, $output, $question);
        $input->setOption('namespace', $namespace);

        // bundle name
        $bundle = $input->getOption('bundle-name') ?: strtr($namespace, array('\\Bundle\\' => '', '\\' => ''));
        $output
            ->writeln(
                array('', 'In your code, a bundle is often referenced by its name. It can be the', 'concatenation of all namespace parts but it\'s really up to you to come',
                    'up with a unique name (a good practice is to start with the vendor name).', 'Based on the namespace, we suggest <comment>' . $bundle . '</comment>.', '',));
        $question = new Question($questionHelper->getQuestion('Bundle name', $bundle), $bundle);
        $question->setValidator(array('Sensio\Bundle\GeneratorBundle\Command\Validators', 'validateBundleName'));
        $bundle = $questionHelper->ask($input, $output, $question);
        $input->setOption('bundle-name', $bundle);

        // target dir
        $dir = $input->getOption('dir') ?: dirname($this
                ->getContainer()
                ->getParameter('kernel.root_dir')) . '/src';
        $output->writeln(array('', 'The bundle can be generated anywhere. The suggested default directory uses', 'the standard conventions.', '',));
        $question = new Question($questionHelper->getQuestion('Target directory', $dir), $dir);
        $question->setValidator(function ($dir) use ($bundle, $namespace) {
            return $this::validateTargetDir($dir, $bundle, $namespace);
        });
        $dir = $questionHelper->ask($input, $output, $question);
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
     * @param OutputInterface $output The output
     * @param string $namespace The namespace
     * @param string $bundle The bundle name
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
     * @param QuestionHelper $questionHelper The question helper
     * @param InputInterface $input The command input
     * @param OutputInterface $output The command output
     * @param KernelInterface $kernel The kernel
     * @param string $namespace The namespace
     * @param string $bundle The bundle
     *
     * @return array
     */
    protected function updateKernel(QuestionHelper $questionHelper, InputInterface $input, OutputInterface $output, KernelInterface $kernel, $namespace, $bundle)
    {
        $auto = true;
        if ($input->isInteractive()) {
            $confirmationQuestion = new ConfirmationQuestion($questionHelper->getQuestion('Confirm automatic update of your Kernel', 'yes', '?'), true);
            $auto = $questionHelper->ask($input, $output, $confirmationQuestion);
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
     * @param QuestionHelper $questionHelper The question helper
     * @param InputInterface $input The command input
     * @param OutputInterface $output The command output
     * @param string $bundle The bundle name
     * @param string $format The format
     *
     * @return array
     */
    protected function updateRouting(QuestionHelper $questionHelper, InputInterface $input, OutputInterface $output, $bundle, $format)
    {
        $auto = true;
        if ($input->isInteractive()) {
            $confirmationQuestion = new ConfirmationQuestion($questionHelper->getQuestion('Confirm automatic update of the Routing', 'yes', '?'), true);
            $auto = $questionHelper->ask($input, $output, $confirmationQuestion);
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
        return new BundleGenerator();
    }

    /**
     * Validation function taken from <3.0 release of Sensio Generator bundle
     *
     * @param string $dir The target directory
     * @param string $bundle The bundle name
     * @param string $namespace The namespace
     *
     * @return string
     */
    public static function validateTargetDir($dir, $bundle, $namespace)
    {
        // add trailing / if necessary
        return '/' === substr($dir, -1, 1) ? $dir : $dir.'/';
    }    
}
