## Extending the admin User entity

From now on, it is possible to use your own admin User entity that will be used in the admin interface. This comes in handy when you want to store some extra information about the administators (eg. first name, last name, department, phone number, ...).

To use this new feature, update the Kunstmaan Bundles to the latest version, and make some minor code changes like in the example below.

In config/kunstmaan_admin.yml add:


```
kunstmaan_admin:
    authentication:
        user_class: App\Entity\User

parameters:
    kunstmaan_user_management.user_admin_list_configurator.class: App\AdminList\UserAdminListConfigurator
```
Create your own User class that extends the BaseUser class:

```php
<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\AdminBundle\Entity\BaseUser;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User entity
 *
 * @ORM\Entity()
 * @ORM\Table(name="your_prefix_users")
 */
class User extends BaseUser
{
    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @ORM\Column(name="first_name", type="string", length=100)
     */
    private $firstName;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @ORM\Column(name="last_name", type="string", length=100)
     */
    private $lastName;

    /**
     * @param string $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }


    /**
     * Get the classname of the formtype.
     *
     * @return string
     */
    public function getFormTypeClass()
    {
        return 'App\Form\UserType';
    }

    /**
     * Get the classname of the admin list configurator.
     *
     * @return string
     */
    public function getAdminListConfiguratorClass()
    {
        return 'App\AdminList\UserAdminListConfigurator';
    }
}
```

Create your own UserType class:

```php
<?php

namespace  App\Form;

use Kunstmaan\AdminBundle\Form\UserType as AdminUserType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * UserType defines the form used for {@link User}
 */
class UserType extends AdminUserType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('firstName', 'text', array(
            'required' => true
        ));
        $builder->add('lastName', 'text', array(
            'required' => true
        ));
        parent::buildForm($builder, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'company_your_user';
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\User',
        ));
    }
}
```

Create your own AdminListConfigurator class:


```php
<?php

namespace App\AdminList;

use Kunstmaan\UserManagementBundle\AdminList\UserAdminListConfigurator as ParentAdminListConfigurator;

/**
 * User admin list configurator
 */
class UserAdminListConfigurator extends ParentAdminListConfigurator
{
    /**
     * Get entity name
     *
     * @return string
     */
    public function getEntityName()
    {
        return 'User';
    }

    /**
     * Get bundle name
     *
     * @return string
     */
    public function getBundleName()
    {
        return 'App';
    }
}
```

After you made these code changes, you have to update your database to create the new user table and change some foreign keys:


```
bin/console doctrine:schema:update --force
or
bin/console doctrine:migrations:diff && bin/console doctrine:migrations:migrate
```

That's it! You can now add/edit the new admin user objects in the backend (Admin → Settings → Users).
