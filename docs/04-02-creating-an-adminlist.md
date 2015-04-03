# Creating an adminlist

So you already created a page part and had it shown on the front-end website, but what if you want to manage other entities, that don't really belong in the nodes tree? We've got a bundle for that!

So, on to the admin list bundle.

## What is an admin list?

In its most basic form it's a paged view of entities, with options to add, edit and delete entities.


## Creating your first admin list

Suppose we want to manage the employees of our virtual company. For every employee we'll store the first and last name, a picture and a link to their Twitter profile (by storing their Twitter handle).

The first thing to do is create an entity to store this information. To do that, we'll use our entity generator :

    app/console kuma:generate:entity

And enter the following data at the respective prompts :

- Entity shortcut name : `MyProjectWebsiteBundle:Employee`
- Tablename prefix : `myproject_websitebundle_`
- Field name : `first_name`, type : `string`, length : `25`
- Field name : `last_name`, type : `string`, length : `50`
- Field name : `twitter_handle`, type : `string`, length : `20`

We don't need an empty repository class, but would like an admin list, generate the source skeleton and initialise the routing, so answer these prompts accordingly.

So, now we have the skeleton ready, but as mentioned before we would like to add a picture of the employee. We'll add this field manually, so open up `src/MyProject/WebsiteBundle/Entity/Employee.php` and add the following :

```php
...
    /**
     * @var \Kunstmaan\MediaBundle\Entity\Media
     *
     * @ORM\ManyToOne(targetEntity="Kunstmaan\MediaBundle\Entity\Media")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="picture_id", referencedColumnName="id")
     * })
     */
    private $picture;

...
    /**
     * Set picture
     *
     * @param \Kunstmaan\MediaBundle\Entity\Media $picture
     * @return Employee
     */
    public function setPicture(\Kunstmaan\MediaBundle\Entity\Media $picture = null)
    {
      $this->picture = $picture;

      return $this;
    }

    /**
     * Get picture
     *
     * @return \Kunstmaan\MediaBundle\Entity\Media
     */
    public function getPicture()
    {
      return $this->picture;
    }

```

Since we've added an extra field to our entity, we'll also have to update the entry form (AdminType) that is attached to our Employee entity, so open up `src/MyProject/WebsiteBundle/Form/EmployeeAdminType.php` and add the picture field there
as well :

```php
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
	...
	$builder->add(
	    'picture',
	    'media',
	    array(
		'pattern'  => 'KunstmaanMediaBundle_chooser',
		'required' => false,
	    )
	);
    }

```

Now everything should be good to go, so we'll create a new migration for the required database changes, and apply it immediately :

    app/console doctrine:migrations:diff && app/console doctrine:migrations:migrate

If all went well, you should see a bare bones admin list when you go to `/app_dev.php/en/admin/employee/`.


## Adding validation constraints

As all fields (except for the picture) should be required, we can add the default NotBlank annotation to make sure validation errors are triggered when people don't fill out the form correctly, so open `src/MyProject/WebsiteBundle/Entity/Employee.php` and add the following:

```php
...
use Symfony\Component\Validator\Constraints as Assert;
...
    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=25)
     * @Assert\NotBlank()
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=50)
     * @Assert\NotBlank()
     */
    private $lastName;

    /**
     * @var string
     *
     * @ORM\Column(name="twitter_handle", type="string", length=20)
     * @Assert\NotBlank()
     */
    private $twitterHandle;

    /**
     * @var \Kunstmaan\MediaBundle\Entity\Media
     *
     * @ORM\ManyToOne(targetEntity="Kunstmaan\MediaBundle\Entity\Media")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="picture_id", referencedColumnName="id")
     * })
     */
    private $picture;

```

## Using a custom column template

It would be neat to display the picture (if there is one) in the admin list as well, and while we're at it, we would like to use proper column names (instead of the field name) as well. So open `src/MyProject/WebsiteBundle/AdminList/EmployeeAdminListConfigurator.php` and make the following changes :

```php
    /**
     * Configure the visible columns
     */
    public function buildFields()
    {
	$this->addField('firstName', 'First name', true);
	$this->addField('lastName', 'Last name', true);
	$this->addField('twitterHandle', 'Twitter handle', true);
	$this->addField('picture', 'Picture', false);
    }
```

