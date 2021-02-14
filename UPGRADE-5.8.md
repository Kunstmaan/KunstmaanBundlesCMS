UPGRADE FROM 5.7 to 5.8
=======================

AdminBundle
------------

Using FosUserBundle as the authentication system for the cms login is deprecated and usages will be removed in 6.0. Use the replacement authentication system instead.
To enable the new authentication (and disable any FosUserBundle usages) set the `kunstmaan_admin.authentication.enable_new_authentication` config to `true` (this option will always default to true in 6.0).

If you previously did an override of the user or group class, your config should be updated.

From:
```yaml
fos_user:
    user_class: App\Entity\User
    group_class: App\Entity\Group
```

To:
```yaml
kunstmaan_admin:
    authentication:
        user_class: App\Entity\User
        group_class: App\Entity\Group
```

Your `security.yaml` will also need an update after enabling the new authentication system. Adapt your config according to the following example,
which is the default `security.yaml` that ships with a new installation.

```yaml
security:
    encoders:
        Kunstmaan\AdminBundle\Entity\UserInterface: bcrypt
    ...

    providers:
        cms_users:
          entity: { class: Kunstmaan\AdminBundle\Entity\User, property: username }

    firewalls:
        main:
            pattern: .*
            form_login:
                login_path: kunstmaan_admin_login
                check_path: kunstmaan_admin_login
                provider: cms_users
            logout:
                path: kunstmaan_admin_logout
                target: KunstmaanAdminBundle_homepage
            ...

    access_control:
        ...
        - { path: ^/([^/]*)/admin/reset.*, role: IS_AUTHENTICATED_ANONYMOUSLY }
        ...
```
