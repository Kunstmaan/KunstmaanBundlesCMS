# KunstmaanSeoBundle [![Build Status](https://travis-ci.org/Kunstmaan/KunstmaanSeoBundle.png?branch=master)](http://travis-ci.org/Kunstmaan/KunstmaanSeoBundle)

Annotating content with metadata for social sharing and seo purposes cannot be overlooked nowadays. The KunstmaanSeoBundle contains default editing functionality for OpenGraph data, meta descriptions, keywords and titles and Metriweb tags. Because the metatagging and tracking options are always changing, a free field to add custom header information is provided as well.

[![KunstmaanMediaBundle](http://bundles.kunstmaan.be/bundles/kunstmaankunstmaanbundles/img/features/meta.png)](http://bundles.kunstmaan.be)
View more screenshots and information [http://bundles.kunstmaan.be](http://bundles.kunstmaan.be).

## Installation

This bundle is compatible with all Symfony 2.3.* releases. More information about installing can be found in this line by line walkthrough of installing Symfony and all our bundles, please refer to the [Getting Started guide](http://bundles.kunstmaan.be/doc/01_GettingStarted.html) and enjoy the full blown experience.

## Symfony 2.2

If you want to use this bundle for a Symfony 2.2 release, use the 2.2 branch.

## Usage

In your template define the following to import all SEO metadata.

```
    {% if page is defined %}
        {{ render_seo_metadata_for(page) }}
    {% endif %}
```

For the title there are several options.
There are several twig functions which return a title based on which is found first.
If they are all null or empty it'll return an empty string.

The ```get_title_for``` twig function used the following order:
* SEO title
* Page title
If nothing is set it'll return an empty string.

Another option is ```get_title_for_page_or_default```. This twig function accepts a default string that is used as a fallback.
* SEO title
* default string
* Page title

You can also access the SEO object through the ```get_seo_for``` function.

And finally the SEO bundle is also capable of generating 'social widgets' such as a facebook like button.
For now only facebook like & linkedin product are supported.

Use the ```get_social_widget_for``` function. Example: ```get_social_widget_for(page, 'linkedin')```.

You can override the views for all the functions that generate HTML output.

