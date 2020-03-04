# KunstmaanMediaBundle

[![Build Status](https://travis-ci.org/Kunstmaan/KunstmaanMediaBundle.png?branch=master)](http://travis-ci.org/Kunstmaan/KunstmaanMediaBundle)
[![Total Downloads](https://poser.pugx.org/kunstmaan/media-bundle/downloads.png)](https://packagist.org/packages/kunstmaan/media-bundle)
[![Latest Stable Version](https://poser.pugx.org/kunstmaan/media-bundle/v/stable.png)](https://packagist.org/packages/kunstmaan/media-bundle)
[![Analytics](https://ga-beacon.appspot.com/UA-3160735-7/Kunstmaan/KunstmaanMediaBundle)](https://github.com/igrigorik/ga-beacon)

To build your perfect website you probably need images, video's or maybe even a presentation too. The Kunstmaan Media Bundle handles all those media assets and centralizes them so you can find your content just the way you like it: fast and efficiently. No central asset management module is useful without some pretty advanced image editing functionality. To provide this we have integrated the Aviary image editing service right from the interface.

View more screenshots and information [https://cms.kunstmaan.be](https://cms.kunstmaan.be).

## Installation

This bundle is compatible with all Symfony 3.* releases. More information about installing can be found in this line by line walkthrough of installing Symfony and all our bundles, please refer to the [Getting Started guide](https://kunstmaanbundlescms.readthedocs.io/en/stable/installation/) and enjoy the full blown experience.

## Symfony 2.2

If you want to use this bundle for a Symfony 2.2 release, use the 2.2 branch.

## Audio

If you want to use your own api key for SoundCloud, you can define this in the config.yml of your application but it works fine without it as well.

```yml
kunstmaan_media:
    soundcloud_api_key: YOUR_CLIENT_ID
```

## Tooltips

If you want to add a nifty tooltip to your media chooser in the admin, you can just add the following to your form type:

```php
$builder
    ->add(
      'media',
      'media',
      array(
          'pattern' => 'KunstmaanMediaBundle_chooser',
          'mediatype' => 'image',
          'attr' => array('info_text' => 'YOUR TOOLTIP TEXT'),
      )
    );
```

## Generating PDF preview thumbnails

For this functionality to work, you need to install the ImageMagick extension with PDF support (using
Ghostscript). You will also have to make sure that the Ghostscript executable (gs) can be found
in the path of the user that is executing the code (apache/www or a custom user depending on your setup).

You can determine that path by running ```which gs``` on the command line in Linux/OS X.

To install Ghostscript on Mac OS X you can use ```brew install gs```.

On OS X with apache you will probably have to add that path to the apache environment settings in
```/System/Library/LaunchDaemons/org.apache.httpd.plist```. Make sure it contains the following :
```
<key>EnvironmentVariables</key>
<dict>
    <key>PATH</key>
    <string>/usr/bin:/bin:/usr/sbin:/sbin:/path/to/gs</string>
</dict>
```

Where ```/path/to/gs``` is just the actual path where the gs binary is stored.

*NOTE:* This functionality has to be enabled by setting the ```enable_pdf_preview``` configuration option to true, ie. :

```yml
kunstmaan_media:
    enable_pdf_preview: true
```
