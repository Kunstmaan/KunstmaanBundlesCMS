<?php

namespace Kunstmaan\PagePartBundle\Controller;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Util\ClassUtils;
use Kunstmaan\PagePartBundle\Helper\HasPagePartsInterface;
use Kunstmaan\PagePartBundle\Helper\PagePartInterface;
use Kunstmaan\PagePartBundle\PagePartAdmin\PagePartAdmin;
use Kunstmaan\PagePartBundle\PagePartConfigurationReader\PagePartConfigurationReader;
use RuntimeException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controller for the pagepart administration
 */
class PagePartAdminController extends Controller
{
    /** @var ObjectManager $em */
    protected $em;

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
        $this->em = $this->get('doctrine.orm.entity_manager');

        $pageId        = $request->get('pageid');
        $pageClassName = $request->get('pageclassname');
        $context       = $request->get('context');
        $pagePartClass = $request->get('type');
        /** @var HasPagePartsInterface $page */
        $page = $this->em->getRepository($pageClassName)->find($pageId);
        $this->hasPagePartsInterfaceCheck($page, $pageClassName, $pageId);
        $pagePartAdminConfigurator = $this->getPagePartAdminConfigurator($page, $context);
        $pagePartAdmin = new PagePartAdmin($pagePartAdminConfigurator, $this->em, $page, $context, $this->container);
        $pagePart = $this->getPagePart($pagePartClass);
        $id = 'newpp_' . time();
        $formview = $this->getFormView($pagePartAdmin, $pagePart, $id);

        return [
            'id'            => $id,
            'form'          => $formview,
            'pagepart'      => $pagePart,
            'pagepartadmin' => $pagePartAdmin,
            'editmode'      => true
        ];
    }

    /**
     * @param $page
     * @param string $pageClassName
     * @param int $pageId
     */
    private function hasPagePartsInterfaceCheck($page, $pageClassName, $pageId)
    {
        if (false === $page instanceof HasPagePartsInterface) {
            throw new RuntimeException(sprintf('Given page (%s:%d) has no pageparts', $pageClassName, $pageId));
        }
    }

    /**
     * @param $page
     * @param $context
     * @return \Kunstmaan\PagePartBundle\PagePartAdmin\PagePartAdminConfiguratorInterface|null
     */
    private function getPagePartAdminConfigurator($page, $context)
    {
        /** @var PagePartConfigurationReader $pagePartConfigurationReader */
        $pagePartConfigurationReader = $this->container->get('kunstmaan_page_part.page_part_configuration_reader');
        $pagePartAdminConfigurators  = $pagePartConfigurationReader->getPagePartAdminConfigurators($page);

        $pagePartAdminConfigurator = null;
        foreach ($pagePartAdminConfigurators as $ppac) {
            if ($context == $ppac->getContext()) {
                $pagePartAdminConfigurator = $ppac;
            }
        }

        if (is_null($pagePartAdminConfigurator)) {
            throw new RuntimeException(sprintf('No page part admin configurator found for context "%s".', $context));
        }

        return $pagePartAdminConfigurator;
    }

    /**
     * @param $pagePartClass
     * @throws RuntimeException
     *
     * @return PagePartInterface
     */
    private function getPagePart($pagePartClass)
    {
        $pagePart = new $pagePartClass();

        if (false === $pagePart instanceof PagePartInterface) {
            throw new RuntimeException(sprintf(
                'Given pagepart expected to implement PagePartInterface, %s given',
                $pagePartClass
            ));
        }

        return $pagePart;
    }

    /**
     * @param PagePartAdmin $pagePartAdmin
     * @param PagePartInterface $pagePart
     * @param $id
     * @return \Symfony\Component\Form\FormView
     */
    private function getFormView(PagePartAdmin $pagePartAdmin, PagePartInterface $pagePart, $id)
    {
        /** @var FormFactory $formFactory */
        $formFactory = $this->container->get('form.factory');
        $formBuilder = $formFactory->createBuilder(FormType::class);
        $pagePartAdmin->adaptForm($formBuilder);

        $data                         = $formBuilder->getData();
        $data['pagepartadmin_' . $id] = $pagePart;
        $adminType                    = $pagePart->getDefaultAdminType();

        if (is_string($adminType)) {
            $adminType = $this->container->get($adminType);
        }

        $adminTypeFqn = ClassUtils::getClass($adminType);

        $formBuilder->add('pagepartadmin_' . $id, $adminTypeFqn);
        $formBuilder->setData($data);
        $form     = $formBuilder->getForm();
        $formView = $form->createView();
        return $formView;
    }
}
