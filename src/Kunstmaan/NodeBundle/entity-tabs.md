# Entity tabs

Are there too many fields in your form, when you're trying to edit a page? Would it make more sense to split your entity up into logical sections?
By implementing the PageTabInterface, you can split your page or entity up into multiple tabs.

## Usage

Start by implementing PageTabInterface. This interface forces you to implement the method ```getTabs()```.
This method expects you to return an array of ```Kunstmaan\NodeBundle\ValueObject\PageTab```.
In the value object you have to define the tabs internal name, display name and form type class. Optionally you can also set its position, to be able to control the order in the tab menu.

Imagine you have a ```AppBundle\Entity\Product``` entity, and you would like to split the display of the fields up into three sections: Product information, categories and pricing.
Then you would have the following entity:

```php
<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\NodeBundle\Entity\AbstractPage;
use Kunstmaan\NodeBundle\Entity\PageTabInterface;
use Kunstmaan\NodeBundle\ValueObject\PageTab;
use Kunstmaan\MediaBundle\Entity\Media;
use AppBundle\Entity\Category;

/**
 * @ORM\Table(name="product")
 * @ORM\Entity()
 */
class Product extends AbstractPage implements PageTabInterface {
    /**
     * @var Media
     *
     * @ORM\ManyToOne(targetEntity="Kunstmaan\MediaBundle\Entity\Media")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="product_image_id", referencedColumnName="id")
     * })
     */
    private $productImage;
    
    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=255, nullable=false)
     */
    private $productDescription;
    
    /**
     * @var Category[]
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Category", mappedBy="products")
     */
    private $categories;
    
    /**
     * @var string
     *
     * @ORM\Column(type="decimal", name="price_excl", precision=12, scale=2, nullable=false)
     */
    private $priceExcl;
    
    /**
     * @var string
     *
     * @ORM\Column(type="decimal", name="vat", precision=12, scale=2, nullable=false)
     */
    private $vat;
    
    /**
     * .. getters and setters here
     */
    
    /**
     * @return array
     */
    public function getPossibleChildTypes()
    {
        return [];
    }
    
    /**
     * @return string
     */
    public function getDefaultAdminType()
    {
        return AppBundle\Form\ProductType::class;
    }
    
    public function getTabs() {
        return [
            new PageTab(
                'product_categories', //Internal name of the tab
                'Categories', //Title displayed in the tab
                AppBundle\Form\ProductCategoriesType::class, //Form type class containing the fields you wish to edit in this tab
                1 //position of the tab
            ),
            new PageTab(
                'product_pricing', //Internal name of the tab
                'Pricing', //Title displayed in the tab
                AppBundle\Form\ProductPricingType::class, //Form type class containing the fields you wish to edit in this tab
                2 //position of the tab
            )
        ];
    }
}
```

Now that you have your entity, all there is left is to define the forms for this entity. The three forms in requested in the entity could look like this:

```php
<?php

namespace Esites\WebsiteBundle\Form;

use Kunstmaan\AdminBundle\Form\WysiwygType;
use Kunstmaan\MediaBundle\Form\Type\MediaType;
use Kunstmaan\NodeBundle\Form\PageAdminType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\Product;

/**
* This form will be displayed in the first tab "Content", which is defined in AppBundle\Entity\Product:getDefaultAdminType()
 */
class ProductType extends PageAdminType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        
        $builder
            ->add('productImage', MediaType::class, [
                'mediatype' => 'image',
                'required' => true,
            ])
            ->add('productDescription', WysiwygType::class, [
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
``` 

```php
<?php

namespace Esites\WebsiteBundle\Form;

use Kunstmaan\NodeBundle\Form\PageAdminType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\Product;
use AppBundle\Entity\Category;

/**
* This form will be displayed in the second tab "Categories", which is defined in AppBundle\Entity\Product:getTabs()
 */
class ProductCategoriesType extends PageAdminType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        
        $builder
            ->add('categories', EntityType::class, [
                'class' => Category::class,
                'multiple' => true,
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
``` 

```php
<?php

namespace Esites\WebsiteBundle\Form;

use Kunstmaan\NodeBundle\Form\PageAdminType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\Product;

/**
* This form will be displayed in the third tab "Pricing", which is defined in AppBundle\Entity\Product:getTabs()
 */
class ProductCategoriesType extends PageAdminType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        
        $builder
            ->add('priceExcl', NumberType::class, [
                'required' => true
            ])
            ->add('vat', NumberType::class, [
                'required' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
``` 

## Result

After implementing all of this, Kunstmaan will show the following tabs when editing the product:

| Content |  Categories | Pricing | Permissions | SEO | Social |
| ------- | ----------- | ------- | ----------- | --- | ------ |