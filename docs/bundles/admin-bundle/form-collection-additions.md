# Form Collection Additions

As we regularly use forms which contain sub entities, we also added support for these.

## Default implementation

To use our default implementation you only have to set the `nested_form` attribute to true on your collection fields :

```php
class MyAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        ...
        $builder->add(
            'prices',
            CollectionType::class,
            array(
                'type'               => new SeriesPriceAdminType(),
                'allow_add'          => true,
                'allow_delete'       => true,
                'by_reference'       => false,
                'attr'               => array(
                    'nested_form'           => true,
                )
            )
        );
        ...
    }
}
```

You can also specify a minimum and/or maximum number of items for the collection, by passing `nested_form_min` and/or
`nested_form_max` attributes.

## Drag & drop sorting

There also is support for drag & drop sorting of the collection elements, this can be enabled by passing in the
`nested_sortable` and - optionally - the `nested_sortable_field` attributes.

```php
class MyAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        ...
        $builder->add(
            'prices',
            CollectionType::class,
            array(
                'type'               => new MySubAdminType(),
                'allow_add'          => true,
                'allow_delete'       => true,
                'by_reference'       => false,
                'attr'               => array(
                    'nested_add_button_label' => 'form.nested.add',
                    'nested_form'             => true,
                    'nested_sortable'         => true,
                    'nested_sortable_field'   => 'displayOrder',
                )
            )
        );
        ...
    }
}

class MySubAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        ...
        $builder->add('displayOrder', HiddenType::class);
        ...
    }
}
```

If you don't set the `nested_sortable_field` attribute, a default of "weight" will be used (so you must make sure your
form type has a getter and setter for a weight field if you don't set it).

> You can override the default "form.add" button label - this is the translation key! - with the `nested_add_button_label`
> attribute.

## Many to Many relations

If you have a many-to-many relation, you wouldn’t want to delete the related entity when removing it from page part.
In order to skip this, set `nested_deletable => false` attribute, i.e.:

```
'attr' => array(
    'nested_form'           => true,
    'nested_deletable'      => false,
)
```                


## References

- [Using sub entities in pageparts](http://bundles.kunstmaan.be/news/using-sub-entities-in-pageparts)
