<?php

namespace Dealer\Model\Base;

use \Exception;
use \PDO;
use Dealer\Model\Dealer as ChildDealer;
use Dealer\Model\DealerI18nQuery as ChildDealerI18nQuery;
use Dealer\Model\DealerQuery as ChildDealerQuery;
use Dealer\Model\Map\DealerTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;
use Thelia\Model\Country;

/**
 * Base class that represents a query for the 'dealer' table.
 *
 *
 *
 * @method     ChildDealerQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildDealerQuery orderByVisible($order = Criteria::ASC) Order by the visible column
 * @method     ChildDealerQuery orderByAddress1($order = Criteria::ASC) Order by the address1 column
 * @method     ChildDealerQuery orderByAddress2($order = Criteria::ASC) Order by the address2 column
 * @method     ChildDealerQuery orderByAddress3($order = Criteria::ASC) Order by the address3 column
 * @method     ChildDealerQuery orderByZipcode($order = Criteria::ASC) Order by the zipcode column
 * @method     ChildDealerQuery orderByCity($order = Criteria::ASC) Order by the city column
 * @method     ChildDealerQuery orderByCountryId($order = Criteria::ASC) Order by the country_id column
 * @method     ChildDealerQuery orderByLatitude($order = Criteria::ASC) Order by the latitude column
 * @method     ChildDealerQuery orderByLongitude($order = Criteria::ASC) Order by the longitude column
 * @method     ChildDealerQuery orderByCreatedAt($order = Criteria::ASC) Order by the created_at column
 * @method     ChildDealerQuery orderByUpdatedAt($order = Criteria::ASC) Order by the updated_at column
 * @method     ChildDealerQuery orderByVersion($order = Criteria::ASC) Order by the version column
 * @method     ChildDealerQuery orderByVersionCreatedAt($order = Criteria::ASC) Order by the version_created_at column
 * @method     ChildDealerQuery orderByVersionCreatedBy($order = Criteria::ASC) Order by the version_created_by column
 *
 * @method     ChildDealerQuery groupById() Group by the id column
 * @method     ChildDealerQuery groupByVisible() Group by the visible column
 * @method     ChildDealerQuery groupByAddress1() Group by the address1 column
 * @method     ChildDealerQuery groupByAddress2() Group by the address2 column
 * @method     ChildDealerQuery groupByAddress3() Group by the address3 column
 * @method     ChildDealerQuery groupByZipcode() Group by the zipcode column
 * @method     ChildDealerQuery groupByCity() Group by the city column
 * @method     ChildDealerQuery groupByCountryId() Group by the country_id column
 * @method     ChildDealerQuery groupByLatitude() Group by the latitude column
 * @method     ChildDealerQuery groupByLongitude() Group by the longitude column
 * @method     ChildDealerQuery groupByCreatedAt() Group by the created_at column
 * @method     ChildDealerQuery groupByUpdatedAt() Group by the updated_at column
 * @method     ChildDealerQuery groupByVersion() Group by the version column
 * @method     ChildDealerQuery groupByVersionCreatedAt() Group by the version_created_at column
 * @method     ChildDealerQuery groupByVersionCreatedBy() Group by the version_created_by column
 *
 * @method     ChildDealerQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildDealerQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildDealerQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildDealerQuery leftJoinCountry($relationAlias = null) Adds a LEFT JOIN clause to the query using the Country relation
 * @method     ChildDealerQuery rightJoinCountry($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Country relation
 * @method     ChildDealerQuery innerJoinCountry($relationAlias = null) Adds a INNER JOIN clause to the query using the Country relation
 *
 * @method     ChildDealerQuery leftJoinDealerShedules($relationAlias = null) Adds a LEFT JOIN clause to the query using the DealerShedules relation
 * @method     ChildDealerQuery rightJoinDealerShedules($relationAlias = null) Adds a RIGHT JOIN clause to the query using the DealerShedules relation
 * @method     ChildDealerQuery innerJoinDealerShedules($relationAlias = null) Adds a INNER JOIN clause to the query using the DealerShedules relation
 *
 * @method     ChildDealerQuery leftJoinDealerContact($relationAlias = null) Adds a LEFT JOIN clause to the query using the DealerContact relation
 * @method     ChildDealerQuery rightJoinDealerContact($relationAlias = null) Adds a RIGHT JOIN clause to the query using the DealerContact relation
 * @method     ChildDealerQuery innerJoinDealerContact($relationAlias = null) Adds a INNER JOIN clause to the query using the DealerContact relation
 *
 * @method     ChildDealerQuery leftJoinDealerContent($relationAlias = null) Adds a LEFT JOIN clause to the query using the DealerContent relation
 * @method     ChildDealerQuery rightJoinDealerContent($relationAlias = null) Adds a RIGHT JOIN clause to the query using the DealerContent relation
 * @method     ChildDealerQuery innerJoinDealerContent($relationAlias = null) Adds a INNER JOIN clause to the query using the DealerContent relation
 *
 * @method     ChildDealerQuery leftJoinDealerFolder($relationAlias = null) Adds a LEFT JOIN clause to the query using the DealerFolder relation
 * @method     ChildDealerQuery rightJoinDealerFolder($relationAlias = null) Adds a RIGHT JOIN clause to the query using the DealerFolder relation
 * @method     ChildDealerQuery innerJoinDealerFolder($relationAlias = null) Adds a INNER JOIN clause to the query using the DealerFolder relation
 *
 * @method     ChildDealerQuery leftJoinDealerBrand($relationAlias = null) Adds a LEFT JOIN clause to the query using the DealerBrand relation
 * @method     ChildDealerQuery rightJoinDealerBrand($relationAlias = null) Adds a RIGHT JOIN clause to the query using the DealerBrand relation
 * @method     ChildDealerQuery innerJoinDealerBrand($relationAlias = null) Adds a INNER JOIN clause to the query using the DealerBrand relation
 *
 * @method     ChildDealerQuery leftJoinDealerProduct($relationAlias = null) Adds a LEFT JOIN clause to the query using the DealerProduct relation
 * @method     ChildDealerQuery rightJoinDealerProduct($relationAlias = null) Adds a RIGHT JOIN clause to the query using the DealerProduct relation
 * @method     ChildDealerQuery innerJoinDealerProduct($relationAlias = null) Adds a INNER JOIN clause to the query using the DealerProduct relation
 *
 * @method     ChildDealerQuery leftJoinDealerAdmin($relationAlias = null) Adds a LEFT JOIN clause to the query using the DealerAdmin relation
 * @method     ChildDealerQuery rightJoinDealerAdmin($relationAlias = null) Adds a RIGHT JOIN clause to the query using the DealerAdmin relation
 * @method     ChildDealerQuery innerJoinDealerAdmin($relationAlias = null) Adds a INNER JOIN clause to the query using the DealerAdmin relation
 *
 * @method     ChildDealerQuery leftJoinDealerImage($relationAlias = null) Adds a LEFT JOIN clause to the query using the DealerImage relation
 * @method     ChildDealerQuery rightJoinDealerImage($relationAlias = null) Adds a RIGHT JOIN clause to the query using the DealerImage relation
 * @method     ChildDealerQuery innerJoinDealerImage($relationAlias = null) Adds a INNER JOIN clause to the query using the DealerImage relation
 *
 * @method     ChildDealerQuery leftJoinDealerMetaSeo($relationAlias = null) Adds a LEFT JOIN clause to the query using the DealerMetaSeo relation
 * @method     ChildDealerQuery rightJoinDealerMetaSeo($relationAlias = null) Adds a RIGHT JOIN clause to the query using the DealerMetaSeo relation
 * @method     ChildDealerQuery innerJoinDealerMetaSeo($relationAlias = null) Adds a INNER JOIN clause to the query using the DealerMetaSeo relation
 *
 * @method     ChildDealerQuery leftJoinDealerI18n($relationAlias = null) Adds a LEFT JOIN clause to the query using the DealerI18n relation
 * @method     ChildDealerQuery rightJoinDealerI18n($relationAlias = null) Adds a RIGHT JOIN clause to the query using the DealerI18n relation
 * @method     ChildDealerQuery innerJoinDealerI18n($relationAlias = null) Adds a INNER JOIN clause to the query using the DealerI18n relation
 *
 * @method     ChildDealerQuery leftJoinDealerVersion($relationAlias = null) Adds a LEFT JOIN clause to the query using the DealerVersion relation
 * @method     ChildDealerQuery rightJoinDealerVersion($relationAlias = null) Adds a RIGHT JOIN clause to the query using the DealerVersion relation
 * @method     ChildDealerQuery innerJoinDealerVersion($relationAlias = null) Adds a INNER JOIN clause to the query using the DealerVersion relation
 *
 * @method     ChildDealer findOne(ConnectionInterface $con = null) Return the first ChildDealer matching the query
 * @method     ChildDealer findOneOrCreate(ConnectionInterface $con = null) Return the first ChildDealer matching the query, or a new ChildDealer object populated from the query conditions when no match is found
 *
 * @method     ChildDealer findOneById(int $id) Return the first ChildDealer filtered by the id column
 * @method     ChildDealer findOneByVisible(int $visible) Return the first ChildDealer filtered by the visible column
 * @method     ChildDealer findOneByAddress1(string $address1) Return the first ChildDealer filtered by the address1 column
 * @method     ChildDealer findOneByAddress2(string $address2) Return the first ChildDealer filtered by the address2 column
 * @method     ChildDealer findOneByAddress3(string $address3) Return the first ChildDealer filtered by the address3 column
 * @method     ChildDealer findOneByZipcode(string $zipcode) Return the first ChildDealer filtered by the zipcode column
 * @method     ChildDealer findOneByCity(string $city) Return the first ChildDealer filtered by the city column
 * @method     ChildDealer findOneByCountryId(int $country_id) Return the first ChildDealer filtered by the country_id column
 * @method     ChildDealer findOneByLatitude(string $latitude) Return the first ChildDealer filtered by the latitude column
 * @method     ChildDealer findOneByLongitude(string $longitude) Return the first ChildDealer filtered by the longitude column
 * @method     ChildDealer findOneByCreatedAt(string $created_at) Return the first ChildDealer filtered by the created_at column
 * @method     ChildDealer findOneByUpdatedAt(string $updated_at) Return the first ChildDealer filtered by the updated_at column
 * @method     ChildDealer findOneByVersion(int $version) Return the first ChildDealer filtered by the version column
 * @method     ChildDealer findOneByVersionCreatedAt(string $version_created_at) Return the first ChildDealer filtered by the version_created_at column
 * @method     ChildDealer findOneByVersionCreatedBy(string $version_created_by) Return the first ChildDealer filtered by the version_created_by column
 *
 * @method     array findById(int $id) Return ChildDealer objects filtered by the id column
 * @method     array findByVisible(int $visible) Return ChildDealer objects filtered by the visible column
 * @method     array findByAddress1(string $address1) Return ChildDealer objects filtered by the address1 column
 * @method     array findByAddress2(string $address2) Return ChildDealer objects filtered by the address2 column
 * @method     array findByAddress3(string $address3) Return ChildDealer objects filtered by the address3 column
 * @method     array findByZipcode(string $zipcode) Return ChildDealer objects filtered by the zipcode column
 * @method     array findByCity(string $city) Return ChildDealer objects filtered by the city column
 * @method     array findByCountryId(int $country_id) Return ChildDealer objects filtered by the country_id column
 * @method     array findByLatitude(string $latitude) Return ChildDealer objects filtered by the latitude column
 * @method     array findByLongitude(string $longitude) Return ChildDealer objects filtered by the longitude column
 * @method     array findByCreatedAt(string $created_at) Return ChildDealer objects filtered by the created_at column
 * @method     array findByUpdatedAt(string $updated_at) Return ChildDealer objects filtered by the updated_at column
 * @method     array findByVersion(int $version) Return ChildDealer objects filtered by the version column
 * @method     array findByVersionCreatedAt(string $version_created_at) Return ChildDealer objects filtered by the version_created_at column
 * @method     array findByVersionCreatedBy(string $version_created_by) Return ChildDealer objects filtered by the version_created_by column
 *
 */
