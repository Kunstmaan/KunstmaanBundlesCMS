<?php

namespace Kunstmaan\SearchBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PopulateIndexCommand extends ContainerAwareCommand {

    protected function configure()
    {
        $this
            ->setName("kuma:search:populate")
            ->addArgument('full', InputArgument::OPTIONAL, 'Delete and create a new index before populating')
            ->setDescription("Populate the index")
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getArgument('full')) {
            $delete_command = $this->getApplication()->find('kuma:search:delete');
            $delete_command->execute(new ArrayInput(array()), $output);
            $setup_command = $this->getApplication()->find('kuma:search:setup');
            $setup_command->execute(new ArrayInput(array()), $output);
        }

        $indexerChain = $this->getContainer()->get('kunstmaan_search.searchconfiguration_chain');
        foreach($indexerChain->getIndexers() as $indexer){
            $indexer->index();
        }

        $output->writeln('Index populated');
    }

}