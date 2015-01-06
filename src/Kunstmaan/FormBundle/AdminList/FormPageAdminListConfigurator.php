<?php

namespace Kunstmaan\FormBundle\AdminList;

use Kunstmaan\AdminBundle\Entity\EntityInterface;
use Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper;
use Kunstmaan\AdminListBundle\AdminList\FilterType\ORM\BooleanFilterType;
use Kunstmaan\AdminListBundle\AdminList\FilterType\ORM\StringFilterType;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AbstractDoctrineORMAdminListConfigurator;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionDefinition;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityManager;

/**
 * Adminlist configuration to list all the form pages
 */
class FormPageAdminListConfigurator extends AbstractDoctrineORMAdminListConfigurator
{

    /**
     * @var string
     */
    protected $permission;

    /**
     * @param EntityManager $em         The entity manager
     * @param AclHelper     $aclHelper  The ACL helper
     * @param string        $permission The permission
     */
    public function __construct(EntityManager $em, AclHelper $aclHelper, $permission)
    {
        parent::__construct($em, $aclHelper);
        $this->setPermissionDefinition(
            new PermissionDefinition(array($permission), 'Kunstmaan\NodeBundle\Entity\Node', 'n')
        );
    }

    /**
     * Configure filters
     */
    public function buildFilters()
    {
        $builder = $this->getFilterBuilder();
        $builder->add('title', new StringFilterType("title"), "Title")
                ->add('online', new BooleanFilterType('online'), 'Online');
    }

    /**
     * Configure the visible columns
     */
    public function buildFields()
    {
        $this->addField("title", "Title", true)
             ->addField("lang", "Language", true)
             ->addField("url", "Form path", true);
    }

    /*
 * Add a view action.
 */
    public function buildItemActions()
    {
        $create_route = function (EntityInterface $item) {
            return array(
                'path'   => 'KunstmaanFormBundle_formsubmissions_list',
                'params' => array('nodeTranslationId' => $item->getId())
            );
        };
        $ia = new \Kunstmaan\AdminListBundle\AdminList\ItemAction\SimpleItemAction($create_route, "eye-open", "View");
        $this->addItemAction($ia);
    }

    /**
     * Return the url to edit the given $item
     *
     * @param mixed $item
     *
     * @return array
     */
    public function getEditUrlFor($item)
    {
        return array(
            'path'   => 'KunstmaanFormBundle_formsubmissions_list',
            'params' => array('nodeTranslationId' => $item->getId())
        );
    }

    /**
     * Return the url to list all the items
     *
     * @return array
     */
    public function getIndexUrl()
    {
        return array('path' => 'KunstmaanFormBundle_formsubmissions');
    }

    /**
     * Configure if it's possible to add new items
     *
     * @return bool
     */
    public function canAdd()
    {
        return false;
    }

    public function canEdit($item)
    {
        return false;
    }

    /**
     * Configure the types of items you can add
     *
     * @param array $params
     *
     * @return array
     */
    public function getAddUrlFor(array $params = array())
    {
        return "";
    }

    /**
     * Configure if it's possible to delete the given $item
     *
     * @param mixed $item
     *
     * @return bool
     */
    public function canDelete($item)
    {
        return false;
    }

    /**
     * Get the delete url for the given $item
     *
     * @param mixed $item
     *
     * @return array
     */
    public function getDeleteUrlFor($item)
    {
        return array();
    }

    /**
     * @return string
     */
    public function getBundleName()
    {
        return 'KunstmaanNodeBundle';
    }

    /**
     * @return string
     */
    public function getEntityName()
    {
        return 'NodeTranslation';
    }

    /**
     * Override controller path (because actions for different entities are defined in a single Settings controller).
     *
     * @return string
     */
    public function getControllerPath()
    {
        return 'KunstmaanFormBundle:FormSubmissions';
    }

    /**
     * @param QueryBuilder $queryBuilder The query builder
     */
    public function adaptQueryBuilder(QueryBuilder $queryBuilder)
    {
        parent::adaptQueryBuilder($queryBuilder);
        $queryBuilder->innerJoin('b.node', 'n', 'WITH', 'b.node = n.id')
            ->andWhere(
                'n.id IN (SELECT m.id FROM Kunstmaan\FormBundle\Entity\FormSubmission s join s.node m)'
            )
            ->addOrderBy('n.sequenceNumber', 'DESC');
    }

}
