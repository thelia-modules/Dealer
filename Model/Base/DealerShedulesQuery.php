<?php

namespace Dealer\Model\Base;

use \Exception;
use \PDO;
use Dealer\Model\DealerShedules as ChildDealerShedules;
use Dealer\Model\DealerShedulesQuery as ChildDealerShedulesQuery;
use Dealer\Model\Map\DealerShedulesTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'dealer_shedules' table.
 *
 *
 *
 * @method     ChildDealerShedulesQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildDealerShedulesQuery orderByDealerId($order = Criteria::ASC) Order by the dealer_id column
 * @method     ChildDealerShedulesQuery orderByDay($order = Criteria::ASC) Order by the day column
 * @method     ChildDealerShedulesQuery orderByBegin($order = Criteria::ASC) Order by the begin column
 * @method     ChildDealerShedulesQuery orderByEnd($order = Criteria::ASC) Order by the end column
 * @method     ChildDealerShedulesQuery orderByClosed($order = Criteria::ASC) Order by the closed column
 * @method     ChildDealerShedulesQuery orderByPeriodBegin($order = Criteria::ASC) Order by the period_begin column
 * @method     ChildDealerShedulesQuery orderByPeriodEnd($order = Criteria::ASC) Order by the period_end column
 * @method     ChildDealerShedulesQuery orderByCreatedAt($order = Criteria::ASC) Order by the created_at column
 * @method     ChildDealerShedulesQuery orderByUpdatedAt($order = Criteria::ASC) Order by the updated_at column
 *
 * @method     ChildDealerShedulesQuery groupById() Group by the id column
 * @method     ChildDealerShedulesQuery groupByDealerId() Group by the dealer_id column
 * @method     ChildDealerShedulesQuery groupByDay() Group by the day column
 * @method     ChildDealerShedulesQuery groupByBegin() Group by the begin column
 * @method     ChildDealerShedulesQuery groupByEnd() Group by the end column
 * @method     ChildDealerShedulesQuery groupByClosed() Group by the closed column
 * @method     ChildDealerShedulesQuery groupByPeriodBegin() Group by the period_begin column
 * @method     ChildDealerShedulesQuery groupByPeriodEnd() Group by the period_end column
 * @method     ChildDealerShedulesQuery groupByCreatedAt() Group by the created_at column
 * @method     ChildDealerShedulesQuery groupByUpdatedAt() Group by the updated_at column
 *
 * @method     ChildDealerShedulesQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildDealerShedulesQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildDealerShedulesQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildDealerShedulesQuery leftJoinDealer($relationAlias = null) Adds a LEFT JOIN clause to the query using the Dealer relation
 * @method     ChildDealerShedulesQuery rightJoinDealer($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Dealer relation
 * @method     ChildDealerShedulesQuery innerJoinDealer($relationAlias = null) Adds a INNER JOIN clause to the query using the Dealer relation
 *
 * @method     ChildDealerShedules findOne(ConnectionInterface $con = null) Return the first ChildDealerShedules matching the query
 * @method     ChildDealerShedules findOneOrCreate(ConnectionInterface $con = null) Return the first ChildDealerShedules matching the query, or a new ChildDealerShedules object populated from the query conditions when no match is found
 *
 * @method     ChildDealerShedules findOneById(int $id) Return the first ChildDealerShedules filtered by the id column
 * @method     ChildDealerShedules findOneByDealerId(int $dealer_id) Return the first ChildDealerShedules filtered by the dealer_id column
 * @method     ChildDealerShedules findOneByDay(int $day) Return the first ChildDealerShedules filtered by the day column
 * @method     ChildDealerShedules findOneByBegin(string $begin) Return the first ChildDealerShedules filtered by the begin column
 * @method     ChildDealerShedules findOneByEnd(string $end) Return the first ChildDealerShedules filtered by the end column
 * @method     ChildDealerShedules findOneByClosed(boolean $closed) Return the first ChildDealerShedules filtered by the closed column
 * @method     ChildDealerShedules findOneByPeriodBegin(string $period_begin) Return the first ChildDealerShedules filtered by the period_begin column
 * @method     ChildDealerShedules findOneByPeriodEnd(string $period_end) Return the first ChildDealerShedules filtered by the period_end column
 * @method     ChildDealerShedules findOneByCreatedAt(string $created_at) Return the first ChildDealerShedules filtered by the created_at column
 * @method     ChildDealerShedules findOneByUpdatedAt(string $updated_at) Return the first ChildDealerShedules filtered by the updated_at column
 *
 * @method     array findById(int $id) Return ChildDealerShedules objects filtered by the id column
 * @method     array findByDealerId(int $dealer_id) Return ChildDealerShedules objects filtered by the dealer_id column
 * @method     array findByDay(int $day) Return ChildDealerShedules objects filtered by the day column
 * @method     array findByBegin(string $begin) Return ChildDealerShedules objects filtered by the begin column
 * @method     array findByEnd(string $end) Return ChildDealerShedules objects filtered by the end column
 * @method     array findByClosed(boolean $closed) Return ChildDealerShedules objects filtered by the closed column
 * @method     array findByPeriodBegin(string $period_begin) Return ChildDealerShedules objects filtered by the period_begin column
 * @method     array findByPeriodEnd(string $period_end) Return ChildDealerShedules objects filtered by the period_end column
 * @method     array findByCreatedAt(string $created_at) Return ChildDealerShedules objects filtered by the created_at column
 * @method     array findByUpdatedAt(string $updated_at) Return ChildDealerShedules objects filtered by the updated_at column
 *
 */
abstract class DealerShedulesQuery extends ModelCriteria
{

    /**
     * Initializes internal state of \Dealer\Model\Base\DealerShedulesQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'thelia', $modelName = '\\Dealer\\Model\\DealerShedules', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildDealerShedulesQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildDealerShedulesQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \Dealer\Model\DealerShedulesQuery) {
            return $criteria;
        }
        $query = new \Dealer\Model\DealerShedulesQuery();
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
     * @return ChildDealerShedules|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = DealerShedulesTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(DealerShedulesTableMap::DATABASE_NAME);
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
     * @return   ChildDealerShedules A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, DEALER_ID, DAY, BEGIN, END, CLOSED, PERIOD_BEGIN, PERIOD_END, CREATED_AT, UPDATED_AT FROM dealer_shedules WHERE ID = :p0';
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
            $obj = new ChildDealerShedules();
            $obj->hydrate($row);
            DealerShedulesTableMap::addInstanceToPool($obj, (string) $key);
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
     * @return ChildDealerShedules|array|mixed the result, formatted by the current formatter
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
     * @return ChildDealerShedulesQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(DealerShedulesTableMap::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildDealerShedulesQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(DealerShedulesTableMap::ID, $keys, Criteria::IN);
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
     * @return ChildDealerShedulesQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(DealerShedulesTableMap::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(DealerShedulesTableMap::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DealerShedulesTableMap::ID, $id, $comparison);
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
     * @return ChildDealerShedulesQuery The current query, for fluid interface
     */
    public function filterByDealerId($dealerId = null, $comparison = null)
    {
        if (is_array($dealerId)) {
            $useMinMax = false;
            if (isset($dealerId['min'])) {
                $this->addUsingAlias(DealerShedulesTableMap::DEALER_ID, $dealerId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dealerId['max'])) {
                $this->addUsingAlias(DealerShedulesTableMap::DEALER_ID, $dealerId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DealerShedulesTableMap::DEALER_ID, $dealerId, $comparison);
    }

    /**
     * Filter the query on the day column
     *
     * Example usage:
     * <code>
     * $query->filterByDay(1234); // WHERE day = 1234
     * $query->filterByDay(array(12, 34)); // WHERE day IN (12, 34)
     * $query->filterByDay(array('min' => 12)); // WHERE day > 12
     * </code>
     *
     * @param     mixed $day The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDealerShedulesQuery The current query, for fluid interface
     */
    public function filterByDay($day = null, $comparison = null)
    {
        if (is_array($day)) {
            $useMinMax = false;
            if (isset($day['min'])) {
                $this->addUsingAlias(DealerShedulesTableMap::DAY, $day['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($day['max'])) {
                $this->addUsingAlias(DealerShedulesTableMap::DAY, $day['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DealerShedulesTableMap::DAY, $day, $comparison);
    }

    /**
     * Filter the query on the begin column
     *
     * Example usage:
     * <code>
     * $query->filterByBegin('2011-03-14'); // WHERE begin = '2011-03-14'
     * $query->filterByBegin('now'); // WHERE begin = '2011-03-14'
     * $query->filterByBegin(array('max' => 'yesterday')); // WHERE begin > '2011-03-13'
     * </code>
     *
     * @param     mixed $begin The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDealerShedulesQuery The current query, for fluid interface
     */
    public function filterByBegin($begin = null, $comparison = null)
    {
        if (is_array($begin)) {
            $useMinMax = false;
            if (isset($begin['min'])) {
                $this->addUsingAlias(DealerShedulesTableMap::BEGIN, $begin['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($begin['max'])) {
                $this->addUsingAlias(DealerShedulesTableMap::BEGIN, $begin['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DealerShedulesTableMap::BEGIN, $begin, $comparison);
    }

    /**
     * Filter the query on the end column
     *
     * Example usage:
     * <code>
     * $query->filterByEnd('2011-03-14'); // WHERE end = '2011-03-14'
     * $query->filterByEnd('now'); // WHERE end = '2011-03-14'
     * $query->filterByEnd(array('max' => 'yesterday')); // WHERE end > '2011-03-13'
     * </code>
     *
     * @param     mixed $end The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDealerShedulesQuery The current query, for fluid interface
     */
    public function filterByEnd($end = null, $comparison = null)
    {
        if (is_array($end)) {
            $useMinMax = false;
            if (isset($end['min'])) {
                $this->addUsingAlias(DealerShedulesTableMap::END, $end['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($end['max'])) {
                $this->addUsingAlias(DealerShedulesTableMap::END, $end['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DealerShedulesTableMap::END, $end, $comparison);
    }

    /**
     * Filter the query on the closed column
     *
     * Example usage:
     * <code>
     * $query->filterByClosed(true); // WHERE closed = true
     * $query->filterByClosed('yes'); // WHERE closed = true
     * </code>
     *
     * @param     boolean|string $closed The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDealerShedulesQuery The current query, for fluid interface
     */
    public function filterByClosed($closed = null, $comparison = null)
    {
        if (is_string($closed)) {
            $closed = in_array(strtolower($closed), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(DealerShedulesTableMap::CLOSED, $closed, $comparison);
    }

    /**
     * Filter the query on the period_begin column
     *
     * Example usage:
     * <code>
     * $query->filterByPeriodBegin('2011-03-14'); // WHERE period_begin = '2011-03-14'
     * $query->filterByPeriodBegin('now'); // WHERE period_begin = '2011-03-14'
     * $query->filterByPeriodBegin(array('max' => 'yesterday')); // WHERE period_begin > '2011-03-13'
     * </code>
     *
     * @param     mixed $periodBegin The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDealerShedulesQuery The current query, for fluid interface
     */
    public function filterByPeriodBegin($periodBegin = null, $comparison = null)
    {
        if (is_array($periodBegin)) {
            $useMinMax = false;
            if (isset($periodBegin['min'])) {
                $this->addUsingAlias(DealerShedulesTableMap::PERIOD_BEGIN, $periodBegin['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($periodBegin['max'])) {
                $this->addUsingAlias(DealerShedulesTableMap::PERIOD_BEGIN, $periodBegin['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DealerShedulesTableMap::PERIOD_BEGIN, $periodBegin, $comparison);
    }

    /**
     * Filter the query on the period_end column
     *
     * Example usage:
     * <code>
     * $query->filterByPeriodEnd('2011-03-14'); // WHERE period_end = '2011-03-14'
     * $query->filterByPeriodEnd('now'); // WHERE period_end = '2011-03-14'
     * $query->filterByPeriodEnd(array('max' => 'yesterday')); // WHERE period_end > '2011-03-13'
     * </code>
     *
     * @param     mixed $periodEnd The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDealerShedulesQuery The current query, for fluid interface
     */
    public function filterByPeriodEnd($periodEnd = null, $comparison = null)
    {
        if (is_array($periodEnd)) {
            $useMinMax = false;
            if (isset($periodEnd['min'])) {
                $this->addUsingAlias(DealerShedulesTableMap::PERIOD_END, $periodEnd['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($periodEnd['max'])) {
                $this->addUsingAlias(DealerShedulesTableMap::PERIOD_END, $periodEnd['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DealerShedulesTableMap::PERIOD_END, $periodEnd, $comparison);
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
     * @return ChildDealerShedulesQuery The current query, for fluid interface
     */
    public function filterByCreatedAt($createdAt = null, $comparison = null)
    {
        if (is_array($createdAt)) {
            $useMinMax = false;
            if (isset($createdAt['min'])) {
                $this->addUsingAlias(DealerShedulesTableMap::CREATED_AT, $createdAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdAt['max'])) {
                $this->addUsingAlias(DealerShedulesTableMap::CREATED_AT, $createdAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DealerShedulesTableMap::CREATED_AT, $createdAt, $comparison);
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
     * @return ChildDealerShedulesQuery The current query, for fluid interface
     */
    public function filterByUpdatedAt($updatedAt = null, $comparison = null)
    {
        if (is_array($updatedAt)) {
            $useMinMax = false;
            if (isset($updatedAt['min'])) {
                $this->addUsingAlias(DealerShedulesTableMap::UPDATED_AT, $updatedAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($updatedAt['max'])) {
                $this->addUsingAlias(DealerShedulesTableMap::UPDATED_AT, $updatedAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DealerShedulesTableMap::UPDATED_AT, $updatedAt, $comparison);
    }

    /**
     * Filter the query by a related \Dealer\Model\Dealer object
     *
     * @param \Dealer\Model\Dealer|ObjectCollection $dealer The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDealerShedulesQuery The current query, for fluid interface
     */
    public function filterByDealer($dealer, $comparison = null)
    {
        if ($dealer instanceof \Dealer\Model\Dealer) {
            return $this
                ->addUsingAlias(DealerShedulesTableMap::DEALER_ID, $dealer->getId(), $comparison);
        } elseif ($dealer instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(DealerShedulesTableMap::DEALER_ID, $dealer->toKeyValue('PrimaryKey', 'Id'), $comparison);
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
     * @return ChildDealerShedulesQuery The current query, for fluid interface
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
     * Exclude object from result
     *
     * @param   ChildDealerShedules $dealerShedules Object to remove from the list of results
     *
     * @return ChildDealerShedulesQuery The current query, for fluid interface
     */
    public function prune($dealerShedules = null)
    {
        if ($dealerShedules) {
            $this->addUsingAlias(DealerShedulesTableMap::ID, $dealerShedules->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the dealer_shedules table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(DealerShedulesTableMap::DATABASE_NAME);
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
            DealerShedulesTableMap::clearInstancePool();
            DealerShedulesTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildDealerShedules or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildDealerShedules object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(DealerShedulesTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(DealerShedulesTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();


        DealerShedulesTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            DealerShedulesTableMap::clearRelatedInstancePool();
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
     * @return     ChildDealerShedulesQuery The current query, for fluid interface
     */
    public function recentlyUpdated($nbDays = 7)
    {
        return $this->addUsingAlias(DealerShedulesTableMap::UPDATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Filter by the latest created
     *
     * @param      int $nbDays Maximum age of in days
     *
     * @return     ChildDealerShedulesQuery The current query, for fluid interface
     */
    public function recentlyCreated($nbDays = 7)
    {
        return $this->addUsingAlias(DealerShedulesTableMap::CREATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by update date desc
     *
     * @return     ChildDealerShedulesQuery The current query, for fluid interface
     */
    public function lastUpdatedFirst()
    {
        return $this->addDescendingOrderByColumn(DealerShedulesTableMap::UPDATED_AT);
    }

    /**
     * Order by update date asc
     *
     * @return     ChildDealerShedulesQuery The current query, for fluid interface
     */
    public function firstUpdatedFirst()
    {
        return $this->addAscendingOrderByColumn(DealerShedulesTableMap::UPDATED_AT);
    }

    /**
     * Order by create date desc
     *
     * @return     ChildDealerShedulesQuery The current query, for fluid interface
     */
    public function lastCreatedFirst()
    {
        return $this->addDescendingOrderByColumn(DealerShedulesTableMap::CREATED_AT);
    }

    /**
     * Order by create date asc
     *
     * @return     ChildDealerShedulesQuery The current query, for fluid interface
     */
    public function firstCreatedFirst()
    {
        return $this->addAscendingOrderByColumn(DealerShedulesTableMap::CREATED_AT);
    }

} // DealerShedulesQuery
