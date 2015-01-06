<?php

namespace Kunstmaan\PagePartBundle\Helper\FormWidgets;

use Doctrine\ORM\EntityManager;

use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Kunstmaan\AdminBundle\Helper\FormWidgets\FormWidget;
use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Kunstmaan\PagePartBundle\PagePartAdmin\PagePartAdmin;
use Kunstmaan\PagePartBundle\PagePartAdmin\PagePartAdminFactory;
use Kunstmaan\PagePartBundle\PagePartAdmin\AbstractPagePartAdminConfigurator;
use Kunstmaan\PagePartBundle\Helper\HasPagePartsInterface;

/**
 * PagePartWidget
 */
class PagePartWidget extends FormWidget
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
     * @var Request
     */
    protected $request;

    /**
     * @param HasNodeInterface                  $page                      The page
     * @param Request                           $request                   The request
     * @param EntityManager                     $em                        The entity manager
     * @param AbstractPagePartAdminConfigurator $pagePartAdminConfigurator The page part admin configurator
     * @param FormFactoryInterface              $formFactory               The form factory
     * @param PagePartAdminFactory              $pagePartAdminFactory      The page part admin factory
     */
    public function __construct(HasPagePartsInterface $page, Request $request, EntityManager $em, AbstractPagePartAdminConfigurator $pagePartAdminConfigurator, FormFactoryInterface $formFactory, PagePartAdminFactory $pagePartAdminFactory)
    {
        parent::__construct();

        $this->page = $page;
        $this->em = $em;
        $this->formFactory = $formFactory;
        $this->pagePartAdminConfigurator = $pagePartAdminConfigurator;
        $this->pagePartAdminFactory = $pagePartAdminFactory;
        $this->request = $request;

        $this->pagePartAdmin = $pagePartAdminFactory->createList($pagePartAdminConfigurator, $em, $page, null);
    }

    /**
     * @param FormBuilderInterface $builder The form builder
     */
    public function buildForm(FormBuilderInterface $builder)
    {
        parent::buildForm($builder);

        $this->pagePartAdmin->preBindRequest($this->request);
        $this->pagePartAdmin->adaptForm($builder, $this->formFactory);
    }

    /**
     * @param Request $request
     */
    public function bindRequest(Request $request)
    {
        parent::bindRequest($request);

        $this->pagePartAdmin->bindRequest($request);
    }

    /**
     * @param EntityManager $em The entity manager
     */
    public function persist(EntityManager $em)
    {
        parent::persist($em);

        $this->pagePartAdmin->persist($this->request);
    }

    /**
     * @param FormView $formView
     *
     * @return array
     */
    public function getFormErrors(FormView $formView)
    {
        $errors = parent::getFormErrors($formView);

        $formHelper = $this->getFormHelper();

        if (isset($formView['pagepartadmin_' . $this->pagePartAdmin->getContext()])) {
            $errors = array_merge($errors, $formHelper->getRecursiveErrorMessages($formView['pagepartadmin_' . $this->pagePartAdmin->getContext()]));
        }

        return $errors;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return 'KunstmaanPagePartBundle:FormWidgets\PagePartWidget:widget.html.twig';
    }

    /**
     * @return PagePartAdmin
     */
    public function getPagePartAdmin()
    {
        return $this->pagePartAdmin;
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    public function getExtraParams(Request $request)
    {
        $params = array();
        $editPagePart = $request->get('edit');
        if (isset($editPagePart)) {
            $params['editpagepart'] = $editPagePart;
        }

        return $params;
    }

}
