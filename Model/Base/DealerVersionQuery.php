<?php

namespace Dealer\Model\Base;

use \Exception;
use \PDO;
use Dealer\Model\DealerVersion as ChildDealerVersion;
use Dealer\Model\DealerVersionQuery as ChildDealerVersionQuery;
use Dealer\Model\Map\DealerVersionTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'dealer_version' table.
 *
 *
 *
 * @method     ChildDealerVersionQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildDealerVersionQuery orderByVisible($order = Criteria::ASC) Order by the visible column
 * @method     ChildDealerVersionQuery orderByAddress1($order = Criteria::ASC) Order by the address1 column
 * @method     ChildDealerVersionQuery orderByAddress2($order = Criteria::ASC) Order by the address2 column
 * @method     ChildDealerVersionQuery orderByAddress3($order = Criteria::ASC) Order by the address3 column
 * @method     ChildDealerVersionQuery orderByZipcode($order = Criteria::ASC) Order by the zipcode column
 * @method     ChildDealerVersionQuery orderByCity($order = Criteria::ASC) Order by the city column
 * @method     ChildDealerVersionQuery orderByCountryId($order = Criteria::ASC) Order by the country_id column
 * @method     ChildDealerVersionQuery orderByLatitude($order = Criteria::ASC) Order by the latitude column
 * @method     ChildDealerVersionQuery orderByLongitude($order = Criteria::ASC) Order by the longitude column
 * @method     ChildDealerVersionQuery orderByCreatedAt($order = Criteria::ASC) Order by the created_at column
 * @method     ChildDealerVersionQuery orderByUpdatedAt($order = Criteria::ASC) Order by the updated_at column
 * @method     ChildDealerVersionQuery orderByVersion($order = Criteria::ASC) Order by the version column
 * @method     ChildDealerVersionQuery orderByVersionCreatedAt($order = Criteria::ASC) Order by the version_created_at column
 * @method     ChildDealerVersionQuery orderByVersionCreatedBy($order = Criteria::ASC) Order by the version_created_by column
 * @method     ChildDealerVersionQuery orderByDealerShedulesIds($order = Criteria::ASC) Order by the dealer_shedules_ids column
 * @method     ChildDealerVersionQuery orderByDealerShedulesVersions($order = Criteria::ASC) Order by the dealer_shedules_versions column
 * @method     ChildDealerVersionQuery orderByDealerContactIds($order = Criteria::ASC) Order by the dealer_contact_ids column
 * @method     ChildDealerVersionQuery orderByDealerContactVersions($order = Criteria::ASC) Order by the dealer_contact_versions column
 * @method     ChildDealerVersionQuery orderByDealerContentIds($order = Criteria::ASC) Order by the dealer_content_ids column
 * @method     ChildDealerVersionQuery orderByDealerContentVersions($order = Criteria::ASC) Order by the dealer_content_versions column
 * @method     ChildDealerVersionQuery orderByDealerFolderIds($order = Criteria::ASC) Order by the dealer_folder_ids column
 * @method     ChildDealerVersionQuery orderByDealerFolderVersions($order = Criteria::ASC) Order by the dealer_folder_versions column
 * @method     ChildDealerVersionQuery orderByDealerBrandIds($order = Criteria::ASC) Order by the dealer_brand_ids column
 * @method     ChildDealerVersionQuery orderByDealerBrandVersions($order = Criteria::ASC) Order by the dealer_brand_versions column
 * @method     ChildDealerVersionQuery orderByDealerProductIds($order = Criteria::ASC) Order by the dealer_product_ids column
 * @method     ChildDealerVersionQuery orderByDealerProductVersions($order = Criteria::ASC) Order by the dealer_product_versions column
 *
 * @method     ChildDealerVersionQuery groupById() Group by the id column
 * @method     ChildDealerVersionQuery groupByVisible() Group by the visible column
 * @method     ChildDealerVersionQuery groupByAddress1() Group by the address1 column
 * @method     ChildDealerVersionQuery groupByAddress2() Group by the address2 column
 * @method     ChildDealerVersionQuery groupByAddress3() Group by the address3 column
 * @method     ChildDealerVersionQuery groupByZipcode() Group by the zipcode column
 * @method     ChildDealerVersionQuery groupByCity() Group by the city column
 * @method     ChildDealerVersionQuery groupByCountryId() Group by the country_id column
 * @method     ChildDealerVersionQuery groupByLatitude() Group by the latitude column
 * @method     ChildDealerVersionQuery groupByLongitude() Group by the longitude column
 * @method     ChildDealerVersionQuery groupByCreatedAt() Group by the created_at column
 * @method     ChildDealerVersionQuery groupByUpdatedAt() Group by the updated_at column
 * @method     ChildDealerVersionQuery groupByVersion() Group by the version column
 * @method     ChildDealerVersionQuery groupByVersionCreatedAt() Group by the version_created_at column
 * @method     ChildDealerVersionQuery groupByVersionCreatedBy() Group by the version_created_by column
 * @method     ChildDealerVersionQuery groupByDealerShedulesIds() Group by the dealer_shedules_ids column
 * @method     ChildDealerVersionQuery groupByDealerShedulesVersions() Group by the dealer_shedules_versions column
 * @method     ChildDealerVersionQuery groupByDealerContactIds() Group by the dealer_contact_ids column
 * @method     ChildDealerVersionQuery groupByDealerContactVersions() Group by the dealer_contact_versions column
 * @method     ChildDealerVersionQuery groupByDealerContentIds() Group by the dealer_content_ids column
 * @method     ChildDealerVersionQuery groupByDealerContentVersions() Group by the dealer_content_versions column
 * @method     ChildDealerVersionQuery groupByDealerFolderIds() Group by the dealer_folder_ids column
 * @method     ChildDealerVersionQuery groupByDealerFolderVersions() Group by the dealer_folder_versions column
 * @method     ChildDealerVersionQuery groupByDealerBrandIds() Group by the dealer_brand_ids column
 * @method     ChildDealerVersionQuery groupByDealerBrandVersions() Group by the dealer_brand_versions column
 * @method     ChildDealerVersionQuery groupByDealerProductIds() Group by the dealer_product_ids column
 * @method     ChildDealerVersionQuery groupByDealerProductVersions() Group by the dealer_product_versions column
 *
 * @method     ChildDealerVersionQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildDealerVersionQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildDealerVersionQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildDealerVersionQuery leftJoinDealer($relationAlias = null) Adds a LEFT JOIN clause to the query using the Dealer relation
 * @method     ChildDealerVersionQuery rightJoinDealer($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Dealer relation
 * @method     ChildDealerVersionQuery innerJoinDealer($relationAlias = null) Adds a INNER JOIN clause to the query using the Dealer relation
 *
 * @method     ChildDealerVersion findOne(ConnectionInterface $con = null) Return the first ChildDealerVersion matching the query
 * @method     ChildDealerVersion findOneOrCreate(ConnectionInterface $con = null) Return the first ChildDealerVersion matching the query, or a new ChildDealerVersion object populated from the query conditions when no match is found
 *
 * @method     ChildDealerVersion findOneById(int $id) Return the first ChildDealerVersion filtered by the id column
 * @method     ChildDealerVersion findOneByVisible(int $visible) Return the first ChildDealerVersion filtered by the visible column
 * @method     ChildDealerVersion findOneByAddress1(string $address1) Return the first ChildDealerVersion filtered by the address1 column
 * @method     ChildDealerVersion findOneByAddress2(string $address2) Return the first ChildDealerVersion filtered by the address2 column
 * @method     ChildDealerVersion findOneByAddress3(string $address3) Return the first ChildDealerVersion filtered by the address3 column
 * @method     ChildDealerVersion findOneByZipcode(string $zipcode) Return the first ChildDealerVersion filtered by the zipcode column
 * @method     ChildDealerVersion findOneByCity(string $city) Return the first ChildDealerVersion filtered by the city column
 * @method     ChildDealerVersion findOneByCountryId(int $country_id) Return the first ChildDealerVersion filtered by the country_id column
 * @method     ChildDealerVersion findOneByLatitude(string $latitude) Return the first ChildDealerVersion filtered by the latitude column
 * @method     ChildDealerVersion findOneByLongitude(string $longitude) Return the first ChildDealerVersion filtered by the longitude column
 * @method     ChildDealerVersion findOneByCreatedAt(string $created_at) Return the first ChildDealerVersion filtered by the created_at column
 * @method     ChildDealerVersion findOneByUpdatedAt(string $updated_at) Return the first ChildDealerVersion filtered by the updated_at column
 * @method     ChildDealerVersion findOneByVersion(int $version) Return the first ChildDealerVersion filtered by the version column
 * @method     ChildDealerVersion findOneByVersionCreatedAt(string $version_created_at) Return the first ChildDealerVersion filtered by the version_created_at column
 * @method     ChildDealerVersion findOneByVersionCreatedBy(string $version_created_by) Return the first ChildDealerVersion filtered by the version_created_by column
 * @method     ChildDealerVersion findOneByDealerShedulesIds(array $dealer_shedules_ids) Return the first ChildDealerVersion filtered by the dealer_shedules_ids column
 * @method     ChildDealerVersion findOneByDealerShedulesVersions(array $dealer_shedules_versions) Return the first ChildDealerVersion filtered by the dealer_shedules_versions column
 * @method     ChildDealerVersion findOneByDealerContactIds(array $dealer_contact_ids) Return the first ChildDealerVersion filtered by the dealer_contact_ids column
 * @method     ChildDealerVersion findOneByDealerContactVersions(array $dealer_contact_versions) Return the first ChildDealerVersion filtered by the dealer_contact_versions column
 * @method     ChildDealerVersion findOneByDealerContentIds(array $dealer_content_ids) Return the first ChildDealerVersion filtered by the dealer_content_ids column
 * @method     ChildDealerVersion findOneByDealerContentVersions(array $dealer_content_versions) Return the first ChildDealerVersion filtered by the dealer_content_versions column
 * @method     ChildDealerVersion findOneByDealerFolderIds(array $dealer_folder_ids) Return the first ChildDealerVersion filtered by the dealer_folder_ids column
 * @method     ChildDealerVersion findOneByDealerFolderVersions(array $dealer_folder_versions) Return the first ChildDealerVersion filtered by the dealer_folder_versions column
 * @method     ChildDealerVersion findOneByDealerBrandIds(array $dealer_brand_ids) Return the first ChildDealerVersion filtered by the dealer_brand_ids column
 * @method     ChildDealerVersion findOneByDealerBrandVersions(array $dealer_brand_versions) Return the first ChildDealerVersion filtered by the dealer_brand_versions column
 * @method     ChildDealerVersion findOneByDealerProductIds(array $dealer_product_ids) Return the first ChildDealerVersion filtered by the dealer_product_ids column
 * @method     ChildDealerVersion findOneByDealerProductVersions(array $dealer_product_versions) Return the first ChildDealerVersion filtered by the dealer_product_versions column
 *
 * @method     array findById(int $id) Return ChildDealerVersion objects filtered by the id column
 * @method     array findByVisible(int $visible) Return ChildDealerVersion objects filtered by the visible column
 * @method     array findByAddress1(string $address1) Return ChildDealerVersion objects filtered by the address1 column
 * @method     array findByAddress2(string $address2) Return ChildDealerVersion objects filtered by the address2 column
 * @method     array findByAddress3(string $address3) Return ChildDealerVersion objects filtered by the address3 column
 * @method     array findByZipcode(string $zipcode) Return ChildDealerVersion objects filtered by the zipcode column
 * @method     array findByCity(string $city) Return ChildDealerVersion objects filtered by the city column
 * @method     array findByCountryId(int $country_id) Return ChildDealerVersion objects filtered by the country_id column
 * @method     array findByLatitude(string $latitude) Return ChildDealerVersion objects filtered by the latitude column
 * @method     array findByLongitude(string $longitude) Return ChildDealerVersion objects filtered by the longitude column
 * @method     array findByCreatedAt(string $created_at) Return ChildDealerVersion objects filtered by the created_at column
 * @method     array findByUpdatedAt(string $updated_at) Return ChildDealerVersion objects filtered by the updated_at column
 * @method     array findByVersion(int $version) Return ChildDealerVersion objects filtered by the version column
 * @method     array findByVersionCreatedAt(string $version_created_at) Return ChildDealerVersion objects filtered by the version_created_at column
 * @method     array findByVersionCreatedBy(string $version_created_by) Return ChildDealerVersion objects filtered by the version_created_by column
 * @method     array findByDealerShedulesIds(array $dealer_shedules_ids) Return ChildDealerVersion objects filtered by the dealer_shedules_ids column
 * @method     array findByDealerShedulesVersions(array $dealer_shedules_versions) Return ChildDealerVersion objects filtered by the dealer_shedules_versions column
 * @method     array findByDealerContactIds(array $dealer_contact_ids) Return ChildDealerVersion objects filtered by the dealer_contact_ids column
 * @method     array findByDealerContactVersions(array $dealer_contact_versions) Return ChildDealerVersion objects filtered by the dealer_contact_versions column
 * @method     array findByDealerContentIds(array $dealer_content_ids) Return ChildDealerVersion objects filtered by the dealer_content_ids column
 * @method     array findByDealerContentVersions(array $dealer_content_versions) Return ChildDealerVersion objects filtered by the dealer_content_versions column
 * @method     array findByDealerFolderIds(array $dealer_folder_ids) Return ChildDealerVersion objects filtered by the dealer_folder_ids column
 * @method     array findByDealerFolderVersions(array $dealer_folder_versions) Return ChildDealerVersion objects filtered by the dealer_folder_versions column
 * @method     array findByDealerBrandIds(array $dealer_brand_ids) Return ChildDealerVersion objects filtered by the dealer_brand_ids column
 * @method     array findByDealerBrandVersions(array $dealer_brand_versions) Return ChildDealerVersion objects filtered by the dealer_brand_versions column
 * @method     array findByDealerProductIds(array $dealer_product_ids) Return ChildDealerVersion objects filtered by the dealer_product_ids column
 * @method     array findByDealerProductVersions(array $dealer_product_versions) Return ChildDealerVersion objects filtered by the dealer_product_versions column
 *
 */
abstract class DealerVersionQuery extends ModelCriteria
{

    /**
     * Initializes internal state of \Dealer\Model\Base\DealerVersionQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'thelia', $modelName = '\\Dealer\\Model\\DealerVersion', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildDealerVersionQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildDealerVersionQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \Dealer\Model\DealerVersionQuery) {
            return $criteria;
        }
        $query = new \Dealer\Model\DealerVersionQuery();
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
     * @return ChildDealerVersion|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = DealerVersionTableMap::getInstanceFromPool(serialize(array((string) $key[0], (string) $key[1]))))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(DealerVersionTableMap::DATABASE_NAME);
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
     * @return   ChildDealerVersion A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, VISIBLE, ADDRESS1, ADDRESS2, ADDRESS3, ZIPCODE, CITY, COUNTRY_ID, LATITUDE, LONGITUDE, CREATED_AT, UPDATED_AT, VERSION, VERSION_CREATED_AT, VERSION_CREATED_BY, DEALER_SHEDULES_IDS, DEALER_SHEDULES_VERSIONS, DEALER_CONTACT_IDS, DEALER_CONTACT_VERSIONS, DEALER_CONTENT_IDS, DEALER_CONTENT_VERSIONS, DEALER_FOLDER_IDS, DEALER_FOLDER_VERSIONS, DEALER_BRAND_IDS, DEALER_BRAND_VERSIONS, DEALER_PRODUCT_IDS, DEALER_PRODUCT_VERSIONS FROM dealer_version WHERE ID = :p0 AND VERSION = :p1';
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
            $obj = new ChildDealerVersion();
            $obj->hydrate($row);
            DealerVersionTableMap::addInstanceToPool($obj, serialize(array((string) $key[0], (string) $key[1])));
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
     * @return ChildDealerVersion|array|mixed the result, formatted by the current formatter
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
     * @return ChildDealerVersionQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {
        $this->addUsingAlias(DealerVersionTableMap::ID, $key[0], Criteria::EQUAL);
        $this->addUsingAlias(DealerVersionTableMap::VERSION, $key[1], Criteria::EQUAL);

        return $this;
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildDealerVersionQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {
        if (empty($keys)) {
            return $this->add(null, '1<>1', Criteria::CUSTOM);
        }
        foreach ($keys as $key) {
            $cton0 = $this->getNewCriterion(DealerVersionTableMap::ID, $key[0], Criteria::EQUAL);
            $cton1 = $this->getNewCriterion(DealerVersionTableMap::VERSION, $key[1], Criteria::EQUAL);
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
     * @see       filterByDealer()
     *
     * @param     mixed $id The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDealerVersionQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(DealerVersionTableMap::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(DealerVersionTableMap::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DealerVersionTableMap::ID, $id, $comparison);
    }

    /**
     * Filter the query on the visible column
     *
     * Example usage:
     * <code>
     * $query->filterByVisible(1234); // WHERE visible = 1234
     * $query->filterByVisible(array(12, 34)); // WHERE visible IN (12, 34)
     * $query->filterByVisible(array('min' => 12)); // WHERE visible > 12
     * </code>
     *
     * @param     mixed $visible The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDealerVersionQuery The current query, for fluid interface
     */
    public function filterByVisible($visible = null, $comparison = null)
    {
        if (is_array($visible)) {
            $useMinMax = false;
            if (isset($visible['min'])) {
                $this->addUsingAlias(DealerVersionTableMap::VISIBLE, $visible['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($visible['max'])) {
                $this->addUsingAlias(DealerVersionTableMap::VISIBLE, $visible['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DealerVersionTableMap::VISIBLE, $visible, $comparison);
    }

    /**
     * Filter the query on the address1 column
     *
     * Example usage:
     * <code>
     * $query->filterByAddress1('fooValue');   // WHERE address1 = 'fooValue'
     * $query->filterByAddress1('%fooValue%'); // WHERE address1 LIKE '%fooValue%'
     * </code>
     *
     * @param     string $address1 The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDealerVersionQuery The current query, for fluid interface
     */
    public function filterByAddress1($address1 = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($address1)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $address1)) {
                $address1 = str_replace('*', '%', $address1);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(DealerVersionTableMap::ADDRESS1, $address1, $comparison);
    }

    /**
     * Filter the query on the address2 column
     *
     * Example usage:
     * <code>
     * $query->filterByAddress2('fooValue');   // WHERE address2 = 'fooValue'
     * $query->filterByAddress2('%fooValue%'); // WHERE address2 LIKE '%fooValue%'
     * </code>
     *
     * @param     string $address2 The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDealerVersionQuery The current query, for fluid interface
     */
    public function filterByAddress2($address2 = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($address2)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $address2)) {
                $address2 = str_replace('*', '%', $address2);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(DealerVersionTableMap::ADDRESS2, $address2, $comparison);
    }

    /**
     * Filter the query on the address3 column
     *
     * Example usage:
     * <code>
     * $query->filterByAddress3('fooValue');   // WHERE address3 = 'fooValue'
     * $query->filterByAddress3('%fooValue%'); // WHERE address3 LIKE '%fooValue%'
     * </code>
     *
     * @param     string $address3 The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDealerVersionQuery The current query, for fluid interface
     */
    public function filterByAddress3($address3 = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($address3)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $address3)) {
                $address3 = str_replace('*', '%', $address3);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(DealerVersionTableMap::ADDRESS3, $address3, $comparison);
    }

    /**
     * Filter the query on the zipcode column
     *
     * Example usage:
     * <code>
     * $query->filterByZipcode('fooValue');   // WHERE zipcode = 'fooValue'
     * $query->filterByZipcode('%fooValue%'); // WHERE zipcode LIKE '%fooValue%'
     * </code>
     *
     * @param     string $zipcode The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDealerVersionQuery The current query, for fluid interface
     */
    public function filterByZipcode($zipcode = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($zipcode)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $zipcode)) {
                $zipcode = str_replace('*', '%', $zipcode);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(DealerVersionTableMap::ZIPCODE, $zipcode, $comparison);
    }

    /**
     * Filter the query on the city column
     *
     * Example usage:
     * <code>
     * $query->filterByCity('fooValue');   // WHERE city = 'fooValue'
     * $query->filterByCity('%fooValue%'); // WHERE city LIKE '%fooValue%'
     * </code>
     *
     * @param     string $city The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDealerVersionQuery The current query, for fluid interface
     */
    public function filterByCity($city = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($city)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $city)) {
                $city = str_replace('*', '%', $city);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(DealerVersionTableMap::CITY, $city, $comparison);
    }

    /**
     * Filter the query on the country_id column
     *
     * Example usage:
     * <code>
     * $query->filterByCountryId(1234); // WHERE country_id = 1234
     * $query->filterByCountryId(array(12, 34)); // WHERE country_id IN (12, 34)
     * $query->filterByCountryId(array('min' => 12)); // WHERE country_id > 12
     * </code>
     *
     * @param     mixed $countryId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDealerVersionQuery The current query, for fluid interface
     */
    public function filterByCountryId($countryId = null, $comparison = null)
    {
        if (is_array($countryId)) {
            $useMinMax = false;
            if (isset($countryId['min'])) {
                $this->addUsingAlias(DealerVersionTableMap::COUNTRY_ID, $countryId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($countryId['max'])) {
                $this->addUsingAlias(DealerVersionTableMap::COUNTRY_ID, $countryId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DealerVersionTableMap::COUNTRY_ID, $countryId, $comparison);
    }

    /**
     * Filter the query on the latitude column
     *
     * Example usage:
     * <code>
     * $query->filterByLatitude(1234); // WHERE latitude = 1234
     * $query->filterByLatitude(array(12, 34)); // WHERE latitude IN (12, 34)
     * $query->filterByLatitude(array('min' => 12)); // WHERE latitude > 12
     * </code>
     *
     * @param     mixed $latitude The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDealerVersionQuery The current query, for fluid interface
     */
    public function filterByLatitude($latitude = null, $comparison = null)
    {
        if (is_array($latitude)) {
            $useMinMax = false;
            if (isset($latitude['min'])) {
                $this->addUsingAlias(DealerVersionTableMap::LATITUDE, $latitude['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($latitude['max'])) {
                $this->addUsingAlias(DealerVersionTableMap::LATITUDE, $latitude['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DealerVersionTableMap::LATITUDE, $latitude, $comparison);
    }

    /**
     * Filter the query on the longitude column
     *
     * Example usage:
     * <code>
     * $query->filterByLongitude(1234); // WHERE longitude = 1234
     * $query->filterByLongitude(array(12, 34)); // WHERE longitude IN (12, 34)
     * $query->filterByLongitude(array('min' => 12)); // WHERE longitude > 12
     * </code>
     *
     * @param     mixed $longitude The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDealerVersionQuery The current query, for fluid interface
     */
    public function filterByLongitude($longitude = null, $comparison = null)
    {
        if (is_array($longitude)) {
            $useMinMax = false;
            if (isset($longitude['min'])) {
                $this->addUsingAlias(DealerVersionTableMap::LONGITUDE, $longitude['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($longitude['max'])) {
                $this->addUsingAlias(DealerVersionTableMap::LONGITUDE, $longitude['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DealerVersionTableMap::LONGITUDE, $longitude, $comparison);
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
     * @return ChildDealerVersionQuery The current query, for fluid interface
     */
    public function filterByCreatedAt($createdAt = null, $comparison = null)
    {
        if (is_array($createdAt)) {
            $useMinMax = false;
            if (isset($createdAt['min'])) {
                $this->addUsingAlias(DealerVersionTableMap::CREATED_AT, $createdAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdAt['max'])) {
                $this->addUsingAlias(DealerVersionTableMap::CREATED_AT, $createdAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DealerVersionTableMap::CREATED_AT, $createdAt, $comparison);
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
     * @return ChildDealerVersionQuery The current query, for fluid interface
     */
    public function filterByUpdatedAt($updatedAt = null, $comparison = null)
    {
        if (is_array($updatedAt)) {
            $useMinMax = false;
            if (isset($updatedAt['min'])) {
                $this->addUsingAlias(DealerVersionTableMap::UPDATED_AT, $updatedAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($updatedAt['max'])) {
                $this->addUsingAlias(DealerVersionTableMap::UPDATED_AT, $updatedAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DealerVersionTableMap::UPDATED_AT, $updatedAt, $comparison);
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
     * @return ChildDealerVersionQuery The current query, for fluid interface
     */
    public function filterByVersion($version = null, $comparison = null)
    {
        if (is_array($version)) {
            $useMinMax = false;
            if (isset($version['min'])) {
                $this->addUsingAlias(DealerVersionTableMap::VERSION, $version['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($version['max'])) {
                $this->addUsingAlias(DealerVersionTableMap::VERSION, $version['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DealerVersionTableMap::VERSION, $version, $comparison);
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
     * @return ChildDealerVersionQuery The current query, for fluid interface
     */
    public function filterByVersionCreatedAt($versionCreatedAt = null, $comparison = null)
    {
        if (is_array($versionCreatedAt)) {
            $useMinMax = false;
            if (isset($versionCreatedAt['min'])) {
                $this->addUsingAlias(DealerVersionTableMap::VERSION_CREATED_AT, $versionCreatedAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($versionCreatedAt['max'])) {
                $this->addUsingAlias(DealerVersionTableMap::VERSION_CREATED_AT, $versionCreatedAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DealerVersionTableMap::VERSION_CREATED_AT, $versionCreatedAt, $comparison);
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
     * @return ChildDealerVersionQuery The current query, for fluid interface
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

        return $this->addUsingAlias(DealerVersionTableMap::VERSION_CREATED_BY, $versionCreatedBy, $comparison);
    }

    /**
     * Filter the query on the dealer_shedules_ids column
     *
     * @param     array $dealerShedulesIds The values to use as filter.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDealerVersionQuery The current query, for fluid interface
     */
    public function filterByDealerShedulesIds($dealerShedulesIds = null, $comparison = null)
    {
        $key = $this->getAliasedColName(DealerVersionTableMap::DEALER_SHEDULES_IDS);
        if (null === $comparison || $comparison == Criteria::CONTAINS_ALL) {
            foreach ($dealerShedulesIds as $value) {
                $value = '%| ' . $value . ' |%';
                if ($this->containsKey($key)) {
                    $this->addAnd($key, $value, Criteria::LIKE);
                } else {
                    $this->add($key, $value, Criteria::LIKE);
                }
            }

            return $this;
        } elseif ($comparison == Criteria::CONTAINS_SOME) {
            foreach ($dealerShedulesIds as $value) {
                $value = '%| ' . $value . ' |%';
                if ($this->containsKey($key)) {
                    $this->addOr($key, $value, Criteria::LIKE);
                } else {
                    $this->add($key, $value, Criteria::LIKE);
                }
            }

            return $this;
        } elseif ($comparison == Criteria::CONTAINS_NONE) {
            foreach ($dealerShedulesIds as $value) {
                $value = '%| ' . $value . ' |%';
                if ($this->containsKey($key)) {
                    $this->addAnd($key, $value, Criteria::NOT_LIKE);
                } else {
                    $this->add($key, $value, Criteria::NOT_LIKE);
                }
            }
            $this->addOr($key, null, Criteria::ISNULL);

            return $this;
        }

        return $this->addUsingAlias(DealerVersionTableMap::DEALER_SHEDULES_IDS, $dealerShedulesIds, $comparison);
    }

    /**
     * Filter the query on the dealer_shedules_ids column
     * @param     mixed $dealerShedulesIds The value to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::CONTAINS_ALL
     *
     * @return ChildDealerVersionQuery The current query, for fluid interface
     */
    public function filterByDealerShedulesId($dealerShedulesIds = null, $comparison = null)
    {
        if (null === $comparison || $comparison == Criteria::CONTAINS_ALL) {
            if (is_scalar($dealerShedulesIds)) {
                $dealerShedulesIds = '%| ' . $dealerShedulesIds . ' |%';
                $comparison = Criteria::LIKE;
            }
        } elseif ($comparison == Criteria::CONTAINS_NONE) {
            $dealerShedulesIds = '%| ' . $dealerShedulesIds . ' |%';
            $comparison = Criteria::NOT_LIKE;
            $key = $this->getAliasedColName(DealerVersionTableMap::DEALER_SHEDULES_IDS);
            if ($this->containsKey($key)) {
                $this->addAnd($key, $dealerShedulesIds, $comparison);
            } else {
                $this->addAnd($key, $dealerShedulesIds, $comparison);
            }
            $this->addOr($key, null, Criteria::ISNULL);

            return $this;
        }

        return $this->addUsingAlias(DealerVersionTableMap::DEALER_SHEDULES_IDS, $dealerShedulesIds, $comparison);
    }

    /**
     * Filter the query on the dealer_shedules_versions column
     *
     * @param     array $dealerShedulesVersions The values to use as filter.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDealerVersionQuery The current query, for fluid interface
     */
    public function filterByDealerShedulesVersions($dealerShedulesVersions = null, $comparison = null)
    {
        $key = $this->getAliasedColName(DealerVersionTableMap::DEALER_SHEDULES_VERSIONS);
        if (null === $comparison || $comparison == Criteria::CONTAINS_ALL) {
            foreach ($dealerShedulesVersions as $value) {
                $value = '%| ' . $value . ' |%';
                if ($this->containsKey($key)) {
                    $this->addAnd($key, $value, Criteria::LIKE);
                } else {
                    $this->add($key, $value, Criteria::LIKE);
                }
            }

            return $this;
        } elseif ($comparison == Criteria::CONTAINS_SOME) {
            foreach ($dealerShedulesVersions as $value) {
                $value = '%| ' . $value . ' |%';
                if ($this->containsKey($key)) {
                    $this->addOr($key, $value, Criteria::LIKE);
                } else {
                    $this->add($key, $value, Criteria::LIKE);
                }
            }

            return $this;
        } elseif ($comparison == Criteria::CONTAINS_NONE) {
            foreach ($dealerShedulesVersions as $value) {
                $value = '%| ' . $value . ' |%';
                if ($this->containsKey($key)) {
                    $this->addAnd($key, $value, Criteria::NOT_LIKE);
                } else {
                    $this->add($key, $value, Criteria::NOT_LIKE);
                }
            }
            $this->addOr($key, null, Criteria::ISNULL);

            return $this;
        }

        return $this->addUsingAlias(DealerVersionTableMap::DEALER_SHEDULES_VERSIONS, $dealerShedulesVersions, $comparison);
    }

    /**
     * Filter the query on the dealer_shedules_versions column
     * @param     mixed $dealerShedulesVersions The value to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::CONTAINS_ALL
     *
     * @return ChildDealerVersionQuery The current query, for fluid interface
     */
    public function filterByDealerShedulesVersion($dealerShedulesVersions = null, $comparison = null)
    {
        if (null === $comparison || $comparison == Criteria::CONTAINS_ALL) {
            if (is_scalar($dealerShedulesVersions)) {
                $dealerShedulesVersions = '%| ' . $dealerShedulesVersions . ' |%';
                $comparison = Criteria::LIKE;
            }
        } elseif ($comparison == Criteria::CONTAINS_NONE) {
            $dealerShedulesVersions = '%| ' . $dealerShedulesVersions . ' |%';
            $comparison = Criteria::NOT_LIKE;
            $key = $this->getAliasedColName(DealerVersionTableMap::DEALER_SHEDULES_VERSIONS);
            if ($this->containsKey($key)) {
                $this->addAnd($key, $dealerShedulesVersions, $comparison);
            } else {
                $this->addAnd($key, $dealerShedulesVersions, $comparison);
            }
            $this->addOr($key, null, Criteria::ISNULL);

            return $this;
        }

        return $this->addUsingAlias(DealerVersionTableMap::DEALER_SHEDULES_VERSIONS, $dealerShedulesVersions, $comparison);
    }

    /**
     * Filter the query on the dealer_contact_ids column
     *
     * @param     array $dealerContactIds The values to use as filter.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDealerVersionQuery The current query, for fluid interface
     */
    public function filterByDealerContactIds($dealerContactIds = null, $comparison = null)
    {
        $key = $this->getAliasedColName(DealerVersionTableMap::DEALER_CONTACT_IDS);
        if (null === $comparison || $comparison == Criteria::CONTAINS_ALL) {
            foreach ($dealerContactIds as $value) {
                $value = '%| ' . $value . ' |%';
                if ($this->containsKey($key)) {
                    $this->addAnd($key, $value, Criteria::LIKE);
                } else {
                    $this->add($key, $value, Criteria::LIKE);
                }
            }

            return $this;
        } elseif ($comparison == Criteria::CONTAINS_SOME) {
            foreach ($dealerContactIds as $value) {
                $value = '%| ' . $value . ' |%';
                if ($this->containsKey($key)) {
                    $this->addOr($key, $value, Criteria::LIKE);
                } else {
                    $this->add($key, $value, Criteria::LIKE);
                }
            }

            return $this;
        } elseif ($comparison == Criteria::CONTAINS_NONE) {
            foreach ($dealerContactIds as $value) {
                $value = '%| ' . $value . ' |%';
                if ($this->containsKey($key)) {
                    $this->addAnd($key, $value, Criteria::NOT_LIKE);
                } else {
                    $this->add($key, $value, Criteria::NOT_LIKE);
                }
            }
            $this->addOr($key, null, Criteria::ISNULL);

            return $this;
        }

        return $this->addUsingAlias(DealerVersionTableMap::DEALER_CONTACT_IDS, $dealerContactIds, $comparison);
    }

    /**
     * Filter the query on the dealer_contact_ids column
     * @param     mixed $dealerContactIds The value to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::CONTAINS_ALL
     *
     * @return ChildDealerVersionQuery The current query, for fluid interface
     */
    public function filterByDealerContactId($dealerContactIds = null, $comparison = null)
    {
        if (null === $comparison || $comparison == Criteria::CONTAINS_ALL) {
            if (is_scalar($dealerContactIds)) {
                $dealerContactIds = '%| ' . $dealerContactIds . ' |%';
                $comparison = Criteria::LIKE;
            }
        } elseif ($comparison == Criteria::CONTAINS_NONE) {
            $dealerContactIds = '%| ' . $dealerContactIds . ' |%';
            $comparison = Criteria::NOT_LIKE;
            $key = $this->getAliasedColName(DealerVersionTableMap::DEALER_CONTACT_IDS);
            if ($this->containsKey($key)) {
                $this->addAnd($key, $dealerContactIds, $comparison);
            } else {
                $this->addAnd($key, $dealerContactIds, $comparison);
            }
            $this->addOr($key, null, Criteria::ISNULL);

            return $this;
        }

        return $this->addUsingAlias(DealerVersionTableMap::DEALER_CONTACT_IDS, $dealerContactIds, $comparison);
    }

    /**
     * Filter the query on the dealer_contact_versions column
     *
     * @param     array $dealerContactVersions The values to use as filter.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDealerVersionQuery The current query, for fluid interface
     */
    public function filterByDealerContactVersions($dealerContactVersions = null, $comparison = null)
    {
        $key = $this->getAliasedColName(DealerVersionTableMap::DEALER_CONTACT_VERSIONS);
        if (null === $comparison || $comparison == Criteria::CONTAINS_ALL) {
            foreach ($dealerContactVersions as $value) {
                $value = '%| ' . $value . ' |%';
                if ($this->containsKey($key)) {
                    $this->addAnd($key, $value, Criteria::LIKE);
                } else {
                    $this->add($key, $value, Criteria::LIKE);
                }
            }

            return $this;
        } elseif ($comparison == Criteria::CONTAINS_SOME) {
            foreach ($dealerContactVersions as $value) {
                $value = '%| ' . $value . ' |%';
                if ($this->containsKey($key)) {
                    $this->addOr($key, $value, Criteria::LIKE);
                } else {
                    $this->add($key, $value, Criteria::LIKE);
                }
            }

            return $this;
        } elseif ($comparison == Criteria::CONTAINS_NONE) {
            foreach ($dealerContactVersions as $value) {
                $value = '%| ' . $value . ' |%';
                if ($this->containsKey($key)) {
                    $this->addAnd($key, $value, Criteria::NOT_LIKE);
                } else {
                    $this->add($key, $value, Criteria::NOT_LIKE);
                }
            }
            $this->addOr($key, null, Criteria::ISNULL);

            return $this;
        }

        return $this->addUsingAlias(DealerVersionTableMap::DEALER_CONTACT_VERSIONS, $dealerContactVersions, $comparison);
    }

    /**
     * Filter the query on the dealer_contact_versions column
     * @param     mixed $dealerContactVersions The value to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::CONTAINS_ALL
     *
     * @return ChildDealerVersionQuery The current query, for fluid interface
     */
    public function filterByDealerContactVersion($dealerContactVersions = null, $comparison = null)
    {
        if (null === $comparison || $comparison == Criteria::CONTAINS_ALL) {
            if (is_scalar($dealerContactVersions)) {
                $dealerContactVersions = '%| ' . $dealerContactVersions . ' |%';
                $comparison = Criteria::LIKE;
            }
        } elseif ($comparison == Criteria::CONTAINS_NONE) {
            $dealerContactVersions = '%| ' . $dealerContactVersions . ' |%';
            $comparison = Criteria::NOT_LIKE;
            $key = $this->getAliasedColName(DealerVersionTableMap::DEALER_CONTACT_VERSIONS);
            if ($this->containsKey($key)) {
                $this->addAnd($key, $dealerContactVersions, $comparison);
            } else {
                $this->addAnd($key, $dealerContactVersions, $comparison);
            }
            $this->addOr($key, null, Criteria::ISNULL);

            return $this;
        }

        return $this->addUsingAlias(DealerVersionTableMap::DEALER_CONTACT_VERSIONS, $dealerContactVersions, $comparison);
    }

    /**
     * Filter the query on the dealer_content_ids column
     *
     * @param     array $dealerContentIds The values to use as filter.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDealerVersionQuery The current query, for fluid interface
     */
    public function filterByDealerContentIds($dealerContentIds = null, $comparison = null)
    {
        $key = $this->getAliasedColName(DealerVersionTableMap::DEALER_CONTENT_IDS);
        if (null === $comparison || $comparison == Criteria::CONTAINS_ALL) {
            foreach ($dealerContentIds as $value) {
                $value = '%| ' . $value . ' |%';
                if ($this->containsKey($key)) {
                    $this->addAnd($key, $value, Criteria::LIKE);
                } else {
                    $this->add($key, $value, Criteria::LIKE);
                }
            }

            return $this;
        } elseif ($comparison == Criteria::CONTAINS_SOME) {
            foreach ($dealerContentIds as $value) {
                $value = '%| ' . $value . ' |%';
                if ($this->containsKey($key)) {
                    $this->addOr($key, $value, Criteria::LIKE);
                } else {
                    $this->add($key, $value, Criteria::LIKE);
                }
            }

            return $this;
        } elseif ($comparison == Criteria::CONTAINS_NONE) {
            foreach ($dealerContentIds as $value) {
                $value = '%| ' . $value . ' |%';
                if ($this->containsKey($key)) {
                    $this->addAnd($key, $value, Criteria::NOT_LIKE);
                } else {
                    $this->add($key, $value, Criteria::NOT_LIKE);
                }
            }
            $this->addOr($key, null, Criteria::ISNULL);

            return $this;
        }

        return $this->addUsingAlias(DealerVersionTableMap::DEALER_CONTENT_IDS, $dealerContentIds, $comparison);
    }

    /**
     * Filter the query on the dealer_content_ids column
     * @param     mixed $dealerContentIds The value to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::CONTAINS_ALL
     *
     * @return ChildDealerVersionQuery The current query, for fluid interface
     */
    public function filterByDealerContentId($dealerContentIds = null, $comparison = null)
    {
        if (null === $comparison || $comparison == Criteria::CONTAINS_ALL) {
            if (is_scalar($dealerContentIds)) {
                $dealerContentIds = '%| ' . $dealerContentIds . ' |%';
                $comparison = Criteria::LIKE;
            }
        } elseif ($comparison == Criteria::CONTAINS_NONE) {
            $dealerContentIds = '%| ' . $dealerContentIds . ' |%';
            $comparison = Criteria::NOT_LIKE;
            $key = $this->getAliasedColName(DealerVersionTableMap::DEALER_CONTENT_IDS);
            if ($this->containsKey($key)) {
                $this->addAnd($key, $dealerContentIds, $comparison);
            } else {
                $this->addAnd($key, $dealerContentIds, $comparison);
            }
            $this->addOr($key, null, Criteria::ISNULL);

            return $this;
        }

        return $this->addUsingAlias(DealerVersionTableMap::DEALER_CONTENT_IDS, $dealerContentIds, $comparison);
    }

    /**
     * Filter the query on the dealer_content_versions column
     *
     * @param     array $dealerContentVersions The values to use as filter.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDealerVersionQuery The current query, for fluid interface
     */
    public function filterByDealerContentVersions($dealerContentVersions = null, $comparison = null)
    {
        $key = $this->getAliasedColName(DealerVersionTableMap::DEALER_CONTENT_VERSIONS);
        if (null === $comparison || $comparison == Criteria::CONTAINS_ALL) {
            foreach ($dealerContentVersions as $value) {
                $value = '%| ' . $value . ' |%';
                if ($this->containsKey($key)) {
                    $this->addAnd($key, $value, Criteria::LIKE);
                } else {
                    $this->add($key, $value, Criteria::LIKE);
                }
            }

            return $this;
        } elseif ($comparison == Criteria::CONTAINS_SOME) {
            foreach ($dealerContentVersions as $value) {
                $value = '%| ' . $value . ' |%';
                if ($this->containsKey($key)) {
                    $this->addOr($key, $value, Criteria::LIKE);
                } else {
                    $this->add($key, $value, Criteria::LIKE);
                }
            }

            return $this;
        } elseif ($comparison == Criteria::CONTAINS_NONE) {
            foreach ($dealerContentVersions as $value) {
                $value = '%| ' . $value . ' |%';
                if ($this->containsKey($key)) {
                    $this->addAnd($key, $value, Criteria::NOT_LIKE);
                } else {
                    $this->add($key, $value, Criteria::NOT_LIKE);
                }
            }
            $this->addOr($key, null, Criteria::ISNULL);

            return $this;
        }

        return $this->addUsingAlias(DealerVersionTableMap::DEALER_CONTENT_VERSIONS, $dealerContentVersions, $comparison);
    }

    /**
     * Filter the query on the dealer_content_versions column
     * @param     mixed $dealerContentVersions The value to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::CONTAINS_ALL
     *
     * @return ChildDealerVersionQuery The current query, for fluid interface
     */
    public function filterByDealerContentVersion($dealerContentVersions = null, $comparison = null)
    {
        if (null === $comparison || $comparison == Criteria::CONTAINS_ALL) {
            if (is_scalar($dealerContentVersions)) {
                $dealerContentVersions = '%| ' . $dealerContentVersions . ' |%';
                $comparison = Criteria::LIKE;
            }
        } elseif ($comparison == Criteria::CONTAINS_NONE) {
            $dealerContentVersions = '%| ' . $dealerContentVersions . ' |%';
            $comparison = Criteria::NOT_LIKE;
            $key = $this->getAliasedColName(DealerVersionTableMap::DEALER_CONTENT_VERSIONS);
            if ($this->containsKey($key)) {
                $this->addAnd($key, $dealerContentVersions, $comparison);
            } else {
                $this->addAnd($key, $dealerContentVersions, $comparison);
            }
            $this->addOr($key, null, Criteria::ISNULL);

            return $this;
        }

        return $this->addUsingAlias(DealerVersionTableMap::DEALER_CONTENT_VERSIONS, $dealerContentVersions, $comparison);
    }

    /**
     * Filter the query on the dealer_folder_ids column
     *
     * @param     array $dealerFolderIds The values to use as filter.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDealerVersionQuery The current query, for fluid interface
     */
    public function filterByDealerFolderIds($dealerFolderIds = null, $comparison = null)
    {
        $key = $this->getAliasedColName(DealerVersionTableMap::DEALER_FOLDER_IDS);
        if (null === $comparison || $comparison == Criteria::CONTAINS_ALL) {
            foreach ($dealerFolderIds as $value) {
                $value = '%| ' . $value . ' |%';
                if ($this->containsKey($key)) {
                    $this->addAnd($key, $value, Criteria::LIKE);
                } else {
                    $this->add($key, $value, Criteria::LIKE);
                }
            }

            return $this;
        } elseif ($comparison == Criteria::CONTAINS_SOME) {
            foreach ($dealerFolderIds as $value) {
                $value = '%| ' . $value . ' |%';
                if ($this->containsKey($key)) {
                    $this->addOr($key, $value, Criteria::LIKE);
                } else {
                    $this->add($key, $value, Criteria::LIKE);
                }
            }

            return $this;
        } elseif ($comparison == Criteria::CONTAINS_NONE) {
            foreach ($dealerFolderIds as $value) {
                $value = '%| ' . $value . ' |%';
                if ($this->containsKey($key)) {
                    $this->addAnd($key, $value, Criteria::NOT_LIKE);
                } else {
                    $this->add($key, $value, Criteria::NOT_LIKE);
                }
            }
            $this->addOr($key, null, Criteria::ISNULL);

            return $this;
        }

        return $this->addUsingAlias(DealerVersionTableMap::DEALER_FOLDER_IDS, $dealerFolderIds, $comparison);
    }

    /**
     * Filter the query on the dealer_folder_ids column
     * @param     mixed $dealerFolderIds The value to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::CONTAINS_ALL
     *
     * @return ChildDealerVersionQuery The current query, for fluid interface
     */
    public function filterByDealerFolderId($dealerFolderIds = null, $comparison = null)
    {
        if (null === $comparison || $comparison == Criteria::CONTAINS_ALL) {
            if (is_scalar($dealerFolderIds)) {
                $dealerFolderIds = '%| ' . $dealerFolderIds . ' |%';
                $comparison = Criteria::LIKE;
            }
        } elseif ($comparison == Criteria::CONTAINS_NONE) {
            $dealerFolderIds = '%| ' . $dealerFolderIds . ' |%';
            $comparison = Criteria::NOT_LIKE;
            $key = $this->getAliasedColName(DealerVersionTableMap::DEALER_FOLDER_IDS);
            if ($this->containsKey($key)) {
                $this->addAnd($key, $dealerFolderIds, $comparison);
            } else {
                $this->addAnd($key, $dealerFolderIds, $comparison);
            }
            $this->addOr($key, null, Criteria::ISNULL);

            return $this;
        }

        return $this->addUsingAlias(DealerVersionTableMap::DEALER_FOLDER_IDS, $dealerFolderIds, $comparison);
    }

    /**
     * Filter the query on the dealer_folder_versions column
     *
     * @param     array $dealerFolderVersions The values to use as filter.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDealerVersionQuery The current query, for fluid interface
     */
    public function filterByDealerFolderVersions($dealerFolderVersions = null, $comparison = null)
    {
        $key = $this->getAliasedColName(DealerVersionTableMap::DEALER_FOLDER_VERSIONS);
        if (null === $comparison || $comparison == Criteria::CONTAINS_ALL) {
            foreach ($dealerFolderVersions as $value) {
                $value = '%| ' . $value . ' |%';
                if ($this->containsKey($key)) {
                    $this->addAnd($key, $value, Criteria::LIKE);
                } else {
                    $this->add($key, $value, Criteria::LIKE);
                }
            }

            return $this;
        } elseif ($comparison == Criteria::CONTAINS_SOME) {
            foreach ($dealerFolderVersions as $value) {
                $value = '%| ' . $value . ' |%';
                if ($this->containsKey($key)) {
                    $this->addOr($key, $value, Criteria::LIKE);
                } else {
                    $this->add($key, $value, Criteria::LIKE);
                }
            }

            return $this;
        } elseif ($comparison == Criteria::CONTAINS_NONE) {
            foreach ($dealerFolderVersions as $value) {
                $value = '%| ' . $value . ' |%';
                if ($this->containsKey($key)) {
                    $this->addAnd($key, $value, Criteria::NOT_LIKE);
                } else {
                    $this->add($key, $value, Criteria::NOT_LIKE);
                }
            }
            $this->addOr($key, null, Criteria::ISNULL);

            return $this;
        }

        return $this->addUsingAlias(DealerVersionTableMap::DEALER_FOLDER_VERSIONS, $dealerFolderVersions, $comparison);
    }

    /**
     * Filter the query on the dealer_folder_versions column
     * @param     mixed $dealerFolderVersions The value to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::CONTAINS_ALL
     *
     * @return ChildDealerVersionQuery The current query, for fluid interface
     */
    public function filterByDealerFolderVersion($dealerFolderVersions = null, $comparison = null)
    {
        if (null === $comparison || $comparison == Criteria::CONTAINS_ALL) {
            if (is_scalar($dealerFolderVersions)) {
                $dealerFolderVersions = '%| ' . $dealerFolderVersions . ' |%';
                $comparison = Criteria::LIKE;
            }
        } elseif ($comparison == Criteria::CONTAINS_NONE) {
            $dealerFolderVersions = '%| ' . $dealerFolderVersions . ' |%';
            $comparison = Criteria::NOT_LIKE;
            $key = $this->getAliasedColName(DealerVersionTableMap::DEALER_FOLDER_VERSIONS);
            if ($this->containsKey($key)) {
                $this->addAnd($key, $dealerFolderVersions, $comparison);
            } else {
                $this->addAnd($key, $dealerFolderVersions, $comparison);
            }
            $this->addOr($key, null, Criteria::ISNULL);

            return $this;
        }

        return $this->addUsingAlias(DealerVersionTableMap::DEALER_FOLDER_VERSIONS, $dealerFolderVersions, $comparison);
    }

    /**
     * Filter the query on the dealer_brand_ids column
     *
     * @param     array $dealerBrandIds The values to use as filter.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDealerVersionQuery The current query, for fluid interface
     */
    public function filterByDealerBrandIds($dealerBrandIds = null, $comparison = null)
    {
        $key = $this->getAliasedColName(DealerVersionTableMap::DEALER_BRAND_IDS);
        if (null === $comparison || $comparison == Criteria::CONTAINS_ALL) {
            foreach ($dealerBrandIds as $value) {
                $value = '%| ' . $value . ' |%';
                if ($this->containsKey($key)) {
                    $this->addAnd($key, $value, Criteria::LIKE);
                } else {
                    $this->add($key, $value, Criteria::LIKE);
                }
            }

            return $this;
        } elseif ($comparison == Criteria::CONTAINS_SOME) {
            foreach ($dealerBrandIds as $value) {
                $value = '%| ' . $value . ' |%';
                if ($this->containsKey($key)) {
                    $this->addOr($key, $value, Criteria::LIKE);
                } else {
                    $this->add($key, $value, Criteria::LIKE);
                }
            }

            return $this;
        } elseif ($comparison == Criteria::CONTAINS_NONE) {
            foreach ($dealerBrandIds as $value) {
                $value = '%| ' . $value . ' |%';
                if ($this->containsKey($key)) {
                    $this->addAnd($key, $value, Criteria::NOT_LIKE);
                } else {
                    $this->add($key, $value, Criteria::NOT_LIKE);
                }
            }
            $this->addOr($key, null, Criteria::ISNULL);

            return $this;
        }

        return $this->addUsingAlias(DealerVersionTableMap::DEALER_BRAND_IDS, $dealerBrandIds, $comparison);
    }

    /**
     * Filter the query on the dealer_brand_ids column
     * @param     mixed $dealerBrandIds The value to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::CONTAINS_ALL
     *
     * @return ChildDealerVersionQuery The current query, for fluid interface
     */
    public function filterByDealerBrandId($dealerBrandIds = null, $comparison = null)
    {
        if (null === $comparison || $comparison == Criteria::CONTAINS_ALL) {
            if (is_scalar($dealerBrandIds)) {
                $dealerBrandIds = '%| ' . $dealerBrandIds . ' |%';
                $comparison = Criteria::LIKE;
            }
        } elseif ($comparison == Criteria::CONTAINS_NONE) {
            $dealerBrandIds = '%| ' . $dealerBrandIds . ' |%';
            $comparison = Criteria::NOT_LIKE;
            $key = $this->getAliasedColName(DealerVersionTableMap::DEALER_BRAND_IDS);
            if ($this->containsKey($key)) {
                $this->addAnd($key, $dealerBrandIds, $comparison);
            } else {
                $this->addAnd($key, $dealerBrandIds, $comparison);
            }
            $this->addOr($key, null, Criteria::ISNULL);

            return $this;
        }

        return $this->addUsingAlias(DealerVersionTableMap::DEALER_BRAND_IDS, $dealerBrandIds, $comparison);
    }

    /**
     * Filter the query on the dealer_brand_versions column
     *
     * @param     array $dealerBrandVersions The values to use as filter.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDealerVersionQuery The current query, for fluid interface
     */
    public function filterByDealerBrandVersions($dealerBrandVersions = null, $comparison = null)
    {
        $key = $this->getAliasedColName(DealerVersionTableMap::DEALER_BRAND_VERSIONS);
        if (null === $comparison || $comparison == Criteria::CONTAINS_ALL) {
            foreach ($dealerBrandVersions as $value) {
                $value = '%| ' . $value . ' |%';
                if ($this->containsKey($key)) {
                    $this->addAnd($key, $value, Criteria::LIKE);
                } else {
                    $this->add($key, $value, Criteria::LIKE);
                }
            }

            return $this;
        } elseif ($comparison == Criteria::CONTAINS_SOME) {
            foreach ($dealerBrandVersions as $value) {
                $value = '%| ' . $value . ' |%';
                if ($this->containsKey($key)) {
                    $this->addOr($key, $value, Criteria::LIKE);
                } else {
                    $this->add($key, $value, Criteria::LIKE);
                }
            }

            return $this;
        } elseif ($comparison == Criteria::CONTAINS_NONE) {
            foreach ($dealerBrandVersions as $value) {
                $value = '%| ' . $value . ' |%';
                if ($this->containsKey($key)) {
                    $this->addAnd($key, $value, Criteria::NOT_LIKE);
                } else {
                    $this->add($key, $value, Criteria::NOT_LIKE);
                }
            }
            $this->addOr($key, null, Criteria::ISNULL);

            return $this;
        }

        return $this->addUsingAlias(DealerVersionTableMap::DEALER_BRAND_VERSIONS, $dealerBrandVersions, $comparison);
    }

    /**
     * Filter the query on the dealer_brand_versions column
     * @param     mixed $dealerBrandVersions The value to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::CONTAINS_ALL
     *
     * @return ChildDealerVersionQuery The current query, for fluid interface
     */
    public function filterByDealerBrandVersion($dealerBrandVersions = null, $comparison = null)
    {
        if (null === $comparison || $comparison == Criteria::CONTAINS_ALL) {
            if (is_scalar($dealerBrandVersions)) {
                $dealerBrandVersions = '%| ' . $dealerBrandVersions . ' |%';
                $comparison = Criteria::LIKE;
            }
        } elseif ($comparison == Criteria::CONTAINS_NONE) {
            $dealerBrandVersions = '%| ' . $dealerBrandVersions . ' |%';
            $comparison = Criteria::NOT_LIKE;
            $key = $this->getAliasedColName(DealerVersionTableMap::DEALER_BRAND_VERSIONS);
            if ($this->containsKey($key)) {
                $this->addAnd($key, $dealerBrandVersions, $comparison);
            } else {
                $this->addAnd($key, $dealerBrandVersions, $comparison);
            }
            $this->addOr($key, null, Criteria::ISNULL);

            return $this;
        }

        return $this->addUsingAlias(DealerVersionTableMap::DEALER_BRAND_VERSIONS, $dealerBrandVersions, $comparison);
    }

    /**
     * Filter the query on the dealer_product_ids column
     *
     * @param     array $dealerProductIds The values to use as filter.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDealerVersionQuery The current query, for fluid interface
     */
    public function filterByDealerProductIds($dealerProductIds = null, $comparison = null)
    {
        $key = $this->getAliasedColName(DealerVersionTableMap::DEALER_PRODUCT_IDS);
        if (null === $comparison || $comparison == Criteria::CONTAINS_ALL) {
            foreach ($dealerProductIds as $value) {
                $value = '%| ' . $value . ' |%';
                if ($this->containsKey($key)) {
                    $this->addAnd($key, $value, Criteria::LIKE);
                } else {
                    $this->add($key, $value, Criteria::LIKE);
                }
            }

            return $this;
        } elseif ($comparison == Criteria::CONTAINS_SOME) {
            foreach ($dealerProductIds as $value) {
                $value = '%| ' . $value . ' |%';
                if ($this->containsKey($key)) {
                    $this->addOr($key, $value, Criteria::LIKE);
                } else {
                    $this->add($key, $value, Criteria::LIKE);
                }
            }

            return $this;
        } elseif ($comparison == Criteria::CONTAINS_NONE) {
            foreach ($dealerProductIds as $value) {
                $value = '%| ' . $value . ' |%';
                if ($this->containsKey($key)) {
                    $this->addAnd($key, $value, Criteria::NOT_LIKE);
                } else {
                    $this->add($key, $value, Criteria::NOT_LIKE);
                }
            }
            $this->addOr($key, null, Criteria::ISNULL);

            return $this;
        }

        return $this->addUsingAlias(DealerVersionTableMap::DEALER_PRODUCT_IDS, $dealerProductIds, $comparison);
    }

    /**
     * Filter the query on the dealer_product_ids column
     * @param     mixed $dealerProductIds The value to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::CONTAINS_ALL
     *
     * @return ChildDealerVersionQuery The current query, for fluid interface
     */
    public function filterByDealerProductId($dealerProductIds = null, $comparison = null)
    {
        if (null === $comparison || $comparison == Criteria::CONTAINS_ALL) {
            if (is_scalar($dealerProductIds)) {
                $dealerProductIds = '%| ' . $dealerProductIds . ' |%';
                $comparison = Criteria::LIKE;
            }
        } elseif ($comparison == Criteria::CONTAINS_NONE) {
            $dealerProductIds = '%| ' . $dealerProductIds . ' |%';
            $comparison = Criteria::NOT_LIKE;
            $key = $this->getAliasedColName(DealerVersionTableMap::DEALER_PRODUCT_IDS);
            if ($this->containsKey($key)) {
                $this->addAnd($key, $dealerProductIds, $comparison);
            } else {
                $this->addAnd($key, $dealerProductIds, $comparison);
            }
            $this->addOr($key, null, Criteria::ISNULL);

            return $this;
        }

        return $this->addUsingAlias(DealerVersionTableMap::DEALER_PRODUCT_IDS, $dealerProductIds, $comparison);
    }

    /**
     * Filter the query on the dealer_product_versions column
     *
     * @param     array $dealerProductVersions The values to use as filter.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDealerVersionQuery The current query, for fluid interface
     */
    public function filterByDealerProductVersions($dealerProductVersions = null, $comparison = null)
    {
        $key = $this->getAliasedColName(DealerVersionTableMap::DEALER_PRODUCT_VERSIONS);
        if (null === $comparison || $comparison == Criteria::CONTAINS_ALL) {
            foreach ($dealerProductVersions as $value) {
                $value = '%| ' . $value . ' |%';
                if ($this->containsKey($key)) {
                    $this->addAnd($key, $value, Criteria::LIKE);
                } else {
                    $this->add($key, $value, Criteria::LIKE);
                }
            }

            return $this;
        } elseif ($comparison == Criteria::CONTAINS_SOME) {
            foreach ($dealerProductVersions as $value) {
                $value = '%| ' . $value . ' |%';
                if ($this->containsKey($key)) {
                    $this->addOr($key, $value, Criteria::LIKE);
                } else {
                    $this->add($key, $value, Criteria::LIKE);
                }
            }

            return $this;
        } elseif ($comparison == Criteria::CONTAINS_NONE) {
            foreach ($dealerProductVersions as $value) {
                $value = '%| ' . $value . ' |%';
                if ($this->containsKey($key)) {
                    $this->addAnd($key, $value, Criteria::NOT_LIKE);
                } else {
                    $this->add($key, $value, Criteria::NOT_LIKE);
                }
            }
            $this->addOr($key, null, Criteria::ISNULL);

            return $this;
        }

        return $this->addUsingAlias(DealerVersionTableMap::DEALER_PRODUCT_VERSIONS, $dealerProductVersions, $comparison);
    }

    /**
     * Filter the query on the dealer_product_versions column
     * @param     mixed $dealerProductVersions The value to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::CONTAINS_ALL
     *
     * @return ChildDealerVersionQuery The current query, for fluid interface
     */
    public function filterByDealerProductVersion($dealerProductVersions = null, $comparison = null)
    {
        if (null === $comparison || $comparison == Criteria::CONTAINS_ALL) {
            if (is_scalar($dealerProductVersions)) {
                $dealerProductVersions = '%| ' . $dealerProductVersions . ' |%';
                $comparison = Criteria::LIKE;
            }
        } elseif ($comparison == Criteria::CONTAINS_NONE) {
            $dealerProductVersions = '%| ' . $dealerProductVersions . ' |%';
            $comparison = Criteria::NOT_LIKE;
            $key = $this->getAliasedColName(DealerVersionTableMap::DEALER_PRODUCT_VERSIONS);
            if ($this->containsKey($key)) {
                $this->addAnd($key, $dealerProductVersions, $comparison);
            } else {
                $this->addAnd($key, $dealerProductVersions, $comparison);
            }
            $this->addOr($key, null, Criteria::ISNULL);

            return $this;
        }

        return $this->addUsingAlias(DealerVersionTableMap::DEALER_PRODUCT_VERSIONS, $dealerProductVersions, $comparison);
    }

    /**
     * Filter the query by a related \Dealer\Model\Dealer object
     *
     * @param \Dealer\Model\Dealer|ObjectCollection $dealer The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDealerVersionQuery The current query, for fluid interface
     */
    public function filterByDealer($dealer, $comparison = null)
    {
        if ($dealer instanceof \Dealer\Model\Dealer) {
            return $this
                ->addUsingAlias(DealerVersionTableMap::ID, $dealer->getId(), $comparison);
        } elseif ($dealer instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(DealerVersionTableMap::ID, $dealer->toKeyValue('PrimaryKey', 'Id'), $comparison);
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
     * @return ChildDealerVersionQuery The current query, for fluid interface
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
     * @param   ChildDealerVersion $dealerVersion Object to remove from the list of results
     *
     * @return ChildDealerVersionQuery The current query, for fluid interface
     */
    public function prune($dealerVersion = null)
    {
        if ($dealerVersion) {
            $this->addCond('pruneCond0', $this->getAliasedColName(DealerVersionTableMap::ID), $dealerVersion->getId(), Criteria::NOT_EQUAL);
            $this->addCond('pruneCond1', $this->getAliasedColName(DealerVersionTableMap::VERSION), $dealerVersion->getVersion(), Criteria::NOT_EQUAL);
            $this->combine(array('pruneCond0', 'pruneCond1'), Criteria::LOGICAL_OR);
        }

        return $this;
    }

    /**
     * Deletes all rows from the dealer_version table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(DealerVersionTableMap::DATABASE_NAME);
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
            DealerVersionTableMap::clearInstancePool();
            DealerVersionTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildDealerVersion or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildDealerVersion object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(DealerVersionTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(DealerVersionTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();


        DealerVersionTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            DealerVersionTableMap::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

} // DealerVersionQuery
