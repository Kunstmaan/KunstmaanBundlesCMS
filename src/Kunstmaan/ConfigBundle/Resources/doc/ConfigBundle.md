#  Using the Config bundle


## Installation

This bundle is compatible with all Symfony 3.* releases. More information about installing can be found in this line by line walkthrough of installing Symfony and all our bundles, please refer to the [Getting Started guide](http://bundles.kunstmaan.be/getting-started) and enjoy the full blown experience.

## Usage

This bundle allows you to create a entity that extends the AbstractConfig class.
By doing this you can create a settings form with some custom configuration variables.

### Create entity

Create a new entity with the fields you would like to be configurable.
This can be for example a site emailaddress, the site logo, ...

Change the created entity and let it extend the AbstractConfig class instead of the default AbstractEntity class.

By extending this class you will have to implement some methods from the ConfigurationInterface.


```PHP
    /**
    * This function is optional. Implement it if you would like a other ROLE to access the configuration section.
    **/
    public function getAccessRoles()
    {
        return array('ROLE_ADMIN_');
    }

    /**
     * Returns the form type to use for this configuratble entity.
     *
     * @return string
     */
    public function getDefaultAdminType()
    {
        return new SiteConfigType();
    }

   /**
    * The internal name will be used as unique id for the route etc.
    *
    * Use a name with no spaces but with underscores.
    *
    * @return string
    */
    public function getInternalName()
    {
        return 'siteconfig';
    }

     /**
     * Returns the label for the menu item that will be created.
     *
     * @return string
     */
    public function getLabel()
    {
        return 'Configuration';
    }
```

### Create the form

You can let symfony autogenerate your form for your custom entity. This formtype needs to be returned by the getDefaultAdminType() function.

### Add the created entity to your config.yml file.

```YAML
kunstmaan_config:
    entities:
        - path\toBundle\Entity\CustomEntity
```

### Add the kunstmaan_config routing to your routing.yml

```YAML
kunstmaan_config:
    resource: "@KunstmaanConfigBundle/Resources/config/routing.yml"
    prefix:   /{_locale}/
```
    
### Result

When you browse to "Settings" in the main menu, you will see that there is a new menu item available with the label you have chosen.

### Twig

There is a custom ConfigTwigExtension with the function get_config_by_internal_name.

The function needs the internal name of your entity as parameter.

You can use this function in your twig template to get your configuration variables.