# Defining custom CKEditor configurations

Sometimes you want to limit or extend the possibilities of your rich text editor fields used in the Kunstmaan bundles admin.
You can do so by following 3 easy steps.

## Extending the configuration file

First step is to extend the `_ckeditor_configs_extra.html.twig` file. You can do this by creating this file in the following location: `app/Resources/KunstmaanAdminBundle/views/Default/` Create the content frame:

```html
<script>
    externalPlugins = [
        // Add arrays with plugins here.
    ];

    ckEditorConfigs = {
        // Add styles here 
    };
</script>
```

## Creating the new configurations

Next step is adding the custom configurations.
As example we have added a option with only some basic styles but you can alter these as you see fit.

```javascript
ckEditorConfigs = {
    'new_style': {
        skin: 'bootstrapck',
        startupFocus: false,
        bodyClass: 'CKEditor',
        filebrowserWindowWidth: 970,
        filebrowserImageWindowWidth: 970,
        filebrowserImageUploadUrl: '',
        toolbar: [{ 
            name: 'basicstyles', 
            items: ['Bold', 'Italic', 'Underline', 'Subscript', 'Superscript', 'RemoveFormat'] 
        }]
    },
    'other_new_style': {
        ...
    }
};
```

Here is the full toolbar:
```javascript
ckEditorConfigs = {
    'full': {
        skin: 'bootstrapck',
        startupFocus: false,
        height: 300,
        bodyClass: 'CKEditor',
        filebrowserWindowWidth: 970,
        filebrowserImageWindowWidth: 970,
        filebrowserImageUploadUrl: '',
        toolbar: [
            { name: 'basicstyles', items : ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', 'RemoveFormat'] },
            { name: 'paragraph', groups: [ 'align' ], items: [ 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'] },
            { name: 'lists', items : ['NumberedList', 'BulletedList'] },
            { name: 'dents', items : ['Outdent', 'Indent'] },
            { name: 'links', items : ['Link','Unlink', 'Anchor'] },
            { name: 'insert', items : ['Image', 'Table', 'SpecialChar'] },
            { name: 'clipboard', items : ['SelectAll', 'Cut', 'Copy', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo'] },
            { name: 'editing', items : [] },
            { name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize' ] },
            { name: 'colors', items: [ 'TextColor', 'BGColor' ] },
            { name: 'document', items : ['Source'] }
        ]
    },
}
```

## Linking the configuration and the form field

Last step is linking your form and newly created styles.
You can do so by adding 'type' as an attribute to your wysiwyg field.

Bundles >= v3.1
```PHP
public function buildForm(FormBuilderInterface $builder, array $options) {
  $builder->add('entityName', 'wysiwyg', array(
    'attr' => array(
        'data-editor-mode' => 'new_style',
    ),
  ));
}

```

The important thing is to match the value of the type with the name of your newly created style.
