<?php

namespace Dealer\Model\Base;

use \Exception;
use \PDO;
use Dealer\Model\DealerContent as ChildDealerContent;
use Dealer\Model\DealerContentQuery as ChildDealerContentQuery;
use Dealer\Model\Map\DealerContentTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;
use Thelia\Model\Content;

/**
 * Base class that represents a query for the 'dealer_content' table.
 *
 *
 *
 * @method     ChildDealerContentQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildDealerContentQuery orderByDealerId($order = Criteria::ASC) Order by the dealer_id column
 * @method     ChildDealerContentQuery orderByContentId($order = Criteria::ASC) Order by the content_id column
 * @method     ChildDealerContentQuery orderByCreatedAt($order = Criteria::ASC) Order by the created_at column
 * @method     ChildDealerContentQuery orderByUpdatedAt($order = Criteria::ASC) Order by the updated_at column
 *
 * @method     ChildDealerContentQuery groupById() Group by the id column
 * @method     ChildDealerContentQuery groupByDealerId() Group by the dealer_id column
 * @method     ChildDealerContentQuery groupByContentId() Group by the content_id column
 * @method     ChildDealerContentQuery groupByCreatedAt() Group by the created_at column
 * @method     ChildDealerContentQuery groupByUpdatedAt() Group by the updated_at column
 *
 * @method     ChildDealerContentQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildDealerContentQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildDealerContentQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildDealerContentQuery leftJoinDealer($relationAlias = null) Adds a LEFT JOIN clause to the query using the Dealer relation
 * @method     ChildDealerContentQuery rightJoinDealer($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Dealer relation
 * @method     ChildDealerContentQuery innerJoinDealer($relationAlias = null) Adds a INNER JOIN clause to the query using the Dealer relation
 *
 * @method     ChildDealerContentQuery leftJoinContent($relationAlias = null) Adds a LEFT JOIN clause to the query using the Content relation
 * @method     ChildDealerContentQuery rightJoinContent($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Content relation
 * @method     ChildDealerContentQuery innerJoinContent($relationAlias = null) Adds a INNER JOIN clause to the query using the Content relation
 *
 * @method     ChildDealerContent findOne(ConnectionInterface $con = null) Return the first ChildDealerContent matching the query
 * @method     ChildDealerContent findOneOrCreate(ConnectionInterface $con = null) Return the first ChildDealerContent matching the query, or a new ChildDealerContent object populated from the query conditions when no match is found
 *
 * @method     ChildDealerContent findOneById(int $id) Return the first ChildDealerContent filtered by the id column
 * @method     ChildDealerContent findOneByDealerId(int $dealer_id) Return the first ChildDealerContent filtered by the dealer_id column
 * @method     ChildDealerContent findOneByContentId(int $content_id) Return the first ChildDealerContent filtered by the content_id column
 * @method     ChildDealerContent findOneByCreatedAt(string $created_at) Return the first ChildDealerContent filtered by the created_at column
 * @method     ChildDealerContent findOneByUpdatedAt(string $updated_at) Return the first ChildDealerContent filtered by the updated_at column
 *
 * @method     array findById(int $id) Return ChildDealerContent objects filtered by the id column
 * @method     array findByDealerId(int $dealer_id) Return ChildDealerContent objects filtered by the dealer_id column
 * @method     array findByContentId(int $content_id) Return ChildDealerContent objects filtered by the content_id column
 * @method     array findByCreatedAt(string $created_at) Return ChildDealerContent objects filtered by the created_at column
 * @method     array findByUpdatedAt(string $updated_at) Return ChildDealerContent objects filtered by the updated_at column
 *
 */
abstract class DealerContentQuery extends ModelCriteria
{

