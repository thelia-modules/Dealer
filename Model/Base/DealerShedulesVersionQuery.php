<?php

namespace Dealer\Model\Base;

use \Exception;
use \PDO;
use Dealer\Model\DealerShedulesVersion as ChildDealerShedulesVersion;
use Dealer\Model\DealerShedulesVersionQuery as ChildDealerShedulesVersionQuery;
use Dealer\Model\Map\DealerShedulesVersionTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'dealer_shedules_version' table.
 *
 *
 *
 * @method     ChildDealerShedulesVersionQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildDealerShedulesVersionQuery orderByDealerId($order = Criteria::ASC) Order by the dealer_id column
 * @method     ChildDealerShedulesVersionQuery orderByDay($order = Criteria::ASC) Order by the day column
 * @method     ChildDealerShedulesVersionQuery orderByBegin($order = Criteria::ASC) Order by the begin column
 * @method     ChildDealerShedulesVersionQuery orderByEnd($order = Criteria::ASC) Order by the end column
 * @method     ChildDealerShedulesVersionQuery orderByPeriodBegin($order = Criteria::ASC) Order by the period_begin column
 * @method     ChildDealerShedulesVersionQuery orderByPeriodEnd($order = Criteria::ASC) Order by the period_end column
 * @method     ChildDealerShedulesVersionQuery orderByCreatedAt($order = Criteria::ASC) Order by the created_at column
 * @method     ChildDealerShedulesVersionQuery orderByUpdatedAt($order = Criteria::ASC) Order by the updated_at column
 * @method     ChildDealerShedulesVersionQuery orderByVersion($order = Criteria::ASC) Order by the version column
 * @method     ChildDealerShedulesVersionQuery orderByVersionCreatedAt($order = Criteria::ASC) Order by the version_created_at column
 * @method     ChildDealerShedulesVersionQuery orderByVersionCreatedBy($order = Criteria::ASC) Order by the version_created_by column
 * @method     ChildDealerShedulesVersionQuery orderByDealerIdVersion($order = Criteria::ASC) Order by the dealer_id_version column
 *
 * @method     ChildDealerShedulesVersionQuery groupById() Group by the id column
 * @method     ChildDealerShedulesVersionQuery groupByDealerId() Group by the dealer_id column
 * @method     ChildDealerShedulesVersionQuery groupByDay() Group by the day column
 * @method     ChildDealerShedulesVersionQuery groupByBegin() Group by the begin column
 * @method     ChildDealerShedulesVersionQuery groupByEnd() Group by the end column
 * @method     ChildDealerShedulesVersionQuery groupByPeriodBegin() Group by the period_begin column
 * @method     ChildDealerShedulesVersionQuery groupByPeriodEnd() Group by the period_end column
 * @method     ChildDealerShedulesVersionQuery groupByCreatedAt() Group by the created_at column
 * @method     ChildDealerShedulesVersionQuery groupByUpdatedAt() Group by the updated_at column
 * @method     ChildDealerShedulesVersionQuery groupByVersion() Group by the version column
 * @method     ChildDealerShedulesVersionQuery groupByVersionCreatedAt() Group by the version_created_at column
 * @method     ChildDealerShedulesVersionQuery groupByVersionCreatedBy() Group by the version_created_by column
 * @method     ChildDealerShedulesVersionQuery groupByDealerIdVersion() Group by the dealer_id_version column
 *
 * @method     ChildDealerShedulesVersionQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildDealerShedulesVersionQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildDealerShedulesVersionQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildDealerShedulesVersionQuery leftJoinDealerShedules($relationAlias = null) Adds a LEFT JOIN clause to the query using the DealerShedules relation
 * @method     ChildDealerShedulesVersionQuery rightJoinDealerShedules($relationAlias = null) Adds a RIGHT JOIN clause to the query using the DealerShedules relation
 * @method     ChildDealerShedulesVersionQuery innerJoinDealerShedules($relationAlias = null) Adds a INNER JOIN clause to the query using the DealerShedules relation
 *
 * @method     ChildDealerShedulesVersion findOne(ConnectionInterface $con = null) Return the first ChildDealerShedulesVersion matching the query
 * @method     ChildDealerShedulesVersion findOneOrCreate(ConnectionInterface $con = null) Return the first ChildDealerShedulesVersion matching the query, or a new ChildDealerShedulesVersion object populated from the query conditions when no match is found
 *
 * @method     ChildDealerShedulesVersion findOneById(int $id) Return the first ChildDealerShedulesVersion filtered by the id column
 * @method     ChildDealerShedulesVersion findOneByDealerId(int $dealer_id) Return the first ChildDealerShedulesVersion filtered by the dealer_id column
 * @method     ChildDealerShedulesVersion findOneByDay(int $day) Return the first ChildDealerShedulesVersion filtered by the day column
 * @method     ChildDealerShedulesVersion findOneByBegin(string $begin) Return the first ChildDealerShedulesVersion filtered by the begin column
 * @method     ChildDealerShedulesVersion findOneByEnd(string $end) Return the first ChildDealerShedulesVersion filtered by the end column
 * @method     ChildDealerShedulesVersion findOneByPeriodBegin(string $period_begin) Return the first ChildDealerShedulesVersion filtered by the period_begin column
 * @method     ChildDealerShedulesVersion findOneByPeriodEnd(string $period_end) Return the first ChildDealerShedulesVersion filtered by the period_end column
 * @method     ChildDealerShedulesVersion findOneByCreatedAt(string $created_at) Return the first ChildDealerShedulesVersion filtered by the created_at column
 * @method     ChildDealerShedulesVersion findOneByUpdatedAt(string $updated_at) Return the first ChildDealerShedulesVersion filtered by the updated_at column
 * @method     ChildDealerShedulesVersion findOneByVersion(int $version) Return the first ChildDealerShedulesVersion filtered by the version column
 * @method     ChildDealerShedulesVersion findOneByVersionCreatedAt(string $version_created_at) Return the first ChildDealerShedulesVersion filtered by the version_created_at column
 * @method     ChildDealerShedulesVersion findOneByVersionCreatedBy(string $version_created_by) Return the first ChildDealerShedulesVersion filtered by the version_created_by column
 * @method     ChildDealerShedulesVersion findOneByDealerIdVersion(int $dealer_id_version) Return the first ChildDealerShedulesVersion filtered by the dealer_id_version column
 *
 * @method     array findById(int $id) Return ChildDealerShedulesVersion objects filtered by the id column
 * @method     array findByDealerId(int $dealer_id) Return ChildDealerShedulesVersion objects filtered by the dealer_id column
 * @method     array findByDay(int $day) Return ChildDealerShedulesVersion objects filtered by the day column
 * @method     array findByBegin(string $begin) Return ChildDealerShedulesVersion objects filtered by the begin column
 * @method     array findByEnd(string $end) Return ChildDealerShedulesVersion objects filtered by the end column
 * @method     array findByPeriodBegin(string $period_begin) Return ChildDealerShedulesVersion objects filtered by the period_begin column
 * @method     array findByPeriodEnd(string $period_end) Return ChildDealerShedulesVersion objects filtered by the period_end column
 * @method     array findByCreatedAt(string $created_at) Return ChildDealerShedulesVersion objects filtered by the created_at column
 * @method     array findByUpdatedAt(string $updated_at) Return ChildDealerShedulesVersion objects filtered by the updated_at column
 * @method     array findByVersion(int $version) Return ChildDealerShedulesVersion objects filtered by the version column
 * @method     array findByVersionCreatedAt(string $version_created_at) Return ChildDealerShedulesVersion objects filtered by the version_created_at column
 * @method     array findByVersionCreatedBy(string $version_created_by) Return ChildDealerShedulesVersion objects filtered by the version_created_by column
 * @method     array findByDealerIdVersion(int $dealer_id_version) Return ChildDealerShedulesVersion objects filtered by the dealer_id_version column
 *
 */
