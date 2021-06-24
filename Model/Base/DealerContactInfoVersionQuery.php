<?php

namespace Dealer\Model\Base;

use \Exception;
use \PDO;
use Dealer\Model\DealerContactInfoVersion as ChildDealerContactInfoVersion;
use Dealer\Model\DealerContactInfoVersionQuery as ChildDealerContactInfoVersionQuery;
use Dealer\Model\Map\DealerContactInfoVersionTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'dealer_contact_info_version' table.
 *
 *
 *
 * @method     ChildDealerContactInfoVersionQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildDealerContactInfoVersionQuery orderByContactId($order = Criteria::ASC) Order by the contact_id column
 * @method     ChildDealerContactInfoVersionQuery orderByContactType($order = Criteria::ASC) Order by the contact_type column
 * @method     ChildDealerContactInfoVersionQuery orderByCreatedAt($order = Criteria::ASC) Order by the created_at column
 * @method     ChildDealerContactInfoVersionQuery orderByUpdatedAt($order = Criteria::ASC) Order by the updated_at column
 * @method     ChildDealerContactInfoVersionQuery orderByVersion($order = Criteria::ASC) Order by the version column
 * @method     ChildDealerContactInfoVersionQuery orderByVersionCreatedAt($order = Criteria::ASC) Order by the version_created_at column
 * @method     ChildDealerContactInfoVersionQuery orderByVersionCreatedBy($order = Criteria::ASC) Order by the version_created_by column
 * @method     ChildDealerContactInfoVersionQuery orderByContactIdVersion($order = Criteria::ASC) Order by the contact_id_version column
 *
 * @method     ChildDealerContactInfoVersionQuery groupById() Group by the id column
 * @method     ChildDealerContactInfoVersionQuery groupByContactId() Group by the contact_id column
 * @method     ChildDealerContactInfoVersionQuery groupByContactType() Group by the contact_type column
 * @method     ChildDealerContactInfoVersionQuery groupByCreatedAt() Group by the created_at column
 * @method     ChildDealerContactInfoVersionQuery groupByUpdatedAt() Group by the updated_at column
 * @method     ChildDealerContactInfoVersionQuery groupByVersion() Group by the version column
 * @method     ChildDealerContactInfoVersionQuery groupByVersionCreatedAt() Group by the version_created_at column
 * @method     ChildDealerContactInfoVersionQuery groupByVersionCreatedBy() Group by the version_created_by column
 * @method     ChildDealerContactInfoVersionQuery groupByContactIdVersion() Group by the contact_id_version column
 *
 * @method     ChildDealerContactInfoVersionQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildDealerContactInfoVersionQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildDealerContactInfoVersionQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildDealerContactInfoVersionQuery leftJoinDealerContactInfo($relationAlias = null) Adds a LEFT JOIN clause to the query using the DealerContactInfo relation
 * @method     ChildDealerContactInfoVersionQuery rightJoinDealerContactInfo($relationAlias = null) Adds a RIGHT JOIN clause to the query using the DealerContactInfo relation
 * @method     ChildDealerContactInfoVersionQuery innerJoinDealerContactInfo($relationAlias = null) Adds a INNER JOIN clause to the query using the DealerContactInfo relation
 *
 * @method     ChildDealerContactInfoVersion findOne(ConnectionInterface $con = null) Return the first ChildDealerContactInfoVersion matching the query
 * @method     ChildDealerContactInfoVersion findOneOrCreate(ConnectionInterface $con = null) Return the first ChildDealerContactInfoVersion matching the query, or a new ChildDealerContactInfoVersion object populated from the query conditions when no match is found
 *
 * @method     ChildDealerContactInfoVersion findOneById(int $id) Return the first ChildDealerContactInfoVersion filtered by the id column
 * @method     ChildDealerContactInfoVersion findOneByContactId(int $contact_id) Return the first ChildDealerContactInfoVersion filtered by the contact_id column
 * @method     ChildDealerContactInfoVersion findOneByContactType(int $contact_type) Return the first ChildDealerContactInfoVersion filtered by the contact_type column
 * @method     ChildDealerContactInfoVersion findOneByCreatedAt(string $created_at) Return the first ChildDealerContactInfoVersion filtered by the created_at column
 * @method     ChildDealerContactInfoVersion findOneByUpdatedAt(string $updated_at) Return the first ChildDealerContactInfoVersion filtered by the updated_at column
 * @method     ChildDealerContactInfoVersion findOneByVersion(int $version) Return the first ChildDealerContactInfoVersion filtered by the version column
 * @method     ChildDealerContactInfoVersion findOneByVersionCreatedAt(string $version_created_at) Return the first ChildDealerContactInfoVersion filtered by the version_created_at column
 * @method     ChildDealerContactInfoVersion findOneByVersionCreatedBy(string $version_created_by) Return the first ChildDealerContactInfoVersion filtered by the version_created_by column
 * @method     ChildDealerContactInfoVersion findOneByContactIdVersion(int $contact_id_version) Return the first ChildDealerContactInfoVersion filtered by the contact_id_version column
 *
 * @method     array findById(int $id) Return ChildDealerContactInfoVersion objects filtered by the id column
 * @method     array findByContactId(int $contact_id) Return ChildDealerContactInfoVersion objects filtered by the contact_id column
 * @method     array findByContactType(int $contact_type) Return ChildDealerContactInfoVersion objects filtered by the contact_type column
 * @method     array findByCreatedAt(string $created_at) Return ChildDealerContactInfoVersion objects filtered by the created_at column
 * @method     array findByUpdatedAt(string $updated_at) Return ChildDealerContactInfoVersion objects filtered by the updated_at column
 * @method     array findByVersion(int $version) Return ChildDealerContactInfoVersion objects filtered by the version column
 * @method     array findByVersionCreatedAt(string $version_created_at) Return ChildDealerContactInfoVersion objects filtered by the version_created_at column
 * @method     array findByVersionCreatedBy(string $version_created_by) Return ChildDealerContactInfoVersion objects filtered by the version_created_by column
 * @method     array findByContactIdVersion(int $contact_id_version) Return ChildDealerContactInfoVersion objects filtered by the contact_id_version column
 *
 */
