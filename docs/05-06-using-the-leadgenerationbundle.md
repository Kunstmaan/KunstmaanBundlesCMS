# KunstmaanLeadGenerationBundle

This bundle makes it easy to create lead generation popups for your website, and lets you configure them so that they 
only appear when you want. To achieve this, you can use the build in rules or create your own custom rules/logic. 

1. USAGE
--------

Including the lead generating logic in html
-------------------------------------------

Include at the bottom of your layout twig file:

    {{ lead_generation_render_js_includes() }}
    {{ lead_generation_render_popups_html() }}
    {{ lead_generation_render_initialize_js() }}
    
If desired, you can replace the `lead_generation_render_js_includes()` with your own logic, or include the javascript 
files in your own mimified javascript file.

*NOTE*: jQuery is required

Create a custom popup
---------------------

### Create a popup entity

For each popup type, you need to create a custom entity class that extends the `AbstractPopup' class.
In this class you should define the controller action that should be executed to render the popup.
It is also possible to add additional fields to the popup entity.

```php
namespace Company\YourBundle\Entity\Popup;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\LeadGenerationBundle\Entity\Popup\AbstractPopup;
use Company\YourBundle\Form\Popup\NewsletterPopupAdminType;

/**
 * @ORM\Entity
 */
class NewsletterPopup extends AbstractPopup
{
    /**
     * {@inheritdoc}
     */
    public function getControllerAction()
    {
        return 'CompanyYourBundle:NewsletterPopup:index';
    }
    
    /**
     * {@inheritdoc}
     */
    public function getAdminType()
    {
        return new NewsLetterPopupAdminType();
    }
}

```

### Create a popup form type

Then you need to create the form type for the custom popup entity.
When you have defined additional fields in the popup class, you also need to override the `buildForm` function.

```php
namespace Company\YourBundle\Form\Popup;

use Kunstmaan\LeadGenerationBundle\Form\Popup\AbstractPopupAdminType;

class NewsletterPopupAdminType extends AbstractPopupAdminType
{
    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'newsletter_popup_type';
    }
}

```

### Create a controller class

Then create the custom controller class that was used in the popup entity above.
You can create the class from scratch, or you can extend on of the abstract controller classes that are included in the KunstmaanLeadGenerationBundle.
In the example below we extend from the `AbstractNewsletterController` and overwrite some logic and templates.

```php
namespace Company\YourBundle\Controller;

use Kunstmaan\LeadGenerationBundle\Controller\AbstractNewsletterController;
use Kunstmaan\LeadGenerationBundle\Entity\Popup\AbstractPopup;
use Symfony\Component\HttpFoundation\Request;

class NewsletterPopupController extends AbstractNewsletterController
{
    public function getIndexTemplate()
    {
        return 'CompanyYourBundle:Popup/Newsletter:index.html.twig';
    }

    public function getFormTemplate()
    {
        return 'CompanyYourBundle:Popup/Newsletter:form.html.twig';
    }

    public function getThanksTemplate()
    {
        return 'CompanyYourBundle:Popup/Newsletter:thanks.html.twig';
    }
    
    /**
     * @param Request $request
     * @param array $data
     * @param AbstractPopup $popup
     */
    public function handleSubscription(Request $request, $data, AbstractPopup $popup)
    {
        // Your subscription logic here
    }
    
    // Extend some more functions if needed
}

```

### Include the controller in your routing config

Include the newly created controller in the `rounting.yml` configuration file of your bundle.

```yml
company_yourbundle_newsletter_popup:
   resource: @CompanyYourBundle/Controller/NewsletterPopupController.php
   type:     annotation
   prefix:   /newsletter-popup/
```

### Configure the new popup

Configure the custom popup thype in the general `config.yml` configuration.
An administrator can only add popups from one of the defined types.

```yml
kunstmaan_lead_generation:
    debug: false
    popup_types:
        - Company\YourBundle\Entity\Popup\NewsletterPopup
```


2. CREATING CUSTOM RULES
-------------------------

### Create a rule entity

Create a custom rule entity that extends the `AbstractRule` entity.
The rule should have the properties (database fields) needed to configure the rule via the admin interface.

```php
namespace Company\YourBundle\Entity\Rule;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\LeadGenerationBundle\Entity\Rule\AbstractRule;
use Symfony\Component\Validator\Constraints as Assert;
use Company\YourBundle\Form\Rule\AfterXSecondsAdminType;

/**
 * @ORM\Entity
 * @ORM\Table(name="prefix_rule_after_x_seconds")
 */
class AfterXSecondsRule extends AbstractRule
{
    /**
     * @var int
     * @ORM\Column(type="integer")
     * @Assert\NotBlank()
     */
    private $seconds;

    /**
     * @return int
     */
    public function getSeconds()
    {
        return $this->seconds;
    }