abstract class DealerShedulesVersionQuery extends ModelCriteria
{

    /**
     * Initializes internal state of \Dealer\Model\Base\DealerShedulesVersionQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'thelia', $modelName = '\\Dealer\\Model\\DealerShedulesVersion', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildDealerShedulesVersionQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildDealerShedulesVersionQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \Dealer\Model\DealerShedulesVersionQuery) {
            return $criteria;
        }
        $query = new \Dealer\Model\DealerShedulesVersionQuery();
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
     * $obj = $c->findPk(array(12, 34), $con);
     * </code>
     *
     * @param array[$id, $version] $key Primary key to use for the query
     * @param ConnectionInterface $con an optional connection object
     *
     * @return ChildDealerShedulesVersion|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = DealerShedulesVersionTableMap::getInstanceFromPool(serialize(array((string) $key[0], (string) $key[1]))))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(DealerShedulesVersionTableMap::DATABASE_NAME);
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
     * @return   ChildDealerShedulesVersion A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, DEALER_ID, DAY, BEGIN, END, PERIOD_BEGIN, PERIOD_END, CREATED_AT, UPDATED_AT, VERSION, VERSION_CREATED_AT, VERSION_CREATED_BY, DEALER_ID_VERSION FROM dealer_shedules_version WHERE ID = :p0 AND VERSION = :p1';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key[0], PDO::PARAM_INT);
            $stmt->bindValue(':p1', $key[1], PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), 0, $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
            $obj = new ChildDealerShedulesVersion();
            $obj->hydrate($row);
            DealerShedulesVersionTableMap::addInstanceToPool($obj, serialize(array((string) $key[0], (string) $key[1])));
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
     * @return ChildDealerShedulesVersion|array|mixed the result, formatted by the current formatter
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
     * $objs = $c->findPks(array(array(12, 56), array(832, 123), array(123, 456)), $con);
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
     * @return ChildDealerShedulesVersionQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {
        $this->addUsingAlias(DealerShedulesVersionTableMap::ID, $key[0], Criteria::EQUAL);
        $this->addUsingAlias(DealerShedulesVersionTableMap::VERSION, $key[1], Criteria::EQUAL);

        return $this;
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildDealerShedulesVersionQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {
        if (empty($keys)) {
            return $this->add(null, '1<>1', Criteria::CUSTOM);
        }
        foreach ($keys as $key) {
            $cton0 = $this->getNewCriterion(DealerShedulesVersionTableMap::ID, $key[0], Criteria::EQUAL);
            $cton1 = $this->getNewCriterion(DealerShedulesVersionTableMap::VERSION, $key[1], Criteria::EQUAL);
            $cton0->addAnd($cton1);
            $this->addOr($cton0);
        }

        return $this;
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
     * @see       filterByDealerShedules()
     *
     * @param     mixed $id The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDealerShedulesVersionQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(DealerShedulesVersionTableMap::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(DealerShedulesVersionTableMap::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DealerShedulesVersionTableMap::ID, $id, $comparison);
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
     * @param     mixed $dealerId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDealerShedulesVersionQuery The current query, for fluid interface
     */
    public function filterByDealerId($dealerId = null, $comparison = null)
    {
        if (is_array($dealerId)) {
            $useMinMax = false;
            if (isset($dealerId['min'])) {
                $this->addUsingAlias(DealerShedulesVersionTableMap::DEALER_ID, $dealerId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dealerId['max'])) {
                $this->addUsingAlias(DealerShedulesVersionTableMap::DEALER_ID, $dealerId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DealerShedulesVersionTableMap::DEALER_ID, $dealerId, $comparison);
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
     * @return ChildDealerShedulesVersionQuery The current query, for fluid interface
     */
    public function filterByDay($day = null, $comparison = null)
    {
        if (is_array($day)) {
            $useMinMax = false;
            if (isset($day['min'])) {
                $this->addUsingAlias(DealerShedulesVersionTableMap::DAY, $day['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($day['max'])) {
                $this->addUsingAlias(DealerShedulesVersionTableMap::DAY, $day['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DealerShedulesVersionTableMap::DAY, $day, $comparison);
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
     * @return ChildDealerShedulesVersionQuery The current query, for fluid interface
     */
    public function filterByBegin($begin = null, $comparison = null)
    {
        if (is_array($begin)) {
            $useMinMax = false;
            if (isset($begin['min'])) {
                $this->addUsingAlias(DealerShedulesVersionTableMap::BEGIN, $begin['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($begin['max'])) {
                $this->addUsingAlias(DealerShedulesVersionTableMap::BEGIN, $begin['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DealerShedulesVersionTableMap::BEGIN, $begin, $comparison);
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
     * @return ChildDealerShedulesVersionQuery The current query, for fluid interface
     */
    public function filterByEnd($end = null, $comparison = null)
    {
        if (is_array($end)) {
            $useMinMax = false;
            if (isset($end['min'])) {
                $this->addUsingAlias(DealerShedulesVersionTableMap::END, $end['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($end['max'])) {
                $this->addUsingAlias(DealerShedulesVersionTableMap::END, $end['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DealerShedulesVersionTableMap::END, $end, $comparison);
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
     * @return ChildDealerShedulesVersionQuery The current query, for fluid interface
     */
    public function filterByPeriodBegin($periodBegin = null, $comparison = null)
    {
        if (is_array($periodBegin)) {
            $useMinMax = false;
            if (isset($periodBegin['min'])) {
                $this->addUsingAlias(DealerShedulesVersionTableMap::PERIOD_BEGIN, $periodBegin['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($periodBegin['max'])) {
                $this->addUsingAlias(DealerShedulesVersionTableMap::PERIOD_BEGIN, $periodBegin['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DealerShedulesVersionTableMap::PERIOD_BEGIN, $periodBegin, $comparison);
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
     * @return ChildDealerShedulesVersionQuery The current query, for fluid interface
     */
    public function filterByPeriodEnd($periodEnd = null, $comparison = null)
    {
        if (is_array($periodEnd)) {
            $useMinMax = false;
            if (isset($periodEnd['min'])) {
                $this->addUsingAlias(DealerShedulesVersionTableMap::PERIOD_END, $periodEnd['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($periodEnd['max'])) {
                $this->addUsingAlias(DealerShedulesVersionTableMap::PERIOD_END, $periodEnd['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DealerShedulesVersionTableMap::PERIOD_END, $periodEnd, $comparison);
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
     * @return ChildDealerShedulesVersionQuery The current query, for fluid interface
     */
    public function filterByCreatedAt($createdAt = null, $comparison = null)
    {
        if (is_array($createdAt)) {
            $useMinMax = false;
            if (isset($createdAt['min'])) {
                $this->addUsingAlias(DealerShedulesVersionTableMap::CREATED_AT, $createdAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdAt['max'])) {
                $this->addUsingAlias(DealerShedulesVersionTableMap::CREATED_AT, $createdAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DealerShedulesVersionTableMap::CREATED_AT, $createdAt, $comparison);
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
     * @return ChildDealerShedulesVersionQuery The current query, for fluid interface
     */
    public function filterByUpdatedAt($updatedAt = null, $comparison = null)
    {
        if (is_array($updatedAt)) {
            $useMinMax = false;
            if (isset($updatedAt['min'])) {
                $this->addUsingAlias(DealerShedulesVersionTableMap::UPDATED_AT, $updatedAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($updatedAt['max'])) {
                $this->addUsingAlias(DealerShedulesVersionTableMap::UPDATED_AT, $updatedAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DealerShedulesVersionTableMap::UPDATED_AT, $updatedAt, $comparison);
    }

    /**
     * Filter the query on the version column
     *
     * Example usage:
     * <code>
     * $query->filterByVersion(1234); // WHERE version = 1234
     * $query->filterByVersion(array(12, 34)); // WHERE version IN (12, 34)
     * $query->filterByVersion(array('min' => 12)); // WHERE version > 12
     * </code>
     *
     * @param     mixed $version The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDealerShedulesVersionQuery The current query, for fluid interface
     */
    public function filterByVersion($version = null, $comparison = null)
    {
        if (is_array($version)) {
            $useMinMax = false;
            if (isset($version['min'])) {
                $this->addUsingAlias(DealerShedulesVersionTableMap::VERSION, $version['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($version['max'])) {
                $this->addUsingAlias(DealerShedulesVersionTableMap::VERSION, $version['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DealerShedulesVersionTableMap::VERSION, $version, $comparison);
    }

    /**
     * Filter the query on the version_created_at column
     *
     * Example usage:
     * <code>
     * $query->filterByVersionCreatedAt('2011-03-14'); // WHERE version_created_at = '2011-03-14'
     * $query->filterByVersionCreatedAt('now'); // WHERE version_created_at = '2011-03-14'
     * $query->filterByVersionCreatedAt(array('max' => 'yesterday')); // WHERE version_created_at > '2011-03-13'
     * </code>
     *
     * @param     mixed $versionCreatedAt The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDealerShedulesVersionQuery The current query, for fluid interface
     */
    public function filterByVersionCreatedAt($versionCreatedAt = null, $comparison = null)
    {
        if (is_array($versionCreatedAt)) {
            $useMinMax = false;
            if (isset($versionCreatedAt['min'])) {
                $this->addUsingAlias(DealerShedulesVersionTableMap::VERSION_CREATED_AT, $versionCreatedAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($versionCreatedAt['max'])) {
                $this->addUsingAlias(DealerShedulesVersionTableMap::VERSION_CREATED_AT, $versionCreatedAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DealerShedulesVersionTableMap::VERSION_CREATED_AT, $versionCreatedAt, $comparison);
    }

    /**
     * Filter the query on the version_created_by column
     *
     * Example usage:
     * <code>
     * $query->filterByVersionCreatedBy('fooValue');   // WHERE version_created_by = 'fooValue'
     * $query->filterByVersionCreatedBy('%fooValue%'); // WHERE version_created_by LIKE '%fooValue%'
     * </code>
     *
     * @param     string $versionCreatedBy The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDealerShedulesVersionQuery The current query, for fluid interface
     */
    public function filterByVersionCreatedBy($versionCreatedBy = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($versionCreatedBy)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $versionCreatedBy)) {
                $versionCreatedBy = str_replace('*', '%', $versionCreatedBy);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(DealerShedulesVersionTableMap::VERSION_CREATED_BY, $versionCreatedBy, $comparison);
    }

    /**
     * Filter the query on the dealer_id_version column
     *
     * Example usage:
     * <code>
     * $query->filterByDealerIdVersion(1234); // WHERE dealer_id_version = 1234
     * $query->filterByDealerIdVersion(array(12, 34)); // WHERE dealer_id_version IN (12, 34)
     * $query->filterByDealerIdVersion(array('min' => 12)); // WHERE dealer_id_version > 12
     * </code>
     *
     * @param     mixed $dealerIdVersion The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDealerShedulesVersionQuery The current query, for fluid interface
     */
    public function filterByDealerIdVersion($dealerIdVersion = null, $comparison = null)
    {
        if (is_array($dealerIdVersion)) {
            $useMinMax = false;
            if (isset($dealerIdVersion['min'])) {
                $this->addUsingAlias(DealerShedulesVersionTableMap::DEALER_ID_VERSION, $dealerIdVersion['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dealerIdVersion['max'])) {
                $this->addUsingAlias(DealerShedulesVersionTableMap::DEALER_ID_VERSION, $dealerIdVersion['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DealerShedulesVersionTableMap::DEALER_ID_VERSION, $dealerIdVersion, $comparison);
    }

    /**
     * Filter the query by a related \Dealer\Model\DealerShedules object
     *
     * @param \Dealer\Model\DealerShedules|ObjectCollection $dealerShedules The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDealerShedulesVersionQuery The current query, for fluid interface
     */
    public function filterByDealerShedules($dealerShedules, $comparison = null)
    {
        if ($dealerShedules instanceof \Dealer\Model\DealerShedules) {
            return $this
                ->addUsingAlias(DealerShedulesVersionTableMap::ID, $dealerShedules->getId(), $comparison);
        } elseif ($dealerShedules instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(DealerShedulesVersionTableMap::ID, $dealerShedules->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByDealerShedules() only accepts arguments of type \Dealer\Model\DealerShedules or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the DealerShedules relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildDealerShedulesVersionQuery The current query, for fluid interface
     */
    public function joinDealerShedules($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('DealerShedules');

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
            $this->addJoinObject($join, 'DealerShedules');
        }

        return $this;
    }

    /**
     * Use the DealerShedules relation DealerShedules object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Dealer\Model\DealerShedulesQuery A secondary query class using the current class as primary query
     */
    public function useDealerShedulesQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinDealerShedules($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'DealerShedules', '\Dealer\Model\DealerShedulesQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildDealerShedulesVersion $dealerShedulesVersion Object to remove from the list of results
     *
     * @return ChildDealerShedulesVersionQuery The current query, for fluid interface
     */
    public function prune($dealerShedulesVersion = null)
    {
        if ($dealerShedulesVersion) {
            $this->addCond('pruneCond0', $this->getAliasedColName(DealerShedulesVersionTableMap::ID), $dealerShedulesVersion->getId(), Criteria::NOT_EQUAL);
            $this->addCond('pruneCond1', $this->getAliasedColName(DealerShedulesVersionTableMap::VERSION), $dealerShedulesVersion->getVersion(), Criteria::NOT_EQUAL);
            $this->combine(array('pruneCond0', 'pruneCond1'), Criteria::LOGICAL_OR);
        }

        return $this;
    }

    /**
     * Deletes all rows from the dealer_shedules_version table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(DealerShedulesVersionTableMap::DATABASE_NAME);
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
            DealerShedulesVersionTableMap::clearInstancePool();
            DealerShedulesVersionTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildDealerShedulesVersion or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildDealerShedulesVersion object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(DealerShedulesVersionTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(DealerShedulesVersionTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();


        DealerShedulesVersionTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            DealerShedulesVersionTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

} // DealerShedulesVersionQuery
