# PagePartBundle

## Pageparts
The KunstmaanPagePartBundle is the basis of our content management framework. A page built using a composition of blocks names pageparts. These pageparts allow you to fully separate the data from the presentation so non-technical webmasters can manage the website. Every page can have it's own list of possible pageparts, and pageparts are easy to create for your specific project to allow for rapid development.


### Configuration

For each page you can configure multiple contexts which pageparts in it. (For example "main", "banners", "right column", "footer").

#### Configuration in PHP
```PHP

class HomePage extends AbstractPage implements HasPagePartsInterface
{

...

public function getPagePartAdminConfigurations()
{
    return array(new HomepageMainPagePartAdminConfigurator());
}


```

```PHP
use Kunstmaan\PagePartBundle\PagePartAdmin\AbstractPagePartAdminConfigurator;

/**
 * HomepageMainPagePartAdminConfigurator
 */
class HomepageMainPagePartAdminConfigurator extends AbstractPagePartAdminConfigurator
{

    /**
     * @var array
     */
    protected $pagePartTypes;

    /**
     * @param array $pagePartTypes
     */
    public function __construct(array $pagePartTypes = array())
    {
        $this->pagePartTypes = array_merge(
            array(
                array(
                    'name' => 'Header',
                    'class'=> 'Kunstmaan\PagePartBundle\Entity\HeaderPagePart'
                ),
                array(
                    'name' => 'Text',
                    'class'=> 'Kunstmaan\PagePartBundle\Entity\TextPagePart'
                ),
                array(
                    'name' => 'Line',
                    'class'=> 'Kunstmaan\PagePartBundle\Entity\LinePagePart'
                ),
                array(
                    'name' => 'Image',
                    'class'=> 'Kunstmaan\MediaPagePartBundle\Entity\ImagePagePart'
                )
            ), $pagePartTypes
        );

    }

    /**
     * @return array
     */
    public function getPossiblePagePartTypes()
    {
        return $this->pagePartTypes;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return "Page parts";
    }

    /**
     * @return string
     */
    public function getDefaultContext()
    {
        return "main";
    }
}
```

#### Configuration in YAML
You can also specify the pagepart configuration in yml:

```PHP
public function getPagePartAdminConfigurations()
{
    return array("WebsiteBundle:homepage_main");
}
```

Resources/config/pageparts/homepage_main.yml
```YAML

name: "Page parts"
context: "main"
types:
    - { name: "Header", class: "Kunstmaan\\PagePartBundle\\Entity\\HeaderPagePart" }
    - { name: "Text", class: "Kunstmaan\\PagePartBundle\\Entity\\TextPagePart" }
    - { name: "Line", class: "Kunstmaan\\PagePartBundle\\Entity\\LinePagePart" }
    - { name: "Image", class: "Kunstmaan\\MediaPagePartBundle\\Entity\\ImagePagePart" }
    
```
### Rendering
Then you can render these pageparts in your template:
```TWIG
{{ render_pageparts(page, 'main') }}
```
Or if you need to manipulate the list with extra html:
```TWIG
{% for pagepart in getpageparts(page, 'main') %}
  <div class="clazz">
	{% include pagepart.defaultview with {'resource': pagepart} %}
  </div>
{% endfor %}
```

## PageTemplates
Until now we had specified some pagepart contexts (= pagepart list) and we can include them in our templates at fixed positions. But it's also possible to define multiple templates for these contexts. For example you can have multiple columns, or a singe column and switch between them in the backend. Then there is a 'switch template' button which shows a popup like this:
[TODO screenshot]

### Configuration
#### Configuration in PHP
```PHP

class HomePage extends AbstractPage implements HasPagePartsInterface
{

...

public function getPageTemplates()
{
    return array(new HomepagePageTemplate(), new HomepageExtendedPageTemplate());
}


```

```PHP
class HomepagePageTemplate extends PageTemplate
{
    /**
     * constructor
     */
    public function __construct()
    {
        $this->setName("Homepage")
             ->setRows(array(
                new Row(array(new Region("main", 12))),
             ))
             ->setTemplate("KunstmaanKumasandboxBundle::PageTemplate\ContentPageTemplate");
    }
}
class HomepageExtendedPageTemplate extends PageTemplate
{
    /**
     * constructor
     */
    public function __construct()
    {
        $this->setName("Homepage extended")
             ->setRows(array(
                new Row(array(new Region("main", 9), new Region("banners", 3))),
                new Row(array(new Region("bottom", 12)))))
             ->setTemplate("KunstmaanKumasandboxBundle::PageTemplate\ContentPageTemplate");
    }
}
```

#### Configuration in YAML
You can also specify the template configuration in yml:

```PHP

class HomePage extends AbstractPage implements HasPagePartsInterface
{

...

public function getPageTemplates()
{
    return array("WebsiteBundle:homepage", "WebsiteBundle:two-column-homepage");
}

```

Resources/config/pagetemplates/homepage.yml
```YAML

name: "Homepage"
rows:
    - regions: 
        - { name: "main", span: 12 }
template: "KunstmaanWebsiteBundle::Pages\ContentPage\pagetemplate.html.twig"
    
```

Resources/config/pagetemplates/extended-homepage.yml
```YAML

name: "Homepage extended"
rows:
    - regions: 
        - { name: "main", span: 9 }
        - { name: "banners", span: 3 }
    - regions: 
        - { name: "bottom", span: 12 }
template: "KunstmaanWebsiteBundle::Pages\ContentPage\extended-pagetemplate.html.twig"
    
```
**PRO-TIP: The name of the region is linked to the context in the pagepart configuration, not the name of the pagepart configuration file.**

### Rendering
This is an example of the extended-pagetemplate.html.twig file:
```TWIG

<!-- CONTENT -->
<div> <!-- div to wrap anchors and content -->

    <!-- MAIN CONTENT -->
    <div class="main-content">

        {{ render_pageparts(page, 'main') }}

    </div><!-- end main-content -->
        
    <!-- BANNERS -->
    <div class="banners-content">

        {{ render_pageparts(page, 'banners') }}

    </div><!-- end banners-content -->
</div>
    
<!-- FOOTER -->
<div class="footer-content">

    {{ render_pageparts(page, 'bottom') }}

</div><!-- end footer-content -->
    
```

Use this in your page template to render the configured "template"
```TWIG
<div class="content-container">
    {{ render_pagetemplate(page) }}
</div><!-- end content-container -->
```