    /**
     * @param int $seconds
     *
     * @return AfterXSecondsRule
     */
    public function setSeconds($seconds)
    {
        $this->seconds = $seconds;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getJsObjectClass()
    {
        return 'AfterXSecondsRule';
    }

    /**
     * {@inheritdoc}
     */
    public function getJsProperties()
    {
        return array(
            'seconds' => $this->getSeconds()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getJsFilePath()
    {
        return '/bundles/companyyourbundle/js/rule/AfterXSecondsRule.js';
    }
    
    /**
     * @return AfterXSecondsAdminType
     */
    public function getAdminType()
    {
        return new AfterXSecondsAdminType();
    }
}
```

### Create a rule form type

Then you need to create the form type for the custom rule entity.

```php
namespace Company\YourBundle\Form\Rule;

use Symfony\Component\Form\FormBuilderInterface;
use Kunstmaan\LeadGenerationBundle\Form\Rule\AbstractRuleAdminType;

class AfterXSecondsAdminType extends AbstractRuleAdminType
{
    /**
     * Builds the form.
     *
     * This method is called for each type in the hierarchy starting form the
     * top most type. Type extensions can further modify the form.
     *
     * @see FormTypeExtensionInterface::buildForm()
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('seconds', 'integer');
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'after_x_seconds_form';
    }
}
```

### Create the javascript object

The class gets automatically activated, and should throw an event when all the conditions of the rule are met.

```js
'use strict';

(function(window, document, $, undefined) {

    window.kunstmaan = window.kunstmaan || {};
    window.kunstmaan.leadGeneration = window.kunstmaan.leadGeneration || {};
    window.kunstmaan.leadGeneration.rules = window.kunstmaan.leadGeneration.rules || {};

    window.kunstmaan.leadGeneration.rules.AfterXSecondsRule = function(id, properties) {
        var instance = {
            'isMet': false
        };

        var _popup;

        var _ready;

        instance.setPopup = function(popup) {
            _popup = popup;
        };

        instance.activate = function() {
            window.kunstmaan.leadGeneration.log(_popup.name + ": activate AfterXSecondsRule rule " + id);
            window.setTimeout(_ready, properties.seconds * 1000);
        };

        _ready = function() {
            window.kunstmaan.leadGeneration.log(_popup.name + ": condition met for AfterXSecondsRule rule " + id);
            instance.isMet = true;
            document.dispatchEvent(new window.CustomEvent(window.kunstmaan.leadGeneration.events.CONDITIONS_MET, { detail: {popup: _popup.name, rule: id} }));
        };

        return instance;
    };

})(window, document, $);

```

3. MISC
-------

### When a rule needs more properties than can be configured

For example when you needs some information from the database.
Then you can create a custom service (that implements the `RuleServiceInterface`) that can add some properties.

Create the service class.

```php
namespace Company\YourBundle\Service;

use Kunstmaan\LeadGenerationBundle\Entity\AbstractRule;
use Kunstmaan\LeadGenerationBundle\Service\RuleServiceInterface;

class YourService implements RuleServiceInterface
{
    public function getJsProperties(AbstractRule $rule)
    {
        return array('someKey' => 'someValue');
    }
}

```

Configure the service in your `services.yml` file.

```yml
companyyourbundle.your_service:
    class: Company\YourBundle\Service\YourService
```

Add the service reference in the `Rule` entity.

```php
namespace Company\YourBundle\Entity\Rule;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\LeadGenerationBundle\Entity\Rule\AbstractRule;

/**
 * @ORM\Entity
 * @ORM\Table(name="prefix_rule_after_x_seconds")
 */
class AfterXSecondsRule extends AbstractRule
{
    // Otherfunctions here
   
   public function getService()
   {
       return 'companyyourbundle.your_service';
   }
}
```

### Define which rules are available/configurable for a popup

In your popup entity class, you can override the `getAvailableRules` function and return a list op `Rule` classes that should be available.

```php
namespace Company\YourBundle\Entity\Popup;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\LeadGenerationBundle\Entity\Popup\AbstractPopup;

/**
 * @ORM\Entity
 */
class NewsletterPopup extends AbstractPopup
{
    /**
     * {@inheritdoc}
     */
    public function getControllerAction()
    {
        return 'CompanyYourBundle:NewsletterPopup:index';
    }
    
    /**
     * Get a list of available rules for this popup.
     * When null is returned, all rules are available.
     *
     * @return array|null
     */
    public function getAvailableRules()
    {
        return array(
            'Kunstmaan\LeadGenerationBundle\Entity\Rule\UrlWhitelistRule',
            'Kunstmaan\LeadGenerationBundle\Entity\Rule\UrlBlacklistRule'
        );
    }
}

```
