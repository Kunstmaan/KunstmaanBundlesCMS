<?php

namespace Kunstmaan\NodeSearchBundle\Helper\FormWidgets;

use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminBundle\Helper\FormWidgets\FormWidget;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeSearchBundle\Entity\NodeSearch;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;

class SearchFormWidget extends FormWidget
{
    /** @var Node $node */
    private $node;

    /** @var NodeSearch $nodeSearch */
    private $nodeSearch;

    /**
     * @param Node          $node
     * @param EntityManager $em
     */
    public function __construct(Node $node, EntityManager $em)
    {
        $this->node       = $node;
        $this->nodeSearch = $em->getRepository('KunstmaanNodeSearchBundle:NodeSearch')->findOneByNode($this->node);
    }

    /**
     * @param FormBuilderInterface $builder The form builder
     */
    public function buildForm(FormBuilderInterface $builder)
    {
        parent::buildForm($builder);
        $data                = $builder->getData();
        $data['node_search'] = $this->nodeSearch;
        $builder->setData($data);
    }

    /**
     * @param Request $request
     */
    public function bindRequest(Request $request)
    {
        $form                      = $request->request->get('form');
        $this->data['node_search'] = $form['node_search']['boost'];
    }

    /**
     * @param EntityManager $em
     */
    public function persist(EntityManager $em)
    {
        $nodeSearch = $em->getRepository('KunstmaanNodeSearchBundle:NodeSearch')->findOneByNode($this->node);

        if ($this->data['node_search'] !== null) {
            if ($nodeSearch === null) {
                $nodeSearch = new NodeSearch();
                $nodeSearch->setNode($this->node);
            }
            $nodeSearch->setBoost($this->data['node_search']);
            $em->persist($nodeSearch);
        }
    }
}
