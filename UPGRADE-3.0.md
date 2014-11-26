UPGRADE FROM 2.3 to 3.0
=======================

Composer.json
-------------

First start to update you `composer.json` file. All `kunstmaan/bundle-name` references must be replaced by the new single repository `kunstmaan/bundles-cms`.
At the moment we are at `3.0.*@dev`

Then run composer update to install the new bundles.

Pro tip: If you are tired of waiting on composer update you can use https://composer.borreli.com/ 


Security
--------

The routing for the admin has been changed. Therefor you will have to update your `app/config/security.yml`.

Replace this:

```yml
access_control:
    - { path: ^/([^/]*)/login$, role: IS_AUTHENTICATED_ANONYMOUSLY }
    - { path: ^/([^/]*)/resetting, role: IS_AUTHENTICATED_ANONYMOUSLY }
    - { path: ^/([^/]*)/admin/settings/, role: ROLE_ADMIN }
    - { path: ^/([^/]*)/admin/settings, role: ROLE_ADMIN }
    - { path: ^/([^/]*)/admin/, role: ROLE_ADMIN }
    - { path: ^/([^/]*)/admin, role: ROLE_ADMIN }
    - { path: ^/([^/]*)/preview, role: ROLE_ADMIN }
```

with this:

```yml
access_control:
    - { path: ^/([^/]*)/admin/login$, role: IS_AUTHENTICATED_ANONYMOUSLY }
    - { path: ^/([^/]*)/admin/resetting, role: IS_AUTHENTICATED_ANONYMOUSLY }
    - { path: ^/([^/]*)/admin, role: ROLE_ADMIN }
```


Migrations
----------

Next you will want to run `app/console doctrine:migrations:diff && app/console doctrine:migrations:migrate` to upgrade your database. This should run without any problems but just check the generated migration first just to be sure.

After your database is upgraded you have to run some migration scripts.
Necessary scripts:

```
app/console kuma:nodes:fix-timestamps
app/console kuma:media:migrate-name
app/console kuma:media:rebuild-folder-tree
```

If you are using the search bundle you NEED to re-index elasticsearch.

```
app/console kuma:search:populate full
```

Since 3.0 you are able to create thumbnails for pdf files. If you want to create them you can execute the following command.

```
app/console kuma:media:create-pdf-previews
```


All these commands are also defined in the service container to make them available in doctrine:migrations.
Below is an example migration file to execute the commands stated above. This makes deployment to production a lot easier!
Run `app/console doctrine:migrations:generate` and copy/paste this into the new generated file. Don't forget to change the class name with the correct timestamp.

```php
<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class VersionYOUR_TIMESTAMP extends AbstractMigration implements ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
    }

    public function postUp(Schema $schema)
    {
        //fix nodetranslation timestamps
        $input  = new ArgvInput(array());
        $output = new ConsoleOutput();

        $command = $this->container->get('kunstmaan_node.command.fixtimestamps');
        $command->run($input, $output);

        //migrate names
        $input  = new ArgvInput(array());
        $output = new ConsoleOutput();

        $command = $this->container->get('kunstmaan_media.command.migratename');
        $command->run($input, $output);

        //migrate media folder tree
        $input  = new ArgvInput(array());
        $output = new ConsoleOutput();

        $command = $this->container->get('kunstmaan_media.command.rebuildfoldertree');
        $command->run($input, $output);

        //create pdf thumbnails
        $input  = new ArgvInput(array());
        $output = new ConsoleOutput();

        $command = $this->container->get('kunstmaan_media.command.createpdfpreview');
        $command->run($input, $output);

        //re-index elasticsearch
        $input  = new ArrayInput(array('full' => true));
        $output = new ConsoleOutput();

        $command = $this->container->get('kunstmaan_search.command.populate');
        $command->run($input, $output);
    }
}
```
