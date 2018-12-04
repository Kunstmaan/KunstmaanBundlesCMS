<?php

namespace Kunstmaan\SearchBundle\Command;

use Kunstmaan\SearchBundle\Configuration\SearchConfigurationChain;
use Kunstmaan\SearchBundle\Configuration\SearchConfigurationInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command to populate all indexes. Use the 'full' argument when you want to delete and add all indexes again
 *
 * It will load the SearchConfigurationChain and call the populateIndex() method on each SearchConfiguration
 *
 * @final since 5.1
 * NEXT_MAJOR extend from `Command` and remove `$this->getContainer` usages
 */
class PopulateIndexCommand extends ContainerAwareCommand
{
    /**
     * @var SearchConfigurationChain
     */
    private $configurationChain;

    public function __construct(/* SearchConfigurationChain */ $configurationChain = null)
    {
        parent::__construct();

        if (!$configurationChain instanceof SearchConfigurationChain) {
            @trigger_error(sprintf('Passing a command name as the first argument of "%s" is deprecated since version symfony 3.4 and will be removed in symfony 4.0. If the command was registered by convention, make it a service instead. ', __METHOD__), E_USER_DEPRECATED);

            $this->setName(null === $configurationChain ? 'kuma:search:populate' : $configurationChain);

            return;
        }

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
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return null|int null or 0 if everything went fine, or an error code
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

        if (null === $this->configurationChain) {
            $this->configurationChain = $this->getContainer()->get('kunstmaan_search.search_configuration_chain');
        }

        /**
         * @var string
         * @var SearchConfigurationInterface $searchConfiguration
         */
        foreach ($this->configurationChain->getConfigurations() as $alias => $searchConfiguration) {
            $searchConfiguration->populateIndex();
            $output->writeln('Index populated : ' . $alias);
        }
    }
}
