UPGRADE FROM 5.3 to 5.4
=======================

AdminBundle
-----------

* The composer script class `Kunstmaan\AdminBundle\Composer\ScriptHandler` is deprecated and will be removed in 6.0. 
  If you use this script handler, remove it from your composer.json scripts section.  

ConfigBundle
------------

* Passing the "@templating" service as the 2nd argument to "Kunstmaan\ConfigBundle\Controller\ConfigController" is deprecated and will be replaced by the Twig service in 6.0. Injected the "@twig" service instead.

FormBundle
----------

* Passing the "@templating" service as the 2nd argument to "Kunstmaan\FormBundle\Helper\FormMailer" is deprecated and will be replaced by the Twig service in 6.0. Injected the "@twig" service instead.
