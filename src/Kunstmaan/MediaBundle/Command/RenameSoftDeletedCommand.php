<?php

namespace Kunstmaan\MediaBundle\Command;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\MediaBundle\Entity\Media;
use Kunstmaan\MediaBundle\Helper\File\FileHandler;
use Kunstmaan\MediaBundle\Helper\MediaManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class RenameSoftDeletedCommand extends Command
{
    /** @var EntityManager */
    protected $em;

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

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Renaming soft-deleted media...');

        $original = $input->getOption('original');
        $medias = $this->em->getRepository(Media::class)->findAll();
        $updates = 0;
        $fileRenameQueue = [];

        try {
            $this->em->beginTransaction();
            /** @var Media $media */
            foreach ($medias as $media) {
                $handler = $this->mediaManager->getHandler($media);
                if ($media->isDeleted() && $media->getLocation() === 'local' && $handler instanceof FileHandler) {
                    $oldFileUrl = $media->getUrl();
                    $newFileName = ($original ? $media->getOriginalFilename() : uniqid() . '.' . pathinfo($oldFileUrl, PATHINFO_EXTENSION));
                    $newFileUrl = \dirname($oldFileUrl) . '/' . $newFileName;
                    $fileRenameQueue[] = [$oldFileUrl, $newFileUrl, $handler];
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
            $fileRenameQueue = [];
        }

        foreach ($fileRenameQueue as $row) {
            [$oldFileUrl, $newFileUrl, $handler] = $row;
            $handler->fileSystem->rename(
                preg_replace('~^' . preg_quote($handler->mediaPath, '~') . '~', '/', $oldFileUrl),
                preg_replace('~^' . preg_quote($handler->mediaPath, '~') . '~', '/', $newFileUrl)
            );
            $output->writeln('Renamed <info>' . $oldFileUrl . '</info> to <info>' . basename($newFileUrl) . '</info>');
        }

        $output->writeln('<info>' . $updates . ' soft-deleted media files have been renamed.</info>');

        return 0;
    }
}
