UPGRADE FROM 5.9 to 5.10
========================

General
-------

### CSRF protection

CSRF protection was added to multiple routes in the cms. No passing a csrf token to those routes
is deprecated and will be required in 6.0. Below is a list of controller actions that will require
a csrf token. Check the specific twig templates or the deprecation messages for the specific csrf token id that needs to
be used.

* `Kunstmaan\AdminListBundle\Controller\AdminListController::doDeleteAction`
* `Kunstmaan\AdminBundle\Controller\ExceptionController::resolveAllAction`
* `Kunstmaan\AdminBundle\Controller\ExceptionController::toggleResolveAction`
* `Kunstmaan\MediaBundle\Controller\FolderController::deleteAction`
* `Kunstmaan\MediaBundle\Controller\MediaController::deleteAction`
* `Kunstmaan\FormBundle\Controller\FormSubmissionsController::deleteAction`
* `Kunstmaan\UserManagementBundle\Controller\GroupsController::deleteAction`
* `Kunstmaan\NodeBundle\Controller\NodeAdminController::recopyFromOtherLanguageAction`
* `Kunstmaan\NodeBundle\Controller\NodeAdminController::deleteAction`
* `Kunstmaan\NodeBundle\Controller\NodeAdminController::duplicateAction`
* `Kunstmaan\NodeBundle\Controller\NodeAdminController::duplicateWithChildrenAction`
* `Kunstmaan\UserManagementBundle\Controller\RolesController::deleteAction`
* `Kunstmaan\TranslatorBundle\Controller\TranslatorController::deleteAction`

Together with the CSRF token some of those routes will only be available to post requests in 6.0

* `Kunstmaan\AdminBundle\Controller\ExceptionController::resolveAllAction`
* `Kunstmaan\AdminBundle\Controller\ExceptionController::toggleResolveAction`
* `Kunstmaan\MediaBundle\Controller\FolderController::deleteAction`
* `Kunstmaan\MediaBundle\Controller\MediaController::deleteAction`

AdminBundle
------------

* The `kunstmaan_admin.google_signin` config and related classes/functionality is deprecated and will be removed in 6.0. If you need the ability 
  to login in the admin interface with google, implement the authenticator in your project.
* Passing a service instance of `Symfony\Component\HttpFoundation\Session\SessionInterface` for the first argument in `Kunstmaan\AdminBundle\EventListener\PasswordCheckListener::__construct` is deprecated and an instance of `Symfony\Component\HttpFoundation\RequestStack` will be required in 6.0.

MultiDomainBundle
-----------------

* Passing a service instance of `Symfony\Component\HttpFoundation\Session\SessionInterface` for the first argument in `Kunstmaan\MultiDomainBundle\EventListener\HostOverrideListener::__construct` is deprecated and will be removed required in 6.0. Check the constructor arguments and inject the required services instead.

NodeBundle
----------

* Passing a service instance of `Symfony\Component\HttpFoundation\Session\SessionInterface` for the first argument in `Kunstmaan\NodeBundle\EventListener\NodeTranslationListener::__construct` is deprecated and an instance of `Symfony\Component\HttpFoundation\RequestStack` will be required in 6.0.
* The fourth argument of `Kunstmaan\NodeBundle\EventListener\NodeTranslationListener::__construct` is deprecated and will be removed in 6.0. Check the constructor arguments and inject the required services instead.
