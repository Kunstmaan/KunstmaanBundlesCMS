<?php

namespace Kunstmaan\TranslatorBundle\Command;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Kunstmaan\TranslatorBundle\Repository\TranslationRepository;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateTranslationsCommand extends ContainerAwareCommand
{
    /**
     * @var EntityManager $em
     */
    private $em = null;

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Migrating translations...');
        $this->setEntityManager($this->getContainer()->get('doctrine.orm.entity_manager'));

        /** @var TranslationRepository $repo */
        $repo = $this->em->getRepository('KunstmaanTranslatorBundle:Translation');

        /** @var QueryBuilder $qb */
        $qb = $repo->createQueryBuilder('t');
        $uniqueTranslations = $qb->select('t.domain,t.keyword')
            ->distinct()
            ->where('t.translationId IS NULL')
            ->getQuery()
            ->getArrayResult();

        $this->em->beginTransaction();
        try {
            foreach ($uniqueTranslations as $uniqueTranslation) {
                // Fetch new unique translation ID & update records
                $newId = $repo->getUniqueTranslationId();

                // Update translations...
                $qb->update()
                    ->set('t.translationId', $newId)
                    ->where('t.domain = :domain')
                    ->andWhere('t.keyword = :keyword')
                    ->setParameter('domain', $uniqueTranslation['domain'])
                    ->setParameter('keyword', $uniqueTranslation['keyword'])
                    ->getQuery()
                    ->execute();
            }
            $this->em->commit();
            $output->writeln('<info>' . count($uniqueTranslations) . ' translations have been migrated.</info>');
        } catch (\Exception $e) {
            $this->em->rollback();
            $output->writeln('An error occured while migrating translations : <error>' . $e->getMessage() . '</error>');
        }
    }

    public function setEntityManager(EntityManager $em)
    {
        $this->em = $em;
    }

    protected function configure()
    {
        parent::configure();

        $this
            ->setName('kuma:translator:migrate')
            ->setDescription('Migrate old translations to the new table structure.')
            ->setHelp(
                "The <info>kuma:translator:migrate</info> command can be used to migrate translations to the new table structure."
            );
    }
}
