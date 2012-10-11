<?php

namespace Kunstmaan\PagePartBundle\Tabs;

use Doctrine\ORM\EntityManager;

use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;

use Kunstmaan\NodeBundle\Tabs\Tab;
use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Kunstmaan\PagePartBundle\PagePartAdmin\PagePartAdmin;
use Kunstmaan\PagePartBundle\PagePartAdmin\PagePartAdminFactory;
use Kunstmaan\PagePartBundle\PagePartAdmin\AbstractPagePartAdminConfigurator;
use Kunstmaan\AdminBundle\Twig\Extension\FormToolsExtension;

class PagePartTab extends Tab
{

    /**
     * @var AbstractPagePartAdminConfigurator
     */
    protected $pagePartAdminConfigurator;

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var PagePartAdminFactory
     */
    protected $pagePartAdminFactory;

    /**
     * @var PagePartAdmin
     */
    protected $pagePartAdmin;

    /**
     * @var HasNodeInterface
     */
    protected $page;

    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;


    function __construct($title, $page, EntityManager $em, AbstractPagePartAdminConfigurator $pagePartAdminConfigurator, FormFactoryInterface $formFactory, PagePartAdminFactory $pagePartAdminFactory, array $types = array(), array $data = array())
    {
        parent::__construct($title, $types, $data);

        $this->page = $page;
        $this->em = $em;
        $this->formFactory = $formFactory;
        $this->pagePartAdminConfigurator = $pagePartAdminConfigurator;
        $this->pagePartAdminFactory = $pagePartAdminFactory;

        $this->pagePartAdmin = $pagePartAdminFactory->createList($pagePartAdminConfigurator, $em, $page, null);
    }

    public function buildForm(FormBuilderInterface $builder, Request $request)
    {
        parent::buildForm($builder, $request);

        $this->pagePartAdmin->preBindRequest($request);
        $this->pagePartAdmin->adaptForm($builder, $this->formFactory);
    }

    public function bindRequest(Request $request)
    {
        $this->pagePartAdmin->bindRequest($request);
    }

    public function persist(EntityManager $em, Request $request)
    {
        $this->pagePartAdmin->postBindRequest($request);
    }

    public function getFormErrors(FormView $formView)
    {
        $errors = parent::getFormErrors($formView);

        $formTools = new FormToolsExtension(); // @todo keep this? move to helper class
        return array_merge($errors, $formTools->getErrorMessages($formView->vars['pagepartadmin_' . $this->pagePartAdmin->getContext()]));
    }

}
