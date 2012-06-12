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

        $nodeTranslations = $em->getRepository('KunstmaanAdminNodeBundle:NodeTranslation')->findAll();
       
        foreach($nodeTranslations as $nodeTranslation) {
           $nodeTranslation->setWeight($nodeTranslation->getNode()->getSequencenumber());

           $em->persist($nodeTranslation);
           $em->flush();
        }

        $output->writeln('Updated all nodes');
    }
}
