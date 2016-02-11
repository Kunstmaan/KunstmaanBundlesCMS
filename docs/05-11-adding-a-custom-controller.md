# Adding a custum controller to a page

Before CMS version 3.1 we used a the `service()` method in a specific entiry to write any custom controller logic. This was a poor sollution. We descided to refactor this.

## Creating a full controller
### Modify the entity
- Add the `SlugActionInterface` to the entity.
- Add the `getControllerAction` method

```PHP
class MyClassName implements SlugActionInterface
{
	...
	
	getControllerAction()
	{
		return MyBundle:MyController:MyMethod;
	}
}
```

MyBundle should be replaced with your bundle name, MyController with the name of your controller and MyMethod with the name of the method without the 'Action' suffix.

### Create or modify the controller

```php
namespace MyVendor\MyBundle\Controller;

class MyController extends Controller
{
	...

    public function MyMethodAction(Request $request)
    {
		...
		$context['variable'] = $variable;

		$request->attributes->set('_renderContext',$context);
    }
}
```
