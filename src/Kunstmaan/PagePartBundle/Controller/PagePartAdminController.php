<?php

namespace Kunstmaan\PagePartBundle\Controller;

use Kunstmaan\PagePartBundle\Helper\HasPagePartsInterface;
use Kunstmaan\PagePartBundle\Helper\PagePartInterface;
use Kunstmaan\PagePartBundle\PagePartAdmin\PagePartAdmin;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\FormType;
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
        $em = $this->get('doctrine.orm.entity_manager');

        $pageId = $request->get('pageid');
        $pageClassName = $request->get('pageclassname');
        $context = $request->get('context');
        $pagePartClass = $request->get('type');

        /** @var HasPagePartsInterface $page */
        $page = $em->getRepository($pageClassName)->find($pageId);

        if (false === $page instanceof HasPagePartsInterface) {
            throw new \RuntimeException(sprintf('Given page (%s:%d) has no pageparts', $pageClassName, $pageId));
        }

        $pagePartConfigurationReader = $this->container->get('kunstmaan_page_part.page_part_configuration_reader');
        $pagePartAdminConfigurators = $pagePartConfigurationReader->getPagePartAdminConfigurators($page);

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
        /** @var PagePartInterface $pagePart */
        $pagePart = new $pagePartClass();

        if (false === $pagePart instanceof PagePartInterface) {
            throw new \RuntimeException(sprintf(
                'Given pagepart expected to implement PagePartInterface, %s given',
                $pagePartClass
            ));
        }

        $formFactory = $this->container->get('form.factory');
        $formBuilder = $formFactory->createBuilder(FormType::class);
        $pagePartAdmin->adaptForm($formBuilder);
        $id = 'newpp_' . time();

        $data = $formBuilder->getData();
        $data['pagepartadmin_' . $id] = $pagePart;

        $formBuilder->add('pagepartadmin_' . $id, $pagePart->getDefaultAdminType());
        $formBuilder->setData($data);
        $form = $formBuilder->getForm();
        $formview = $form->createView();
        $extended = $this->getParameter('kunstmaan_page_part.extended');

        return [
            'id' => $id,
            'form' => $formview,
            'pagepart' => $pagePart,
            'pagepartadmin' => $pagePartAdmin,
            'page' => $pagePartAdmin->getPage(),
            'editmode' => true,
            'extended' => $extended,
        ];
    }
}
