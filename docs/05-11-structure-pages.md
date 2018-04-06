# Structure pages

Structure pages are a pagetype only used for structuring other pages.
They do not have a representation in the frontend side of your project.

## Creating a structure page

```PHP
<?php

namespace MyProject\WebsiteBundle\Entity\Pages;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\NodeBundle\Entity\StructureNode;

/**
 * @ORM\Table(name="myproject_structure_pages")
 * @ORM\Entity
 */
class StructurePage extends StructureNode
{
    /**
     * @return array
     */
    public function getPossibleChildTypes()
    {
        return array(
            array(
                'name'  => 'ContentPage',
                'class' => 'MyProject\WebsiteBundle\Entity\Pages\ContentPage'
            ),
            ...
    }
}

```
