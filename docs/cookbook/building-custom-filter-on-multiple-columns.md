# Building a custom admin list filter that works on multiple columns

## The problem

We have an admin list that displays information from a specific entity. It optionally contains a reference
to another entity, and both of them contain a name field (well, actually the name is split up in a
firstName and lastName field in the related entity, but not on the main one - this is external data that
only provided one field, don't we all love this :p).

Our client asked us if we could create one filter for both of the name fields (so if either one has a
match, display it as a result in the filtered admin list).

## The solution



First we needed a solution to be able to concatenate mutliple fields using the Doctrine ORM (I know, I
could have opted for native queries, but I needed an extra challenge :p). I had a look around and decided
to use the excellent [beberlei/DoctrineExtensions](https://github.com/beberlei/DoctrineExtensions).

That's one down, but we still have to create the actual filter.

For the filter I decided to use the same approach as usual, ie. attach it to a main field, but there
should also be a way to add other columns and column expressions to the filter.

This results in the following basic functionality :

```php
class MultiColumnStringFilterType extends AbstractORMFilterType
{
    private $fields = array();

    /**
     * @param string $columnName The column name
     * @param string $alias      The alias
     */
    public function __construct($columnName, $alias = 'b')
    {
        parent::__construct($columnName, $alias);

        $this->fields[] = $this->getAlias() . $this->columnName;
    }

    /**
     * @param string $columnName
     * @param string $alias
     */
    public function addColumn($columnName, $alias = 'b')
    {
        $this->addFieldExpression($alias . '.' . $columnName);
    }

    /**
     * @param string $expression
     */
    public function addFieldExpression($expression)
    {
        if (!in_array($expression, $this->fields)) {
            $this->fields[] = $expression;
        }
    }
}
```



So, the filter has a fields property that will store all fields (or field expressions) that will be used
in the filter. By default it will just add the main field that you attach the filter to. But you can add
extra columns by providing either a column (including alias if it's a related entity) or a field expression
using the **addColumn** or **addFieldExpression** methods respectively.

Now for the difficult part, the actual filter step. I first created a generic field expression builder
method, that would apply a DQL template to all of the fields and OR them all together. The source is
below :

```php
/**
 * @param \Doctrine\ORM\Query\Expr $expr
 * @param string                   $dqlTemplate
 *
 * @return \Doctrine\ORM\Query\Expr\Orx
 */
private function getFieldExpression(Expr $expr, $dqlTemplate)
{
    $expressions = array();
    foreach ($this->fields as $field) {
        $expressions[] = sprintf($dqlTemplate, $field);
    }

    return call_user_func_array(array($expr, 'orX'), $expressions);
}
```



As you can see, I just feed it with the Doctrine ORM expression builder and the DQL template that should be applied.

It will loop over every field or field expression in the fields collection, apply these to the DQL template and store
the result in the $expressions array. Then I need to pass these into the orX method of the Doctrine ORM expression
builder. This method assumes you pass all parameters separately, that's why I had to use
[call_user_func_array](http://php.net/manual/en/function.call-user-func-array.php) to feed the array as separate
parameters into the **orX** method of the expression builder.

The rest was the easy part, I just needed to make sure that for every possible comparator on the StringFilterType I
could generate the matching filter, and apply it to the queryBuilder.

The resulting MultiColumnStringFilterType class can be seen below :

```php
<?php

namespace Kunstmaan\CustomBundle\AdminList\FilterType\ORM;

use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use Kunstmaan\AdminListBundle\AdminList\FilterType\ORM\AbstractORMFilterType;
use Symfony\Component\HttpFoundation\Request;

class MultiColumnStringFilterType extends AbstractORMFilterType
{
    private $fields = array();

    /**
     * @param string $columnName The column name
     * @param string $alias      The alias
     */
    public function __construct($columnName, $alias = 'b')
    {
        parent::__construct($columnName, $alias);

        $this->fields[] = $this->getAlias() . $this->columnName;
    }

    /**
     * @param Request $request  The request
     * @param array   &$data    The data
     * @param string  $uniqueId The unique identifier
     */
    public function bindRequest(Request $request, array &$data, $uniqueId)
    {
        $data['comparator'] = $request->query->get('filter_comparator_' . $uniqueId);
        $data['value']      = $request->query->get('filter_value_' . $uniqueId);
    }

    /**
     * @param array  $data     The data
     * @param string $uniqueId The unique identifier
     */
    public function apply(array $data, $uniqueId)
    {
        if (isset($data['value']) && isset($data['comparator'])) {
            switch ($data['comparator']) {
                case 'equals':
                    $this->queryBuilder->andWhere(
                        $this->getFieldExpression($this->queryBuilder->expr(), '%s = :var_' . $uniqueId)
                    );
                    $this->queryBuilder->setParameter('var_' . $uniqueId, $data['value']);
                    break;
                case 'notequals':
                    $this->queryBuilder->andWhere(
                        $this->getFieldExpression($this->queryBuilder->expr(), '%s <> :var_' . $uniqueId)
                    );
                    $this->queryBuilder->setParameter('var_' . $uniqueId, $data['value']);
                    break;
                case 'contains':
                    $this->queryBuilder->andWhere(
                        $this->getFieldExpression($this->queryBuilder->expr(), '%s LIKE :var_' . $uniqueId)
                    );
                    $this->queryBuilder->setParameter('var_' . $uniqueId, '%' . $data['value'] . '%');
                    break;
                case 'doesnotcontain':
                    $this->queryBuilder->andWhere(
                        $this->getFieldExpression($this->queryBuilder->expr(), '%s NOT LIKE :var_' . $uniqueId)
                    );
                    $this->queryBuilder->setParameter('var_' . $uniqueId, '%' . $data['value'] . '%');
                    break;
                case 'startswith':
                    $this->queryBuilder->andWhere(
                        $this->getFieldExpression($this->queryBuilder->expr(), '%s LIKE :var_' . $uniqueId)
                    );
                    $this->queryBuilder->setParameter('var_' . $uniqueId, $data['value'] . '%');
                    break;
                case 'endswith':
                    $this->queryBuilder->andWhere(
                        $this->getFieldExpression($this->queryBuilder->expr(), '%s LIKE :var_' . $uniqueId)
                    );
                    $this->queryBuilder->setParameter('var_' . $uniqueId, '%' . $data['value']);
                    break;
            }
        }
    }

    /**
     * @param \Doctrine\ORM\Query\Expr $expr
     * @param string                   $dqlTemplate
     *
     * @return \Doctrine\ORM\Query\Expr\Orx
     */
    private function getFieldExpression(Expr $expr, $dqlTemplate)
    {
        $expressions = array();
        foreach ($this->fields as $field) {
            $expressions[] = sprintf($dqlTemplate, $field);
        }

        return call_user_func_array(array($expr, 'orX'), $expressions);
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return 'KunstmaanAdminListBundle:FilterType:stringFilter.html.twig';
    }

    /**
     * @param string $columnName
     * @param string $alias
     */
    public function addColumn($columnName, $alias = 'b')
    {
        $this->addFieldExpression($alias . '.' . $columnName);
    }

    /**
     * @param string $expression
     */
    public function addFieldExpression($expression)
    {
        if (!in_array($expression, $this->fields)) {
            $this->fields[] = $expression;
        }
    }
}
```

Then it was time for the final step, applying this filter in the admin list configurator, to do that, I just had
to add the following snippet to the AdminListConfigurator :

```php
/**
 * Build filters for admin list
 */
public function buildFilters()
{
    ...
    $nameFilter = new MultiColumnStringFilterType('name');
    $nameFilter->addFieldExpression('CONCAT_WS(\' \', o.firstName, o.lastName)');
    $this->addFilter('name', $nameFilter, 'Name');
    ...
}
```

And that's it, the filter should act just like the normal StringFilterType, but do much more.

Hope you found this useful.
