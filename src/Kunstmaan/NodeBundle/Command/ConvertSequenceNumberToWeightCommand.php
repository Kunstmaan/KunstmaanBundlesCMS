<?php

namespace Kunstmaan\AdminNodeBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Kunstmaan\AdminBundle\Entity\Group;

class ConvertSequenceNumberToWeightCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        parent::configure();

        $this->setName('Nodes:nodes:convertsequencenumbertoweight')
            ->setDescription('Set all the nodetranslations weights based on the nodes sequencenumber')
            ->setHelp("The <info>AdminNode:nodetranslations:updateweights</info> will loop over all nodetranslation and set their weight based on the nodes sequencenumber.");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $batchSize = 20; 
        $i =0;
        $q = $em->createQuery('select t from Kunstmaan\AdminNodeBundle\Entity\NodeTranslation t');
        
        $iterableResult = $q->iterate(); 
        
        while (($row = $iterableResult->next()) !== false){
            if($row[0]->getWeight() == null){
                $output->writeln('editing node: '. $row[0]->getTitle());
                $row[0]->setWeight($row[0]->getNode()->getSequencenumber());
                $em->persist($row[0]); 
            }
            if(($i % $batchSize) == 0){
                $em->flush(); 
                $em->clear();
            }
            ++$i; 
        }

        $output->writeln('Updated all nodes');
    }
}
