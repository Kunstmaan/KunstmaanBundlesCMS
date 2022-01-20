<?php

namespace Kunstmaan\DashboardBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\DashboardBundle\Entity\AnalyticsConfig;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class GoogleAnalyticsConfigsListCommand extends Command
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
            ->setName('kuma:dashboard:widget:googleanalytics:configs:list')
            ->setDescription('List available configs');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $configs = $this->getconfigs();

        if (\count($configs)) {
            $result = "\t" . '<fg=green>' . \count($configs) . '</fg=green> configs found:';
            $output->writeln($result);
            foreach ($configs as $config) {
                $result = "\t" . '(id: <fg=cyan>' . $config->getId() . '</fg=cyan>)';
                $result .= "\t" . $config->getName();

                $output->writeln($result);
            }
        } else {
            $output->writeln('No configs found');
        }

        return 0;
    }

    /**
     * get all segments
     */
    private function getconfigs(): array
    {
        // get all segments
        $configRepository = $this->em->getRepository(AnalyticsConfig::class);

        return $configRepository->findAll();
    }
}
