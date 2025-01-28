UPGRADE FROM 7.1 to 7.2
========================

FormBundle
-----------

- The frontendformobject view variable of form pagepart templates is deprecated and will be removed in 8.0. There is no replacement for this variable.

GeneratorBundle
---------------

- All classes in the generator bundle are marked internal. If you are using or extending these classes, please be aware that they can change in any release.

PagePartBundle
--------------

- Not passing a HasPagePartsInterface as second parameter in PagePartEvent is deprecated and will be required in 8.0.


NodeBundle
-----------------

- The node-bundle/multidomain-bundle has now some improved logic in the router itself. Using the old router logic is deprecated and the new will be the default in 8.0.
  To enable the new and improved router, set the `kunstmaan_node.enable_improved_router` config to `true`.
