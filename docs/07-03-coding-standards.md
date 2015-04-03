# Coding standards

It's really important to follow the guidelines, it saves lots of times when checking PR's...

For php coding guidelines we use this:

## Naming conventions

* use camelCase for variables *(ex. $numberOfColumns)*
* use underscores in column names *(ex. @ORM\Column(name="number_of_columns"))*
* use camelCase of getters, setters and other functions*(ex.: getNumberOfColumns(),  setNumberOfColumns())*
* foreign keys should be suffixed with _id *(ex.: @ORM\OneToMany(targetEntity="Entity"),  @ORM\JoinColumn(name="entity_id", referencedColumnName="id"))*
* table names should use underscores and be plural *(ex.: some_entities)*
* class names should be camelCase and singular *(ex.: SomeEntity)*
* event listeners should be suffixed with Listener
* controllers should be suffixed with Controller
* service names should be prefixed with a normalised version of the bundle name *(ex. kunstmaan_admin.admin_menu.adaptor.pages)*
* constants should be UPPER_CASE and should use underscores *(ex. UPPER_CASE)*

## Folder structure

This is based on the best practices described in the [Symfony2 Cookbook](http://symfony.com/doc/current/cookbook/bundles/best_practices.html).

* **Command/**: Should contain all the commands
* **Controller/**: Should contain all the controllers
* **Dependency injection/**: Should contain all the service container extensions
* **Event/**: Should contain all the events
* **EventListener/**: Should contain all the listeners
* **Resources/config/**: Should contain all the config files
* **Resources/public/**: Should contain all the web resources like javascript, css, html
* **Resources/translations/**: should contain all the translation files
* **Resources/views/**: Should contain all the twig templates
* **Resources/doc/**: Should contain all the feature documentation files
* **Tests/**: Should contain everything test related
* **AdminList/**: Should contain all the admin list configurations
* **PagePartAdmin/** Should contain all the pagepart configurations
* **Entity/**: Should contain all the doctrine entity classes
* **Repository/**: Should contain all the custom repositories
* **Form/**: Should contain all the form configurations
* **Form/DataTransformer/**: Should contain all the datatransformers
* **DataFixtures/**: Should contain all the datafixtures
* **Twig/**: Should contain all the Twig extensions
* **Helper/**: Should contain all the helper classes (everything that doesn't belong in one of the other folders)

## Documentation

* PHPDoc blocks should be added for all classes, methods and functions (@Param, @return, @throws)
  * use "int" instead of "integer", "bool" instead of "boolean", PagePartRef[] instead of array(PagePartRef)
  * use inline typecasting: /* @var EntityManager $em */ (be sure the "use" statement is also done at the top)
* classes in the PHPDoc blocks should not be fully namespaced, but imported (use) at the top
* @return should be removed, if the function doesn't return anything

## Other

* We'll use keys to identify translatable strings which are prefixed per section, everything will be lowercase *(ex. actions.save)*
* use 4 spaces instead of tabs
* the constructor is always the first method
* use phpcs with the symfony2 coding standard: https://github.com/opensky/Symfony2-coding-standard
* use php mess detector already configured when running phpunit
* use php duplicate code check
* Form Types will be suffixes with Type when they are used in the frontend and AdminType when they are used for the backend