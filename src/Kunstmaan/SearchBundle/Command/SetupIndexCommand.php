<?php

namespace Kunstmaan\SearchBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SetupIndexCommand extends ContainerAwareCommand {

    protected function configure()
    {
        $this
            ->setName("kuma:search:setup")
            ->setDescription("Set up the index")
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $sherlock = $this->getContainer()->get('kunstmaan_search.sherlock');
        $response = $sherlock->setupIndex();

        $output->writeln('Index created : ' . $response);
    }


}