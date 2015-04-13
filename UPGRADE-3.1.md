# UPGRADE FROM 3.0 to 3.1

## Manual upgrade needed in generated adminlist Twig templates for the ArticleBundle

When using the Article generator, it will create a list.html.twig template in your website bundle. Due to the upgrade to Bootstrap 3, these templates need to be updated, unfortunately by hand.

Change the ```extra_actions_header``` block to match the one from the GeneratorBundle found in:

```
vendor/kunstmaan/bundles-cms/src/Kunstmaan/GeneratorBundle/Resources/SensioGeneratorBundle/skeleton/article/Resources/views/PageAdminList/list.html.twig
```

Replace all variables to match your original version

## Deprecated the service method in Pages in favour of controller methods

You should remove the service method in your entities and replace them by implementing the SlugActionInterface. Add the method getControllerAction and make it return a callable string.

```php
    public function getControllerAction()
    {
	return 'Bundle:Controller:service';
    }
```

In your controller just add the serviceAction method, let it handle the logic and set the renderContext in the request attributes.

```php
    public function serviceAction(Request $request)
    {
	..logic..
	$context['variable'] = $variable;

	$request->attributes->set('_renderContext',$context);
    }

```

## Additional tables and routing for the SeoBundle

There is an additional table for the SeoBundle to provide the content of the robots.txt. Create it manually

```
CREATE TABLE kuma_robots (id BIGINT AUTO_INCREMENT NOT NULL, robots_txt LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB
```

or create and execute a migration

```
app/console doctrine:migrations:diff
app/console doctrine:migrations:migrate
```

Add the folowing into `app/config/routing.yml`

```yml
# KunstmaanSeoBundle
KunstmaanSeoBundle:
    resource: "@KunstmaanSeoBundle/Resources/config/routing.yml"
    prefix:   /{_locale}/
    requirements:
	_locale: %requiredlocales%

Robots_txt:
    resource: "@KunstmaanSeoBundle/Controller/RobotsController.php"
    type:     annotation
```


## Changed index names

There were several changes in the naming of database indexes to support SQLite3. You should generate a migration and run it on all your databases (dev/production).

```
app/console doctrine:migrations:diff
app/console doctrine:migrations:migrate
```

## The minimum PHP version for this release is 5.4

Since PHP 5.3 has been out of maintenance for a long time now, you shouldn't have any issues with this. In case you are forced to host on PHP 5.3, you should stay on the 3.0 releases. We will try to backport security fixes for a while.

## The chosen plugin has been replaced by select2

You need to change the "class" property in the attr property. An example:

```php
	$builder->add('categories', 'entity', array(
	   'class' => 'KunstmaanWebsiteBundle:Category',
	   'multiple' => true,
	   'expanded' => false,
	   'attr' => array(
	       'class' => 'chzn-select',
	       'data-placeholder' => 'Choose the related categories'
	   ),
	   'required' => false
       ));
```

by

```php
	$builder->add('categories', 'entity', array(
	   'class' => 'KunstmaanWebsiteBundle:Category',
	   'multiple' => true,
	   'expanded' => false,
	   'attr' => array(
	       'class' => 'js-advanced-select',
	       'data-placeholder' => 'Choose the related categories'
	   ),
	   'required' => false
       ));
```
