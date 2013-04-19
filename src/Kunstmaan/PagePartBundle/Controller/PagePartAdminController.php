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
        $pageId = $request->get('pageid');
        $pageClassName = $request->get('pageclassname');
        $context = $request->get('context');
        $pagePartClass = $request->get('type');

        $page = $this->em->getRepository($pageClassName)->findOneById($pageId);

        $pagePartConfigurationReader = new PagePartConfigurationReader($this->container->get('kernel'));
        $pagePartAdminConfigurators = $pagePartConfigurationReader->getPagePartAdminConfigurators($page);

        $pagePartAdminConfigurator = null;
        foreach ($pagePartAdminConfigurators as $ppac) {
            if ($context == $ppac->getContext()) {
                $pagePartAdminConfigurator = $ppac;
            }
        }

        $pagePartAdmin = new PagePartAdmin($pagePartAdminConfigurator, $this->em, $page, $context, $this->container);
        $pagePart = new $pagePartClass();

        $formFactory = $this->container->get('form.factory');
        $formBuilder = $formFactory->createBuilder('form');
        $pagePartAdmin->adaptForm($formBuilder);
        $form = $formBuilder->getForm();
        $id = 'newpp_' . time();

        $data = $formBuilder->getData();
        $data['pagepartadmin_' . $id] = $pagePart;
        $formBuilder->add('pagepartadmin_' . $id, $pagePart->getDefaultAdminType());
        $formBuilder->setData($data);
        $form = $formBuilder->getForm();
        $formview = $form->createView();

        return array(
                'id'=> $id,
                'form' => $formview,
                'pagepart' => $pagePart,
                'pagepartadmin' => $pagePartAdmin,
                'editmode'=> true);
    }
}
