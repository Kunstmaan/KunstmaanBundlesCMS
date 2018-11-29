<?php

namespace Kunstmaan\MediaBundle\Command;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\MediaBundle\Entity\Media;
use Kunstmaan\MediaBundle\Helper\File\FileHandler;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @final since 5.1
 * NEXT_MAJOR extend from `Command` and remove `$this->getContainer` usages
 */
class RenameSoftDeletedCommand extends ContainerAwareCommand
{
    /** @var EntityManager */
    protected $em;

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

            $this->setName(null === $em ? 'kuma:media:rename-soft-deleted' : $em);

            return;
        }

        $this->em = $em;
        $this->mediaManager = $mediaManager;
    }

    protected function configure()
    {
        parent::configure();

        $this
            ->setName('kuma:media:rename-soft-deleted')
            ->setDescription('Rename physical files for soft-deleted media.')
            ->setHelp(
                'The <info>kuma:media:rename-soft-deleted</info> command can be used to rename soft-deleted media which is still publically available under the original filename.'
            )
            ->addOption(
                'original',
                'o',
                InputOption::VALUE_NONE,
                'If set renames soft-deleted media to its original filename'
            );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        if (null === $this->em) {
            $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');
            $this->mediaManager = $this->getContainer()->get('kunstmaan_media.media_manager');
        }

        $output->writeln('Renaming soft-deleted media...');

        $original = $input->getOption('original');
        $medias = $this->em->getRepository('KunstmaanMediaBundle:Media')->findAll();
        $updates = 0;
        $fileRenameQueue = array();

        try {
            $this->em->beginTransaction();
            /** @var Media $media */
            foreach ($medias as $media) {
                $handler = $this->mediaManager->getHandler($media);
                if ($media->isDeleted() && $media->getLocation() === 'local' && $handler instanceof FileHandler) {
                    $oldFileUrl = $media->getUrl();
                    $newFileName = ($original ? $media->getOriginalFilename() : uniqid() . '.' . pathinfo($oldFileUrl, PATHINFO_EXTENSION));
                    $newFileUrl = dirname($oldFileUrl) . '/' . $newFileName;
                    $fileRenameQueue[] = array($oldFileUrl, $newFileUrl, $handler);
                    $media->setUrl($newFileUrl);
                    $this->em->persist($media);
                    ++$updates;
                }
            }
            $this->em->flush();
            $this->em->commit();
        } catch (\Exception $e) {
            $this->em->rollback();
            $output->writeln('An error occured while updating soft-deleted media : <error>' . $e->getMessage() . '</error>');
            $updates = 0;
            $fileRenameQueue = array();
        }

        foreach ($fileRenameQueue as $row) {
            list($oldFileUrl, $newFileUrl, $handler) = $row;
            $handler->fileSystem->rename(
                preg_replace('~^' . preg_quote($handler->mediaPath, '~') . '~', '/', $oldFileUrl),
                preg_replace('~^' . preg_quote($handler->mediaPath, '~') . '~', '/', $newFileUrl)
            );
            $output->writeln('Renamed <info>' . $oldFileUrl . '</info> to <info>' . basename($newFileUrl) . '</info>');
        }

        $output->writeln('<info>' . $updates . ' soft-deleted media files have been renamed.</info>');
    }
}
