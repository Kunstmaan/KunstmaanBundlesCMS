<?php

namespace Kunstmaan\MediaBundle\Command;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use ImagickException;
use Kunstmaan\MediaBundle\Entity\Media;
use Kunstmaan\MediaBundle\Helper\Transformer\PdfTransformer;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @final since 5.1
 * NEXT_MAJOR extend from `Command` and remove `$this->getContainer` usages
 */
class CreatePdfPreviewCommand extends ContainerAwareCommand
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

    /**
     * @param EntityManagerInterface|null $em
     * @param PdfTransformer|null         $mediaManager
     */
    public function __construct(/* EntityManagerInterface */ $em = null, /* PdfTransformer */ $pdfTransformer = null, $webRoot = null, $enablePdfPreview = null)
    {
        parent::__construct();

        if (!$em instanceof EntityManagerInterface) {
            @trigger_error(sprintf('Passing a command name as the first argument of "%s" is deprecated since version symfony 3.4 and will be removed in symfony 4.0. If the command was registered by convention, make it a service instead. ', __METHOD__), E_USER_DEPRECATED);

            $this->setName(null === $em ? 'kuma:media:create-pdf-previews' : $em);

            return;
        }

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

    public function execute(InputInterface $input, OutputInterface $output)
    {
        if (null === $this->em) {
            $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');
            $this->pdfTransformer = $this->getContainer()->get('kunstmaan_media.pdf_transformer');
            $this->webRoot = $this->getContainer()->getParameter('kunstmaan_media.web_root');
            $this->enablePdfPreview = $this->getContainer()->getParameter('kunstmaan_media.enable_pdf_preview');
        }

        $output->writeln('Creating PDF preview images...');

        /**
         * @var EntityManager
         */
        $medias = $this->em->getRepository('KunstmaanMediaBundle:Media')->findBy(
            array('contentType' => 'application/pdf', 'deleted' => false)
        );
        /** @var Media $media */
        foreach ($medias as $media) {
            try {
                $this->pdfTransformer->apply($this->webRoot . $media->getUrl());
            } catch (ImagickException $e) {
                $output->writeln('<comment>'.$e->getMessage().'</comment>');
            }
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
        if (null === $this->enablePdfPreview) {
            $this->enablePdfPreview = $this->getContainer()->getParameter('kunstmaan_media.enable_pdf_preview');
        }

        return $this->enablePdfPreview;
    }
}