As the picture field has a toString method that just returns the id of the relevant record in the media table this will display a number instead of the actual image, which is not what we want. So let's fix that. First we'll a template file to display the column. So create a new folder (we like a consistent naming scheme, so we'll add these custom column templates in `AdminList/entity-name/column-name.twig.html`) :

```
mkdir -p src/MyProject/WebsiteBundle/Resources/views/AdminList/Employee
```

Then create a new file called picture.html.twig in this folder:

```php
{% if object is not null and object.url is not empty %}
<img class="thumbnail" src="{{ object.url | imagine_filter('employee_thumbnail') }}" />
{% else %}
No picture!
{% endif %}
```

Add an extra entry for the `employee_thumbnail` filter to the `filter_sets` in `app/config/config.yml` :

```yml
...
liip_imagine:
    ...
    filter_sets:
    ...
	employee_thumbnail:
	    quality: 80
	    filters:
		thumbnail: { size: [100, 100], mode: outbound }
```

And finally specify this template in the `buildFields` method in `src/MyProject/WebsiteBundle/AdminList/EmployeeAdminListConfigurator.php` :

```php
    /**
     * Configure the visible columns
     */
    public function buildFields()
    {
	$this->addField('firstName', 'First name', true);
	$this->addField('lastName', 'Last name', true);
	$this->addField('twitterHandle', 'Twitter handle', true);
	$this->addField('picture', 'Picture', false, 'MyProjectWebsiteBundle:AdminList\Employee:picture.html.twig');
    }
```


## Creating an admin list for entities you already created


If you want to use the admin list for entities you already created, you will have to refactor your code (and probably create a database migration as well) so your entity extends our AbstractEntity (`\Kunstmaan\AdminBundle\Entity\AbstractEntity`).

And then you can simply run the admin list generator to generate the basic admin list skeleton:

    app/console kuma:generate:adminlist


## Adding the admin list to the Modules menu

Everything works great thus far, but when you look around the admin area, you'll notice that there's no entry for the admin list yet. So let's remedy this and make sure the admin list will appear in the Modules menu, so people using
the back-end can simply click on a menu item to access the admin list.

To do this, you have to create a service which implements the `Kunstmaan\AdminBundle\Helper\Menu\MenuAdaptorInterface`
interface, and this service should provide an implementation for the `adaptChildren(MenuBuilder $menu, array &amp;$children,
MenuItem $parent = null, Request $request = null)` function.

So, let's first create the necessary folder (by convention we put these in `src/Vendor/WebsiteBundle/Helper/Menu`, but you're free to use your own naming scheme of course) :

```
mkdir -p src/MyProject/WebsiteBundle/Helper/Menu
```

Create a new menu adaptor class file (`ModulesMenuAdaptor.php`) in this folder with the following code :

```php
<?php
namespace MyProject\WebsiteBundle\Helper\Menu;

use Kunstmaan\AdminBundle\Helper\Menu\MenuAdaptorInterface;
use Kunstmaan\AdminBundle\Helper\Menu\MenuBuilder;
use Kunstmaan\AdminBundle\Helper\Menu\MenuItem;
use Kunstmaan\AdminBundle\Helper\Menu\TopMenuItem;
use Symfony\Component\HttpFoundation\Request;

class ModulesMenuAdaptor implements MenuAdaptorInterface
{

    /**
     * {@inheritDoc}
     */
    public function adaptChildren(MenuBuilder $menu, array &$children, MenuItem $parent = null, Request $request = null)
    {
	if (!is_null($parent) && 'KunstmaanAdminBundle_modules' == $parent->getRoute()) {
	    $menuItem = new TopMenuItem($menu);
	    $menuItem->setRoute('MyProjectWebsitebundle_admin_employee');
	    $menuItem->setInternalName('Employee');
	    $menuItem->setParent($parent);
	    if (stripos($request->attributes->get('_route'), $menuItem->getRoute()) === 0) {
		$menuItem->setActive(true);
		$parent->setActive(true);
	    }
	    $children[] = $menuItem;
	}
    }
}
```

The route name used above (`MyProjectWebsitebundle_admin_employee`) should match the route name for the index method of the admin list.

And finally register this service in `src/MyProject/WebsiteBundle/Resources/config/services.yml` by adding the following snippet:

```yml
    MyProjectWebsitebundle.menu.adaptor.modules:
	class: MyProject\WebsiteBundle\Helper\Menu\ModulesMenuAdaptor
	tags:
	    -  { name: 'kunstmaan_admin.menu.adaptor' }
```

If you reload the page in the backend, you should now see a new "Employee" menu item in the Modules menu.