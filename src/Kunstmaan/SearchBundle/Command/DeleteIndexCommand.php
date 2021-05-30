<?php

namespace Kunstmaan\SearchBundle\Command;

use Kunstmaan\SearchBundle\Configuration\SearchConfigurationChain;
use Kunstmaan\SearchBundle\Configuration\SearchConfigurationInterface;
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

    protected function configure()
    {
        $this
            ->setName('kuma:search:delete')
            ->setDescription('Delete the index(es)');
    }

    /**
     * @return int|null null or 0 if everything went fine, or an error code
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /**
         * @var string
         * @var SearchConfigurationInterface $searchConfiguration
         */
        foreach ($this->configurationChain->getConfigurations() as $alias => $searchConfiguration) {
            $searchConfiguration->deleteIndex();
            $output->writeln('Index deleted : ' . $alias);
        }

        return 0;
    }
}
