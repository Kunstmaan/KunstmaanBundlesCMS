<?php

namespace Kunstmaan\PagePartBundle\Controller;

use Kunstmaan\PagePartBundle\Helper\PagePartConfigurationReader;
use Kunstmaan\PagePartBundle\PagePartAdmin\PagePartAdmin;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controller for the pagepart administration
 */
class PagePartAdminController extends Controller
{
    /**
     * @Route("/newPagePart", name="KunstmaanPagePartBundle_admin_newpagepart")
     * @Template("KunstmaanPagePartBundle:PagePartAdminTwigExtension:pagepart.html.twig")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array
     */
    public function newPagePartAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $pageId        = $request->get('pageid');
        $pageClassName = $request->get('pageclassname');
        $context       = $request->get('context');
        $pagePartClass = $request->get('type');

        $page = $em->getRepository($pageClassName)->findOneById($pageId);

        $pagePartConfigurationReader = new PagePartConfigurationReader($this->container->get('kernel'));
        $pagePartAdminConfigurators  = $pagePartConfigurationReader->getPagePartAdminConfigurators($page);

        $pagePartAdminConfigurator = null;
        foreach ($pagePartAdminConfigurators as $ppac) {
            if ($context == $ppac->getContext()) {
                $pagePartAdminConfigurator = $ppac;
            }
        }

        if (is_null($pagePartAdminConfigurator)) {
            throw new \RuntimeException(sprintf('No page part admin configurator found for context "%s".', $context));
        }

        $pagePartAdmin = new PagePartAdmin($pagePartAdminConfigurator, $em, $page, $context, $this->container);
        $pagePart      = new $pagePartClass();

        $formFactory = $this->container->get('form.factory');
        $formBuilder = $formFactory->createBuilder('form');
        $pagePartAdmin->adaptForm($formBuilder);
        $id = 'newpp_' . time();

        $data                         = $formBuilder->getData();
        $data['pagepartadmin_' . $id] = $pagePart;
        $adminType                    = $pagePart->getDefaultAdminType();
        if (!is_object($adminType) && is_string($adminType)) {
            $adminType = $this->container->get($adminType);
        }
        $formBuilder->add('pagepartadmin_' . $id, $adminType);
        $formBuilder->setData($data);
        $form     = $formBuilder->getForm();
        $formview = $form->createView();

        return array(
            'id'            => $id,
            'form'          => $formview,
            'pagepart'      => $pagePart,
            'pagepartadmin' => $pagePartAdmin,
            'editmode'      => true
        );
    }
}
