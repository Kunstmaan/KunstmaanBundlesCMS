<?php

namespace Kunstmaan\SearchBundle\Command;

use Kunstmaan\SearchBundle\Configuration\SearchConfigurationChain;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command to delete all indexes.
 *
 * It will load the SearchConfigurationChain and call the deleteIndex() method on each SearchConfiguration
 */
final class DeleteIndexCommand extends Command
{
    /**
     * @var SearchConfigurationChain
     */
    private $configurationChain;

    public function __construct(SearchConfigurationChain $configurationChain)
    {
        parent::__construct();

        $this->configurationChain = $configurationChain;
    }

    protected function configure(): void
    {
        $this
            ->setName('kuma:search:delete')
            ->setDescription('Delete the index(es)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->configurationChain->getConfigurations() as $alias => $searchConfiguration) {
            $searchConfiguration->deleteIndex();
            $output->writeln('Index deleted : ' . $alias);
        }

        return 0;
    }
}
