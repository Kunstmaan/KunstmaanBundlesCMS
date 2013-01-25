# MediaBundle
## Add Aviary image editor

This document describes how you can enable the Aviary image editor to the [KunstmaanMediaBundle][KunstmaanMediaBundle].

### Add Api Key to parameters.yml:

Register and get your Api Key on [http://www.aviary.com/](http://www.aviary.com/)

```yaml
    parameters:
      ...
      aviary_api_key: 'XXXXXXX'

```

Now you will get an "edit" button when you view an image.

[KunstmaanMediaBundle]: https://github.com/Kunstmaan/KunstmaanMediaBundle "KunstmaanMediaBundle"

## Add media handler

This document describes how you can add a new media handlers to the [KunstmaanMediaBundle][KunstmaanMediaBundle].

### Create a MediaHandler

### Create a MediaHelper

### Add the mediahandler service:

```yaml
    service:
        ...
        pdf:
            default: false
            id: kunstmaan_media.provider.pdf

```

[KunstmaanMediaBundle]: https://github.com/Kunstmaan/KunstmaanMediaBundle "KunstmaanMediaBundle"

## MediaField

A field for media references. It has a "choose" button which opens a popup where you can select your media item from the media repository.

### Example Usage:

```php
$builder->add('ogImage', 'media', array(
    'mediatype' => 'image',
    'label' => 'OG image'
));
```

### Options:

mediatype:
    type: string
    default: null
    description:
        You can specify a specific mediahandler by its name, when this is null all media items are possible.
        Knows possible values are: image|file|remotevideo|remoteslide

### Parent type:

form

### Class:

Kunstmaan\MediaBundle\Form\Type\MediaType
