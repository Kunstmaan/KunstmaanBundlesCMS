<?php

namespace Kunstmaan\DashboardBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\DashboardBundle\Entity\AnalyticsConfig;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'kuma:dashboard:widget:googleanalytics:config:flush', description: 'Flush configs')]
final class GoogleAnalyticsConfigFlushCommand extends Command
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();

        $this->em = $em;
    }

    protected function configure(): void
    {
        $this
            ->addOption('config', null, InputOption::VALUE_OPTIONAL, 'Specify to only flush one config', false)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $configRepository = $this->em->getRepository(AnalyticsConfig::class);
        $configId = $input->getOption('config');
        $configs = [];

        try {
            if ($configId) {
                $configs[] = $configRepository->find($configId);
            } else {
                $configs = $configRepository->findAll();
            }

            foreach ($configs as $config) {
                $this->em->remove($config);
            }
            $this->em->flush();
            $output->writeln('<fg=green>Config flushed</fg=green>');

            return 0;
        } catch (\Exception $e) {
            $output->writeln('<fg=red>' . $e->getMessage() . '</fg=red>');

            return 1;
        }
    }
}
