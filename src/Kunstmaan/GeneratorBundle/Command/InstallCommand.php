<?php

namespace Kunstmaan\GeneratorBundle\Command;

use Kunstmaan\GeneratorBundle\Helper\CommandAssistant;
use Sensio\Bundle\GeneratorBundle\Command\GeneratorCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Output\OutputInterface;
use Sensio\Bundle\GeneratorBundle\Command\Validators;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Process\Exception\RuntimeException;
use Sensio\Bundle\GeneratorBundle\Command\Helper\QuestionHelper;

/**
 * Kunstmaan installer
 */
final class InstallCommand extends GeneratorCommand
{
    /**
     * @var int
     */
    private $commandSteps = 0;

    /** @var CommandAssistant */
    private $assistant;

    /** @var string */
    private $projectDir;

    /** @var bool */
    private $shouldStop = false;

    /**
     * @param string $rootDir
     */
    public function __construct(string $projectDir)
    {
        $this->projectDir = $projectDir;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('kuma:install')
            ->setDescription('KunstmaanCMS installer.')
            ->setDefinition(
                new InputDefinition(
                    [
                        new InputOption('demosite', '', InputOption::VALUE_REQUIRED, 'Do you want to create a "demosite"'),
                        new InputOption('create-tests', '', InputOption::VALUE_REQUIRED, 'Do you want to create tests for you pages/pageparts'),
                        new InputOption('namespace', '', InputOption::VALUE_OPTIONAL, 'The namespace of the bundle to create (only for SF3)'),
                        new InputOption('dir', '', InputOption::VALUE_OPTIONAL, 'The directory where to create the bundle (only for SF3)'),
                        new InputOption('bundle-name', '', InputOption::VALUE_OPTIONAL, 'The optional bundle name (only for SF3)'),
                    ]
                )
            );
    }

    private function initAssistant($input, $output)
    {
        if (is_null($this->assistant)) {
            $this->assistant = new CommandAssistant();
            $this->assistant->setQuestionHelper($this->getQuestionHelper());
            $this->assistant->setKernel($this->getApplication()->getKernel());
        }
        $this->assistant->setOutput($output);
        $this->assistant->setInput($input);
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $this->initAssistant($input, $output);

        $questionHelper = new QuestionHelper();

        $outputStyle = new SymfonyStyle($input, $output);
        $outputStyle->writeln('<info>Installing KunstmaanCms...</info>');
        $outputStyle->writeln($this->getKunstmaanLogo());

        if (Kernel::VERSION_ID >= 40000) {
            $this->shouldStop = !$this->assistant->askConfirmation('We need access to your database. Are the database credentials setup properly? (y/n)', 'y');
            if ($this->shouldStop) {
                return;
            }
        }

        // Only ask namespace for Symfony 3
        if (Kernel::VERSION_ID < 40000) {
            $question = new Question(
                $questionHelper->getQuestion('Bundle namespace', $input->getOption('namespace')),
                $input->getOption('namespace')
            );
            $question->setValidator([Validators::class, 'validateBundleNamespace']);
            $namespace = $questionHelper->ask($input, $output, $question);
            $input->setOption('namespace', $namespace);
            $input->setOption('bundle-name', strtr($namespace, ['\\Bundle\\' => '', '\\' => '']));

            $dir = $input->getOption('dir') ?: $this->projectDir . '/src';
            $input->setOption('dir', $dir);
        }


        $demoSiteOption = $this->assistant->askConfirmation('Do you want to create a "demosite"? (y/n)', 'n');
        $input->setOption('demosite', $demoSiteOption);

        $createTests = $this->assistant->askConfirmation('Do you want to create tests for you pages/pageparts? (y/n)', 'n');
        $input->setOption('create-tests', $createTests);

        $output->writeln('<info>Installation start</info>');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($this->shouldStop) {
            return;
        }

        $this->initAssistant($input, $output);

        $defaultSiteOptions = [];
        if ($input->getOption('demosite') === 'Yes') {
            $defaultSiteOptions['--demosite'] = true;
        }

        if (Kernel::VERSION_ID < 40000) {
            $defaultSiteOptions = ['--namespace' => $input->getOption('namespace')];

            $this->executeCommand($output, 'kuma:generate:bundle', [
                '--namespace' => $input->getOption('namespace'),
                '--dir' => $input->getOption('dir'),
                '--bundle-name' => $input->getOption('bundle-name'),
            ]);
        }

        $this
            ->executeCommand($output, 'kuma:generate:config')
            ->executeCommand($output, 'kuma:generate:default-site', $defaultSiteOptions)
            ->executeCommand($output, 'doctrine:database:create')
            ->executeCommand($output, 'doctrine:schema:drop', ['--force' => true])
            ->executeCommand($output, 'doctrine:schema:create')
            ->executeCommand($output, 'doctrine:fixtures:load')
            ->executeCommand($output, 'assets:install')
        ;

        if ($input->getOption('create-tests') === 'Yes') {
            $adminTestOptions = [];
            if (Kernel::VERSION_ID < 40000) {
                $adminTestOptions = ['--namespace' => $input->getOption('namespace')];
            }

            $this->executeCommand($output, 'kuma:generate:admin-tests', $adminTestOptions);
        }

        $this->assistant->writeSection('Installation done. Enjoy your KunstmaanCMS', 'bg=green;fg=black');
        $this->assistant->writeSection('PRO TIP: If you like to use our frontend setup, run the buildUI.sh script or run the commands separate to compile the frontend assets. ', 'bg=blue;fg=white');
    }

