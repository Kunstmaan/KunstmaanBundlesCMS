<?php

namespace Kunstmaan\AdminBundle\Helper\FormWidgets;

use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminBundle\Helper\FormHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;

/**
 * The default tab implementation
 */
class FormWidget implements FormWidgetInterface
{

    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var AbstractType[]
     */
    protected $types;

    /**
     * @var array
     */
    protected $data;

    /**
     * @var FormHelper
     */
    private $formHelper = null;

    /**
     * @param array $types The types
     * @param array $data  The data attached to the types
     */
    public function __construct(array $types = array(), array $data = array())
    {
        $this->types = $types;
        $this->data = $data;
    }

    /**
     * @param FormBuilderInterface $builder The form builder
     */
    public function buildForm(FormBuilderInterface $builder)
    {
        $data = $builder->getData();

        foreach ($this->types as $name => $type) {
            $builder->add($name, $type);
            $data[$name] = $this->data[$name];
        }

        $builder->setData($data);
    }

    /**
     * @param Request $request
     */
    public function bindRequest(Request $request)
    {

    }

    /**
     * @param EntityManager $em
     */
    public function persist(EntityManager $em)
    {
        foreach ($this->data as $item) {
            $em->persist($item);
        }
    }

    /**
     * @param FormView $formView
     *
     * @return array
     */
    public function getFormErrors(FormView $formView)
    {
        $formViews = array();
        foreach ($this->types as $name => $type) {
            $formViews[] = $formView[$name];
        }

        $formHelper = $this->getFormHelper();

        return $formHelper->getRecursiveErrorMessages($formViews);
    }

    /**
     * @return FormHelper
     */
    protected function getFormHelper()
    {
        if (is_null($this->formHelper)) {
            $this->formHelper = new FormHelper();
        }

        return $this->formHelper;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return 'KunstmaanAdminBundle:FormWidgets\FormWidget:widget.html.twig';
    }

    /**
     * @param string $identifier
     *
     * @return TabInterface
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @param string       $name
     * @param AbstractType $type
     * @param null         $data
     *
     * @return FormWidget
     */
    public function addType($name, AbstractType $type, $data = null)
    {
        $this->types[$name] = $type;
        $this->data[$name] = $data;

        return $this;
    }

    /**
     * @return AbstractType[]
     */
    public function getTypes()
    {
        return $this->types;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    public function getExtraParams(Request $request)
    {
        return array();
    }
}
