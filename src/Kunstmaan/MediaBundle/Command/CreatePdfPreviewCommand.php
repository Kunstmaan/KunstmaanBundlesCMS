<?php

namespace Kunstmaan\MediaBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreatePdfPreviewCommand extends ContainerAwareCommand
{

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Creating PDF preview images...');

        $pdfTransformer = $this->getContainer()->get('kunstmaan_media.pdf_transformer');
        $webPath = realpath($this->getContainer()->get('kernel')->getRootDir() . '/../web') . DIRECTORY_SEPARATOR;

        /**
         * @var EntityManager
         */
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $medias = $em->getRepository('KunstmaanMediaBundle:Media')->findBy(
            array('contentType' => 'application/pdf', 'deleted' => false)
        );
        /** @var Media $media */
        foreach ($medias as $media) {
            $pdfTransformer->apply($webPath . $media->getUrl());
        }
        $output->writeln('<info>PDF preview images have been created.</info>');
    }

    /**
     * Checks whether the command is enabled or not in the current environment.
     *
     * Override this to check for x or y and return false if the command can not
     * run properly under the current conditions.
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->getContainer()->getParameter('kunstmaan_media.enable_pdf_preview');
    }

    protected function configure()
    {
        parent::configure();

        $this
            ->setName('kuma:media:create-pdf-previews')
            ->setDescription('Create preview images for PDFs that have already been uploaded')
            ->setHelp(
                "The <info>kuma:media:create-pdf-previews</info> command can be used to create preview images for PDFs that have already been uploaded."
            );
    }
}
