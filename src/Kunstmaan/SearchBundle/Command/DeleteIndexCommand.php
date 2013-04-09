<?php

namespace Kunstmaan\SearchBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command to delete all indexes
 */
class DeleteIndexCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName("kuma:search:delete")
            ->setDescription("Delete the index")
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $searchConfigurationChain = $this->getContainer()->get('kunstmaan_search.searchconfiguration_chain');
        foreach ($searchConfigurationChain->getSearchConfigurations() as $alias => $searchConfiguration) {
            $searchConfiguration->delete();
            $output->writeln('Index deleted : ' . $alias);
        }

    }

}
