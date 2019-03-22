<?php

namespace Kunstmaan\GeneratorBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Output\OutputInterface;
use Sensio\Bundle\GeneratorBundle\Command\Validators;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Process\Exception\RuntimeException;
use Sensio\Bundle\GeneratorBundle\Command\Helper\QuestionHelper;
use Symfony\Component\Debug\Exception\ContextErrorException;

/**
 * Kunstmaan installer
 */
class InstallCommand extends Command
{
    /**
     * @var int
     */
    private $commandSteps = 0;

    /** @var string */
    private $rootDir;

    /**
     * @param string $rootDir
     */
    public function __construct(string $rootDir)
    {
        $this->rootDir = $rootDir;

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
                        new InputOption('namespace', '', InputOption::VALUE_REQUIRED, 'The namespace of the bundle to create'),
                        new InputOption('demosite', '', InputOption::VALUE_REQUIRED, 'Do you want create "demosite"'),
                        new InputOption('dir', '', InputOption::VALUE_REQUIRED, 'The directory where to create the bundle'),
                        new InputOption('bundle-name', '', InputOption::VALUE_REQUIRED, 'The optional bundle name'),
                    ]
                )
            );
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $questionHelper = new QuestionHelper();

        $outputStyle = new SymfonyStyle($input, $output);
        $outputStyle->writeln('<info>Installing KunstmaanCms...</info>');
        $outputStyle->writeln($this->getKunstmaanLogo());

        $question = new Question(
            $questionHelper->getQuestion('Bundle namespace', $input->getOption('namespace')),
            $input->getOption('namespace')
        );
        $question->setValidator([Validators::class, 'validateBundleNamespace']);
        $namespace = $questionHelper->ask($input, $output, $question);
        $input->setOption('namespace', $namespace);

        $question = new ChoiceQuestion(
            'Do you want create "demosite"',
            ['No', 'Yes'],
            0
        );
        $question->setErrorMessage('Option "%s" is invalid.');
        $demositeOption = $questionHelper->ask($input, $output, $question);

        $input->setOption('demosite', $demositeOption);
        $input->setOption('bundle-name', strtr($namespace, ['\\Bundle\\' => '', '\\' => '']));

        $dir = $input->getOption('dir') ?: dirname($this->rootDir) . '/src';
        $input->setOption('dir', $dir);

        $output->writeln('<info>Installation start</info>');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $demositeOptions = ['--namespace' => $input->getOption('namespace')];
        if ($input->getOption('demosite') === 'Yes') {
            $demositeOptions['--demosite'] = true;
        }

        $this
            ->executeCommand($output, 'kuma:generate:bundle', [
                '--namespace' => $input->getOption('namespace'),
                '--dir' => $input->getOption('dir'),
                '--bundle-name' => $input->getOption('bundle-name'),
            ])
            ->executeCommand($output, 'kuma:generate:default-site', $demositeOptions)
            ->executeCommand($output, 'doctrine:database:create')
            ->executeCommand($output, 'doctrine:schema:drop', ['--force' => true])
            ->executeCommand($output, 'doctrine:schema:create')
            ->executeCommand($output, 'doctrine:fixtures:load')
            ->executeCommand($output, 'kuma:generate:admin-tests', [
                '--namespace' => $input->getOption('namespace'),
            ])
        ;
    }

    protected function executeCommand(OutputInterface $output, $command, array $options = [])
    {
        $options = array_merge(
            [
                '--no-debug' => true,
                '--no-interaction' => true,
            ],
            $options
        );

        ++$this->commandSteps;

        try {
            $updateInput = new ArrayInput($options);
            $updateInput->setInteractive(false);
            $this->getApplication()->find($command)->run($updateInput, new NullOutput());
            $output->writeln(sprintf('<info>Step %d: "%s" - [OK]</info>', $this->commandSteps, $command));
        } catch (RuntimeException $exception) {
            $output->writeln(sprintf('<error>Step %d: "%s" - [FAILED]</error>', $this->commandSteps, $command));
        } catch (ContextErrorException $e) {
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
}
