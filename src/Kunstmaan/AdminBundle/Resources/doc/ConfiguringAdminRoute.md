# Configuring the admin route.

You can now configure the url for the Admin side of your Kunstmaan CMS.

You can do this by configuring the following option in your applications configurationg file.

```yaml
kunstmaan_admin:
    admin_prefix: 'vip' #example
```

In your security.yml you should double check your firewalls and
access_control_list for any hardcoded references to /admin/ and replace them
with your new prefix.

```yaml
#Example
access_control:
    - { path: ^/([^/]*)/vip/login$, role: IS_AUTHENTICATED_ANONYMOUSLY }
    - { path: ^/([^/]*)/vip/resetting, role: IS_AUTHENTICATED_ANONYMOUSLY }
    - { path: ^/([^/]*)/vip, role: ROLE_ADMIN }
```

No other configuration is needed to have a custom route for the backend of your
application.
