<?php

namespace Kunstmaan\NodeBundle\Helper\FormWidgets;

use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminBundle\Helper\FormWidgets\FormWidget;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionAdmin;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionMapInterface;
use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Kunstmaan\NodeBundle\Entity\Node;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;

/**
 * A tab to show permissions
 */
class PermissionsFormWidget extends FormWidget
{
    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var PermissionAdmin
     */
    protected $permissionAdmin;

    /**
     * @var PermissionMapInterface
     */
    protected $permissionMap;

    /**
     * @var HasNodeInterface
     */
    protected $page;

    /**
     * @var Node
     */
    protected $node;

    /**
     * @param HasNodeInterface       $page            The page
     * @param Node                   $node            The node
     * @param PermissionAdmin        $permissionAdmin The permission admin
     * @param PermissionMapInterface $permissionMap   The permission map
     */
    public function __construct(HasNodeInterface $page, Node $node, PermissionAdmin $permissionAdmin, PermissionMapInterface $permissionMap)
    {
        $this->page = $page;
        $this->node = $node;
        $this->permissionAdmin = $permissionAdmin;
        $this->permissionMap = $permissionMap;
    }

    /**
     * @param FormBuilderInterface $builder The form builder
     */
    public function buildForm(FormBuilderInterface $builder)
    {
        $this->permissionAdmin->initialize($this->node, $this->permissionMap);
    }

    /**
     * @param Request $request
     */
    public function bindRequest(Request $request)
    {
        $this->permissionAdmin->bindRequest($request);
    }

    /**
     * @param EntityManager $em
     */
    public function persist(EntityManager $em)
    {
    }

    /**
     * @param FormView $formView
     *
     * @return array
     */
    public function getFormErrors(FormView $formView)
    {
        return array();
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return 'KunstmaanNodeBundle:FormWidgets\PermissionsFormWidget:widget.html.twig';
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
     * @param HasNodeInterface $page
     *
     * @return PermissionTab
     */
    public function setPage($page)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * @return HasNodeInterface
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @param PermissionAdmin $permissionAdmin
     *
     * @return PermissionTab
     */
    public function setPermissionAdmin($permissionAdmin)
    {
        $this->permissionAdmin = $permissionAdmin;

        return $this;
    }

    /**
     * @return PermissionAdmin
     */
    public function getPermissionAdmin()
    {
        return $this->permissionAdmin;
    }

    /**
     * @param PermissionMapInterface $permissionMap
     *
     * @return PermissionTab
     */
    public function setPermissionMap($permissionMap)
    {
        $this->permissionMap = $permissionMap;

        return $this;
    }

    /**
     * @return PermissionMapInterface
     */
    public function getPermissionMap()
    {
        return $this->permissionMap;
    }

    /**
     * @param Node $node
     *
     * @return PermissionsFormWidget
     */
    public function setNode($node)
    {
        $this->node = $node;

        return $this;
    }

    /**
     * @return Node
     */
    public function getNode()
    {
        return $this->node;
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
