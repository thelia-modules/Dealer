<?php

namespace Dealer\Model\Base;

use \Exception;
use \PDO;
use Dealer\Model\DealerFolderVersion as ChildDealerFolderVersion;
use Dealer\Model\DealerFolderVersionQuery as ChildDealerFolderVersionQuery;
use Dealer\Model\Map\DealerFolderVersionTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'dealer_folder_version' table.
 *
 *
 *
 * @method     ChildDealerFolderVersionQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildDealerFolderVersionQuery orderByDealerId($order = Criteria::ASC) Order by the dealer_id column
 * @method     ChildDealerFolderVersionQuery orderByFolderId($order = Criteria::ASC) Order by the folder_id column
 * @method     ChildDealerFolderVersionQuery orderByCreatedAt($order = Criteria::ASC) Order by the created_at column
 * @method     ChildDealerFolderVersionQuery orderByUpdatedAt($order = Criteria::ASC) Order by the updated_at column
 * @method     ChildDealerFolderVersionQuery orderByVersion($order = Criteria::ASC) Order by the version column
 * @method     ChildDealerFolderVersionQuery orderByVersionCreatedAt($order = Criteria::ASC) Order by the version_created_at column
 * @method     ChildDealerFolderVersionQuery orderByVersionCreatedBy($order = Criteria::ASC) Order by the version_created_by column
 * @method     ChildDealerFolderVersionQuery orderByDealerIdVersion($order = Criteria::ASC) Order by the dealer_id_version column
 * @method     ChildDealerFolderVersionQuery orderByFolderIdVersion($order = Criteria::ASC) Order by the folder_id_version column
 *
 * @method     ChildDealerFolderVersionQuery groupById() Group by the id column
 * @method     ChildDealerFolderVersionQuery groupByDealerId() Group by the dealer_id column
 * @method     ChildDealerFolderVersionQuery groupByFolderId() Group by the folder_id column
 * @method     ChildDealerFolderVersionQuery groupByCreatedAt() Group by the created_at column
 * @method     ChildDealerFolderVersionQuery groupByUpdatedAt() Group by the updated_at column
 * @method     ChildDealerFolderVersionQuery groupByVersion() Group by the version column
 * @method     ChildDealerFolderVersionQuery groupByVersionCreatedAt() Group by the version_created_at column
 * @method     ChildDealerFolderVersionQuery groupByVersionCreatedBy() Group by the version_created_by column
 * @method     ChildDealerFolderVersionQuery groupByDealerIdVersion() Group by the dealer_id_version column
 * @method     ChildDealerFolderVersionQuery groupByFolderIdVersion() Group by the folder_id_version column
 *
 * @method     ChildDealerFolderVersionQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildDealerFolderVersionQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildDealerFolderVersionQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildDealerFolderVersionQuery leftJoinDealerFolder($relationAlias = null) Adds a LEFT JOIN clause to the query using the DealerFolder relation
 * @method     ChildDealerFolderVersionQuery rightJoinDealerFolder($relationAlias = null) Adds a RIGHT JOIN clause to the query using the DealerFolder relation
 * @method     ChildDealerFolderVersionQuery innerJoinDealerFolder($relationAlias = null) Adds a INNER JOIN clause to the query using the DealerFolder relation
 *
 * @method     ChildDealerFolderVersion findOne(ConnectionInterface $con = null) Return the first ChildDealerFolderVersion matching the query
 * @method     ChildDealerFolderVersion findOneOrCreate(ConnectionInterface $con = null) Return the first ChildDealerFolderVersion matching the query, or a new ChildDealerFolderVersion object populated from the query conditions when no match is found
 *
 * @method     ChildDealerFolderVersion findOneById(int $id) Return the first ChildDealerFolderVersion filtered by the id column
 * @method     ChildDealerFolderVersion findOneByDealerId(int $dealer_id) Return the first ChildDealerFolderVersion filtered by the dealer_id column
 * @method     ChildDealerFolderVersion findOneByFolderId(int $folder_id) Return the first ChildDealerFolderVersion filtered by the folder_id column
 * @method     ChildDealerFolderVersion findOneByCreatedAt(string $created_at) Return the first ChildDealerFolderVersion filtered by the created_at column
 * @method     ChildDealerFolderVersion findOneByUpdatedAt(string $updated_at) Return the first ChildDealerFolderVersion filtered by the updated_at column
 * @method     ChildDealerFolderVersion findOneByVersion(int $version) Return the first ChildDealerFolderVersion filtered by the version column
 * @method     ChildDealerFolderVersion findOneByVersionCreatedAt(string $version_created_at) Return the first ChildDealerFolderVersion filtered by the version_created_at column
 * @method     ChildDealerFolderVersion findOneByVersionCreatedBy(string $version_created_by) Return the first ChildDealerFolderVersion filtered by the version_created_by column
 * @method     ChildDealerFolderVersion findOneByDealerIdVersion(int $dealer_id_version) Return the first ChildDealerFolderVersion filtered by the dealer_id_version column
 * @method     ChildDealerFolderVersion findOneByFolderIdVersion(int $folder_id_version) Return the first ChildDealerFolderVersion filtered by the folder_id_version column
 *
 * @method     array findById(int $id) Return ChildDealerFolderVersion objects filtered by the id column
 * @method     array findByDealerId(int $dealer_id) Return ChildDealerFolderVersion objects filtered by the dealer_id column
 * @method     array findByFolderId(int $folder_id) Return ChildDealerFolderVersion objects filtered by the folder_id column
 * @method     array findByCreatedAt(string $created_at) Return ChildDealerFolderVersion objects filtered by the created_at column
 * @method     array findByUpdatedAt(string $updated_at) Return ChildDealerFolderVersion objects filtered by the updated_at column
 * @method     array findByVersion(int $version) Return ChildDealerFolderVersion objects filtered by the version column
 * @method     array findByVersionCreatedAt(string $version_created_at) Return ChildDealerFolderVersion objects filtered by the version_created_at column
 * @method     array findByVersionCreatedBy(string $version_created_by) Return ChildDealerFolderVersion objects filtered by the version_created_by column
 * @method     array findByDealerIdVersion(int $dealer_id_version) Return ChildDealerFolderVersion objects filtered by the dealer_id_version column
 * @method     array findByFolderIdVersion(int $folder_id_version) Return ChildDealerFolderVersion objects filtered by the folder_id_version column
 *
 */
