<?php

namespace Kunstmaan\MediaBundle\Command;

use Doctrine\ORM\EntityManager;
use Kunstmaan\MediaBundle\Entity\Media;
use Kunstmaan\MediaBundle\Helper\File\FileHandler;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RenameSoftDeletedCommand extends ContainerAwareCommand
{
    /** @var EntityManager $em */
    protected $em;

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Renaming soft-deleted media...');
        /**
         * @var EntityManager
         */
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $original = $input-> getOption('original');
        $medias = $em->getRepository('KunstmaanMediaBundle:Media')->findAll();
        $manager = $this->getContainer()->get('kunstmaan_media.media_manager');
        $updates = 0;
        $fileRenameQueue = array();
        try {
            $em->beginTransaction();
            /** @var Media $media */
            foreach ($medias as $media) {
                $handler = $manager->getHandler($media);
                if ($media->isDeleted() && $media->getLocation() === 'local' && $handler instanceof FileHandler) {
                    $oldFileUrl = $media->getUrl();
                    $newFileName = ($original ? $media->getOriginalFilename() : uniqid() . '.' . pathinfo($oldFileUrl, PATHINFO_EXTENSION));
                    $newFileUrl = dirname($oldFileUrl) . '/' . $newFileName;
                    $fileRenameQueue[] = array($oldFileUrl, $newFileUrl, $handler);
                    $media->setUrl($newFileUrl);
                    $em->persist($media);
                    $updates++;
                }
            }
            $em->flush();
            $em->commit();
        } catch (\Exception $e) {
            $em->rollback();
            $output->writeln('An error occured while updating soft-deleted media : <error>' . $e->getMessage() . '</error>');
            $updates = 0;
            $fileRenameQueue = array();
        }

        foreach ($fileRenameQueue as $row) {
            list ($oldFileUrl, $newFileUrl, $handler) = $row;
            $handler->fileSystem->rename(
                preg_replace('~^' . preg_quote($handler->mediaPath, '~') . '~', '/', $oldFileUrl),
                preg_replace('~^' . preg_quote($handler->mediaPath, '~') . '~', '/', $newFileUrl)
            );
            $output->writeln('Renamed <info>' . $oldFileUrl . '</info> to <info>' . basename($newFileUrl) . '</info>');
        }

        $output->writeln('<info>' . $updates . ' soft-deleted media files have been renamed.</info>');
    }

    protected function configure()
    {
        parent::configure();

        $this
            ->setName('kuma:media:rename-soft-deleted')
            ->setDescription('Rename physical files for soft-deleted media.')
            ->setHelp(
                "The <info>kuma:media:rename-soft-deleted</info> command can be used to rename soft-deleted media which is still publically available under the original filename."
            )
            ->addOption(
                'original',
                'o',
                InputOption::VALUE_NONE,
                'If set renames soft-deleted media to its original filename'
            );
    }
}
