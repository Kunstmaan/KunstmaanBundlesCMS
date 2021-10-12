UPGRADE FROM 5.9 to 5.10
========================

AdminBundle
------------

* The `kunstmaan_admin.google_signin` config and related classes/functionality is deprecated and will be removed in 6.0. If you need the ability 
  to login in the admin interface with google, implement the authenticator in your project.
* Passing a service instance of `Symfony\Component\HttpFoundation\Session\SessionInterface` for the first argument in `Kunstmaan\AdminBundle\EventListener\PasswordCheckListener::__construct` is deprecated and an instance of `Symfony\Component\HttpFoundation\RequestStack` will be required in 6.0.

NodeBundle
----------

* Passing a service instance of `Symfony\Component\HttpFoundation\Session\SessionInterface` for the first argument in `Kunstmaan\NodeBundle\EventListener\NodeTranslationListener::__construct` is deprecated and an instance of `Symfony\Component\HttpFoundation\RequestStack` will be required in 6.0.
* The fourth argument of `Kunstmaan\NodeBundle\EventListener\NodeTranslationListener::__construct` is deprecated and will be removed in 6.0. Check the constructor arguments and inject the required services instead.
