<?php

namespace Kunstmaan\AdminListBundle\Helper;

use Doctrine\DBAL\Query\QueryBuilder;
use Pagerfanta\Adapter\AdapterInterface;
use Pagerfanta\Exception\LogicException;

/**
 * DoctrineDBALAdapter.
 *
 * @author Michael Williams <michael@whizdevelopment.com>
 *
 * @api
 */
class DoctrineDBALAdapter implements AdapterInterface
{
    private $queryBuilder;

    private $countField;

    private $useDistinct;

    /**
     * Constructor.
     *
     * @param QueryBuilder $queryBuilder a DBAL query builder
     * @param string       $countField   Primary key for the table in query. Used in count expression. Must include table alias
     * @param bool         $useDistinct  when set to true it'll count the countfield with a distinct in front of it
     *
     * @api
     */
    public function __construct(QueryBuilder $queryBuilder, $countField, $useDistinct = true)
    {
        if (strpos($countField, '.') === false) {
            throw new LogicException('The $countField must contain a table alias in the string.');
        }

        if (QueryBuilder::SELECT !== $queryBuilder->getType()) {
            throw new LogicException('Only SELECT queries can be paginated.');
        }

        $this->queryBuilder = $queryBuilder;
        $this->countField = $countField;

        $this->useDistinct = $useDistinct;
    }

    /**
     * Returns the query builder.
     *
     * @return QueryBuilder the query builder
     *
     * @api
     */
    public function getQueryBuilder()
    {
        return $this->queryBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getNbResults()
    {
        $query = clone $this->queryBuilder;
        $distinctString = '';
        if ($this->useDistinct) {
            $distinctString = 'DISTINCT ';
        }
        $statement = $query->select('COUNT('. $distinctString . $this->countField.') AS total_results')
            ->orderBy($this->countField)
            ->setMaxResults(1)
            ->execute();

        return ($results = $statement->fetchColumn(0)) ? $results : 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getSlice($offset, $length)
    {
        $query = clone $this->queryBuilder;

        $result = $query->setMaxResults($length)
            ->setFirstResult($offset)
            ->execute();

        return $result->fetchAll();
    }
}
