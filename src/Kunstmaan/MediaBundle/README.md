KunstmaanMediaBundle by Kunstmaan
=================================

About
-----

While building websites for clients we have learned that almost every website benefits from a centralised management of multimedia assets. To facilitate our building process we have created this bundle to add this functionality with the least amount of hassle. Additionally, we want our websites to leverage external services as much as possible, so all video and presentation content is offloaded (manually) to external website.

This bundle provides this centralised module for multimedia assets, and has built in providers for local images, [YouTube](http://www.youtube.com), [Vimeo](http://www.vimeo.com) and [Dailymotion](http://www.Dailymotion.com) video's, [Speakerdeck](http://speakerdeck.com/) and [Slideshare](http://www.slideshare.net/) presentations, and generic local files.

No cental asset management module is useful without some pretty advanced image editing functionality. To provide this we have integrated the [Avairy](http://www.aviary.com/) image editing service right from the interface. This provides us with all the tools needed to manage images in a website, without the need of a Photoshop or other image editing tool on the administrators computer.

View some screenshots on this bundles [github page](http://kunstmaan.github.com/KunstmaanMediaBundle).

[![Build Status](https://secure.travis-ci.org/Kunstmaan/KunstmaanMediaBundle.png?branch=master)](http://travis-ci.org/Kunstmaan/KunstmaanMediaBundle)


Installation requirements
-------------------------
You should be able to get Symfony 2 up and running before you can install the KunstmaanMediaBundle.

Installation instructions
-------------------------
Installation is straightforward, add the following lines to your deps file:

```
[KunstmaanMediaBundle]
    git=git@github.com:Kunstmaan/KunstmaanMediaBundle.git
    target=/bundles/Kunstmaan/MediaBundle
```

Register the Kunstmaan namespace in your autoload.php file:

```
'Kunstmaan'        => __DIR__.'/../vendor/bundles'
```

Add the KunstmaanMediaBundle to your AppKernel.php file:

```
new Kunstmaan\MediaBundle\KunstmaanMediaBundle(),
```

Contact
-------
Kunstmaan (support@kunstmaan.be)

Download
--------
You can also clone the project with Git by running:

```
$ git clone git://github.com/Kunstmaan/KunstmaanMediaBundle
```