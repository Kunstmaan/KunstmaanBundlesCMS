<?php

namespace Kunstmaan\SearchBundle\Command;

use Kunstmaan\SearchBundle\Configuration\SearchConfigurationChain;
use Kunstmaan\SearchBundle\Configuration\SearchConfigurationInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command to populate all indexes. Use the 'full' argument when you want to delete and add all indexes again
 *
 * It will load the SearchConfigurationChain and call the populateIndex() method on each SearchConfiguration
 */
final class PopulateIndexCommand extends Command
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
            ->setName('kuma:search:populate')
            ->addArgument('full', InputArgument::OPTIONAL, 'Delete and create new index(es) before populating')
            ->setDescription('Populate the index(es)');
    }

    /**
     * @return int|null null or 0 if everything went fine, or an error code
     *
     * @throws \LogicException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getArgument('full')) {
            $deleteCommand = $this->getApplication()->find('kuma:search:delete');
            $deleteCommand->run(new ArrayInput([]), $output);

            $setupCommand = $this->getApplication()->find('kuma:search:setup');
            $setupCommand->setHelperSet($this->getHelperSet());
            $setupCommand->run(new ArrayInput([]), $output);
        }

        /**
         * @var string
         * @var SearchConfigurationInterface $searchConfiguration
         */
        foreach ($this->configurationChain->getConfigurations() as $alias => $searchConfiguration) {
            $searchConfiguration->populateIndex();
            $output->writeln('Index populated : ' . $alias);
        }

        return 0;
    }
}
