<?php

namespace Kunstmaan\MediaBundle\Command;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\MediaBundle\Entity\Media;
use Kunstmaan\MediaBundle\Helper\MediaManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

#[AsCommand(name: 'kuma:media:clean-deleted-media', description: 'Throw away all files from the file system that have been deleted in the database')]
final class CleanDeletedMediaCommand extends Command
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var MediaManager
     */
    private $mediaManager;

    public function __construct(EntityManagerInterface $em, MediaManager $mediaManager)
    {
        parent::__construct();

        $this->em = $em;
        $this->mediaManager = $mediaManager;
    }

    protected function configure(): void
    {
        parent::configure();

        $this
            ->setHelp(
                'The <info>kuma:media:clean-deleted-media</info> command can be used to clean up your file system after having deleted Media items using the backend.'
            )
            ->addOption(
                'force',
                'f',
                InputOption::VALUE_NONE,
                'If set does not prompt the user if he is certain he wants to remove Media'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($input->getOption('force') !== true) {
            $helper = $this->getHelper('question');
            $question = new ConfirmationQuestion('<question>Are you sure you want to remove all deleted Media from the file system?</question> ', false);

            if (!$helper->ask($input, $output, $question)) {
                return 0;
            }
        }

        $output->writeln('<info>Removing all Media from the file system that have their status set to deleted in the database.</info>');

        $medias = $this->em->getRepository(Media::class)->findAllDeleted();

        try {
            $this->em->beginTransaction();
            foreach ($medias as $media) {
                $this->mediaManager->removeMedia($media);
            }
            $this->em->flush();
            $this->em->commit();
            $output->writeln('<info>All Media flagged as deleted, have now been removed from the file system.<info>');

            return 0;
        } catch (\Exception $e) {
            $this->em->rollback();
            $output->writeln('An error occured while trying to delete Media from the file system:');
            $output->writeln('<error>' . $e->getMessage() . '</error>');

            return 1;
        }
    }
}