    /**
     * Initializes internal state of \Dealer\Model\Base\DealerContentQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'thelia', $modelName = '\\Dealer\\Model\\DealerContent', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildDealerContentQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildDealerContentQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \Dealer\Model\DealerContentQuery) {
            return $criteria;
        }
        $query = new \Dealer\Model\DealerContentQuery();
        if (null !== $modelAlias) {
            $query->setModelAlias($modelAlias);
        }
        if ($criteria instanceof Criteria) {
            $query->mergeWith($criteria);
        }

        return $query;
    }

    /**
     * Find object by primary key.
     * Propel uses the instance pool to skip the database if the object exists.
     * Go fast if the query is untouched.
     *
     * <code>
     * $obj  = $c->findPk(12, $con);
     * </code>
     *
     * @param mixed $key Primary key to use for the query
     * @param ConnectionInterface $con an optional connection object
     *
     * @return ChildDealerContent|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = DealerContentTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(DealerContentTableMap::DATABASE_NAME);
        }
        $this->basePreSelect($con);
        if ($this->formatter || $this->modelAlias || $this->with || $this->select
         || $this->selectColumns || $this->asColumns || $this->selectModifiers
         || $this->map || $this->having || $this->joins) {
            return $this->findPkComplex($key, $con);
        } else {
            return $this->findPkSimple($key, $con);
        }
    }

    /**
     * Find object by primary key using raw SQL to go fast.
     * Bypass doSelect() and the object formatter by using generated code.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     ConnectionInterface $con A connection object
     *
     * @return   ChildDealerContent A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, DEALER_ID, CONTENT_ID, CREATED_AT, UPDATED_AT FROM dealer_content WHERE ID = :p0';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key, PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), 0, $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
            $obj = new ChildDealerContent();
            $obj->hydrate($row);
            DealerContentTableMap::addInstanceToPool($obj, (string) $key);
        }
        $stmt->closeCursor();

        return $obj;
    }

    /**
     * Find object by primary key.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     ConnectionInterface $con A connection object
     *
     * @return ChildDealerContent|array|mixed the result, formatted by the current formatter
     */
    protected function findPkComplex($key, $con)
    {
        // As the query uses a PK condition, no limit(1) is necessary.
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $dataFetcher = $criteria
            ->filterByPrimaryKey($key)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->formatOne($dataFetcher);
    }

