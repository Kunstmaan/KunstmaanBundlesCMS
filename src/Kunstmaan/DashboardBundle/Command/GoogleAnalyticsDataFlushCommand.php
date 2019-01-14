<?php

namespace Kunstmaan\DashboardBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @final since 5.1
 * NEXT_MAJOR extend from `Command` and remove `$this->getContainer` usages
 */
class GoogleAnalyticsDataFlushCommand extends ContainerAwareCommand
{
    /** @var EntityManagerInterface */
    private $em;

    /**
     * @param EntityManagerInterface|null $em
     */
    public function __construct(/* EntityManagerInterface */ $em = null)
    {
        parent::__construct();

        if (!$em instanceof EntityManagerInterface) {
            @trigger_error(sprintf('Passing a command name as the first argument of "%s" is deprecated since version symfony 3.4 and will be removed in symfony 4.0. If the command was registered by convention, make it a service instead. ', __METHOD__), E_USER_DEPRECATED);

            $this->setName(null === $em ? 'kuma:dashboard:widget:googleanalytics:data:flush' : $em);

            return;
        }

        $this->em = $em;
    }

    protected function configure()
    {
        $this
            ->setName('kuma:dashboard:widget:googleanalytics:data:flush')
            ->setDescription('Flush the data of a config')
            ->addArgument(
                'config',
                InputArgument::OPTIONAL,
                'The config id'
            );
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (null === $this->em) {
            $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');
        }

        $configRepository = $this->em->getRepository('KunstmaanDashboardBundle:AnalyticsConfig');

        $configId = $input->getArgument('config') ? $input->getArgument('config') : false;

        try {
            $configRepository->flushConfig($configId);
            $output->writeln('<fg=green>Data flushed</fg=green>');
        } catch (\Exception $e) {
            $output->writeln('<fg=red>'.$e->getMessage().'</fg=red>');
        }
    }
}
