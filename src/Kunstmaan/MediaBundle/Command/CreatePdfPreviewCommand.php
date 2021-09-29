<?php

namespace Kunstmaan\MediaBundle\Command;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use ImagickException;
use Kunstmaan\MediaBundle\Entity\Media;
use Kunstmaan\MediaBundle\Helper\Transformer\PdfTransformer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class CreatePdfPreviewCommand extends Command
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var PdfTransformer
     */
    private $pdfTransformer;

    /**
     * @var string
     */
    private $webRoot;

    /**
     * @var bool
     */
    private $enablePdfPreview;

    public function __construct(EntityManagerInterface $em, PdfTransformer $pdfTransformer, string $webRoot, bool $enablePdfPreview)
    {
        parent::__construct();

        $this->em = $em;
        $this->pdfTransformer = $pdfTransformer;
        $this->webRoot = $webRoot;
        $this->enablePdfPreview = $enablePdfPreview;
    }

    protected function configure()
    {
        parent::configure();

        $this
            ->setName('kuma:media:create-pdf-previews')
            ->setDescription('Create preview images for PDFs that have already been uploaded')
            ->setHelp(
                'The <info>kuma:media:create-pdf-previews</info> command can be used to create preview images for PDFs that have already been uploaded.'
            );
    }

    /**
     * @return int
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Creating PDF preview images...');

        /**
         * @var EntityManager
         */
        $medias = $this->em->getRepository('KunstmaanMediaBundle:Media')->findBy(
            ['contentType' => 'application/pdf', 'deleted' => false]
        );
        /** @var Media $media */
        foreach ($medias as $media) {
            try {
                $this->pdfTransformer->apply($this->webRoot . $media->getUrl());
            } catch (ImagickException $e) {
                $output->writeln('<comment>' . $e->getMessage() . '</comment>');
            }
        }

        $output->writeln('<info>PDF preview images have been created.</info>');

        return 0;
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
        return $this->enablePdfPreview;
    }
}
