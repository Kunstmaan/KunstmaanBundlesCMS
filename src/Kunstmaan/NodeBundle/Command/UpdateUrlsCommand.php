<?php

namespace Kunstmaan\NodeBundle\Command;

use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * UpdateUrlsCommand
 */
class UpdateUrlsCommand extends ContainerAwareCommand
{

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();

        $this->setName('kuma:nodes:updateurls')
            ->setDescription('Update all urls for all translations.')
            ->setHelp("The <info>kuma:nodes:updateurls</info> will loop over all node translation entries and update the urls for the entries.");
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $mainNodes = $em->getRepository('KunstmaanNodeBundle:NodeTranslation')->getTopNodeTranslations();
        if (count($mainNodes)) {
            /* @var NodeTranslation $mainNode */
            foreach ($mainNodes as $mainNode) {
                $mainNode->setUrl('');
                $em->persist($mainNode);
                $em->flush($mainNode);
            }
        }

        $output->writeln('Updated all nodes');
    }
}
