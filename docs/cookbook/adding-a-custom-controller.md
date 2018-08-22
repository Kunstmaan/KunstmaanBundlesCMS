# Adding a custom controller to a page

Before CMS version 3.1 we used a the `service()` method in a specific entity to write any custom controller logic. This was a poor solution. We decided to refactor this.

## Creating a full controller
### Modify the entity
- Add the `SlugActionInterface` to the entity.
- Add the `getControllerAction` method

```PHP
class HomePage implements SlugActionInterface
{
	...
	
	public function getControllerAction()
	{
		return AppBundle:HomePageController:FooMethod;
		
		// or return a service.
		
		return appbundle.controller.home:fooAction;
	}
}
```

### Create or modify the controller

```PHP
namespace App\Bundle\Controller;

class HomePageController extends Controller
{
	...

    /**
     * Return a new Response or just an array with the data for your pagetemplate.
     */
    public function fooAction(Request $request)
    {
        ...
    }
}
```