abstract class DealerQuery extends ModelCriteria
{

    // versionable behavior

    /**
     * Whether the versioning is enabled
     */
    static $isVersioningEnabled = true;

    /**
     * Initializes internal state of \Dealer\Model\Base\DealerQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'thelia', $modelName = '\\Dealer\\Model\\Dealer', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildDealerQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildDealerQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof \Dealer\Model\DealerQuery) {
            return $criteria;
        }
        $query = new \Dealer\Model\DealerQuery();
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
     * @return ChildDealer|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = DealerTableMap::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(DealerTableMap::DATABASE_NAME);
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
     * @return   ChildDealer A model object, or null if the key is not found
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, VISIBLE, ADDRESS1, ADDRESS2, ADDRESS3, ZIPCODE, CITY, COUNTRY_ID, LATITUDE, LONGITUDE, CREATED_AT, UPDATED_AT, VERSION, VERSION_CREATED_AT, VERSION_CREATED_BY FROM dealer WHERE ID = :p0';
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
            $obj = new ChildDealer();
            $obj->hydrate($row);
            DealerTableMap::addInstanceToPool($obj, (string) $key);
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
     * @return ChildDealer|array|mixed the result, formatted by the current formatter
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
     * @return ChildDealerQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(DealerTableMap::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ChildDealerQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(DealerTableMap::ID, $keys, Criteria::IN);
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
     * @return ChildDealerQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(DealerTableMap::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(DealerTableMap::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DealerTableMap::ID, $id, $comparison);
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
     * @return ChildDealerQuery The current query, for fluid interface
     */
    public function filterByVisible($visible = null, $comparison = null)
    {
        if (is_array($visible)) {
            $useMinMax = false;
            if (isset($visible['min'])) {
                $this->addUsingAlias(DealerTableMap::VISIBLE, $visible['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($visible['max'])) {
                $this->addUsingAlias(DealerTableMap::VISIBLE, $visible['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DealerTableMap::VISIBLE, $visible, $comparison);
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
     * @return ChildDealerQuery The current query, for fluid interface
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

        return $this->addUsingAlias(DealerTableMap::ADDRESS1, $address1, $comparison);
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
     * @return ChildDealerQuery The current query, for fluid interface
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

        return $this->addUsingAlias(DealerTableMap::ADDRESS2, $address2, $comparison);
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
     * @return ChildDealerQuery The current query, for fluid interface
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

        return $this->addUsingAlias(DealerTableMap::ADDRESS3, $address3, $comparison);
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
     * @return ChildDealerQuery The current query, for fluid interface
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

        return $this->addUsingAlias(DealerTableMap::ZIPCODE, $zipcode, $comparison);
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
     * @return ChildDealerQuery The current query, for fluid interface
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

        return $this->addUsingAlias(DealerTableMap::CITY, $city, $comparison);
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
     * @see       filterByCountry()
     *
     * @param     mixed $countryId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDealerQuery The current query, for fluid interface
     */
    public function filterByCountryId($countryId = null, $comparison = null)
    {
        if (is_array($countryId)) {
            $useMinMax = false;
            if (isset($countryId['min'])) {
                $this->addUsingAlias(DealerTableMap::COUNTRY_ID, $countryId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($countryId['max'])) {
                $this->addUsingAlias(DealerTableMap::COUNTRY_ID, $countryId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DealerTableMap::COUNTRY_ID, $countryId, $comparison);
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
     * @return ChildDealerQuery The current query, for fluid interface
     */
    public function filterByLatitude($latitude = null, $comparison = null)
    {
        if (is_array($latitude)) {
            $useMinMax = false;
            if (isset($latitude['min'])) {
                $this->addUsingAlias(DealerTableMap::LATITUDE, $latitude['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($latitude['max'])) {
                $this->addUsingAlias(DealerTableMap::LATITUDE, $latitude['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DealerTableMap::LATITUDE, $latitude, $comparison);
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
     * @return ChildDealerQuery The current query, for fluid interface
     */
    public function filterByLongitude($longitude = null, $comparison = null)
    {
        if (is_array($longitude)) {
            $useMinMax = false;
            if (isset($longitude['min'])) {
                $this->addUsingAlias(DealerTableMap::LONGITUDE, $longitude['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($longitude['max'])) {
                $this->addUsingAlias(DealerTableMap::LONGITUDE, $longitude['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DealerTableMap::LONGITUDE, $longitude, $comparison);
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
     * @return ChildDealerQuery The current query, for fluid interface
     */
    public function filterByCreatedAt($createdAt = null, $comparison = null)
    {
        if (is_array($createdAt)) {
            $useMinMax = false;
            if (isset($createdAt['min'])) {
                $this->addUsingAlias(DealerTableMap::CREATED_AT, $createdAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdAt['max'])) {
                $this->addUsingAlias(DealerTableMap::CREATED_AT, $createdAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DealerTableMap::CREATED_AT, $createdAt, $comparison);
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
     * @return ChildDealerQuery The current query, for fluid interface
     */
    public function filterByUpdatedAt($updatedAt = null, $comparison = null)
    {
        if (is_array($updatedAt)) {
            $useMinMax = false;
            if (isset($updatedAt['min'])) {
                $this->addUsingAlias(DealerTableMap::UPDATED_AT, $updatedAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($updatedAt['max'])) {
                $this->addUsingAlias(DealerTableMap::UPDATED_AT, $updatedAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DealerTableMap::UPDATED_AT, $updatedAt, $comparison);
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
     * @return ChildDealerQuery The current query, for fluid interface
     */
    public function filterByVersion($version = null, $comparison = null)
    {
        if (is_array($version)) {
            $useMinMax = false;
            if (isset($version['min'])) {
                $this->addUsingAlias(DealerTableMap::VERSION, $version['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($version['max'])) {
                $this->addUsingAlias(DealerTableMap::VERSION, $version['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DealerTableMap::VERSION, $version, $comparison);
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
     * @return ChildDealerQuery The current query, for fluid interface
     */
    public function filterByVersionCreatedAt($versionCreatedAt = null, $comparison = null)
    {
        if (is_array($versionCreatedAt)) {
            $useMinMax = false;
            if (isset($versionCreatedAt['min'])) {
                $this->addUsingAlias(DealerTableMap::VERSION_CREATED_AT, $versionCreatedAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($versionCreatedAt['max'])) {
                $this->addUsingAlias(DealerTableMap::VERSION_CREATED_AT, $versionCreatedAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DealerTableMap::VERSION_CREATED_AT, $versionCreatedAt, $comparison);
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
     * @return ChildDealerQuery The current query, for fluid interface
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

        return $this->addUsingAlias(DealerTableMap::VERSION_CREATED_BY, $versionCreatedBy, $comparison);
    }

    /**
     * Filter the query by a related \Thelia\Model\Country object
     *
     * @param \Thelia\Model\Country|ObjectCollection $country The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDealerQuery The current query, for fluid interface
     */
    public function filterByCountry($country, $comparison = null)
    {
        if ($country instanceof \Thelia\Model\Country) {
            return $this
                ->addUsingAlias(DealerTableMap::COUNTRY_ID, $country->getId(), $comparison);
        } elseif ($country instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(DealerTableMap::COUNTRY_ID, $country->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByCountry() only accepts arguments of type \Thelia\Model\Country or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Country relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildDealerQuery The current query, for fluid interface
     */
    public function joinCountry($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Country');

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
            $this->addJoinObject($join, 'Country');
        }

        return $this;
    }

    /**
     * Use the Country relation Country object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Thelia\Model\CountryQuery A secondary query class using the current class as primary query
     */
    public function useCountryQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinCountry($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Country', '\Thelia\Model\CountryQuery');
    }

    /**
     * Filter the query by a related \Dealer\Model\DealerShedules object
     *
     * @param \Dealer\Model\DealerShedules|ObjectCollection $dealerShedules  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDealerQuery The current query, for fluid interface
     */
    public function filterByDealerShedules($dealerShedules, $comparison = null)
    {
        if ($dealerShedules instanceof \Dealer\Model\DealerShedules) {
            return $this
                ->addUsingAlias(DealerTableMap::ID, $dealerShedules->getDealerId(), $comparison);
        } elseif ($dealerShedules instanceof ObjectCollection) {
            return $this
                ->useDealerShedulesQuery()
                ->filterByPrimaryKeys($dealerShedules->getPrimaryKeys())
                ->endUse();
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
     * @return ChildDealerQuery The current query, for fluid interface
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
     * Filter the query by a related \Dealer\Model\DealerContact object
     *
     * @param \Dealer\Model\DealerContact|ObjectCollection $dealerContact  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDealerQuery The current query, for fluid interface
     */
    public function filterByDealerContact($dealerContact, $comparison = null)
    {
        if ($dealerContact instanceof \Dealer\Model\DealerContact) {
            return $this
                ->addUsingAlias(DealerTableMap::ID, $dealerContact->getDealerId(), $comparison);
        } elseif ($dealerContact instanceof ObjectCollection) {
            return $this
                ->useDealerContactQuery()
                ->filterByPrimaryKeys($dealerContact->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByDealerContact() only accepts arguments of type \Dealer\Model\DealerContact or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the DealerContact relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildDealerQuery The current query, for fluid interface
     */
    public function joinDealerContact($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('DealerContact');

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
            $this->addJoinObject($join, 'DealerContact');
        }

        return $this;
    }

    /**
     * Use the DealerContact relation DealerContact object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Dealer\Model\DealerContactQuery A secondary query class using the current class as primary query
     */
    public function useDealerContactQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinDealerContact($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'DealerContact', '\Dealer\Model\DealerContactQuery');
    }

    /**
     * Filter the query by a related \Dealer\Model\DealerContent object
     *
     * @param \Dealer\Model\DealerContent|ObjectCollection $dealerContent  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDealerQuery The current query, for fluid interface
     */
    public function filterByDealerContent($dealerContent, $comparison = null)
    {
        if ($dealerContent instanceof \Dealer\Model\DealerContent) {
            return $this
                ->addUsingAlias(DealerTableMap::ID, $dealerContent->getDealerId(), $comparison);
        } elseif ($dealerContent instanceof ObjectCollection) {
            return $this
                ->useDealerContentQuery()
                ->filterByPrimaryKeys($dealerContent->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByDealerContent() only accepts arguments of type \Dealer\Model\DealerContent or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the DealerContent relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildDealerQuery The current query, for fluid interface
     */
    public function joinDealerContent($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('DealerContent');

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
            $this->addJoinObject($join, 'DealerContent');
        }

        return $this;
    }

    /**
     * Use the DealerContent relation DealerContent object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Dealer\Model\DealerContentQuery A secondary query class using the current class as primary query
     */
    public function useDealerContentQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinDealerContent($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'DealerContent', '\Dealer\Model\DealerContentQuery');
    }

    /**
     * Filter the query by a related \Dealer\Model\DealerFolder object
     *
     * @param \Dealer\Model\DealerFolder|ObjectCollection $dealerFolder  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDealerQuery The current query, for fluid interface
     */
    public function filterByDealerFolder($dealerFolder, $comparison = null)
    {
        if ($dealerFolder instanceof \Dealer\Model\DealerFolder) {
            return $this
                ->addUsingAlias(DealerTableMap::ID, $dealerFolder->getDealerId(), $comparison);
        } elseif ($dealerFolder instanceof ObjectCollection) {
            return $this
                ->useDealerFolderQuery()
                ->filterByPrimaryKeys($dealerFolder->getPrimaryKeys())
                ->endUse();
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
     * @return ChildDealerQuery The current query, for fluid interface
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
     * Filter the query by a related \Dealer\Model\DealerBrand object
     *
     * @param \Dealer\Model\DealerBrand|ObjectCollection $dealerBrand  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDealerQuery The current query, for fluid interface
     */
    public function filterByDealerBrand($dealerBrand, $comparison = null)
    {
        if ($dealerBrand instanceof \Dealer\Model\DealerBrand) {
            return $this
                ->addUsingAlias(DealerTableMap::ID, $dealerBrand->getDealerId(), $comparison);
        } elseif ($dealerBrand instanceof ObjectCollection) {
            return $this
                ->useDealerBrandQuery()
                ->filterByPrimaryKeys($dealerBrand->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByDealerBrand() only accepts arguments of type \Dealer\Model\DealerBrand or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the DealerBrand relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildDealerQuery The current query, for fluid interface
     */
    public function joinDealerBrand($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('DealerBrand');

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
            $this->addJoinObject($join, 'DealerBrand');
        }

        return $this;
    }

    /**
     * Use the DealerBrand relation DealerBrand object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Dealer\Model\DealerBrandQuery A secondary query class using the current class as primary query
     */
    public function useDealerBrandQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinDealerBrand($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'DealerBrand', '\Dealer\Model\DealerBrandQuery');
    }

    /**
     * Filter the query by a related \Dealer\Model\DealerProduct object
     *
     * @param \Dealer\Model\DealerProduct|ObjectCollection $dealerProduct  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDealerQuery The current query, for fluid interface
     */
    public function filterByDealerProduct($dealerProduct, $comparison = null)
    {
        if ($dealerProduct instanceof \Dealer\Model\DealerProduct) {
            return $this
                ->addUsingAlias(DealerTableMap::ID, $dealerProduct->getDealerId(), $comparison);
        } elseif ($dealerProduct instanceof ObjectCollection) {
            return $this
                ->useDealerProductQuery()
                ->filterByPrimaryKeys($dealerProduct->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByDealerProduct() only accepts arguments of type \Dealer\Model\DealerProduct or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the DealerProduct relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildDealerQuery The current query, for fluid interface
     */
    public function joinDealerProduct($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('DealerProduct');

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
            $this->addJoinObject($join, 'DealerProduct');
        }

        return $this;
    }

    /**
     * Use the DealerProduct relation DealerProduct object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Dealer\Model\DealerProductQuery A secondary query class using the current class as primary query
     */
    public function useDealerProductQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinDealerProduct($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'DealerProduct', '\Dealer\Model\DealerProductQuery');
    }

    /**
     * Filter the query by a related \Dealer\Model\DealerAdmin object
     *
     * @param \Dealer\Model\DealerAdmin|ObjectCollection $dealerAdmin  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDealerQuery The current query, for fluid interface
     */
    public function filterByDealerAdmin($dealerAdmin, $comparison = null)
    {
        if ($dealerAdmin instanceof \Dealer\Model\DealerAdmin) {
            return $this
                ->addUsingAlias(DealerTableMap::ID, $dealerAdmin->getDealerId(), $comparison);
        } elseif ($dealerAdmin instanceof ObjectCollection) {
            return $this
                ->useDealerAdminQuery()
                ->filterByPrimaryKeys($dealerAdmin->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByDealerAdmin() only accepts arguments of type \Dealer\Model\DealerAdmin or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the DealerAdmin relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildDealerQuery The current query, for fluid interface
     */
    public function joinDealerAdmin($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('DealerAdmin');

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
            $this->addJoinObject($join, 'DealerAdmin');
        }

        return $this;
    }

    /**
     * Use the DealerAdmin relation DealerAdmin object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Dealer\Model\DealerAdminQuery A secondary query class using the current class as primary query
     */
    public function useDealerAdminQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinDealerAdmin($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'DealerAdmin', '\Dealer\Model\DealerAdminQuery');
    }

    /**
     * Filter the query by a related \Dealer\Model\DealerImage object
     *
     * @param \Dealer\Model\DealerImage|ObjectCollection $dealerImage  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDealerQuery The current query, for fluid interface
     */
    public function filterByDealerImage($dealerImage, $comparison = null)
    {
        if ($dealerImage instanceof \Dealer\Model\DealerImage) {
            return $this
                ->addUsingAlias(DealerTableMap::ID, $dealerImage->getDealerId(), $comparison);
        } elseif ($dealerImage instanceof ObjectCollection) {
            return $this
                ->useDealerImageQuery()
                ->filterByPrimaryKeys($dealerImage->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByDealerImage() only accepts arguments of type \Dealer\Model\DealerImage or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the DealerImage relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildDealerQuery The current query, for fluid interface
     */
    public function joinDealerImage($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('DealerImage');

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
            $this->addJoinObject($join, 'DealerImage');
        }

        return $this;
    }

    /**
     * Use the DealerImage relation DealerImage object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Dealer\Model\DealerImageQuery A secondary query class using the current class as primary query
     */
    public function useDealerImageQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinDealerImage($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'DealerImage', '\Dealer\Model\DealerImageQuery');
    }

    /**
     * Filter the query by a related \Dealer\Model\DealerMetaSeo object
     *
     * @param \Dealer\Model\DealerMetaSeo|ObjectCollection $dealerMetaSeo  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDealerQuery The current query, for fluid interface
     */
    public function filterByDealerMetaSeo($dealerMetaSeo, $comparison = null)
    {
        if ($dealerMetaSeo instanceof \Dealer\Model\DealerMetaSeo) {
            return $this
                ->addUsingAlias(DealerTableMap::ID, $dealerMetaSeo->getDealerId(), $comparison);
        } elseif ($dealerMetaSeo instanceof ObjectCollection) {
            return $this
                ->useDealerMetaSeoQuery()
                ->filterByPrimaryKeys($dealerMetaSeo->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByDealerMetaSeo() only accepts arguments of type \Dealer\Model\DealerMetaSeo or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the DealerMetaSeo relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildDealerQuery The current query, for fluid interface
     */
    public function joinDealerMetaSeo($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('DealerMetaSeo');

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
            $this->addJoinObject($join, 'DealerMetaSeo');
        }

        return $this;
    }

    /**
     * Use the DealerMetaSeo relation DealerMetaSeo object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Dealer\Model\DealerMetaSeoQuery A secondary query class using the current class as primary query
     */
    public function useDealerMetaSeoQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinDealerMetaSeo($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'DealerMetaSeo', '\Dealer\Model\DealerMetaSeoQuery');
    }

    /**
     * Filter the query by a related \Dealer\Model\DealerI18n object
     *
     * @param \Dealer\Model\DealerI18n|ObjectCollection $dealerI18n  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDealerQuery The current query, for fluid interface
     */
    public function filterByDealerI18n($dealerI18n, $comparison = null)
    {
        if ($dealerI18n instanceof \Dealer\Model\DealerI18n) {
            return $this
                ->addUsingAlias(DealerTableMap::ID, $dealerI18n->getId(), $comparison);
        } elseif ($dealerI18n instanceof ObjectCollection) {
            return $this
                ->useDealerI18nQuery()
                ->filterByPrimaryKeys($dealerI18n->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByDealerI18n() only accepts arguments of type \Dealer\Model\DealerI18n or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the DealerI18n relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildDealerQuery The current query, for fluid interface
     */
    public function joinDealerI18n($relationAlias = null, $joinType = 'LEFT JOIN')
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('DealerI18n');

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
            $this->addJoinObject($join, 'DealerI18n');
        }

        return $this;
    }

    /**
     * Use the DealerI18n relation DealerI18n object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Dealer\Model\DealerI18nQuery A secondary query class using the current class as primary query
     */
    public function useDealerI18nQuery($relationAlias = null, $joinType = 'LEFT JOIN')
    {
        return $this
            ->joinDealerI18n($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'DealerI18n', '\Dealer\Model\DealerI18nQuery');
    }

    /**
     * Filter the query by a related \Dealer\Model\DealerVersion object
     *
     * @param \Dealer\Model\DealerVersion|ObjectCollection $dealerVersion  the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildDealerQuery The current query, for fluid interface
     */
    public function filterByDealerVersion($dealerVersion, $comparison = null)
    {
        if ($dealerVersion instanceof \Dealer\Model\DealerVersion) {
            return $this
                ->addUsingAlias(DealerTableMap::ID, $dealerVersion->getId(), $comparison);
        } elseif ($dealerVersion instanceof ObjectCollection) {
            return $this
                ->useDealerVersionQuery()
                ->filterByPrimaryKeys($dealerVersion->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByDealerVersion() only accepts arguments of type \Dealer\Model\DealerVersion or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the DealerVersion relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ChildDealerQuery The current query, for fluid interface
     */
    public function joinDealerVersion($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('DealerVersion');

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
            $this->addJoinObject($join, 'DealerVersion');
        }

        return $this;
    }

    /**
     * Use the DealerVersion relation DealerVersion object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Dealer\Model\DealerVersionQuery A secondary query class using the current class as primary query
     */
    public function useDealerVersionQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinDealerVersion($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'DealerVersion', '\Dealer\Model\DealerVersionQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildDealer $dealer Object to remove from the list of results
     *
     * @return ChildDealerQuery The current query, for fluid interface
     */
    public function prune($dealer = null)
    {
        if ($dealer) {
            $this->addUsingAlias(DealerTableMap::ID, $dealer->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the dealer table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(DealerTableMap::DATABASE_NAME);
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
            DealerTableMap::clearInstancePool();
            DealerTableMap::clearRelatedInstancePool();

            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $affectedRows;
    }

    /**
     * Performs a DELETE on the database, given a ChildDealer or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or ChildDealer object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(DealerTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(DealerTableMap::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();


        DealerTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            DealerTableMap::clearRelatedInstancePool();
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
     * @return     ChildDealerQuery The current query, for fluid interface
     */
    public function recentlyUpdated($nbDays = 7)
    {
        return $this->addUsingAlias(DealerTableMap::UPDATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Filter by the latest created
     *
     * @param      int $nbDays Maximum age of in days
     *
     * @return     ChildDealerQuery The current query, for fluid interface
     */
    public function recentlyCreated($nbDays = 7)
    {
        return $this->addUsingAlias(DealerTableMap::CREATED_AT, time() - $nbDays * 24 * 60 * 60, Criteria::GREATER_EQUAL);
    }

    /**
     * Order by update date desc
     *
     * @return     ChildDealerQuery The current query, for fluid interface
     */
    public function lastUpdatedFirst()
    {
        return $this->addDescendingOrderByColumn(DealerTableMap::UPDATED_AT);
    }

    /**
     * Order by update date asc
     *
     * @return     ChildDealerQuery The current query, for fluid interface
     */
    public function firstUpdatedFirst()
    {
        return $this->addAscendingOrderByColumn(DealerTableMap::UPDATED_AT);
    }

    /**
     * Order by create date desc
     *
     * @return     ChildDealerQuery The current query, for fluid interface
     */
    public function lastCreatedFirst()
    {
        return $this->addDescendingOrderByColumn(DealerTableMap::CREATED_AT);
    }

    /**
     * Order by create date asc
     *
     * @return     ChildDealerQuery The current query, for fluid interface
     */
    public function firstCreatedFirst()
    {
        return $this->addAscendingOrderByColumn(DealerTableMap::CREATED_AT);
    }

    // i18n behavior

    /**
     * Adds a JOIN clause to the query using the i18n relation
     *
     * @param     string $locale Locale to use for the join condition, e.g. 'fr_FR'
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'. Defaults to left join.
     *
     * @return    ChildDealerQuery The current query, for fluid interface
     */
    public function joinI18n($locale = 'en_US', $relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $relationName = $relationAlias ? $relationAlias : 'DealerI18n';

        return $this
            ->joinDealerI18n($relationAlias, $joinType)
            ->addJoinCondition($relationName, $relationName . '.Locale = ?', $locale);
    }

    /**
     * Adds a JOIN clause to the query and hydrates the related I18n object.
     * Shortcut for $c->joinI18n($locale)->with()
     *
     * @param     string $locale Locale to use for the join condition, e.g. 'fr_FR'
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'. Defaults to left join.
     *
     * @return    ChildDealerQuery The current query, for fluid interface
     */
    public function joinWithI18n($locale = 'en_US', $joinType = Criteria::LEFT_JOIN)
    {
        $this
            ->joinI18n($locale, null, $joinType)
            ->with('DealerI18n');
        $this->with['DealerI18n']->setIsWithOneToMany(false);

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
     * @return    ChildDealerI18nQuery A secondary query class using the current class as primary query
     */
    public function useI18nQuery($locale = 'en_US', $relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinI18n($locale, $relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'DealerI18n', '\Dealer\Model\DealerI18nQuery');
    }

    // versionable behavior

    /**
     * Checks whether versioning is enabled
     *
     * @return boolean
     */
    static public function isVersioningEnabled()
    {
        return self::$isVersioningEnabled;
    }

    /**
     * Enables versioning
     */
    static public function enableVersioning()
    {
        self::$isVersioningEnabled = true;
    }

    /**
     * Disables versioning
     */
    static public function disableVersioning()
    {
        self::$isVersioningEnabled = false;
    }

} // DealerQuery
