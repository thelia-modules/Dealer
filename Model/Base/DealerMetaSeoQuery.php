<?php

namespace Dealer\Model\Base;

use \Exception;
use \PDO;
use Dealer\Model\DealerMetaSeo as ChildDealerMetaSeo;
use Dealer\Model\DealerMetaSeoI18nQuery as ChildDealerMetaSeoI18nQuery;
use Dealer\Model\DealerMetaSeoQuery as ChildDealerMetaSeoQuery;
use Dealer\Model\Map\DealerMetaSeoTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'dealer_meta_seo' table.
 *
 *
 *
 * @method     ChildDealerMetaSeoQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildDealerMetaSeoQuery orderByDealerId($order = Criteria::ASC) Order by the dealer_id column
 * @method     ChildDealerMetaSeoQuery orderBySlug($order = Criteria::ASC) Order by the slug column
 * @method     ChildDealerMetaSeoQuery orderByJson($order = Criteria::ASC) Order by the json column
 *
 * @method     ChildDealerMetaSeoQuery groupById() Group by the id column
 * @method     ChildDealerMetaSeoQuery groupByDealerId() Group by the dealer_id column
 * @method     ChildDealerMetaSeoQuery groupBySlug() Group by the slug column
 * @method     ChildDealerMetaSeoQuery groupByJson() Group by the json column
 *
 * @method     ChildDealerMetaSeoQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildDealerMetaSeoQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildDealerMetaSeoQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildDealerMetaSeoQuery leftJoinDealer($relationAlias = null) Adds a LEFT JOIN clause to the query using the Dealer relation
 * @method     ChildDealerMetaSeoQuery rightJoinDealer($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Dealer relation
 * @method     ChildDealerMetaSeoQuery innerJoinDealer($relationAlias = null) Adds a INNER JOIN clause to the query using the Dealer relation
 *
 * @method     ChildDealerMetaSeoQuery leftJoinDealerMetaSeoI18n($relationAlias = null) Adds a LEFT JOIN clause to the query using the DealerMetaSeoI18n relation
 * @method     ChildDealerMetaSeoQuery rightJoinDealerMetaSeoI18n($relationAlias = null) Adds a RIGHT JOIN clause to the query using the DealerMetaSeoI18n relation
 * @method     ChildDealerMetaSeoQuery innerJoinDealerMetaSeoI18n($relationAlias = null) Adds a INNER JOIN clause to the query using the DealerMetaSeoI18n relation
 *
 * @method     ChildDealerMetaSeo findOne(ConnectionInterface $con = null) Return the first ChildDealerMetaSeo matching the query
 * @method     ChildDealerMetaSeo findOneOrCreate(ConnectionInterface $con = null) Return the first ChildDealerMetaSeo matching the query, or a new ChildDealerMetaSeo object populated from the query conditions when no match is found
 *
 * @method     ChildDealerMetaSeo findOneById(int $id) Return the first ChildDealerMetaSeo filtered by the id column
 * @method     ChildDealerMetaSeo findOneByDealerId(int $dealer_id) Return the first ChildDealerMetaSeo filtered by the dealer_id column
 * @method     ChildDealerMetaSeo findOneBySlug(string $slug) Return the first ChildDealerMetaSeo filtered by the slug column
 * @method     ChildDealerMetaSeo findOneByJson(string $json) Return the first ChildDealerMetaSeo filtered by the json column
 *
 * @method     array findById(int $id) Return ChildDealerMetaSeo objects filtered by the id column
 * @method     array findByDealerId(int $dealer_id) Return ChildDealerMetaSeo objects filtered by the dealer_id column
 * @method     array findBySlug(string $slug) Return ChildDealerMetaSeo objects filtered by the slug column
 * @method     array findByJson(string $json) Return ChildDealerMetaSeo objects filtered by the json column
 *
 */
abstract class DealerMetaSeoQuery extends ModelCriteria
{

    /**
     * Initializes internal state of \Dealer\Model\Base\DealerMetaSeoQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'thelia', $modelName = '\\Dealer\\Model\\DealerMetaSeo', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildDealerMetaSeoQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildDealerMetaSeoQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \Dealer\Model\DealerMetaSeoQuery) {
            return $criteria;
        }
        $query = new \Dealer\Model\DealerMetaSeoQuery();
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
     * @return ChildDealerMetaSeo|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = DealerMetaSeoTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(DealerMetaSeoTableMap::DATABASE_NAME);
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
     * @return   ChildDealerMetaSeo A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, DEALER_ID, SLUG, JSON FROM dealer_meta_seo WHERE ID = :p0';
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
            $obj = new ChildDealerMetaSeo();
            $obj->hydrate($row);
            DealerMetaSeoTableMap::addInstanceToPool($obj, (string) $key);
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
     * @return ChildDealerMetaSeo|array|mixed the result, formatted by the current formatter
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
     * @return ChildDealerMetaSeoQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(DealerMetaSeoTableMap::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildDealerMetaSeoQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(DealerMetaSeoTableMap::ID, $keys, Criteria::IN);
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
     * @return ChildDealerMetaSeoQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(DealerMetaSeoTableMap::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(DealerMetaSeoTableMap::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DealerMetaSeoTableMap::ID, $id, $comparison);
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
     * @return ChildDealerMetaSeoQuery The current query, for fluid interface
     */
    public function filterByDealerId($dealerId = null, $comparison = null)
    {
        if (is_array($dealerId)) {
            $useMinMax = false;
            if (isset($dealerId['min'])) {
                $this->addUsingAlias(DealerMetaSeoTableMap::DEALER_ID, $dealerId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dealerId['max'])) {
                $this->addUsingAlias(DealerMetaSeoTableMap::DEALER_ID, $dealerId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DealerMetaSeoTableMap::DEALER_ID, $dealerId, $comparison);
    }

    /**
     * Filter the query on the slug column
     *
     * Example usage:
     * <code>
     * $query->filterBySlug('fooValue');   // WHERE slug = 'fooValue'
     * $query->filterBySlug('%fooValue%'); // WHERE slug LIKE '%fooValue%'
     * </code>
     *
     * @param     string $slug The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDealerMetaSeoQuery The current query, for fluid interface
     */
    public function filterBySlug($slug = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($slug)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $slug)) {
                $slug = str_replace('*', '%', $slug);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(DealerMetaSeoTableMap::SLUG, $slug, $comparison);
    }

    /**
     * Filter the query on the json column
     *
     * Example usage:
     * <code>
     * $query->filterByJson('fooValue');   // WHERE json = 'fooValue'
     * $query->filterByJson('%fooValue%'); // WHERE json LIKE '%fooValue%'
     * </code>
     *
     * @param     string $json The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDealerMetaSeoQuery The current query, for fluid interface
     */
    public function filterByJson($json = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($json)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $json)) {
                $json = str_replace('*', '%', $json);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(DealerMetaSeoTableMap::JSON, $json, $comparison);
    }

    /**
     * Filter the query by a related \Dealer\Model\Dealer object
     *
     * @param \Dealer\Model\Dealer|ObjectCollection $dealer The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDealerMetaSeoQuery The current query, for fluid interface
     */
    public function filterByDealer($dealer, $comparison = null)
    {
        if ($dealer instanceof \Dealer\Model\Dealer) {
            return $this
                ->addUsingAlias(DealerMetaSeoTableMap::DEALER_ID, $dealer->getId(), $comparison);
        } elseif ($dealer instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(DealerMetaSeoTableMap::DEALER_ID, $dealer->toKeyValue('PrimaryKey', 'Id'), $comparison);
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
     * @return ChildDealerMetaSeoQuery The current query, for fluid interface
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
     * Filter the query by a related \Dealer\Model\DealerMetaSeoI18n object
     *
     * @param \Dealer\Model\DealerMetaSeoI18n|ObjectCollection $dealerMetaSeoI18n  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDealerMetaSeoQuery The current query, for fluid interface
     */
    public function filterByDealerMetaSeoI18n($dealerMetaSeoI18n, $comparison = null)
    {
        if ($dealerMetaSeoI18n instanceof \Dealer\Model\DealerMetaSeoI18n) {
            return $this
                ->addUsingAlias(DealerMetaSeoTableMap::ID, $dealerMetaSeoI18n->getId(), $comparison);
        } elseif ($dealerMetaSeoI18n instanceof ObjectCollection) {
            return $this
                ->useDealerMetaSeoI18nQuery()
                ->filterByPrimaryKeys($dealerMetaSeoI18n->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByDealerMetaSeoI18n() only accepts arguments of type \Dealer\Model\DealerMetaSeoI18n or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the DealerMetaSeoI18n relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildDealerMetaSeoQuery The current query, for fluid interface
     */
    public function joinDealerMetaSeoI18n($relationAlias = null, $joinType = 'LEFT JOIN')
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('DealerMetaSeoI18n');

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
            $this->addJoinObject($join, 'DealerMetaSeoI18n');
        }

        return $this;
    }

    /**
     * Use the DealerMetaSeoI18n relation DealerMetaSeoI18n object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Dealer\Model\DealerMetaSeoI18nQuery A secondary query class using the current class as primary query
     */
    public function useDealerMetaSeoI18nQuery($relationAlias = null, $joinType = 'LEFT JOIN')
    {
        return $this
            ->joinDealerMetaSeoI18n($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'DealerMetaSeoI18n', '\Dealer\Model\DealerMetaSeoI18nQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildDealerMetaSeo $dealerMetaSeo Object to remove from the list of results
     *
     * @return ChildDealerMetaSeoQuery The current query, for fluid interface
     */
    public function prune($dealerMetaSeo = null)
    {
        if ($dealerMetaSeo) {
            $this->addUsingAlias(DealerMetaSeoTableMap::ID, $dealerMetaSeo->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the dealer_meta_seo table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(DealerMetaSeoTableMap::DATABASE_NAME);
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
            DealerMetaSeoTableMap::clearInstancePool();
            DealerMetaSeoTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildDealerMetaSeo or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildDealerMetaSeo object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(DealerMetaSeoTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(DealerMetaSeoTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();


        DealerMetaSeoTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            DealerMetaSeoTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

    // i18n behavior

    /**
     * Adds a JOIN clause to the query using the i18n relation
     *
     * @param     string $locale Locale to use for the join condition, e.g. 'fr_FR'
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'. Defaults to left join.
     *
     * @return    ChildDealerMetaSeoQuery The current query, for fluid interface
     */
    public function joinI18n($locale = 'en_US', $relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $relationName = $relationAlias ? $relationAlias : 'DealerMetaSeoI18n';

        return $this
            ->joinDealerMetaSeoI18n($relationAlias, $joinType)
            ->addJoinCondition($relationName, $relationName . '.Locale = ?', $locale);
    }

    /**
     * Adds a JOIN clause to the query and hydrates the related I18n object.
     * Shortcut for $c->joinI18n($locale)->with()
     *
     * @param     string $locale Locale to use for the join condition, e.g. 'fr_FR'
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'. Defaults to left join.
     *
     * @return    ChildDealerMetaSeoQuery The current query, for fluid interface
     */
    public function joinWithI18n($locale = 'en_US', $joinType = Criteria::LEFT_JOIN)
    {
        $this
            ->joinI18n($locale, null, $joinType)
            ->with('DealerMetaSeoI18n');
        $this->with['DealerMetaSeoI18n']->setIsWithOneToMany(false);

        return $this;
    }

    /**
     * Use the I18n relation query object
     *
     * @see       useQuery()
     *
     * @param     string $locale Locale to use for the join condition, e.g. 'fr_FR'
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'. Defaults to left join.
     *
     * @return    ChildDealerMetaSeoI18nQuery A secondary query class using the current class as primary query
     */
    public function useI18nQuery($locale = 'en_US', $relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinI18n($locale, $relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'DealerMetaSeoI18n', '\Dealer\Model\DealerMetaSeoI18nQuery');
    }

} // DealerMetaSeoQuery