abstract class DealerFolderVersionQuery extends ModelCriteria
{

    /**
     * Initializes internal state of \Dealer\Model\Base\DealerFolderVersionQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'thelia', $modelName = '\\Dealer\\Model\\DealerFolderVersion', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildDealerFolderVersionQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildDealerFolderVersionQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \Dealer\Model\DealerFolderVersionQuery) {
            return $criteria;
        }
        $query = new \Dealer\Model\DealerFolderVersionQuery();
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
     * @return ChildDealerFolderVersion|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = DealerFolderVersionTableMap::getInstanceFromPool(serialize(array((string) $key[0], (string) $key[1]))))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(DealerFolderVersionTableMap::DATABASE_NAME);
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
     * @return   ChildDealerFolderVersion A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, DEALER_ID, FOLDER_ID, CREATED_AT, UPDATED_AT, VERSION, VERSION_CREATED_AT, VERSION_CREATED_BY, DEALER_ID_VERSION, FOLDER_ID_VERSION FROM dealer_folder_version WHERE ID = :p0 AND VERSION = :p1';
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
            $obj = new ChildDealerFolderVersion();
            $obj->hydrate($row);
            DealerFolderVersionTableMap::addInstanceToPool($obj, serialize(array((string) $key[0], (string) $key[1])));
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
     * @return ChildDealerFolderVersion|array|mixed the result, formatted by the current formatter
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
     * @return ChildDealerFolderVersionQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {
        $this->addUsingAlias(DealerFolderVersionTableMap::ID, $key[0], Criteria::EQUAL);
        $this->addUsingAlias(DealerFolderVersionTableMap::VERSION, $key[1], Criteria::EQUAL);

        return $this;
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildDealerFolderVersionQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {
        if (empty($keys)) {
            return $this->add(null, '1<>1', Criteria::CUSTOM);
        }
        foreach ($keys as $key) {
            $cton0 = $this->getNewCriterion(DealerFolderVersionTableMap::ID, $key[0], Criteria::EQUAL);
            $cton1 = $this->getNewCriterion(DealerFolderVersionTableMap::VERSION, $key[1], Criteria::EQUAL);
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
     * @see       filterByDealerFolder()
     *
     * @param     mixed $id The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDealerFolderVersionQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(DealerFolderVersionTableMap::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(DealerFolderVersionTableMap::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DealerFolderVersionTableMap::ID, $id, $comparison);
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
     * @return ChildDealerFolderVersionQuery The current query, for fluid interface
     */
    public function filterByDealerId($dealerId = null, $comparison = null)
    {
        if (is_array($dealerId)) {
            $useMinMax = false;
            if (isset($dealerId['min'])) {
                $this->addUsingAlias(DealerFolderVersionTableMap::DEALER_ID, $dealerId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dealerId['max'])) {
                $this->addUsingAlias(DealerFolderVersionTableMap::DEALER_ID, $dealerId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DealerFolderVersionTableMap::DEALER_ID, $dealerId, $comparison);
    }

    /**
     * Filter the query on the folder_id column
     *
     * Example usage:
     * <code>
     * $query->filterByFolderId(1234); // WHERE folder_id = 1234
     * $query->filterByFolderId(array(12, 34)); // WHERE folder_id IN (12, 34)
     * $query->filterByFolderId(array('min' => 12)); // WHERE folder_id > 12
     * </code>
     *
     * @param     mixed $folderId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDealerFolderVersionQuery The current query, for fluid interface
     */
    public function filterByFolderId($folderId = null, $comparison = null)
    {
        if (is_array($folderId)) {
            $useMinMax = false;
            if (isset($folderId['min'])) {
                $this->addUsingAlias(DealerFolderVersionTableMap::FOLDER_ID, $folderId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($folderId['max'])) {
                $this->addUsingAlias(DealerFolderVersionTableMap::FOLDER_ID, $folderId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DealerFolderVersionTableMap::FOLDER_ID, $folderId, $comparison);
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
     * @return ChildDealerFolderVersionQuery The current query, for fluid interface
     */
    public function filterByCreatedAt($createdAt = null, $comparison = null)
    {
        if (is_array($createdAt)) {
            $useMinMax = false;
            if (isset($createdAt['min'])) {
                $this->addUsingAlias(DealerFolderVersionTableMap::CREATED_AT, $createdAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdAt['max'])) {
                $this->addUsingAlias(DealerFolderVersionTableMap::CREATED_AT, $createdAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DealerFolderVersionTableMap::CREATED_AT, $createdAt, $comparison);
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
     * @return ChildDealerFolderVersionQuery The current query, for fluid interface
     */
    public function filterByUpdatedAt($updatedAt = null, $comparison = null)
    {
        if (is_array($updatedAt)) {
            $useMinMax = false;
            if (isset($updatedAt['min'])) {
                $this->addUsingAlias(DealerFolderVersionTableMap::UPDATED_AT, $updatedAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($updatedAt['max'])) {
                $this->addUsingAlias(DealerFolderVersionTableMap::UPDATED_AT, $updatedAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DealerFolderVersionTableMap::UPDATED_AT, $updatedAt, $comparison);
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
     * @return ChildDealerFolderVersionQuery The current query, for fluid interface
     */
    public function filterByVersion($version = null, $comparison = null)
    {
        if (is_array($version)) {
            $useMinMax = false;
            if (isset($version['min'])) {
                $this->addUsingAlias(DealerFolderVersionTableMap::VERSION, $version['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($version['max'])) {
                $this->addUsingAlias(DealerFolderVersionTableMap::VERSION, $version['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DealerFolderVersionTableMap::VERSION, $version, $comparison);
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
     * @return ChildDealerFolderVersionQuery The current query, for fluid interface
     */
    public function filterByVersionCreatedAt($versionCreatedAt = null, $comparison = null)
    {
        if (is_array($versionCreatedAt)) {
            $useMinMax = false;
            if (isset($versionCreatedAt['min'])) {
                $this->addUsingAlias(DealerFolderVersionTableMap::VERSION_CREATED_AT, $versionCreatedAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($versionCreatedAt['max'])) {
                $this->addUsingAlias(DealerFolderVersionTableMap::VERSION_CREATED_AT, $versionCreatedAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DealerFolderVersionTableMap::VERSION_CREATED_AT, $versionCreatedAt, $comparison);
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
     * @return ChildDealerFolderVersionQuery The current query, for fluid interface
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

        return $this->addUsingAlias(DealerFolderVersionTableMap::VERSION_CREATED_BY, $versionCreatedBy, $comparison);
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
     * @return ChildDealerFolderVersionQuery The current query, for fluid interface
     */
    public function filterByDealerIdVersion($dealerIdVersion = null, $comparison = null)
    {
        if (is_array($dealerIdVersion)) {
            $useMinMax = false;
            if (isset($dealerIdVersion['min'])) {
                $this->addUsingAlias(DealerFolderVersionTableMap::DEALER_ID_VERSION, $dealerIdVersion['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($dealerIdVersion['max'])) {
                $this->addUsingAlias(DealerFolderVersionTableMap::DEALER_ID_VERSION, $dealerIdVersion['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DealerFolderVersionTableMap::DEALER_ID_VERSION, $dealerIdVersion, $comparison);
    }

    /**
     * Filter the query on the folder_id_version column
     *
     * Example usage:
     * <code>
     * $query->filterByFolderIdVersion(1234); // WHERE folder_id_version = 1234
     * $query->filterByFolderIdVersion(array(12, 34)); // WHERE folder_id_version IN (12, 34)
     * $query->filterByFolderIdVersion(array('min' => 12)); // WHERE folder_id_version > 12
     * </code>
     *
     * @param     mixed $folderIdVersion The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDealerFolderVersionQuery The current query, for fluid interface
     */
    public function filterByFolderIdVersion($folderIdVersion = null, $comparison = null)
    {
        if (is_array($folderIdVersion)) {
            $useMinMax = false;
            if (isset($folderIdVersion['min'])) {
                $this->addUsingAlias(DealerFolderVersionTableMap::FOLDER_ID_VERSION, $folderIdVersion['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($folderIdVersion['max'])) {
                $this->addUsingAlias(DealerFolderVersionTableMap::FOLDER_ID_VERSION, $folderIdVersion['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DealerFolderVersionTableMap::FOLDER_ID_VERSION, $folderIdVersion, $comparison);
    }

    /**
     * Filter the query by a related \Dealer\Model\DealerFolder object
     *
     * @param \Dealer\Model\DealerFolder|ObjectCollection $dealerFolder The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDealerFolderVersionQuery The current query, for fluid interface
     */
    public function filterByDealerFolder($dealerFolder, $comparison = null)
    {
        if ($dealerFolder instanceof \Dealer\Model\DealerFolder) {
            return $this
                ->addUsingAlias(DealerFolderVersionTableMap::ID, $dealerFolder->getId(), $comparison);
        } elseif ($dealerFolder instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(DealerFolderVersionTableMap::ID, $dealerFolder->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByDealerFolder() only accepts arguments of type \Dealer\Model\DealerFolder or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the DealerFolder relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildDealerFolderVersionQuery The current query, for fluid interface
     */
    public function joinDealerFolder($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('DealerFolder');

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
            $this->addJoinObject($join, 'DealerFolder');
        }

        return $this;
    }

    /**
     * Use the DealerFolder relation DealerFolder object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Dealer\Model\DealerFolderQuery A secondary query class using the current class as primary query
     */
    public function useDealerFolderQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinDealerFolder($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'DealerFolder', '\Dealer\Model\DealerFolderQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildDealerFolderVersion $dealerFolderVersion Object to remove from the list of results
     *
     * @return ChildDealerFolderVersionQuery The current query, for fluid interface
     */
    public function prune($dealerFolderVersion = null)
    {
        if ($dealerFolderVersion) {
            $this->addCond('pruneCond0', $this->getAliasedColName(DealerFolderVersionTableMap::ID), $dealerFolderVersion->getId(), Criteria::NOT_EQUAL);
            $this->addCond('pruneCond1', $this->getAliasedColName(DealerFolderVersionTableMap::VERSION), $dealerFolderVersion->getVersion(), Criteria::NOT_EQUAL);
            $this->combine(array('pruneCond0', 'pruneCond1'), Criteria::LOGICAL_OR);
        }

        return $this;
    }

    /**
     * Deletes all rows from the dealer_folder_version table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(DealerFolderVersionTableMap::DATABASE_NAME);
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
            DealerFolderVersionTableMap::clearInstancePool();
            DealerFolderVersionTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildDealerFolderVersion or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildDealerFolderVersion object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(DealerFolderVersionTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(DealerFolderVersionTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();


        DealerFolderVersionTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            DealerFolderVersionTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

} // DealerFolderVersionQuery
