<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130611131506 extends AbstractMigration implements ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");

        $this->addSql("ALTER TABLE kuma_nodes ADD lft INT DEFAULT NULL, ADD lvl INT DEFAULT NULL, ADD rgt INT DEFAULT NULL");

        $sql = "DROP PROCEDURE IF EXISTS treerecover;
        SET SQL_SAFE_UPDATES=0;

        CREATE PROCEDURE `treerecover`()
        MODIFIES SQL DATA
        BEGIN
            DECLARE currentId, currentParentId  CHAR(36);
            DECLARE currentLeft, currentLevel   INT;
            DECLARE startId                     INT DEFAULT 1;

            # Determines the max size for MEMORY tables.
            SET max_heap_table_size = 1024 * 1024 * 512;

            START TRANSACTION;

            DROP TABLE IF EXISTS `tmp_tree`;

            # Temporary MEMORY table to do all the heavy lifting in,
            # otherwise performance is simply abysmal.
            CREATE TABLE `tmp_tree` (
                `id`        char(36) NOT NULL DEFAULT '',
                `parent_id` char(36)          DEFAULT NULL,
                `lft`       int(11)  unsigned DEFAULT NULL,
                `lvl`       int(11)  unsigned DEFAULT NULL,
                `rgt`       int(11)  unsigned DEFAULT NULL,
                PRIMARY KEY      (`id`),
                INDEX USING HASH (`parent_id`),
                INDEX USING HASH (`lft`),
                INDEX USING HASH (`rgt`)
            ) ENGINE = MEMORY
            SELECT `id`,
                   `parent_id`,
                   `lft`,
                   `lvl`,
                   `rgt`
            FROM   `kuma_nodes`;

            # Leveling the playing field.
            UPDATE  `tmp_tree`
            SET     `lft` = NULL,
                    `rgt` = NULL,
                    `lvl` = NULL;

            # Establishing starting numbers for all root elements.
            WHILE EXISTS (SELECT * FROM `tmp_tree` WHERE `parent_id` IS NULL AND `lft` IS NULL AND `rgt` IS NULL LIMIT 1) DO

                UPDATE `tmp_tree`
                SET    `lft` = startId,
                       `rgt` = startId + 1,
                       `lvl` = 0
                WHERE  `parent_id` IS NULL
                  AND  `lft`       IS NULL
                  AND  `rgt`      IS NULL
                LIMIT  1;

                SET startId = startId + 2;

            END WHILE;

            # Switching the indexes for the lft/rgt columns to B-Trees to speed up the next section, which uses range queries.
            DROP INDEX `lft`  ON `tmp_tree`;
            DROP INDEX `rgt` ON `tmp_tree`;
            CREATE INDEX `lft`  USING BTREE ON `tmp_tree` (`lft`);
            CREATE INDEX `rgt` USING BTREE ON `tmp_tree` (`rgt`);

            # Numbering all child elements
            WHILE EXISTS (SELECT * FROM `tmp_tree` WHERE `lft` IS NULL LIMIT 1) DO

                # Picking an unprocessed element which has a processed parent.
                SELECT     `tmp_tree`.`id`, `parents`.`lvl`
                  INTO     currentId, currentLevel
                FROM       `tmp_tree`
                INNER JOIN `tmp_tree` AS `parents`
                        ON `tmp_tree`.`parent_id` = `parents`.`id`
                WHERE      `tmp_tree`.`lft` IS NULL
                  AND      `parents`.`lft`  IS NOT NULL
                LIMIT      1;

                # Finding the element's parent.
                SELECT  `parent_id`
                  INTO  currentParentId
                FROM    `tmp_tree`
                WHERE   `id` = currentId;

                # Finding the parent's lft value.
                SELECT  `lft`
                  INTO  currentLeft
                FROM    `tmp_tree`
                WHERE   `id` = currentParentId;

                # Shifting all elements to the right of the current element 2 to the right.
                UPDATE `tmp_tree`
                SET    `rgt` = `rgt` + 2
                WHERE  `rgt` > currentLeft;

                UPDATE `tmp_tree`
                SET    `lft` = `lft` + 2
                WHERE  `lft` > currentLeft;

                # Setting lft and rgt values for current element.
                UPDATE `tmp_tree`
                SET    `lft` = currentLeft + 1,
                       `rgt` = currentLeft + 2,
                       `lvl` = currentLevel + 1
                WHERE  `id`  = currentId;

            END WHILE;

            # Writing calculated values back to physical table.
            UPDATE `kuma_nodes`, `tmp_tree`
            SET    `kuma_nodes`.`lft`  = `tmp_tree`.`lft`,
                   `kuma_nodes`.`rgt` = `tmp_tree`.`rgt`,
                   `kuma_nodes`.`lvl`  = `tmp_tree`.`lvl`
            WHERE  `kuma_nodes`.`id`   = `tmp_tree`.`id`;

            COMMIT;

            DROP TABLE `tmp_tree`;

        END";

        $em = $this->container->get('doctrine.orm.entity_manager');
        $stmt = $em->getConnection()->prepare($sql);
        $stmt->execute();

        $this->addSql("CALL treerecover()");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql", "Migration can only be executed safely on 'mysql'.");

        $this->addSql("ALTER TABLE kuma_nodes DROP lft, DROP lvl, DROP rgt");
        $this->addSql("DROP PROCEDURE IF EXISTS treerecover");
    }
}
