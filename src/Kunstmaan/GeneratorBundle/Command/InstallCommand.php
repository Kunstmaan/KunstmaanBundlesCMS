<?php
namespace Kunstmaan\GeneratorBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class InstallCommand extends ContainerAwareCommand {

    protected function configure()
    {
        $this
            ->setName('kuma:install')
            ->setDescription('Generates a full bundle site');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->runCommand('kuma:generate:bundle', 'Generating a new bundle', $input, $output);

        $this->runCommand('kuma:generate:default-site', 'Generating the default site', $input, $output)
            ->runCommand('kuma:generate:admin-tests', 'Generating the admin test suite', $input, $output)
            ->runCommand('kuma:generate:article', 'Generating a news area', $input, $output)
            ->runCommand('doctrine:schema:create', 'Creating the DB schema', $input, $output)
            ->runCommand('doctrine:fixtures:load', 'Loading the fixtures', $input, $output);

        $this
            ->runCLICommand("npm install", $output)
            ->runCLICommand("bower install", $output)
            ->runCLICommand("grunt build", $output);

        $this
            ->runCommand('assets:install', 'Installing the assets', $input, $output)
            ->runCommand('assetic:dump', 'Dumping via Assetic',$input, $output);
    }

    private function runCommand($command, $message, InputInterface $input, OutputInterface $output)
    {
        $output->writeln("<info>$message</info>");
        $this->getApplication()->find($command)->run($input, $output);
        return $this;
    }

    private function runCLICommand($command, OutputInterface $output){
        $output->writeln("<info>Running $command</info>");
        $process = new Process($command);
        $process->run();
        return $this;
    }

}
