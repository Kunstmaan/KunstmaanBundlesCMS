# PagePartBundle Documentation

## Creating PageParts in Your Code

We've provided a ```PagePartCreatorService``` that simplifies the creation of pageparts for your pages.

This works nicely in conjunction with the ```PageCreatorService``` that's present in the NodeBundle.
Another handy service is the ```MediaCreatorService``` that can be found in the MediaBundle.
It's used to easily upload files to the Media part of the backend.

The PagePartCreatorService supports several different syntax styles that can be interchanged with one another.

There are 2 functions available. The first one is the basic function ```addPagePartToPage``` which can only
add a single PagePart to a single page at once. It can however be added in a position of your own choosing.
If the position parameter is left as null it'll just be appended at the end.

It simply expects a Node instance or more conveniently an internal name of a node, a fully instantiated PagePart,
the language for which translation to append it to and finally the position.


The second function is far more useful and can be used to append multiple PageParts for multiple regions
on a single page for a single language. The function's interface is quite flexible. It expects a named array with
the name of the region as the key and an array containing the PageParts as the value.

The pageparts don't have to be instantiated but can be callables instead.
You can also mix & match instantiated PageParts and callables.
No PageParts are saved before each callable has returned an instantiated PagePart.


There are multiple of ways you can provide PagePart information.

* The most obvious one is a PagePart object that you manually instantiated yourself.

* The second way is an anonymous function that in itself instantiates a PagePart.
This is a pretty useful way to do more complex things. You could for example attempt to
upload assets using the ```MediaCreatorService```.
```PHP
function() {
    $pp = new HeaderPagePart();
    $pp->setTitle("General Conditions");
    $pp->setNiv(1);
    return $pp;
}
```

* The last one is a convenience-method provided by the PagePartCreatorService.
It basically expects the full class name as a string and a named array with
the keys being the names of the functions to call and the values what will be provided. Perfect for calling setters etc.
```PHP
$ppCreatorService->getCreatorArgumentsForPagePartAndProperties('Kunstmaan\PagePartBundle\Entity\HeaderPagePart',
    array('setNiv' => 1, 'setTitle' => 'General Conditions')
)
```

It's also important to note that the singular function ```addPagePartToPage``` does NOT support callables.


Below is an example of what you can do with this with all the styles interchanged.

```PHP
    $ppCreatorService = new PagePartCreatorService($this->container->get('doctrine.orm.entity_manager'));

    $logo = new Logo();
    $logo->setUrl('http://kunstmaan.be');

    $pageparts = array(
        'banners' => array(function() {
                $pp = new Satellite();
                $pp->setType('sputnik');
                return $pp;
            }, $logo
        ),
        'main' => array(
                $ppCreatorService->getCreatorArgumentsForPagePartAndProperties('Kunstmaan\PagePartBundle\Entity\HeaderPagePart',
                    array('setNiv' => 2, 'setTitle' => 'Some Title')
                ),
                $ppCreatorService->getCreatorArgumentsForPagePartAndProperties('Kunstmaan\PagePartBundle\Entity\TextPagePart',
                    array('setContent' => '<p>A bunch of interesting content.</p>')
                ),
                $ppCreatorService->getCreatorArgumentsForPagePartAndProperties('Kunstmaan\PagePartBundle\Entity\LinePagePart'),
                function() {
                                $pp = new InfoButtonPagePart();
                                $pp->setTitle('Show me more!');
                                return $pp; // Don't forget to return the PagePart ;)
                },
            )
        );

        $ppCreatorService->addPagePartsToPage('homepage', $pageparts, 'en');
```
