# AdminList

## Permission support

### AbstractAdminListConfigurator

  * There is a new method called 'getPermissionDefinition()' (and a matching setter 'setPermissionDefinition()')

    This method should return either null or a PermissionDefinition object that will be used in calls (by AdminList)
    to an AclHelper, applying ACL constraints you want to impose. When you return null (the default return value),
    no restrictions will be applied.

### AdminList

  * There is a new method called 'setAclHelper()' & 'getAclHelper()'

    The setter method will allow you to set an AclHelper to be used to apply ACL constraints. If it is not set,
    no restrictions will be imposed, even if a PermissionDefinition was set (and vice versa).

## Create your own AdminList

### Using a Generator

The [KunstmaanGeneratorBundle](https://github.com/Kunstmaan/KunstmaanGeneratorBundle) offers a generator to generate an AdminList for your entity. It will generate the required classes and settings based on your Entity class.

For more information, see the AdminList generator [documentation](https://github.com/Kunstmaan/KunstmaanGeneratorBundle/blob/master/Resources/doc/GeneratorBundle.md#adminlist).

### Manually

Below you will find a how-to on how to create your own AdminList for your Entity manually. We also offer an [AdminList Generator](https://github.com/Kunstmaan/KunstmaanGeneratorBundle/blob/master/Resources/doc/GeneratorBundle.md#adminlist) to do this for you in the [KunstmaanGeneratorBundle](https://github.com/Kunstmaan/KunstmaanGeneratorBundle).

You will need to create 3 classes. An AdminListConfigurator, AdminListController and an AdminType. Let's assume you have already created an Entity called Document (with fields Title, Type and Reviewed) located in your Entity folder and its corresponding Repository.

#### Classes

##### Configurator

As its name implies the configurator will configure the listed fields and filters in your AdminList.

Create your DocumentAdminListConfigurator class in the AdminList folder in your Bundle and import your Entity class and the [FilterTypes](#adminlist-filters) you want to use to filter your AdminList.

```PHP
use Your\Bundle\Entity\Document;
use Kunstmaan\AdminListBundle\AdminList\FilterType\ORM\BooleanFilterType;
use Kunstmaan\AdminListBundle\AdminList\FilterType\ORM\StringFilterType;

class DocumentAdminListConfigurator extends AbstractSettingsAdminListConfigurator
{
```

Next we add the buildFilters() method and supply the fields we want to filter on. Our document has a title, type and a boolean telling us the document has been reviewed.

The first parameter of addFilter() method is the fieldname, the second parameter is the FilterType you want to use to filter this field with. The last parameter is the label for the filter.


```PHP
    public function buildFilters()
    {
        $this->addFilter('title', new StringFilterType('title'), 'Title');
        $this->addFilter('type', new StringFilterType('type'), 'Type');
        $this->addFilter('reviewed', new BooleanFilterType('reviewed'), 'Reviewed');
    }
```

The buildFields() method will allow you to configure which fields will be displayed in the list and is independent from the form used to edit your Entity.

The first parameter of the addField() method is the fieldname, second one is the column header and the last parameter you see here allows you to enable sorting for this field.

``` PHP
    public function buildFields()
    {
        $this->addField('title', 'Title', true);
        $this->addField('type', 'Type', true);
        $this->addField('reviewed', 'Reviewed', false);
    }
```

And at last we add our Entity name

```PHP
    public function getEntityName()
    {
        return 'Document';
    }
}
```

##### Controller

The controller will allow you to list, add, edit and delete your Entity. There's also a method to export the list of entities.

Create your DocumentAdminListController in your Controller folder and import your Entity class and the FilterTypes you want to use to filter your AdminList with.

```PHP
use Your\Bundle\Form\DocumentType;
use Your\Bundle\AdminList\DocumentAdminListConfigurator;

use Kunstmaan\AdminListBundle\Controller\AdminListController;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class DocumentAdminController extends AdminListController
{
    /**
     * @var AdminListConfiguratorInterface
     */
    private $configurator;

    /**
     * @return AdminListConfiguratorInterface
     */
    public function getAdminListConfigurator()
    {
        if (!isset($this->configurator)) {
            $this->configurator = new DocumentAdminListConfigurator($this->getEntityManager());
        }
        return $this->configurator;
    }
```

The first method will simply list your Entities.

```PHP
    /**
     * @Route("/", name="yourbundle_admin_document")
     * @Template("KunstmaanAdminListBundle:Default:list.html.twig")
     */
    public function indexAction(Request $request)
    {
        return parent::doIndexAction($this->getAdminListConfigurator(), $request);
    }
```

The add action method will build the form to add a new entity.

```PHP
    /**
     * The add action
     *
     * @Route("/add", name="yourbundle_admin_document_add")
     * @Method({"GET", "POST"})
     * @Template("KunstmaanAdminListBundle:Default:add_or_edit.html.twig")
     * @return array
     */
    public function addAction(Request $request)
    {
        return parent::doAddAction($this->getAdminListConfigurator(), $request);
    }
```

The edit action method will build and process the edit form.

```PHP
    /**
     * @param $id
     *
     * @throws NotFoundHttpException
     * @internal param $eid
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/{id}/edit", requirements={"id" = "\d+"}, name="yourbundle_admin_document_edit")
     * @Method({"GET", "POST"})
     * @Template("KunstmaanAdminListBundle:Default:add_or_edit.html.twig")
     */
    public function editAction(Request $request, $id)
    {
        return parent::doEditAction($this->getAdminListConfigurator(), $id, $request);
    }
```

The delete action will handle the deletion of your Entity.

```PHP
    /**
     * @param $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws NotFoundHttpException
     * @Route("/{id}/delete", requirements={"id" = "\d+"}, name="yourbundle_admin_document_delete")
     * @Method({"GET", "POST"})
     */
    public function deleteAction(Request $request, $id)
    {
        return parent::doDeleteAction($this->getAdminListConfigurator(), $id, $request);
    }
```

To export your Entities, there's the export action method.

```PHP
    /**
     * @Route("/export.{_format}", requirements={"_format" = "csv"}, name="yourbundle_document_export")
     * @Method({"GET", "POST"})
     *
     * @param $_format
     *
     * @return array
     */
    public function exportAction(Request $request, $_format) {
        $em = $this->getEntityManager();
        return parent::doExportAction(new DocumentAdminListConfigurator($em), $_format, $request);
    }
}
```

##### Form

The form Type class will create the form for the Entity when adding or editing one.

 ```PHP
 use Symfony\Component\Form\AbstractType;
 use Symfony\Component\Form\FormBuilderInterface;

 class DocumentType extends AbstractType
 {
 ```

Add your fields to the buildForm() method to add them to the add and edit form.

The add method's first parameter is the fieldname, the second one is the [field type](http://symfony.com/doc/2.0/reference/forms/types.html) and at last an array of additional options.

```PHP
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', 'text', array("required" => true));
        $builder->add('type', 'text', array("required" => true));
        $builder->add('reviewed', 'checkbox', array("required" => false));
    }
```

And include the following methods.

```PHP
    public function getName()
    {
        return 'document';
    }
}
```

#### Routing

Add the following lines to your routing.yml.

```YAML
YourBundle_documents:
    resource: "@YourBundle/Controller/DocumentAdminController.php"
    type: annotation
    prefix: /{_locale}/admin/documents
    requirements:
        _locale: %requiredlocales%
```

## AdminList Filters

The AdminList has by default several filters : String, Boolean, Date and Number

TODO : Add additional documentation
