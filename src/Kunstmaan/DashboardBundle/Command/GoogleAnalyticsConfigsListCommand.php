<?php

namespace Kunstmaan\DashboardBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @final since 5.1
 * NEXT_MAJOR extend from `Command` and remove `$this->getContainer` usages
 */
class GoogleAnalyticsConfigsListCommand extends ContainerAwareCommand
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @param EntityManagerInterface|null $em
     */
    public function __construct(/* EntityManagerInterface */ $em = null)
    {
        parent::__construct();

        if (!$em instanceof EntityManagerInterface) {
            @trigger_error(sprintf('Passing a command name as the first argument of "%s" is deprecated since version symfony 3.4 and will be removed in symfony 4.0. If the command was registered by convention, make it a service instead. ', __METHOD__), E_USER_DEPRECATED);

            $this->setName(null === $em ? 'kuma:dashboard:widget:googleanalytics:config:list' : $em);

            return;
        }

        $this->em = $em;
    }

    protected function configure()
    {
        $this
            ->setName('kuma:dashboard:widget:googleanalytics:configs:list')
            ->setDescription('List available configs');
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

        $configs = $this->getconfigs();

        if (count($configs)) {
            $result = "\t".'<fg=green>' . count($configs) . '</fg=green> configs found:';
            $output->writeln($result);
            foreach ($configs as $config) {
                $result = "\t".'(id: <fg=cyan>' .$config->getId() . '</fg=cyan>)';
                $result .= "\t" . $config->getName();

                $output->writeln($result);
            }
        } else {
            $output->writeln('No configs found');
        }
    }

    /**
     * get all segments
     *
     * @return array
     */
    private function getconfigs()
    {
        // get all segments
        $configRepository = $this->em->getRepository('KunstmaanDashboardBundle:AnalyticsConfig');

        return $configRepository->findAll();
    }
}
