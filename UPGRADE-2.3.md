UPGRADE TO 2.3
==============

## AdminBundle

### To v2.3 without ROLE_GUEST dependency

Since ROLE_GUEST and the guest user were superfluous - the default Symfony IS_AUTHENTICATED_ANONYMOUSLY & anon. user cover the same use case - we decided to drop them in favor of the Symfony defaults. This is a BC breaking change, so a migration path is provided.

To upgrade, first pull in the new version of all bundles.

Remove guest_user: true from app/config/security.yml.

And finally execute the following command : app/console kuma:fix:guest

This should execute the necessary changes (you could delete/rename the guest user afterwards as well - we just leave it in case there are items linked to it).

Note: it will no longer be possible to add extra roles to the guest/anonymous user in the back-end. You will also have to adapt your code (in most cases replacing ROLE_GUEST with IS_AUTHENTICATED_ANONYMOUSLY should suffice though).

## AdminListBundle

### UPGRADE FROM 1.x TO 2.x - BC BREAK Admin list filters

Admin list filters have been moved and split into ORM/DBAL filters.

The namespace has been changed to Kunstmaan\AdminListBundle\AdminList\Filters\ORM for the ORM filters and Kunstmaan\AdminListBundle\AdminList\Filters\DBAL for the DBAL filters. And instead of the FilterType suffix in the class names, the now just have a Filter suffix.

Instead of inheriting from AbstractAdminListConfigurator, you will have to inherit from the ORM or DBAL specific implementation (AbstractDoctrineORMAdminListConfigurator).

### BC BREAK AbstractAdminListConfigurator

The getPathByConvention() method is changed

The method returns the name of the route in lowercase now. All the names of the routes in the controllers have to be lowercase.

## DashboardBundle

### Upgrading from v2.3.6 or lower

Because of the changes with the multi-config setup, you'll need to flush your database and refill it.

Use either a migration or force a schema update to update the database

    app/console doctrine:migrations:diff && app/console doctrine:migrations:migrate
    app/console doctrine:schema:update --force

Flush the data and reload it

    app/console kuma:dashboard:widget:googleanalytics:data:flush
    app/console kuma:dashboard:collect

## MediaBundle

### To v2.3.18 with extra fields, indexes and folder tree

When upgrading from a previous version, make sure you update the table structure (
```app/console doctrine:schema:update --force```
or ```app/console doctrine:migrations:diff && app/console doctrine:migrations:migrate```).

A new field to store the original filename was added to the Media table, so you will have to update the table structure
when upgrading from a version prior to 2.3.18.

You can use ```app/console kuma:media:migrate-name``` to initialize the original filename field for already
uploaded media (it will just copy the contents of name field into the original_filename field, so you could also just
update this using a simple SQL query if you want).

The Folder entity has been refactored to be a nested tree, which should speed up the media section (this will
especially be noticeable if you have lots of media folders).

To migrate your current media tree to the new format, you have to execute ```app/console kuma:media:rebuild-folder-tree```
to initialize the folder tree. If you decide to undelete folders you should run this command as well.

If you want to create PDF preview images for PDF files that have already been uploaded  (provided that you have the
necessary PDF support enabled), you can run the ```app/console kuma:media:create-pdf-previews``` command.

## NodeBundle

### To v2.2 with nested set support

To upgrade from a previous version, you have to copy the Doctrine migrations file from this bundle (Resources/DoctrineMigrations/Version20130611131506.php)
to your app/DoctrineMigrations/ folder and run it: ```app/console doctrine:migrations:migrate```

This migration will:
* create some new columns in the kuma_node table
* create a stored procedure to rebuild the nested tree (you can call this procedure at any time to rebuild a corrupted tree)
* run the store procedure to update the current records in the node table

Note: make sure your database user has privileges to create and run a stored procedure.

### To v2.0+

The Node services now all have kunstmaan_node as prefix.
