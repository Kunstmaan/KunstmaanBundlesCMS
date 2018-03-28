# Creating Nodes in Your Code

We've provided a ```PageCreatorService``` that simplifies the creation of nodes with multiple translations.
You can use this class in your own code like controllers, service methods in your Entities but perhaps most notably
in migrations. You can implement the ContainerAwareInterface in your migrations and fetch the service from there.

A sample of how you would create a page with an internal name, hooked right under the homepage,
for dutch and english and publish it immediately.

```
        $nodeRepo = $em->getRepository('KunstmaanNodeBundle:Node');
        $homePage = $nodeRepo->findOneBy(array('internalName' => 'homepage'));

        $overviewPage = new ContentPage();
        $overviewPage->setTitle('My Satellites');

        $translations = [];
        $translations[] = ['language' =>  'en', 'callback' => function($page, $translation, $seo) {
            $translation->setTitle('My collection of satellites');
            $translation->setSlug('my-collection-of-satellites');
        }];
        $translations[] = ['language' =>  'nl', 'callback' => function($page, $translation, $seo) {
            $translation->setTitle('Mijn collectie satellieten');
            $translation->setSlug('mijn-collectie-satellieten');
        }];

        $options = [
            'parent' => $homePage,
            'page_internal_name' => 'satellites',
            'set_online' => true,
            'creator' => 'Admin'
        ];

        $pageCreator->createPage($overviewPage, $translations, $options);
```

Check the ```PagePartBundle``` documentation for a service that does something similar but for adding pageparts to a page.
