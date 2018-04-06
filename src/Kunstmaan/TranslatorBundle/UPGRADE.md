Upgrade Instructions
====================

## Upgrade to v2.3.2 - inline translation editing on the AdminList grid

There has been a B/C breaking change in the translator tables, an extra column was added that contains a unique
translation ID per domain/keyword.

To upgrade, first backup your database (just in case) and pull in the new versions of the AdminListBundle and
TranslatorBundle.

Then apply the DB change by running :
```bin/console doctrine:migrations:diff && bin/console doctrine:migrations:migrate```
or
```bin/console doctrine:schema:update --force```

Finally execute the following command :
```bin/console kuma:translator:migrate```

This should perform the necessary changes.
