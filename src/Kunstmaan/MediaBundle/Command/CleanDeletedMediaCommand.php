<?php

namespace Kunstmaan\MediaBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class CleanDeletedMediaCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('kuma:media:clean-deleted-media')
            ->setDescription('Throw away all files from the file system that have been deleted in the database')
            ->setHelp(
                "The <info>kuma:media:clean-deleted-media</info> command can be used to clean up your file system after having deleted Media items using the backend."
            )
            ->addOption(
                'force',
                'f',
                InputOption::VALUE_NONE,
                'If set does not prompt the user if he is certain he wants to remove Media'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('force') !== true) {
            $helper = $this->getHelper('question');
            $question = new ConfirmationQuestion('<question>Are you sure you want to remove all deleted Media from the file system?</question> ', false);

            if (!$helper->ask($input, $output, $question)) {
                return;
            }
        }

        $output->writeln('<info>Removing all Media from the file system that have their status set to deleted in the database.</info>');

        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $mediaManager = $this->getContainer()->get('kunstmaan_media.media_manager');

        $medias = $em->getRepository('KunstmaanMediaBundle:Media')->findAllDeleted();
        try {
            $em->beginTransaction();
            foreach ($medias as $media) {
                $mediaManager->removeMedia($media);
            }
            $em->flush();
            $em->commit();
            $output->writeln('<info>All Media flagged as deleted, have now been removed from the file system.<info>');
        } catch (\Exception $e) {
            $em->rollback();
            $output->writeln('An error occured while trying to delete Media from the file system:');
            $output->writeln('<error>'. $e->getMessage() . '</error>');
        }
    }
}
