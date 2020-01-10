# PageAdminList

## Create your own AdminList based on an existing page

### Manually

Below you will find a how-to how to create an admin list based on existing page type. 
You will need to create 2 classes. A pageAdminListConfigurator and a pageAdminstListController. Let's assume you allready created a page type named 'project'.

#### Classes

##### Configurator


Create your ProjectPageAdminListConfigurator class in the AdminList folder in your Bundle and extend from the AbstractPageAdminListConfigurator. Override the 3 abstract methods :
getBundleName, getEntityName and getReadableName

```PHP
<?php

namespace YourProject\WebsiteBundle\AdminList;

use Kunstmaan\AdminListBundle\AdminList\Configurator\AbstractPageAdminListConfigurator;

/**
 * The admin list configurator for Project
 */
class ProjectPageAdminListConfigurator extends AbstractPageAdminListConfigurator
{

    /**
     * Get bundle name
     *
     * @return string
     */
    public function getBundleName()
    {
        return 'YourProjectWebsiteBundle';
    }

    /**
     * Get entity name
     *
     * @return string
     */
    public function getEntityName()
    {
        return 'ProjectPage';
    }


    /**
     *  Get readable name
     *
     * @return string
     */
    public function getReadableName()
    {
        return 'Project page';
    }


}

```


##### Controller

The controller will allow you to list yourpage. 

Create your ProjectPageAdminListController in your Controller folder and let it extend from a the AdminListController. Only the indexAction is used here.

```PHP
<?php

namespace YourProject\WebsiteBundle\Controller;

use YourProject\WebsiteBundle\AdminList\ProjectPageAdminListConfigurator;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AdminListConfiguratorInterface;
use Kunstmaan\AdminListBundle\Controller\AdminListController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * The admin list controller for ProjectPage
 */
class ProjectPageAdminListController extends AdminListController
{
    /**
     * @var AdminListConfiguratorInterface
     */
    private $configurator;

    /**
     * @return AdminListConfiguratorInterface
     */
    private function getAdminListConfigurator(Request $request)
    {

        if (!isset($this->configurator)) {
            $this->configurator = new ProjectPageAdminListConfigurator($this->getEntityManager(),$request->getLocale());
        }

        return $this->configurator;
    }

    /**
     * The index action
     *
     * @Route("/", name="yourprojectwebsitebundle_admin_projectpage")
     */
    public function indexAction(Request $request)
    {
        return parent::doIndexAction($this->getAdminListConfigurator($request), $request);
    }

}

```

#### Routing

Add the following lines to your routing.yml.

```YAML
YourBundle_documents:
    resource: '@YourBundle/Controller/ProjectPageAdminListController.php'
    type:     annotation
    prefix:   /{_locale}/admin/projectpage/
    requirements:
         _locale: "%requiredlocales%"
         
```

### Hiding the sidebar

To hide the sidebar in the node edit view let the page entity implement the HideSidebarInNodeEditInterface interface

```PHP

namespace YourProject\WebsiteBundle\Entity\Pages;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\NodeBundle\Entity\HideSidebarInNodeEditInterface;
use Kunstmaan\PagePartBundle\Helper\HasPageTemplateInterface;

/**
 * ProjectPage
 *
 * @ORM\Table(name="yourproject_websitebundle_project_pages")
 * @ORM\Entity
 */
class ProjectPage extends \Kunstmaan\NodeBundle\Entity\AbstractPage
    implements HasPageTemplateInterface ,HideSidebarInNodeEditInterface 
{
    

```

### Back to overview link

To add a link to go back to the overview implement the OverviewNavigationInterface and use the getOverViewRoute method to return the index link.


```PHP
<?php

namespace YourProject\WebsiteBundle\Entity\Pages;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\AdminListBundle\Entity\OverviewNavigationInterface;
use Kunstmaan\NodeBundle\Entity\HideSidebarInNodeEditInterface;
use Kunstmaan\PagePartBundle\Helper\HasPageTemplateInterface;

/**
 * ProjectPage
 *
 * @ORM\Table(name="yourproject_websitebundle_project_pages")
 * @ORM\Entity
 */
class ProjectPage extends \Kunstmaan\NodeBundle\Entity\AbstractPage
    implements HasPageTemplateInterface ,HideSidebarInNodeEditInterface, OverviewNavigationInterface
{


    public function getOverViewRoute(){
        return 'yourprojectwebsitebundle_admin_projectpage';
    }


```