abstract class DealerContactInfoVersionQuery extends ModelCriteria
{

    /**
     * Initializes internal state of \Dealer\Model\Base\DealerContactInfoVersionQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'thelia', $modelName = '\\Dealer\\Model\\DealerContactInfoVersion', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildDealerContactInfoVersionQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildDealerContactInfoVersionQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \Dealer\Model\DealerContactInfoVersionQuery) {
            return $criteria;
        }
        $query = new \Dealer\Model\DealerContactInfoVersionQuery();
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
     * @return ChildDealerContactInfoVersion|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = DealerContactInfoVersionTableMap::getInstanceFromPool(serialize(array((string) $key[0], (string) $key[1]))))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(DealerContactInfoVersionTableMap::DATABASE_NAME);
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
     * @return   ChildDealerContactInfoVersion A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, CONTACT_ID, CONTACT_TYPE, CREATED_AT, UPDATED_AT, VERSION, VERSION_CREATED_AT, VERSION_CREATED_BY, CONTACT_ID_VERSION FROM dealer_contact_info_version WHERE ID = :p0 AND VERSION = :p1';
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
            $obj = new ChildDealerContactInfoVersion();
            $obj->hydrate($row);
            DealerContactInfoVersionTableMap::addInstanceToPool($obj, serialize(array((string) $key[0], (string) $key[1])));
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
     * @return ChildDealerContactInfoVersion|array|mixed the result, formatted by the current formatter
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
     * @return ChildDealerContactInfoVersionQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {
        $this->addUsingAlias(DealerContactInfoVersionTableMap::ID, $key[0], Criteria::EQUAL);
        $this->addUsingAlias(DealerContactInfoVersionTableMap::VERSION, $key[1], Criteria::EQUAL);

        return $this;
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildDealerContactInfoVersionQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {
        if (empty($keys)) {
            return $this->add(null, '1<>1', Criteria::CUSTOM);
        }
        foreach ($keys as $key) {
            $cton0 = $this->getNewCriterion(DealerContactInfoVersionTableMap::ID, $key[0], Criteria::EQUAL);
            $cton1 = $this->getNewCriterion(DealerContactInfoVersionTableMap::VERSION, $key[1], Criteria::EQUAL);
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
     * @see       filterByDealerContactInfo()
     *
     * @param     mixed $id The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDealerContactInfoVersionQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(DealerContactInfoVersionTableMap::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(DealerContactInfoVersionTableMap::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DealerContactInfoVersionTableMap::ID, $id, $comparison);
    }

    /**
     * Filter the query on the contact_id column
     *
     * Example usage:
     * <code>
     * $query->filterByContactId(1234); // WHERE contact_id = 1234
     * $query->filterByContactId(array(12, 34)); // WHERE contact_id IN (12, 34)
     * $query->filterByContactId(array('min' => 12)); // WHERE contact_id > 12
     * </code>
     *
     * @param     mixed $contactId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDealerContactInfoVersionQuery The current query, for fluid interface
     */
    public function filterByContactId($contactId = null, $comparison = null)
    {
        if (is_array($contactId)) {
            $useMinMax = false;
            if (isset($contactId['min'])) {
                $this->addUsingAlias(DealerContactInfoVersionTableMap::CONTACT_ID, $contactId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($contactId['max'])) {
                $this->addUsingAlias(DealerContactInfoVersionTableMap::CONTACT_ID, $contactId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DealerContactInfoVersionTableMap::CONTACT_ID, $contactId, $comparison);
    }

    /**
     * Filter the query on the contact_type column
     *
     * @param     mixed $contactType The value to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDealerContactInfoVersionQuery The current query, for fluid interface
     */
    public function filterByContactType($contactType = null, $comparison = null)
    {
        $valueSet = DealerContactInfoVersionTableMap::getValueSet(DealerContactInfoVersionTableMap::CONTACT_TYPE);
        if (is_scalar($contactType)) {
            if (!in_array($contactType, $valueSet)) {
                throw new PropelException(sprintf('Value "%s" is not accepted in this enumerated column', $contactType));
            }
            $contactType = array_search($contactType, $valueSet);
        } elseif (is_array($contactType)) {
            $convertedValues = array();
            foreach ($contactType as $value) {
                if (!in_array($value, $valueSet)) {
                    throw new PropelException(sprintf('Value "%s" is not accepted in this enumerated column', $value));
                }
                $convertedValues []= array_search($value, $valueSet);
            }
            $contactType = $convertedValues;
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DealerContactInfoVersionTableMap::CONTACT_TYPE, $contactType, $comparison);
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
     * @return ChildDealerContactInfoVersionQuery The current query, for fluid interface
     */
    public function filterByCreatedAt($createdAt = null, $comparison = null)
    {
        if (is_array($createdAt)) {
            $useMinMax = false;
            if (isset($createdAt['min'])) {
                $this->addUsingAlias(DealerContactInfoVersionTableMap::CREATED_AT, $createdAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdAt['max'])) {
                $this->addUsingAlias(DealerContactInfoVersionTableMap::CREATED_AT, $createdAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DealerContactInfoVersionTableMap::CREATED_AT, $createdAt, $comparison);
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
     * @return ChildDealerContactInfoVersionQuery The current query, for fluid interface
     */
    public function filterByUpdatedAt($updatedAt = null, $comparison = null)
    {
        if (is_array($updatedAt)) {
            $useMinMax = false;
            if (isset($updatedAt['min'])) {
                $this->addUsingAlias(DealerContactInfoVersionTableMap::UPDATED_AT, $updatedAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($updatedAt['max'])) {
                $this->addUsingAlias(DealerContactInfoVersionTableMap::UPDATED_AT, $updatedAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DealerContactInfoVersionTableMap::UPDATED_AT, $updatedAt, $comparison);
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
     * @return ChildDealerContactInfoVersionQuery The current query, for fluid interface
     */
    public function filterByVersion($version = null, $comparison = null)
    {
        if (is_array($version)) {
            $useMinMax = false;
            if (isset($version['min'])) {
                $this->addUsingAlias(DealerContactInfoVersionTableMap::VERSION, $version['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($version['max'])) {
                $this->addUsingAlias(DealerContactInfoVersionTableMap::VERSION, $version['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DealerContactInfoVersionTableMap::VERSION, $version, $comparison);
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
     * @return ChildDealerContactInfoVersionQuery The current query, for fluid interface
     */
    public function filterByVersionCreatedAt($versionCreatedAt = null, $comparison = null)
    {
        if (is_array($versionCreatedAt)) {
            $useMinMax = false;
            if (isset($versionCreatedAt['min'])) {
                $this->addUsingAlias(DealerContactInfoVersionTableMap::VERSION_CREATED_AT, $versionCreatedAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($versionCreatedAt['max'])) {
                $this->addUsingAlias(DealerContactInfoVersionTableMap::VERSION_CREATED_AT, $versionCreatedAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DealerContactInfoVersionTableMap::VERSION_CREATED_AT, $versionCreatedAt, $comparison);
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
     * @return ChildDealerContactInfoVersionQuery The current query, for fluid interface
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

        return $this->addUsingAlias(DealerContactInfoVersionTableMap::VERSION_CREATED_BY, $versionCreatedBy, $comparison);
    }

    /**
     * Filter the query on the contact_id_version column
     *
     * Example usage:
     * <code>
     * $query->filterByContactIdVersion(1234); // WHERE contact_id_version = 1234
     * $query->filterByContactIdVersion(array(12, 34)); // WHERE contact_id_version IN (12, 34)
     * $query->filterByContactIdVersion(array('min' => 12)); // WHERE contact_id_version > 12
     * </code>
     *
     * @param     mixed $contactIdVersion The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDealerContactInfoVersionQuery The current query, for fluid interface
     */
    public function filterByContactIdVersion($contactIdVersion = null, $comparison = null)
    {
        if (is_array($contactIdVersion)) {
            $useMinMax = false;
            if (isset($contactIdVersion['min'])) {
                $this->addUsingAlias(DealerContactInfoVersionTableMap::CONTACT_ID_VERSION, $contactIdVersion['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($contactIdVersion['max'])) {
                $this->addUsingAlias(DealerContactInfoVersionTableMap::CONTACT_ID_VERSION, $contactIdVersion['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DealerContactInfoVersionTableMap::CONTACT_ID_VERSION, $contactIdVersion, $comparison);
    }

    /**
     * Filter the query by a related \Dealer\Model\DealerContactInfo object
     *
     * @param \Dealer\Model\DealerContactInfo|ObjectCollection $dealerContactInfo The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDealerContactInfoVersionQuery The current query, for fluid interface
     */
    public function filterByDealerContactInfo($dealerContactInfo, $comparison = null)
    {
        if ($dealerContactInfo instanceof \Dealer\Model\DealerContactInfo) {
            return $this
                ->addUsingAlias(DealerContactInfoVersionTableMap::ID, $dealerContactInfo->getId(), $comparison);
        } elseif ($dealerContactInfo instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(DealerContactInfoVersionTableMap::ID, $dealerContactInfo->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByDealerContactInfo() only accepts arguments of type \Dealer\Model\DealerContactInfo or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the DealerContactInfo relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildDealerContactInfoVersionQuery The current query, for fluid interface
     */
    public function joinDealerContactInfo($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('DealerContactInfo');

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
            $this->addJoinObject($join, 'DealerContactInfo');
        }

        return $this;
    }

    /**
     * Use the DealerContactInfo relation DealerContactInfo object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Dealer\Model\DealerContactInfoQuery A secondary query class using the current class as primary query
     */
    public function useDealerContactInfoQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinDealerContactInfo($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'DealerContactInfo', '\Dealer\Model\DealerContactInfoQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildDealerContactInfoVersion $dealerContactInfoVersion Object to remove from the list of results
     *
     * @return ChildDealerContactInfoVersionQuery The current query, for fluid interface
     */
    public function prune($dealerContactInfoVersion = null)
    {
        if ($dealerContactInfoVersion) {
            $this->addCond('pruneCond0', $this->getAliasedColName(DealerContactInfoVersionTableMap::ID), $dealerContactInfoVersion->getId(), Criteria::NOT_EQUAL);
            $this->addCond('pruneCond1', $this->getAliasedColName(DealerContactInfoVersionTableMap::VERSION), $dealerContactInfoVersion->getVersion(), Criteria::NOT_EQUAL);
            $this->combine(array('pruneCond0', 'pruneCond1'), Criteria::LOGICAL_OR);
        }

        return $this;
    }

    /**
     * Deletes all rows from the dealer_contact_info_version table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(DealerContactInfoVersionTableMap::DATABASE_NAME);
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
            DealerContactInfoVersionTableMap::clearInstancePool();
            DealerContactInfoVersionTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildDealerContactInfoVersion or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildDealerContactInfoVersion object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(DealerContactInfoVersionTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(DealerContactInfoVersionTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();


        DealerContactInfoVersionTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            DealerContactInfoVersionTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

} // DealerContactInfoVersionQuery
