<?php

namespace Kunstmaan\NodeBundle\Command;

use Kunstmaan\NodeBundle\Entity\NodeTranslationLock;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CleanupNodeTranslationLockCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();

        $this->setName('kuma:nodes:clean-lock')
            ->setAliases(array('kncl'))
            ->setDescription('Clean the node translation lock table')
            ->setHelp("The <info>kuma:nodes:clean-lock</info> will loop over all locked nodes and remove the record if threshold exceeded");
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $threshold = $this->getContainer()->getParameter('kunstmaan_node.lock_threshold');
        $enabled = $this->getContainer()->getParameter('kunstmaan_node.lock_enabled');

        if ($enabled) {
            $em = $this->getContainer()->get('doctrine.orm.entity_manager');

            $locks = $em->getRepository('KunstmaanNodeBundle:NodeTranslationLock')->getAllPassedLocks($threshold);

            if (count($locks)) {
                /** @var NodeTranslationLock $lock */
                foreach ($locks as $lock) {
                    $em->remove($lock);

                    $output->writeln(sprintf('Removed lock on node translation <info>%s</info> for user <info>%s</info>', $lock->getNodeTranslation()->getTitle(), $lock->getUser()));
                }

                $em->flush();

                $output->writeln('Done');
            } else {
                $output->writeln('No locks found');
            }
        }
        else {
            $output->writeln('Node translation lock is not enabled');
        }
    }
}
