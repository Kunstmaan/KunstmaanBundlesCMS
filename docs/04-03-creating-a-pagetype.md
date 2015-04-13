# Creating new page type

There are times when a basic content page does not suffice, because you want to do some extra processing or want to integrate external services, and that's when you'll probably want to add your own custom page types.

## Creating your first custom page type

In the [previous chapter](04-02-creating-an-adminlist.md) we created an admin list of employees, but how about displaying these on the front-end?

That's what this chapter is all about. Custom page types can define pages containing a specific set of page parts or pages that need their own specific rendering or handling.

The first thing to do is to create a skeleton for the page, and again, we've got a generator that does that :

```
app/console kuma:generate:page
```

It will ask for a table prefix name and a page name, which is the PHP class name of the page to generate. Since I want to display my Employees on this page, I'll call it `EmployeesPage`.

Now, the generator will ask for custom fields to add to this page. By default a title and page title field will always be created. As we don't need any extra fields, just press return to continue.

Next you can select the page template to use. We have some predefined page templates (and you can add your own as well of course), but for now just select the number corresponding with the one column page.

Next up is the page part configuration to use. You can use any of the page part configuration files that are defined for the main context. Select the number corresponding with the (default) 'Main' configuration.

For the footer context select the 'Footer' page part configuration and lastly select the HomePage as page type that can have this page as sub-page. This will make sure you can only add the Employees page as a subpage of the homepage
in the page tree.

The basic code skeleton should now be generated, so go ahead and create (and apply) a migration for the database changes :

```
app/console doctrine:migrations:diff
app/console doctrine:migrations:migrate
```

If you head over to the admin area, to the Pages menu and click on the homepage in the tree on the left, you should be able to add the EmployeesPage as a subpage. So just go ahead and do that, using `Employees` as title.

So now you've got your first custom page, which currently still acts like a basic content page - ie. you can add page parts, set menu properties, change permissions, SEO and social settings. But what about the actual custom rendering?

To customise the rendering of the page you'll have to dig into the source code, so open `src/MyProject/WebsiteBundle/Entity/Pages/EmployeesPage.php`

First note the following :

```php
    public function getPossibleChildTypes()
    {
	return array(
	    array(
		'name' => 'EmployeesPage',
		'class'=> 'MyProject\WebsiteBundle\Entity\Pages\EmployeesPage'
	    )
	);
    }
```

The ```getPossibleChildTypes()``` function should return an array returning the page types that can be added as subpage of this page type. For every page type that you want to be able to add as a sub page, you should return an associative
array  with key `name`, containing the label that will be shown in the select box on the page, and `class` which is the PHP class name (with full namespace) for that page type.

Since we don't really need any subpages for our Employees page, we can just return an empty array, so replace it with :

```php
    public function getPossibleChildTypes()
    {
	return array();
    }
```

If you do that and reload the Employees page you created in the backend, you should see the "Add subpage" button disappear.

The next function that needs or attention is `getDefaultView` :

```php
    /**
     * Get the twig view.
     *
     * @return string
     */
    public function getDefaultView()
    {
	return "MyProjectWebsiteBundle:Pages:Common/view.html.twig";
    }
```

As it clearly states, this function returns the name of the Twig view that will be used to render the page. Since we want a custom view, we'll change it into something that suits our needs :

```php
    /**
     * Get the twig view.
     *
     * @return string
     */
    public function getDefaultView()
    {
	return "MyProjectWebsiteBundle:Pages:EmployeesPage/view.html.twig";
    }
```

Since it's easier to start from an existing template, just copy the original one over :

```
mkdir -p src/MyProject/WebsiteBundle/Resources/views/Pages/EmployeesPage
cp src/MyProject/WebsiteBundle/Resources/views/Pages/Common/view.html.twig src/MyProject/WebsiteBundle/Resources/views/Pages/EmployeesPage/view.html.twig
```

Next open up `src/MyProject/WebsiteBundle/Resources/views/Pages/EmployeesPage/view.html.twig`.

By default the contents of this file should match the following :

```php
{% extends 'MyProjectWebsiteBundle:Page:layout.html.twig' %}
{% block content %}
    {{ render_pagetemplate(page) }}
{% endblock %}
```

The content block will be injected in the page layout, and as you can see by default we just render the page template defined for the page instance, by passing the current page reference into the `render_pagetemplate` Twig function.

It would be nice for the user to be able to add some custom page parts (some headers and introductory text) before we display the list of employees, so we'll just make sure to pass the list of employees as a variable to the template and
render it below the page parts.

```php
{% extends 'MyProjectWebsiteBundle:Page:layout.html.twig' %}
{% block content %}
    {{ render_pagetemplate(page) }}
    <ul class="media-list employees">
    {% if employees is defined %}
	{% for employee in employees %}
	<li class="media employee">
	    {% set fullName = employee.firstName ~ ' ' ~ employee.lastName %}
	    {% if employee.picture is not empty %}<img class="media-object" src="{{ asset(employee.picture.url | imagine_filter('employee_thumbnail')) }}" alt="{{ fullName }}" />{% endif %}
	    <div class="media-body">
		<h4 class="media-heading">{{ fullName }}</h4>
	    </div>
	</li>
	{% endfor %}
    {% endif %}
    </ul>
{% endblock %}
```

Next we'll have to pass the employees to the Twig function. To do that we currently have a to implements the SlugActionInterface.

So add the following in `EmployeesPage.php` :

```php
    use Kunstmaan\NodeBundle\Controller\SlugActionInterface;
    
    public function getControllerAction()
    {
        return 'MyProjectWebsiteBundle:Controller:service';
    }
```

And create a new Controller `EmployeesPageController.php` to handle the logic :

```php
    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Symfony\Component\HttpFoundation\Request;
    ...

    public function serviceAction(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $employees = $em->getRepository('MyProjectWebsiteBundle:Employee')->findAll();
        
        $context['employees'] = $employees;
        $request->attributes->set('_renderContext',$context);
    }
```

As you can see we just fetch all employees (using Doctrine), and pass them into the RenderContext (which is passed into Twig, so you'll get the list in your Twig template as the `employees` variable).


## Under the hood

- `src/YourVendor/YourWebsiteBundle/Resources/config/pagetemplates` contains the YML files defining page templates you can use.
- `src/YourVendor/YourWebsiteBundle/Entity/Pages` contains the source code of the page type entities.
- `src/YourVendor/YourWebsiteBundle/Form/Pages` contains the source code of the AdminTypes for your page types (ie. the definition of the page entry form).
- `src/YourVendor/YourWebsiteBundle/Resources/views/Pages` contains the Twig views for your page types (every page type will be stored in a separate folder).