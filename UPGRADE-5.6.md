UPGRADE FROM 5.5 to 5.6
=======================

General
-------

SearchBundle/NodeSearchBundle: Support for ruflin/elastica and elasticasearch 7 was added. If you still use 
elasticsearch 6 you should add `"ruflin/elastica": "^5.0|^6.0"` to your project `composer.json`.

SearchBundle
------------

Support for `ruflin/elastica` and elasticsearch versions below 7 is deprecated and will be removed in 6.0. Upgrade to `ruflin/elastica` ^7.0 and elasticsearch 7.
