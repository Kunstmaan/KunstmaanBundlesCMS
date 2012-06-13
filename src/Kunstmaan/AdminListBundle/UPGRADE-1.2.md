UPGRADE FROM 1.1 TO 1.2
=======================

### BC BREAK AbstractAdminListConfigurator

  * There is a new abstract method here called 'getIndexUrlFor()'

    All AdminList configurations inheriting directly from this class must implement that method.
    It is used to redirect the user back to a list overview
    after an entity action has taken place (edit/delete/...)