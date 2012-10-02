# Add Aviary image editor to the [KunstmaanMediaBundle][KunstmaanMediaBundle]

This document describes how you can enable the Aviary image editor to the [KunstmaanMediaBundle][KunstmaanMediaBundle].

## Add Api Key to parameters.yml:

Register and get your Api Key on [http://www.aviary.com/](http://www.aviary.com/)

```yaml
    parameters:
      ...
      aviary_api_key: 'XXXXXXX'

```

Now you will get an "edit" button when you view an image.

[KunstmaanMediaBundle]: https://github.com/Kunstmaan/KunstmaanMediaBundle "KunstmaanMediaBundle"
