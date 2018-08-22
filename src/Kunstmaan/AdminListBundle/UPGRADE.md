UPGRADE
=======================

### BC BREAK AbstractAdminListConfigurator

  * The getPathByConvention() method is changed

    The method returns the name of the route in lowercase now. All the names of the routes
    in the controllers have to be lowercase.

UPGRADE FROM 1.x TO 2.x
=======================

### BC BREAK Admin list filters

  * Admin list filters have been moved and split into ORM/DBAL filters.

    The namespace has been changed to Kunstmaan\AdminListBundle\AdminList\Filters\ORM for the ORM filters
    and Kunstmaan\AdminListBundle\AdminList\Filters\DBAL for the DBAL filters.
    And instead of the FilterType suffix in the class names, the now just have a Filter suffix.

  * Instead of inheriting from AbstractAdminListConfigurator, you will have to inherit from the ORM or DBAL specific
    implementation (AbstractDoctrineORMAdminListConfigurator).

UPGRADE FROM 1.1 TO 1.2
=======================

### BC BREAK AbstractAdminListConfigurator

  * There is a new abstract method here called 'getIndexUrlFor()'

    All AdminList configurations inheriting directly from this class must implement that method.
    It is used to redirect the user back to a list overview
    after an entity action has taken place (edit/delete/...)
