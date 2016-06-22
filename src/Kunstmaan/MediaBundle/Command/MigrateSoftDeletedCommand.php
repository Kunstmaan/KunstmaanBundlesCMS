<?php

namespace Kunstmaan\MediaBundle\Command;

use Doctrine\ORM\EntityManager;
use Kunstmaan\MediaBundle\Entity\Media;
use Kunstmaan\MediaBundle\Helper\File\FileHandler;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateSoftDeletedCommand extends ContainerAwareCommand
{
    /** @var EntityManager $em */
    protected $em;

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Migrating soft-deleted media...');
        /**
         * @var EntityManager
         */
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

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
                    $oldUrl = $media->getUrl();
                    $newUrl = $newFileUrl = dirname($oldUrl) . '/' . uniqid() . '.' . pathinfo($oldUrl, PATHINFO_EXTENSION);
                    $fileRenameQueue[] = array($oldUrl, $newUrl, $handler);
                    $media->setUrl($newUrl);
                    $em->persist($media);
                    $updates++;
                }
            }
            $em->flush();
            $em->commit();
        } catch (\Exception $e) {
            $em->rollback();
            $output->writeln('An error occured while migrating soft-deleted media : <error>' . $e->getMessage() . '</error>');
        }

        foreach ($fileRenameQueue as $row) {
            list ($oldUrl, $newUrl, $handler) = $row;
            $handler->fileSystem->rename(
                preg_replace('~^' . preg_quote(FileHandler::MEDIA_PATH, '~') . '~', '/', $oldUrl),
                preg_replace('~^' . preg_quote(FileHandler::MEDIA_PATH, '~') . '~', '/', $newUrl)
            );
            $output->writeln('Renamed <info>' . $oldUrl . '</info> to <info>' . basename($newUrl) . '</info>');
        }

        $output->writeln('<info>' . $updates . ' soft-deleted media files have been renamed.</info>');
    }

    protected function configure()
    {
        parent::configure();

        $this
            ->setName('kuma:media:migrate-soft-deleted')
            ->setDescription('Migrate soft-deleted media to rename public media.')
            ->setHelp(
                "The <info>kuma:media:migrate-name</info> command can be used to migrate soft-deleted media which is still publically available under the original filename."
            );
    }
}
