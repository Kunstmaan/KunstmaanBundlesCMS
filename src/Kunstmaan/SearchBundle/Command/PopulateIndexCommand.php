<?php

namespace Kunstmaan\SearchBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command to populate all indexes. Use the 'full' argument when you want to delete and add all indexes again
 *
 * It will load the SearchConfigurationChain and call the populateIndex() method on each SearchConfguration
 */
class PopulateIndexCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName("kuma:search:populate")
            ->addArgument('full', InputArgument::OPTIONAL, 'Delete and create new index(es) before populating')
            ->setDescription("Populate the index(es)")
        ;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getArgument('full')) {
            $delete_command = $this->getApplication()->find('kuma:search:delete');
            $delete_command->execute(new ArrayInput(array()), $output);
            $setup_command = $this->getApplication()->find('kuma:search:setup');
            $setup_command->execute(new ArrayInput(array()), $output);
        }

        $searchConfigurationChain = $this->getContainer()->get('kunstmaan_search.searchconfiguration_chain');
        foreach ($searchConfigurationChain->getSearchConfigurations() as $alias => $searchconfiguration) {
            $searchconfiguration->populateIndex();
            $output->writeln('Index populated : ' . $alias);
        }
    }

}
