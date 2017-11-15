<?php

namespace Kunstmaan\MediaBundle\Command;

use Doctrine\ORM\EntityManager;
use Gedmo\Translatable\Entity\Translation;
use Kunstmaan\MediaBundle\Entity\Folder;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateFolderSlugsCommand extends ContainerAwareCommand
{
    /** @var EntityManager $em */
    protected $em;

    public function execute(InputInterface $input, OutputInterface $output)
    {
        //Force slug (re)gen by appending a space to the name which will force the slug generation.
        //Then reset the name
        /* @var $em EntityManager */
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $repo = $em->getRepository(Folder::class);
        $translationRepo = $em->getRepository(Translation::class);
        $entities = $repo->findAll();
        foreach ($entities as $entity) {
            $entity->setName($entity->getName() . ' ');
            $em->persist($entity);
            $em->flush();
            $translations = $translationRepo->findTranslations($entity);
            foreach ($translations as $locale => $fields) {
                if (isset($fields['name'])) {
                    $entity->setTranslatableLocale($locale);
                    $entity->setName($fields['name']);
                    $em->persist($entity);
                    $em->flush();
                }
            }
        }
        $em->flush();

        foreach ($entities as $entity) {
            $entity->setName(trim($entity->getName()));
            $em->persist($entity);
        }
        $em->flush();

        $output->writeln('<info>All slugs have been generated.</info>');
    }

    protected function configure()
    {
        parent::configure();

        $this
            ->setName('kuma:media:generate-folder-slugs')
            ->setDescription('Fill existing media folder slugs')
            ->setHelp(
                "The <info>kuma:media:generate-folder-slugs</info> command can be used to generate slugs for media folders."
            );
    }
}
