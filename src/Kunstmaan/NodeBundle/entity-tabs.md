# Entity tabs

Are there too many fields in your form, when you're trying to edit a page? Would it make more sense to split your entity up into logical sections?
By implementing the PageTabInterface, you can split your page or entity up into multiple tabs.

## Usage

Start by implementing PageTabInterface

```php
<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\AdminBundle\Entity\AbstractEntity;
use Kunstmaan\NodeBundle\Entity\PageTabInterface;
use Kunstmaan\NodeBundle\ValueObject\PageTab;

/**
 * Product
 *
 * @ORM\Table(name="product")
 * @ORM\Entity()
 */
class Product extends AbstractEntity implements PageTabInterface {
    //define all of your fields
    
    public function getTabs() {
        return [
            new PageTab(
                'product_details', //Internal name of the tab
                'Details', //Title displayed in the tab
                Your\Form\Type\::class, //Form type class containing the fields you wish to edit in this tab
                1 //position of the tab
            ),
            new PageTab(
                'product_categories', //Internal name of the tab
                'Categories', //Title displayed in the tab
                Another\Form\Type::class, //Form type class containing the fields you wish to edit in this tab
                2 //position of the tab
            )
        ];
    }
}
```

The page tab interface forces you to implement the method ```getTabs()```. This method expects you to return an array of ```Kunstmaan\NodeBundle\ValueObject\PageTab```. 
This value object requires you to set an internal name of the tab, the title of the tab and form type that should be used in the tab. Optionally you can also set its position, to be able to control the order in the tab menu.

Since every PageTab requires a form type, it means more than one form type has to be defined for an entity or page.