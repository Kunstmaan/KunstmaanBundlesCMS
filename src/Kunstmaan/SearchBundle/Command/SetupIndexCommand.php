<?php

namespace Kunstmaan\SearchBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command to create the indexes
 *
 * It will load the SearchConfigurationChain and call the createIndex() method on each SearchConfguration
 */
class SetupIndexCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName("kuma:search:setup")
            ->setDescription("Set up the index(es)")
            ;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $searchConfigurationChain = $this->getContainer()->get('kunstmaan_search.searchconfiguration_chain');
        foreach ($searchConfigurationChain->getSearchConfigurations() as $alias => $searchConfiguration) {
            $searchConfiguration->createIndex();
            $output->writeln('Index created : ' . $alias);
        }
    }

}
