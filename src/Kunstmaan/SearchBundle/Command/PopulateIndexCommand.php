<?php

namespace Kunstmaan\SearchBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PopulateIndexCommand extends ContainerAwareCommand {

    protected function configure()
    {
        $this
            ->setName("kuma:search:populate")
            ->setDescription("Populate the index")
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $sherlock = $this->getContainer()->get('kunstmaan_search.sherlock');
        $response = $sherlock->populateIndex();

        $output->writeln('Index populated');
        $output->writeln($response);
    }


}