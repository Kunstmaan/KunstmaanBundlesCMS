<?php

namespace Kunstmaan\PagePartBundle\Controller;

use Kunstmaan\PagePartBundle\Helper\PagePartConfigurationReader;
use Kunstmaan\PagePartBundle\PagePartAdmin\PagePartAdmin;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

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
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();

        $pageId = $request->get('pageid');
        $pageClassName = $request->get('pageclassname');
        $context = $request->get('context');
        $pagePartClass = $request->get('type');

        $page = $em->getRepository($pageClassName)->findOneById($pageId);

        $pagePartConfigurationReader = new PagePartConfigurationReader($this->container->get('kernel'));
        $pagePartAdminConfigurators = $pagePartConfigurationReader->getPagePartAdminConfigurators($page);

        $pagePartAdminConfigurator = null;
        foreach ($pagePartAdminConfigurators as $ppac) {
            if ($context == $ppac->getContext()) {
                $pagePartAdminConfigurator = $ppac;
            }
        }

        $pagePartAdmin = new PagePartAdmin($pagePartAdminConfigurator, $em, $page, $context, $this->container);
        $pagePart = new $pagePartClass();

        $formFactory = $this->container->get('form.factory');
        $formBuilder = $formFactory->createBuilder('form');
        $pagePartAdmin->adaptForm($formBuilder);
        $id = 'newpp_' . time();

        $data = $formBuilder->getData();
        $data['pagepartadmin_' . $id] = $pagePart;
        $adminType = $pagePart->getDefaultAdminType();
        if (!is_object($adminType) && is_string($adminType)) {
            $adminType = $this->container->get($adminType);
        }
        $formBuilder->add('pagepartadmin_' . $id, $adminType);
        $formBuilder->setData($data);
        $form = $formBuilder->getForm();
        $formview = $form->createView();

        return array(
            'id'=> $id,
            'form' => $formview,
            'pagepart' => $pagePart,
            'pagepartadmin' => $pagePartAdmin,
            'editmode'=> true
        );
    }
}
