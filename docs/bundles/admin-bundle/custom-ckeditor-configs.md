# Create and use custom ckeditor configs

Create an overrride for `@KunstmaanAdmin/Default/_ckeditor_configs_extra.html.twig` in `templates/bundles/KunstmaanAdminBundle/Default/_ckeditor_configs_extra.html.twig`
and define the custom ckeditor config(s).

```javascript
<script>
    var basicConfig = {
        skin: 'bootstrapck',
        startupFocus: false,
        bodyClass: 'CKEditor',
        filebrowserWindowWidth: 970,
        filebrowserImageWindowWidth: 970,
        filebrowserImageUploadUrl: '',
        toolbar: [
            { name: 'basicstyles', items: ['Bold', 'Italic', '-', 'RemoveFormat'] },
            { name: 'links', items : ['Link','Unlink', 'Anchor'] },
            { name: 'insert', items : ['SpecialChar'] },
            { name: 'clipboard', items : ['SelectAll', 'Cut', 'Copy', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo'] },
            { name: 'document', items : [ 'Source' ] }
        ],
        removePlugins: 'elementspath',
        disallowedContent: 'p{margin*}'
    };

    ckEditorConfigs['basic'] = basicConfig;
</script>
```

In the `WysiwygType` you can use the `editor-mode` option to switch between the different editor configs.

```php
namespace App\Form;

use Kunstmaan\AdminBundle\Form\WysiwygType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ExampleFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('content', WysiwygType::class, [
            'required' => true,
            'editor-mode' => 'basic',
        ]);
    }
}
```
