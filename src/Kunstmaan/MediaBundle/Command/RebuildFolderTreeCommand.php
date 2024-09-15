<?php

namespace Kunstmaan\MediaBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\MediaBundle\Entity\Folder;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'kuma:media:rebuild-folder-tree', description: 'Rebuild the media folder tree.')]
final class RebuildFolderTreeCommand extends Command
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();

        $this->em = $em;
    }

    protected function configure(): void
    {
        $this
            ->setHelp('The <info>kuma:media:rebuild-folder-tree</info> will loop over all media folders and update the media folder tree.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->em->getRepository(Folder::class)->rebuildTree();
        $output->writeln('Updated all folders');

        return 0;
    }
}
