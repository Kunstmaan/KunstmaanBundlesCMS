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

## Uploading Media in Your Code

Using the ```MediaCreatorService``` you can easily upload a media-asset to a Folder.

The API is straightforward:

```
    $mediaCreatorService = $this->container->get('kunstmaan_media.media_creator_service');
    $media = $mediaCreatorService->createFile('./app/Content/Images/placeholder.jpg', 1);
```

The path is relevant to the root of your Symfony project. The context can be either web or console.
You'll have to set this to console when you are calling the code from an environment outside of your webserver.
For example for a migration you would use the console context. Otherwise you can just omit the parameter
so the default web context is used.

## Commands:

### Clean Deleted Media Command:

Description:

	Removes all files from the filesystem related to Media that has been flagged as deleted in the database.

Invoked by: ```app/console kuma:media:clean-deleted-media```

Options:

	--force/-f : Does not prompt the user if he is certain he wants to remove all deleted Media from the filesystem.
