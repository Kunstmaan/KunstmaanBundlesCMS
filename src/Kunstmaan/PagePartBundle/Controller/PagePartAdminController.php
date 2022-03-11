<?php

namespace Kunstmaan\PagePartBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\PagePartBundle\Helper\HasPagePartsInterface;
use Kunstmaan\PagePartBundle\Helper\PagePartInterface;
use Kunstmaan\PagePartBundle\PagePartAdmin\PagePartAdmin;
use Kunstmaan\PagePartBundle\PagePartConfigurationReader\PagePartConfigurationReaderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class PagePartAdminController extends AbstractController
{
    /** @var EntityManagerInterface */
    private $em;
    /** @var PagePartConfigurationReaderInterface */
    private $pagePartConfigurationReader;
    /** @var FormFactory */
    private $formFactory;
    /** @var ContainerInterface */
    private $fullContainer;

    public function __construct(EntityManagerInterface $em, PagePartConfigurationReaderInterface $pagePartConfigurationReader, FormFactory $formFactory, ContainerInterface $container)
    {
        $this->em = $em;
        $this->pagePartConfigurationReader = $pagePartConfigurationReader;
        $this->formFactory = $formFactory;
        $this->fullContainer = $container;
    }

    /**
     * @Route("/newPagePart", name="KunstmaanPagePartBundle_admin_newpagepart")
     * @Template("@KunstmaanPagePart/PagePartAdminTwigExtension/pagepart.html.twig")
     *
     * @return array
     */
    public function newPagePartAction(Request $request)
    {
        $pageId = $request->get('pageid');
        $pageClassName = $request->get('pageclassname');
        $context = $request->get('context');
        $pagePartClass = $request->get('type');

        /** @var HasPagePartsInterface $page */
        $page = $this->em->getRepository($pageClassName)->find($pageId);

        if (false === $page instanceof HasPagePartsInterface) {
            throw new \RuntimeException(sprintf('Given page (%s:%d) has no pageparts', $pageClassName, $pageId));
        }

        $pagePartAdminConfigurators = $this->pagePartConfigurationReader->getPagePartAdminConfigurators($page);

        $pagePartAdminConfigurator = null;
        foreach ($pagePartAdminConfigurators as $ppac) {
            if ($context == $ppac->getContext()) {
                $pagePartAdminConfigurator = $ppac;
            }
        }

        if (\is_null($pagePartAdminConfigurator)) {
            throw new \RuntimeException(sprintf('No page part admin configurator found for context "%s".', $context));
        }

        $pagePartAdmin = new PagePartAdmin($pagePartAdminConfigurator, $this->em, $page, $context, $this->fullContainer);
        /** @var PagePartInterface $pagePart */
        $pagePart = new $pagePartClass();

        if (false === $pagePart instanceof PagePartInterface) {
            throw new \RuntimeException(sprintf('Given pagepart expected to implement PagePartInterface, %s given', $pagePartClass));
        }

        $formBuilder = $this->formFactory->createBuilder(FormType::class);
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
