# AdminList

## How to create a new AdminList

Below you will find a how-to on how to create your own AdminList for your Entity manually. We also offer an [AdminList Generator](https://github.com/Kunstmaan/KunstmaanGeneratorBundle/blob/master/Resources/doc/GeneratorBundle.md#adminlist) to do this for you in the [KunstmaanGeneratorBundle](https://github.com/Kunstmaan/KunstmaanGeneratorBundle).

You will need to create 3 classes. An AdminListConfigurator, AdminListController and an AdminType. Let's assume you have already created an Entity called Document (with fields Title, Type and Reviewed) located in your Entity folder and its corresponding Repository.

## Classes

### Configurator

As its name implies the configurator will configure the listed fields and filters in your AdminList.

Create your DocumentAdminListConfigurator class in the AdminList folder in your Bundle and import your Entity class and the [FilterTypes](https://github.com/Kunstmaan/KunstmaanAdminListBundle/edit/master/Resources/doc/Filters.md) you want to use to filter your AdminList.

```PHP
use Your\Bundle\Entity\Document;
use Kunstmaan\AdminListBundle\AdminList\FilterType\ORM\BooleanFilterType;
use Kunstmaan\AdminListBundle\AdminList\FilterType\ORM\StringFilterType;

class DocumentAdminListConfigurator extends AbstractSettingsAdminListConfigurator
{
```

Next we add the buildFiters() method and supply the fields we want to filter on. Our document has a title, type and a boolean telling us the document has been reviewed.

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
        return 'User';
    }
}
```

### Controller

The controller will allow you to list, add, edit and delete your Entity.

Create your DocumentAdminListController in your Controller folder and import you Entity class and the FilterTypes you want to use to filter your AdminList.

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
```

The first method will simply list your Entities.

```PHP
    /**
     * @Route("/document", name="yourbundle_admin_document")
     * @Template("KunstmaanAdminListBundle:Default:list.html.twig")
     */
    public function documentAction()
    {
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();
        $adminlist = $this->get("kunstmaan_adminlist.factory")->createList(new DocumentAdminListConfigurator($em));
        $adminlist->bindRequest($request);

        return array(
            'adminlist' => $adminlist,
            'addparams' => array()
        );
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
     * @Route("/document/{id}/edit", requirements={"id" = "\d+"}, name="yourbundle_admin_document_edit")
     * @Method({"GET", "POST"})
     * @Template("KunstmaanAdminListBundle:Default:edit.html.twig")
     */
    public function editDocumentAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $request = $this->getRequest();
        $text = $em->getRepository('YourBundle:Document')->find($id);
        if ($text == NULL) {
            throw new NotFoundHttpException("Entity not found.");
        }
        $form = $this->createForm(new DocumentType(), $text);

        if('POST' == $request->getMethod()){
            $form->bind($request);
            if($form->isValid()){
                $em->persist($text);
                $em->flush();

                return new RedirectResponse($this->generateUrl('yourbundle_admin_document'));
            }
        }

        return array(
            'form' => $form->createView(),
            'entity' => $text,
            'adminlistconfigurator' => new DocumentAdminListConfigurator($em)
        );
    }
```

The delete action will handle the deletion of your Entity.

```PHP
    /**
     * @param $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws NotFoundHttpException
     * @Route("/document/{id}/delete", requirements={"id" = "\d+"}, name="yourbundle_admin_document_delete")
     * @Method({"GET", "POST"})
     */
    public function deleteDocumentAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $helper = $em->getRepository('YourBundle:Document')->findOneById($id);
        if ($helper != NULL) {
            $em->remove($helper);
            $em->flush();
        }

        return new RedirectResponse($this->generateUrl('yourbundle_admin_document'));
    }
```

To export your Entities, there's the export action method.

```PHP
    /**
     * @Route("/document/export.{_format}", requirements={"_format" = "csv"}, name="yourbundle_document_export")
     * @Method({"GET", "POST"})
     *
     * @param $_format
     *
     * @return array
     */
    public function exportDocumentAction($_format) {
        $em = $this->getDoctrine()->getManager();
        return parent::doExportAction(new DocumentAdminListConfigurator($em), $_format);
    }
}
```

### Form

The form Type class will create the form for the Entity when adding or editing one.

 ```PHP
 use Symfony\Component\Form\AbstractType;
 use Symfony\Component\OptionsResolver\OptionsResolverInterface;
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
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Your\Bundle\Entity\Document'
        ));
    }

    public function getName()
    {
        return 'document';
    }
}
```

## Routing

Add the following lines to your routing.yml.

```YAML
YourBundle_documents:
    resource: "@YourBundle/Controller/DocumentAdminController.php"
    type: annotation
    prefix: /{_locale}/admin/documents
    requirements:
        _locale: %requiredlocales%
```