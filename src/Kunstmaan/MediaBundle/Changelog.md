# Changelog

### dev (2014-08-13)

* The originalFilename property has been added to Media, to store the original filename (so the name property can
be used to add a meaningful name for use in the backend)
* The Folder entity has been converted to a nested tree for performance reasons.
* A command 'kuma:media:rebuild-folder-tree' was added to (re)build the folder tree.
* Preview images for PDF documents will be created when you upload them (if you have PDF support
