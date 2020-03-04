# KunstmaanSeoBundle 


[![Build Status](https://travis-ci.org/Kunstmaan/KunstmaanSeoBundle.png?branch=master)](http://travis-ci.org/Kunstmaan/KunstmaanSeoBundle)
[![Total Downloads](https://poser.pugx.org/kunstmaan/seo-bundle/downloads.png)](https://packagist.org/packages/kunstmaan/seo-bundle)
[![Latest Stable Version](https://poser.pugx.org/kunstmaan/seo-bundle/v/stable.png)](https://packagist.org/packages/kunstmaan/seo-bundle)
[![Analytics](https://ga-beacon.appspot.com/UA-3160735-7/Kunstmaan/KunstmaanSeoBundle)](https://github.com/igrigorik/ga-beacon)

Annotating content with metadata for social sharing and seo purposes cannot be overlooked nowadays. The KunstmaanSeoBundle contains default editing functionality for OpenGraph data, meta descriptions, keywords and titles and Metriweb tags. Because the metatagging and tracking options are always changing, a free field to add custom header information is provided as well.

View more screenshots and information [https://cms.kunstmaan.be](https://cms.kunstmaan.be).

## Installation

This bundle is compatible with all Symfony 3.* releases. More information about installing can be found in this line by line walkthrough of installing Symfony and all our bundles, please refer to the [Getting Started guide](https://kunstmaanbundlescms.readthedocs.io/en/stable/installation/) and enjoy the full blown experience.

## Symfony 2.2

If you want to use this bundle for a Symfony 2.2 release, use the 2.2 branch.

## Usage

### Metadata

In your template define the following to import all SEO metadata.
This SEO metadata is set in the SEO tab for your page in the backend.

```TWIG
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

You can also access the raw SEO object through the ```get_seo_for``` function.

### Social Widgets

And finally the SEO bundle is also capable of generating 'social widgets' such as a facebook like button.
For now only facebook like & linkedin product are supported.

Use the ```get_social_widget_for``` function. Example: ```get_social_widget_for(page, 'linkedin')```.

You can override the views for all the functions that generate HTML output.


### Google Analytics

Added to the SEO bundle are a few helpers to control Google Analytics.

These helpers rely on the inclusion of the analytics.js file that's provided in this bundle.
The script itself relies on jQuery to be present.

```TWIG
    {% javascripts
        'vendor/jquery/jquery.js'
        '@KunstmaanSeoBundle/Resources/public/js/analytics.js'
        output='frontend/footer.js'
    %}
        <script src='{{ asset_url }}'></script>
    {% endjavascripts %}
```

First up is ```google_analytics_initialize``` which looks for the Google Analytics Account ID in your config.

```YAML
    parameters:
        google.analytics.account_id: 'UA-XXXXX-1'
```

You can also optionally pass the ```account_id``` as an argument to the function.
```TWIG
    {{ google_analytics_initialize({'account_id': 'UA-XXXXX-1'}) }}
```

This script will set up a queue with commands (_gaq) and the Google Analytics script itself (_ga).
When Twig is in debug mode it'll not initialize the script but instead it'll just dump all requests to the console.
This way you can easily monitor what Google Analytics is planning on doing in your production environment.

The script will automatically track downloads, external links, links to email addresses & button clicks.

Finally, we've also provided a JavaScript function
which you'll have to call manually once the Twitter/Facebook SDK's have been loaded.
This will then bind a callback via the SDKs which will log the events.

```JavaScript
    googleAnalyticsApi.trackSocial('optional pageurl', 'optional trackername')
```


We've also added a helper for e-commerce tracking. You have to set up an ```Order``` object with its ```OrderItem```s
and pass it along to the ```google_analytics_track_order``` twig function. This will output the correctly formatted syntax for Google Analytics.

```PHP
    public function service(ContainerInterface $container, Request $request, RenderContext $renderContext)
    {
        $order = (new Order())
            ->setShippingTotal(5)
            ->setTransactionID('ORD2013-00231');

        $order->orderItems[] = (new OrderItem())
            ->setName('ACME 3000 Starter Kit')
            ->setQuantity(20)
            ->setUnitPrice(13.5)
            ->setTaxes((20 * 13.5) * 0.21)
            ->setCategoryorVariation('World Domination')
            ->setSKU('ACM300-SK');

        $order->orderItems[] = (new OrderItem())
            ->setName('Super Duper Kit')
            ->setQuantity(1)
            ->setUnitPrice(3000)
            ->setTaxes((3000) * 0.21)
            ->setCategoryorVariation('Fun')
            ->setSKU('SDK-3000');

        $renderContext['order'] = $order;
    }
```

```TWIG
    {{ google_analytics_track_order(order) }}
```
