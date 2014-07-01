# Form Collection Additions

As we regularly use forms which contain sub entities, we also added support for these.

## Default implementation

To use our default implementation you only have to set the nested_form attribute to true on your collection fields :

```php
class MyAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        ...
        $builder->add(
            'prices',
            'collection',
            array(
                'type'               => new SeriesPriceAdminType(),
                'allow_add'          => true,
                'allow_delete'       => true,
                'by_reference'       => false,
                'cascade_validation' => true,
                'attr'               => array(
                    'nested_form'           => true,
                )
            )
        );
        ...
```

You can also specify a minimum and/or maximum number of items for the collection, by passing nested_form_min and/or
nested_form_max attributes.

## Drag & drop sorting

There also is support for drag & drop sorting of the collection elements, this can be enabled by passing in the
nested_sortable and - optionally - the nested_sortable_field attributes.

```php
        $builder->add(
            'prices',
            'collection',
            array(
                'type'               => new SeriesPriceAdminType(),
                'allow_add'          => true,
                'allow_delete'       => true,
                'by_reference'       => false,
                'cascade_validation' => true,
                'attr'               => array(
                    'nested_form'           => true,
                    'nested_sortable'       => true,
                    'nested_sortable_field' => 'displayOrder',
                )
            )
        );
```

If you don't set the nested_sortable_field attribute, a default of "weight" will be used (so you must make sure your
form type has a getter and setter for a weight field if you don't set it).

## References

- [Using sub entities in pageparts](http://bundles.kunstmaan.be/news/using-sub-entities-in-pageparts)
