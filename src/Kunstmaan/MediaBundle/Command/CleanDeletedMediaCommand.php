<?php

namespace Kunstmaan\MediaBundle\Command;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\MediaBundle\Helper\MediaManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

/**
 * @final since 5.1
 * NEXT_MAJOR extend from `Command` and remove `$this->getContainer` usages
 */
class CleanDeletedMediaCommand extends ContainerAwareCommand
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var MediaManager
     */
    private $mediaManager;

    /**
     * @param EntityManagerInterface|null $em
     * @param MediaManager|null           $mediaManager
     */
    public function __construct(/* EntityManagerInterface */ $em = null, /* MediaManager */ $mediaManager = null)
    {
        parent::__construct();

        if (!$em instanceof EntityManagerInterface) {
            @trigger_error(sprintf('Passing a command name as the first argument of "%s" is deprecated since version symfony 3.4 and will be removed in symfony 4.0. If the command was registered by convention, make it a service instead. ', __METHOD__), E_USER_DEPRECATED);

            $this->setName(null === $em ? 'kuma:media:clean-deleted-media' : $em);

            return;
        }

        $this->em = $em;
        $this->mediaManager = $mediaManager;
    }

    protected function configure()
    {
        parent::configure();

        $this
            ->setName('kuma:media:clean-deleted-media')
            ->setDescription('Throw away all files from the file system that have been deleted in the database')
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

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (null === $this->em) {
            $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');
            $this->mediaManager = $this->getContainer()->get('kunstmaan_media.media_manager');
        }

        if ($input->getOption('force') !== true) {
            $helper = $this->getHelper('question');
            $question = new ConfirmationQuestion('<question>Are you sure you want to remove all deleted Media from the file system?</question> ', false);

            if (!$helper->ask($input, $output, $question)) {
                return;
            }
        }

        $output->writeln('<info>Removing all Media from the file system that have their status set to deleted in the database.</info>');

        $medias = $this->em->getRepository('KunstmaanMediaBundle:Media')->findAllDeleted();

        try {
            $this->em->beginTransaction();
            foreach ($medias as $media) {
                $this->mediaManager->removeMedia($media);
            }
            $this->em->flush();
            $this->em->commit();
            $output->writeln('<info>All Media flagged as deleted, have now been removed from the file system.<info>');
        } catch (\Exception $e) {
            $this->em->rollback();
            $output->writeln('An error occured while trying to delete Media from the file system:');
            $output->writeln('<error>'. $e->getMessage() . '</error>');
        }
    }
}
