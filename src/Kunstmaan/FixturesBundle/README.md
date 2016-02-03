# How to use the FixturesBundle

You will still use the default doctrine fixtures way but instead of extending from the `Doctrine\Common\DataFixtures\AbstractFixture` class, 
you will instead extend from the `Kunstmaan\FixturesBundle\Loader\FixtureLoader` class.
 
This is what your fixture class will look like:

```php
<?php

namespace Acme\SomeBundle\DataFixtures\ORM\DefaultSiteGenerator;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Kunstmaan\FixturesBundle\Loader\FixtureLoader;

class YamlFixtures extends FixtureLoader implements OrderedFixtureInterface
{
    protected function getFixtures()
    {
        return [
            __DIR__ . '/media.yml',
            __DIR__ . '/default.yml',
        ];
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 50;
    }
}
```

## Yaml you say?

Yes! Creating fixtures will has never been this easy. It's inspired by the Nelmio/Alice bundle. Why don't we use that bundle?
That's because Nelmio/Alice is great for "simple" entities, but with our CMS alot of stuff happens during the creation of pages and pageparts.
For this reason we wanted to start from scratch instead of hacking our way into another bundle.

So how do these yaml files look like?

```yaml
\Kunstmaan\MediaBundle\Entity\Media:
    image_homepage-header:
        folder: image
        originalFilename: <getMediaPath()>homepage-header.jpg

    image_{some.svg, this.svg, that.svg, yes.svg, no.svg}:
        folder: image
        originalFilename: <getMediaPath()><current()>


\Acme\SomeBundle\Entity\Pages\HomePage:
    homepage:
        title: <word()>
        parameters:
            page_internal_name: homepage
            set_online: true
            hidden_from_nav: false
            creator: <adminUser()>
        translations:
            nl:
                title: Home
            fr:
                title: Home

\Acme\SomeBundle\Entity\Pages\ContentPage:
    content{1..10}:
        title: <word()>
        parameters:
            parent: @homepage
            page_internal_name: content<current()>
            set_online: true
            hidden_from_nav: false
            creator: <adminUser()>
        translations:
            nl: []
            fr: []

\Acme\SomeBundle\Entity\Contact:
    contact_{ironman, blackwidow, thor, hulk, captainamerica, hawkeye}:
        firstName: <firstName()>
        lastName: <lastName()>
        email: <email()>
        function: <word()>
        mobile: <phoneNumber()>
        phone: <phoneNumber()>

\Acme\SomeBundle\Entity\PageParts\HeaderPagePart:
    header_pp_{1..10}:
        title: <word()>
        niv: 1
        parameters:
            page: @content<current()>
            context: header
        translations:
            nl: []
            fr: []

\Acme\SomeBundle\Entity\PageParts\ContactPagePart:
    contact_pp_{1..5}:
        contacts: 
            - @contact_ironman
            - @contact_blackwidow
            - @contact_thor
        parameters:
            page: @content<current()>
            context: main
        translations:
            nl: []
            fr: []

    contact_pp_{6..10}:
        contacts: 
            - @contact_hulk
            - @contact_captainamerica
            - @contact_hawkeye
        parameters:
            page: @content<current()>
            context: main
        translations:
            nl: []
            fr: []
```

### Providers

Providers are classes that can be used to return data to a fixture. For example, if you use the ```<current()>``` method, the Spec provider will be called upon.
So if you want to add some functionality to easily return the value of the page creator, you can add a Provider that contains the method

```php
public function adminUser()
{
    return 'admin';
}
```

By default you can add these kind of methods to your fixture class as it's automatically added as a provider.

Furthermore you have the Spec provider, NodeTranslation provider and Faker providers. You can add your own by adding the function ```getProviders()```
to your fixture class and returning an array containing your providers or you can tag your providers with ```kunstmaan_fixtures.provider```.

### Parsers

Parsers are used to translate the yaml data into actual data. So something like ```@content<current()>``` will be transformed to an object by different parsers.
By default you have the Method and the Reference parser for property data and the Listed and Range parser for specs. If you want to add your 
own parser, you can simply to that by tagging them with ```kunstmaan_fixtures.parser.property``` or ```kunstmaan_fixtures.parser.spec```

### Populators

Does exactly what the name says. Populators will populate the entities once all the yaml data is parsed. If you want to add your own populator, 
simply tag it with ```kunstmaan_fixtures.populator```

### Builders

With builders you can manipulate the behaviour during the creation of your entity. This can happen in three stages, preBuild, postBuild and postFlushBuild.
During these stages you can manipulate your entity or add more entities like we do in the PageBuilder for instance. If you want to add your own builder, 
simply tag it with ```kunstmaan_fixtures.builder```
