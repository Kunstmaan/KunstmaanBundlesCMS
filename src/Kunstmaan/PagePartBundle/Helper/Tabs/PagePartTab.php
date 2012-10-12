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

    /**
     * @param string                            $title                     The title
     * @param HasNodeInterface                  $page                      The page
     * @param EntityManager                     $em                        The entity manager
     * @param AbstractPagePartAdminConfigurator $pagePartAdminConfigurator The page part admin configurator
     * @param FormFactoryInterface              $formFactory               The form factory
     * @param PagePartAdminFactory              $pagePartAdminFactory      The page part admin factory
     * @param array                             $types                     The types
     * @param array                             $data                      The data for the types
     */
    function __construct($title, HasNodeInterface $page, EntityManager $em, AbstractPagePartAdminConfigurator $pagePartAdminConfigurator, FormFactoryInterface $formFactory, PagePartAdminFactory $pagePartAdminFactory, array $types = array(), array $data = array())
    {
        parent::__construct($title, $types, $data);

        $this->page = $page;
        $this->em = $em;
        $this->formFactory = $formFactory;
        $this->pagePartAdminConfigurator = $pagePartAdminConfigurator;
        $this->pagePartAdminFactory = $pagePartAdminFactory;

        $this->pagePartAdmin = $pagePartAdminFactory->createList($pagePartAdminConfigurator, $em, $page, null);
    }

    /**
     * @param FormBuilderInterface $builder The form builder
     * @param Request              $request The request
     */
    public function buildForm(FormBuilderInterface $builder, Request $request)
    {
        parent::buildForm($builder, $request);

        $this->pagePartAdmin->preBindRequest($request);
        $this->pagePartAdmin->adaptForm($builder, $this->formFactory);
    }

    /**
     * @param Request $request
     */
    public function bindRequest(Request $request)
    {
        $this->pagePartAdmin->bindRequest($request);

        if ($editPagePart = $request->get('edit') && isset($editPagePart)) {
            $request->getSession()->set('editpagepart', $editPagePart);
        }
    }

    /**
     * @param EntityManager $em      The entity manager
     * @param Request       $request The request
     */
    public function persist(EntityManager $em, Request $request)
    {
        $this->pagePartAdmin->postBindRequest($request);
    }

    /**
     * @param FormView $formView
     *
     * @return array
     */
    public function getFormErrors(FormView $formView)
    {
        $errors = parent::getFormErrors($formView);

        $formTools = new FormToolsExtension(); // @todo keep this? move to helper class
        return array_merge($errors, $formTools->getErrorMessages($formView->vars['pagepartadmin_' . $this->pagePartAdmin->getContext()]));
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return 'KunstmaanPagePartBundle:Tabs:pagepart_tab.html.twig';
    }

    /**
     * @return PagePartAdmin
     */
    public function getPagePartAdmin()
    {
        return $this->pagePartAdmin;
    }

}
