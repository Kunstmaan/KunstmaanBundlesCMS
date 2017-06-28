Enabling google authentication login
=====================

Enabling google authentication will give your users the chance to authenticate
themselves within the CMS using their gmail accounts. Their access level will
be determined based on the domain of their gmail account and your configuration.

## 1) Add the necessary settings to your app/config/config.yml

You will need to have a google application with the necessary credentials.
You can configure as many hosted domains as you would like. For each domain
name you can specify an array of access levels, these will be granted to the
user upon first login.

```
    kunstmaan_admin:
        google_signin:
        ¦   enabled: true
        ¦   client_id:  some_client_id.apps.googleusercontent
        ¦   client_secret: some_secret
        ¦   hosted_domains:
        ¦   ¦   - { domain_name: kunstmaan.be, access_levels: ['ROLE_SUPER_ADMIN'] }
                - { domain_name: mydomain.example, access_levels: ['ROLE_USER'] }
```

## 2) Configure the guard component in your app/config/security.yml

```
security:
    ...
    firewalls:
        main:
            pattern: .*
            guard:
                authenticators:
                    - kunstmaan_admin.oauth_authenticator
            form_login:
                login_path: fos_user_security_login
                check_path: fos_user_security_check
                provider: fos_userbundle
            ...
```
And that's it! An extra button should now have appeared on your login screen.
If you have properly configured your hosted domains you should be able to login
using your gmail account.
