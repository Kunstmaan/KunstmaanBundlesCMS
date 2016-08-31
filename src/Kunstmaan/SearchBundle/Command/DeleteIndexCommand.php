<?php

namespace Kunstmaan\SearchBundle\Command;

use Kunstmaan\SearchBundle\Configuration\SearchConfigurationInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command to delete all indexes.
 *
 * It will load the SearchConfigurationChain and call the deleteIndex() method on each SearchConfiguration
 */
class DeleteIndexCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('kuma:search:delete')
            ->setDescription('Delete the index(es)');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return null|int null or 0 if everything went fine, or an error code
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $searchConfigurationChain = $this->getContainer()->get('kunstmaan_search.search_configuration_chain');
        /**
         * @var string
         * @var SearchConfigurationInterface $searchConfiguration
         */
        foreach ($searchConfigurationChain->getConfigurations() as $alias => $searchConfiguration) {
            $searchConfiguration->deleteIndex();
            $output->writeln('Index deleted : '.$alias);
        }
    }
}
