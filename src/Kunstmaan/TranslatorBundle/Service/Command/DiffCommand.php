<?php

namespace Kunstmaan\TranslatorBundle\Service\Command;

use Doctrine\DBAL\Migrations\Configuration\Configuration;
use Doctrine\DBAL\Migrations\Tools\Console\Command\GenerateCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

if (class_exists(\Doctrine\Migrations\Tools\Console\Command\GenerateCommand::class)) {
    /**
     * @deprecated This class is deprecated since KunstmaanTranslatorBundle 5.2 and will be removed in 6.0.
     */
    class DiffCommand extends \Doctrine\Migrations\Tools\Console\Command\GenerateCommand
    {
        protected function configure(): void
        {
            parent::configure();
        }

        public function execute(InputInterface $input, OutputInterface $output): ?int
        {
            $configuration = $this->getMigrationConfiguration($input, $output);
            $sql = $this->getApplication()->getKernel()->getContainer()->get('kunstmaan_translator.service.migrations.migrations')->getDiffSqlArray();

            $up = $this->buildCodeFromSql($configuration, $sql);
            $down = '';

            if (!$up && !$down) {
                $output->writeln('No changes detected in your mapping information.', 'ERROR');

                return 0;
            }

            $version = date('YmdHis');
            $path = $this->generateMigration($configuration, $input, $version, $up, $down);

            $output->writeln(sprintf('Generated new migration class to "<info>%s</info>" from schema differences.', $path));

            return 0;
        }

        private function buildCodeFromSql(\Doctrine\Migrations\Configuration\Configuration $configuration, array $sql)
        {
            $currentPlatform = $configuration->getConnection()->getDatabasePlatform()->getName();
            $code = [
                "\$this->abortIf(\$this->connection->getDatabasePlatform()->getName() != \"$currentPlatform\", \"Migration can only be executed safely on '$currentPlatform'.\");", '',
            ];
            foreach ($sql as $query) {
                if (strpos($query, $configuration->getMigrationsTableName()) !== false) {
                    continue;
                }
                $code[] = "\$this->addSql(\"$query\");";
            }

            return implode("\n", $code);
        }
    }
} else {
    /**
     * @deprecated This class is deprecated since KunstmaanTranslatorBundle 5.2 and will be removed in 6.0.
     */
    class DiffCommand extends GenerateCommand
    {
        protected function configure()
        {
            parent::configure();
        }

        public function execute(InputInterface $input, OutputInterface $output)
        {
            $configuration = $this->getMigrationConfiguration($input, $output);
            $sql = $this->getApplication()->getKernel()->getContainer()->get('kunstmaan_translator.service.migrations.migrations')->getDiffSqlArray();

            $up = $this->buildCodeFromSql($configuration, $sql);
            $down = '';

            if (!$up && !$down) {
                $output->writeln('No changes detected in your mapping information.', 'ERROR');

                return 0;
            }

            $version = date('YmdHis');
            $path = $this->generateMigration($configuration, $input, $version, $up, $down);

            $output->writeln(sprintf('Generated new migration class to "<info>%s</info>" from schema differences.', $path));

            return 0;
        }

        private function buildCodeFromSql(Configuration $configuration, array $sql)
        {
            $currentPlatform = $configuration->getConnection()->getDatabasePlatform()->getName();
            $code = [
                "\$this->abortIf(\$this->connection->getDatabasePlatform()->getName() != \"$currentPlatform\", \"Migration can only be executed safely on '$currentPlatform'.\");", '',
            ];
            foreach ($sql as $query) {
                if (strpos($query, $configuration->getMigrationsTableName()) !== false) {
                    continue;
                }
                $code[] = "\$this->addSql(\"$query\");";
            }

            return implode("\n", $code);
        }
    }
}
