Upgrade Instructions
====================

## To v2.3.18 with extra fields, indexes and folder tree

When upgrading from a previous version, make sure you update the table structure (
```bin/console doctrine:schema:update --force```
or ```bin/console doctrine:migrations:diff && bin/console doctrine:migrations:migrate```).

A new field to store the original filename was added to the Media table, so you will have to update the table structure
when upgrading from a version prior to 2.3.18.

You can use ```bin/console kuma:media:migrate-name``` to initialize the original filename field for already
uploaded media (it will just copy the contents of name field into the original_filename field, so you could also just
update this using a simple SQL query if you want).

The Folder entity has been refactored to be a nested tree, which should speed up the media section (this will
especially be noticeable if you have lots of media folders).

To migrate your current media tree to the new format, you have to execute ```bin/console kuma:media:rebuild-folder-tree```
to initialize the folder tree. If you decide to undelete folders you should run this command as well.

If you want to create PDF preview images for PDF files that have already been uploaded  (provided that you have the
necessary PDF support enabled), you can run the ```bin/console kuma:media:create-pdf-previews``` command.
