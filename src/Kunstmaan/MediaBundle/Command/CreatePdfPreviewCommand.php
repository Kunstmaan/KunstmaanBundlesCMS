<?php

namespace Kunstmaan\MediaBundle\Command;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use ImagickException;
use Kunstmaan\MediaBundle\Entity\Media;
use Kunstmaan\MediaBundle\Helper\Transformer\PdfTransformer;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'kuma:media:create-pdf-previews', description: 'Create preview images for PDFs that have already been uploaded')]
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

    protected function configure(): void
    {
        parent::configure();

        $this
            ->setHelp(
                'The <info>kuma:media:create-pdf-previews</info> command can be used to create preview images for PDFs that have already been uploaded.'
            );
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Creating PDF preview images...');

        $medias = $this->em->getRepository(Media::class)->findBy(
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