    protected function executeCommand(OutputInterface $output, $command, array $options = [])
    {
        $options = array_merge(
            [
                '--no-debug' => true,
            ],
            $options
        );

        ++$this->commandSteps;

        try {
            $updateInput = new ArrayInput($options);
            $updateInput->setInteractive(true);
            $this->getApplication()->find($command)->run($updateInput, $output);
            $output->writeln(sprintf('<info>Step %d: "%s" - [OK]</info>', $this->commandSteps, $command));
        } catch (RuntimeException $exception) {
            $output->writeln(sprintf('<error>Step %d: "%s" - [FAILED]</error>', $this->commandSteps, $command));
        } catch (\Throwable $e) {
            $output->writeln(sprintf('<error>Step %d: "%s" - [FAILED]</error>', $this->commandSteps, $command));
        }

        return $this;
    }

    protected function getKunstmaanLogo()
    {
        return '
         /$$   /$$                                 /$$                                                  /$$$$$$                         
        | $$  /$$/                                | $$                                                 /$$__  $$                        
        | $$ /$$/  /$$   /$$ /$$$$$$$   /$$$$$$$ /$$$$$$   /$$$$$$/$$$$   /$$$$$$   /$$$$$$  /$$$$$$$ | $$  \__/ /$$$$$$/$$$$   /$$$$$$$
        | $$$$$/  | $$  | $$| $$__  $$ /$$_____/|_  $$_/  | $$_  $$_  $$ |____  $$ |____  $$| $$__  $$| $$      | $$_  $$_  $$ /$$_____/
        | $$  $$  | $$  | $$| $$  \ $$|  $$$$$$   | $$    | $$ \ $$ \ $$  /$$$$$$$  /$$$$$$$| $$  \ $$| $$      | $$ \ $$ \ $$|  $$$$$$ 
        | $$\  $$ | $$  | $$| $$  | $$ \____  $$  | $$ /$$| $$ | $$ | $$ /$$__  $$ /$$__  $$| $$  | $$| $$    $$| $$ | $$ | $$ \____  $$
        | $$ \  $$|  $$$$$$/| $$  | $$ /$$$$$$$/  |  $$$$/| $$ | $$ | $$|  $$$$$$$|  $$$$$$$| $$  | $$|  $$$$$$/| $$ | $$ | $$ /$$$$$$$/
        |__/  \__/ \______/ |__/  |__/|_______/    \___/  |__/ |__/ |__/ \_______/ \_______/|__/  |__/ \______/ |__/ |__/ |__/|_______/ 
        ';
    }

    protected function createGenerator()
    {
        // we don't need generator here
    }
}