    /**
     * Find objects by primary key
     * <code>
     * $objs = $c->findPks(array(12, 56, 832), $con);
     * </code>
     * @param     array $keys Primary keys to use for the query
     * @param     ConnectionInterface $con an optional connection object
     *
     * @return ObjectCollection|array|mixed the list of results, formatted by the current formatter
     */
    public function findPks($keys, $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getReadConnection($this->getDbName());
        }
        $this->basePreSelect($con);
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $dataFetcher = $criteria
            ->filterByPrimaryKeys($keys)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->format($dataFetcher);
    }

    /**
     * Filter the query by primary key
     *
     * @param     mixed $key Primary key to use for the query
     *
     * @return ChildDealerContentQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(DealerContentTableMap::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildDealerContentQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(DealerContentTableMap::ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the id column
     *
     * Example usage:
     * <code>
     * $query->filterById(1234); // WHERE id = 1234
     * $query->filterById(array(12, 34)); // WHERE id IN (12, 34)
     * $query->filterById(array('min' => 12)); // WHERE id > 12
     * </code>
     *
     * @param     mixed $id The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDealerContentQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(DealerContentTableMap::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(DealerContentTableMap::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DealerContentTableMap::ID, $id, $comparison);
    }

    /**
     * Filter the query on the dealer_id column
     *
     * Example usage:
     * <code>
     * $query->filterByDealerId(1234); // WHERE dealer_id = 1234
     * $query->filterByDealerId(array(12, 34)); // WHERE dealer_id IN (12, 34)
     * $query->filterByDealerId(array('min' => 12)); // WHERE dealer_id > 12
     * </code>
     *
     * @see       filterByDealer()
     *
     * @param     mixed $dealerId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDealerContentQuery The current query, for fluid interface
     */
    public function filterByDealerId($dealerId = null, $comparison = null)
    {
        if (is_array($dealerId)) {
            $useMinMax = false;
            if (isset($dealerId['min'])) {
                $this->addUsingAlias(DealerContentTableMap::DEALER_ID, $dealerId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dealerId['max'])) {
                $this->addUsingAlias(DealerContentTableMap::DEALER_ID, $dealerId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DealerContentTableMap::DEALER_ID, $dealerId, $comparison);
    }

    /**
     * Filter the query on the content_id column
     *
     * Example usage:
     * <code>
     * $query->filterByContentId(1234); // WHERE content_id = 1234
     * $query->filterByContentId(array(12, 34)); // WHERE content_id IN (12, 34)
     * $query->filterByContentId(array('min' => 12)); // WHERE content_id > 12
     * </code>
     *
     * @see       filterByContent()
     *
     * @param     mixed $contentId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDealerContentQuery The current query, for fluid interface
     */
    public function filterByContentId($contentId = null, $comparison = null)
    {
        if (is_array($contentId)) {
            $useMinMax = false;
            if (isset($contentId['min'])) {
                $this->addUsingAlias(DealerContentTableMap::CONTENT_ID, $contentId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($contentId['max'])) {
                $this->addUsingAlias(DealerContentTableMap::CONTENT_ID, $contentId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DealerContentTableMap::CONTENT_ID, $contentId, $comparison);
    }

    /**
     * Filter the query on the created_at column
     *
     * Example usage:
     * <code>
     * $query->filterByCreatedAt('2011-03-14'); // WHERE created_at = '2011-03-14'
     * $query->filterByCreatedAt('now'); // WHERE created_at = '2011-03-14'
     * $query->filterByCreatedAt(array('max' => 'yesterday')); // WHERE created_at > '2011-03-13'
     * </code>
     *
     * @param     mixed $createdAt The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDealerContentQuery The current query, for fluid interface
     */
    public function filterByCreatedAt($createdAt = null, $comparison = null)
    {
        if (is_array($createdAt)) {
            $useMinMax = false;
            if (isset($createdAt['min'])) {
                $this->addUsingAlias(DealerContentTableMap::CREATED_AT, $createdAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdAt['max'])) {
                $this->addUsingAlias(DealerContentTableMap::CREATED_AT, $createdAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DealerContentTableMap::CREATED_AT, $createdAt, $comparison);
    }

    /**
     * Filter the query on the updated_at column
     *
     * Example usage:
     * <code>
     * $query->filterByUpdatedAt('2011-03-14'); // WHERE updated_at = '2011-03-14'
     * $query->filterByUpdatedAt('now'); // WHERE updated_at = '2011-03-14'
     * $query->filterByUpdatedAt(array('max' => 'yesterday')); // WHERE updated_at > '2011-03-13'
     * </code>
     *
     * @param     mixed $updatedAt The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDealerContentQuery The current query, for fluid interface
     */
    public function filterByUpdatedAt($updatedAt = null, $comparison = null)
    {
        if (is_array($updatedAt)) {
            $useMinMax = false;
            if (isset($updatedAt['min'])) {
                $this->addUsingAlias(DealerContentTableMap::UPDATED_AT, $updatedAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($updatedAt['max'])) {
                $this->addUsingAlias(DealerContentTableMap::UPDATED_AT, $updatedAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DealerContentTableMap::UPDATED_AT, $updatedAt, $comparison);
    }

    /**
     * Filter the query by a related \Dealer\Model\Dealer object
     *
     * @param \Dealer\Model\Dealer|ObjectCollection $dealer The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDealerContentQuery The current query, for fluid interface
     */
    public function filterByDealer($dealer, $comparison = null)
    {
        if ($dealer instanceof \Dealer\Model\Dealer) {
            return $this
                ->addUsingAlias(DealerContentTableMap::DEALER_ID, $dealer->getId(), $comparison);
        } elseif ($dealer instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(DealerContentTableMap::DEALER_ID, $dealer->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByDealer() only accepts arguments of type \Dealer\Model\Dealer or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Dealer relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildDealerContentQuery The current query, for fluid interface
     */
    public function joinDealer($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Dealer');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'Dealer');
        }

        return $this;
    }

    /**
     * Use the Dealer relation Dealer object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Dealer\Model\DealerQuery A secondary query class using the current class as primary query
     */
    public function useDealerQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinDealer($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Dealer', '\Dealer\Model\DealerQuery');
    }

    /**
     * Filter the query by a related \Thelia\Model\Content object
     *
     * @param \Thelia\Model\Content|ObjectCollection $content The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDealerContentQuery The current query, for fluid interface
     */
    public function filterByContent($content, $comparison = null)
    {
        if ($content instanceof \Thelia\Model\Content) {
            return $this
                ->addUsingAlias(DealerContentTableMap::CONTENT_ID, $content->getId(), $comparison);
        } elseif ($content instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(DealerContentTableMap::CONTENT_ID, $content->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByContent() only accepts arguments of type \Thelia\Model\Content or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Content relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildDealerContentQuery The current query, for fluid interface
     */
    public function joinContent($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Content');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'Content');
        }

        return $this;
    }

    /**
     * Use the Content relation Content object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Thelia\Model\ContentQuery A secondary query class using the current class as primary query
     */
    public function useContentQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinContent($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Content', '\Thelia\Model\ContentQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildDealerContent $dealerContent Object to remove from the list of results
     *
     * @return ChildDealerContentQuery The current query, for fluid interface
     */
    public function prune($dealerContent = null)
    {
        if ($dealerContent) {
            $this->addUsingAlias(DealerContentTableMap::ID, $dealerContent->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the dealer_content table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(DealerContentTableMap::DATABASE_NAME);
        }
        $affectedRows = 0; // initialize var to track total num of affected rows
        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            DealerContentTableMap::clearInstancePool();
            DealerContentTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildDealerContent or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildDealerContent object or primary key or array of primary keys
     *              which is used to create the DELETE statement
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *                if supported by native driver or if emulated using Propel.
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
     public function delete(ConnectionInterface $con = null)
     {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(DealerContentTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(DealerContentTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();


        DealerContentTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            DealerContentTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

    // timestampable behavior

    /**
     * Filter by the latest updated
     *
     * @param      int $nbDays Maximum age of the latest update in days
     *
     * @return     ChildDealerContentQuery The current query, for fluid interface
     */
    public function recentlyUpdated($nbDays = 7)
    {
        return $this->addUsingAlias(DealerContentTableMap::UPDATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Filter by the latest created
     *
     * @param      int $nbDays Maximum age of in days
     *
     * @return     ChildDealerContentQuery The current query, for fluid interface
     */
    public function recentlyCreated($nbDays = 7)
    {
        return $this->addUsingAlias(DealerContentTableMap::CREATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by update date desc
     *
     * @return     ChildDealerContentQuery The current query, for fluid interface
     */
    public function lastUpdatedFirst()
    {
        return $this->addDescendingOrderByColumn(DealerContentTableMap::UPDATED_AT);
    }

    /**
     * Order by update date asc
     *
     * @return     ChildDealerContentQuery The current query, for fluid interface
     */
    public function firstUpdatedFirst()
    {
        return $this->addAscendingOrderByColumn(DealerContentTableMap::UPDATED_AT);
    }

    /**
     * Order by create date desc
     *
     * @return     ChildDealerContentQuery The current query, for fluid interface
     */
    public function lastCreatedFirst()
    {
        return $this->addDescendingOrderByColumn(DealerContentTableMap::CREATED_AT);
    }

    /**
     * Order by create date asc
     *
     * @return     ChildDealerContentQuery The current query, for fluid interface
     */
    public function firstCreatedFirst()
    {
        return $this->addAscendingOrderByColumn(DealerContentTableMap::CREATED_AT);
    }

} // DealerContentQuery
