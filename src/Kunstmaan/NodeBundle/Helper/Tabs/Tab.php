<?php

namespace Kunstmaan\NodeBundle\Tabs;

use Doctrine\ORM\EntityManager;

use Kunstmaan\AdminBundle\Twig\Extension\FormToolsExtension;

use Symfony\Component\Form\FormView;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @todo maybe another name for this?
 */
class Tab implements TabInterface
{

    /**
     * @var string
     */
    protected $title;

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
     * @param string $title The title
     * @param array  $types The types
     * @param array  $data  The data attached to the types
     */
    public function __construct($title, array $types = array(), array $data = array())
    {
        $this->title = $title;
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
     * @param EntityManager $em      The entity manager
     */
    public function persist(EntityManager $em)
    {
        foreach ($this->data as $item) {
            $em->persist($item);
        }
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
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
            $formViews[] = $formView->vars[$name];
        }

        $formTools = new FormToolsExtension(); // @todo keep this? move to helper class
        return $formTools->getErrorMessages($formViews);
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return 'KunstmaanNodeBundle:Tabs:tab.html.twig';
    }

    /**
     * @param string $identifier
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
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @param string       $name
     * @param AbstractType $type
     * @param null         $data
     */
    public function addType($name, AbstractType $type, $data = null)
    {
        $types[$name] = $type;
        $data[$name] = $data;
    }

    /**
     * @return AbstractType[]
     */
    public function getTypes()
    {
        return $this->types;
    }

}
