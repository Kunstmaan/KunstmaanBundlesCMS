<?php
namespace Kunstmaan\PagePartBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionMap;
use Kunstmaan\PagePartBundle\Entity\PagePartRef;
use Kunstmaan\PagePartBundle\Repository\PagePartRefRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Kunstmaan\NodeBundle\Helper\NodeMenu;
use Kunstmaan\PagePartBundle\PagePartAdmin\PagePartAdmin;
use Symfony\Component\HttpKernel\KernelInterface;
use Kunstmaan\PagePartBundle\Helper\PagePartConfigurationReader;
use Kunstmaan\PagePartBundle\PagePartAdmin\AbstractPagePartAdminConfigurator;

/**
 * Controller for the pagepart administration
 */
class PagePartAdminController extends Controller
{

    /**
     * @Route("/newPagePart", name="KunstmaanPagePartBundle_admin_newpagepart")
     * @Template("KunstmaanPagePartBundle:PagePartAdminTwigExtension:pagepart.html.twig")
     *
     * @return array
     */
    public function newPagePartAction()
    {
        $this->em = $this->getDoctrine()->getManager();
        $this->locale = $this->getRequest()->getLocale();

        $request = $this->getRequest();
        $pageid = $request->get('pageid');
        $pageclassname = $request->get('pageclassname');
        $context = $request->get('context');
        $type = $request->get('type');

        $page = $this->em->getRepository($pageclassname)->findOneById($pageid);

        $pagePartConfigurationReader = new PagePartConfigurationReader($this->container->get('kernel'));
        $pagePartAdminConfigurators = $pagePartConfigurationReader->getPagePartAdminConfigurators($page);

        $pagepartadminconfigurator = null;
        foreach ($pagePartAdminConfigurators as $ppac) {
            if ($context == $ppac->getContext()) {
                $pagepartadminconfigurator = $ppac;
            }
        }

        $pagePartAdmin = new PagePartAdmin($pagepartadminconfigurator, $this->em, $page, $context, $this->container);
        $pagepart = new $type();

        $formFactory = $this->container->get('form.factory');
        $formbuilder = $formFactory->createBuilder('form');
        $pagePartAdmin->adaptForm($formbuilder);
        $form = $formbuilder->getForm();
        $id = 'newpp_' . time();

        $data = $formbuilder->getData();
        $data['pagepartadmin_' . $id] = $pagepart;
        $formbuilder->add('pagepartadmin_' . $id, $pagepart->getDefaultAdminType());
        $formbuilder->setData($data);
        $form = $formbuilder->getForm();
        $formview = $form->createView();

        return array(
                'id'=> $id,
                'form' => $formview,
                'pagepart' => $pagepart,
                'pagepartadmin' => $pagePartAdmin,
                'editmode'=> true);
    }
}
