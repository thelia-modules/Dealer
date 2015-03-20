<?php

namespace Dealer\Model\Map;

use Dealer\Model\DealerTab;
use Dealer\Model\DealerTabQuery;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\InstancePoolTrait;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\DataFetcher\DataFetcherInterface;
use Propel\Runtime\Exception\PropelException;
use Propel\Runtime\Map\RelationMap;
use Propel\Runtime\Map\TableMap;
use Propel\Runtime\Map\TableMapTrait;


/**
 * This class defines the structure of the 'dealer_tab' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 */
class DealerTabTableMap extends TableMap
{
    use InstancePoolTrait;
    use TableMapTrait;
    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'Dealer.Model.Map.DealerTabTableMap';

    /**
     * The default database name for this class
     */
    const DATABASE_NAME = 'thelia';

    /**
     * The table name for this class
     */
    const TABLE_NAME = 'dealer_tab';

    /**
     * The related Propel class for this table
     */
    const OM_CLASS = '\\Dealer\\Model\\DealerTab';

    /**
     * A class that can be returned by this tableMap
     */
    const CLASS_DEFAULT = 'Dealer.Model.DealerTab';

    /**
     * The total number of columns
     */
    const NUM_COLUMNS = 14;

    /**
     * The number of lazy-loaded columns
     */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /**
     * The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS)
     */
    const NUM_HYDRATE_COLUMNS = 14;

    /**
     * the column name for the ID field
     */
    const ID = 'dealer_tab.ID';

    /**
     * the column name for the COMPANY field
     */
    const COMPANY = 'dealer_tab.COMPANY';

    /**
     * the column name for the ADDRESS1 field
     */
    const ADDRESS1 = 'dealer_tab.ADDRESS1';

    /**
     * the column name for the ADDRESS2 field
     */
    const ADDRESS2 = 'dealer_tab.ADDRESS2';

    /**
     * the column name for the ZIPCODE field
     */
    const ZIPCODE = 'dealer_tab.ZIPCODE';

    /**
     * the column name for the CITY field
     */
    const CITY = 'dealer_tab.CITY';

    /**
     * the column name for the DESCRIPTION field
     */
    const DESCRIPTION = 'dealer_tab.DESCRIPTION';

    /**
     * the column name for the SCHEDULE field
     */
    const SCHEDULE = 'dealer_tab.SCHEDULE';

    /**
     * the column name for the PHONE_NUMBER field
     */
    const PHONE_NUMBER = 'dealer_tab.PHONE_NUMBER';

    /**
     * the column name for the WEB_SITE field
     */
    const WEB_SITE = 'dealer_tab.WEB_SITE';

    /**
     * the column name for the LATITUDE field
     */
    const LATITUDE = 'dealer_tab.LATITUDE';

    /**
     * the column name for the LONGITUDE field
     */
    const LONGITUDE = 'dealer_tab.LONGITUDE';

    /**
     * the column name for the CREATED_AT field
     */
    const CREATED_AT = 'dealer_tab.CREATED_AT';

    /**
     * the column name for the UPDATED_AT field
     */
    const UPDATED_AT = 'dealer_tab.UPDATED_AT';

    /**
     * The default string format for model objects of the related table
     */
    const DEFAULT_STRING_FORMAT = 'YAML';

    /**
     * holds an array of fieldnames
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
     */
    protected static $fieldNames = array (
        self::TYPE_PHPNAME       => array('Id', 'Company', 'Address1', 'Address2', 'Zipcode', 'City', 'Description', 'Schedule', 'PhoneNumber', 'WebSite', 'Latitude', 'Longitude', 'CreatedAt', 'UpdatedAt', ),
        self::TYPE_STUDLYPHPNAME => array('id', 'company', 'address1', 'address2', 'zipcode', 'city', 'description', 'schedule', 'phoneNumber', 'webSite', 'latitude', 'longitude', 'createdAt', 'updatedAt', ),
        self::TYPE_COLNAME       => array(DealerTabTableMap::ID, DealerTabTableMap::COMPANY, DealerTabTableMap::ADDRESS1, DealerTabTableMap::ADDRESS2, DealerTabTableMap::ZIPCODE, DealerTabTableMap::CITY, DealerTabTableMap::DESCRIPTION, DealerTabTableMap::SCHEDULE, DealerTabTableMap::PHONE_NUMBER, DealerTabTableMap::WEB_SITE, DealerTabTableMap::LATITUDE, DealerTabTableMap::LONGITUDE, DealerTabTableMap::CREATED_AT, DealerTabTableMap::UPDATED_AT, ),
        self::TYPE_RAW_COLNAME   => array('ID', 'COMPANY', 'ADDRESS1', 'ADDRESS2', 'ZIPCODE', 'CITY', 'DESCRIPTION', 'SCHEDULE', 'PHONE_NUMBER', 'WEB_SITE', 'LATITUDE', 'LONGITUDE', 'CREATED_AT', 'UPDATED_AT', ),
        self::TYPE_FIELDNAME     => array('id', 'company', 'address1', 'address2', 'zipcode', 'city', 'description', 'schedule', 'phone_number', 'web_site', 'latitude', 'longitude', 'created_at', 'updated_at', ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldKeys[self::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        self::TYPE_PHPNAME       => array('Id' => 0, 'Company' => 1, 'Address1' => 2, 'Address2' => 3, 'Zipcode' => 4, 'City' => 5, 'Description' => 6, 'Schedule' => 7, 'PhoneNumber' => 8, 'WebSite' => 9, 'Latitude' => 10, 'Longitude' => 11, 'CreatedAt' => 12, 'UpdatedAt' => 13, ),
        self::TYPE_STUDLYPHPNAME => array('id' => 0, 'company' => 1, 'address1' => 2, 'address2' => 3, 'zipcode' => 4, 'city' => 5, 'description' => 6, 'schedule' => 7, 'phoneNumber' => 8, 'webSite' => 9, 'latitude' => 10, 'longitude' => 11, 'createdAt' => 12, 'updatedAt' => 13, ),
        self::TYPE_COLNAME       => array(DealerTabTableMap::ID => 0, DealerTabTableMap::COMPANY => 1, DealerTabTableMap::ADDRESS1 => 2, DealerTabTableMap::ADDRESS2 => 3, DealerTabTableMap::ZIPCODE => 4, DealerTabTableMap::CITY => 5, DealerTabTableMap::DESCRIPTION => 6, DealerTabTableMap::SCHEDULE => 7, DealerTabTableMap::PHONE_NUMBER => 8, DealerTabTableMap::WEB_SITE => 9, DealerTabTableMap::LATITUDE => 10, DealerTabTableMap::LONGITUDE => 11, DealerTabTableMap::CREATED_AT => 12, DealerTabTableMap::UPDATED_AT => 13, ),
        self::TYPE_RAW_COLNAME   => array('ID' => 0, 'COMPANY' => 1, 'ADDRESS1' => 2, 'ADDRESS2' => 3, 'ZIPCODE' => 4, 'CITY' => 5, 'DESCRIPTION' => 6, 'SCHEDULE' => 7, 'PHONE_NUMBER' => 8, 'WEB_SITE' => 9, 'LATITUDE' => 10, 'LONGITUDE' => 11, 'CREATED_AT' => 12, 'UPDATED_AT' => 13, ),
        self::TYPE_FIELDNAME     => array('id' => 0, 'company' => 1, 'address1' => 2, 'address2' => 3, 'zipcode' => 4, 'city' => 5, 'description' => 6, 'schedule' => 7, 'phone_number' => 8, 'web_site' => 9, 'latitude' => 10, 'longitude' => 11, 'created_at' => 12, 'updated_at' => 13, ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, )
    );

    /**
     * Initialize the table attributes and columns
     * Relations are not initialized by this method since they are lazy loaded
     *
     * @return void
     * @throws PropelException
     */
    public function initialize()
    {
        // attributes
        $this->setName('dealer_tab');
        $this->setPhpName('DealerTab');
        $this->setClassName('\\Dealer\\Model\\DealerTab');
        $this->setPackage('Dealer.Model');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
        $this->addColumn('COMPANY', 'Company', 'VARCHAR', true, 255, null);
        $this->addColumn('ADDRESS1', 'Address1', 'VARCHAR', true, 255, null);
        $this->addColumn('ADDRESS2', 'Address2', 'VARCHAR', false, 255, null);
        $this->addColumn('ZIPCODE', 'Zipcode', 'VARCHAR', true, 10, null);
        $this->addColumn('CITY', 'City', 'VARCHAR', true, 255, null);
        $this->addColumn('DESCRIPTION', 'Description', 'LONGVARCHAR', false, null, null);
        $this->addColumn('SCHEDULE', 'Schedule', 'VARCHAR', false, 255, null);
        $this->addColumn('PHONE_NUMBER', 'PhoneNumber', 'VARCHAR', false, 255, null);
        $this->addColumn('WEB_SITE', 'WebSite', 'VARCHAR', false, 255, null);
        $this->addColumn('LATITUDE', 'Latitude', 'FLOAT', false, null, 0);
        $this->addColumn('LONGITUDE', 'Longitude', 'FLOAT', false, null, 0);
        $this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
        $this->addColumn('UPDATED_AT', 'UpdatedAt', 'TIMESTAMP', false, null, null);
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
    } // buildRelations()

    /**
     *
     * Gets the list of behaviors registered for this table
     *
     * @return array Associative array (name => parameters) of behaviors
     */
    public function getBehaviors()
    {
        return array(
            'timestampable' => array('create_column' => 'created_at', 'update_column' => 'updated_at', ),
        );
    } // getBehaviors()

    /**
     * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
     *
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, a serialize()d version of the primary key will be returned.
     *
     * @param array  $row       resultset row.
     * @param int    $offset    The 0-based offset for reading from the resultset row.
     * @param string $indexType One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME
     *                           TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM
     */
    public static function getPrimaryKeyHashFromRow($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        // If the PK cannot be derived from the row, return NULL.
        if ($row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)] === null) {
            return null;
        }

        return (string) $row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
    }

    /**
     * Retrieves the primary key from the DB resultset row
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, an array of the primary key columns will be returned.
     *
     * @param array  $row       resultset row.
     * @param int    $offset    The 0-based offset for reading from the resultset row.
     * @param string $indexType One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME
     *                           TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM
     *
     * @return mixed The primary key of the row
     */
    public static function getPrimaryKeyFromRow($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {

            return (int) $row[
                            $indexType == TableMap::TYPE_NUM
                            ? 0 + $offset
                            : self::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)
                        ];
    }

    /**
     * The class that the tableMap will make instances of.
     *
     * If $withPrefix is true, the returned path
     * uses a dot-path notation which is translated into a path
     * relative to a location on the PHP include_path.
     * (e.g. path.to.MyClass -> 'path/to/MyClass.php')
     *
     * @param boolean $withPrefix Whether or not to return the path with the class name
     * @return string path.to.ClassName
     */
    public static function getOMClass($withPrefix = true)
    {
        return $withPrefix ? DealerTabTableMap::CLASS_DEFAULT : DealerTabTableMap::OM_CLASS;
    }

    /**
     * Populates an object of the default type or an object that inherit from the default.
     *
     * @param array  $row       row returned by DataFetcher->fetch().
     * @param int    $offset    The 0-based offset for reading from the resultset row.
     * @param string $indexType The index type of $row. Mostly DataFetcher->getIndexType().
                                 One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME
     *                           TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     * @return array (DealerTab object, last column rank)
     */
    public static function populateObject($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        $key = DealerTabTableMap::getPrimaryKeyHashFromRow($row, $offset, $indexType);
        if (null !== ($obj = DealerTabTableMap::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $offset, true); // rehydrate
            $col = $offset + DealerTabTableMap::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = DealerTabTableMap::OM_CLASS;
            $obj = new $cls();
            $col = $obj->hydrate($row, $offset, false, $indexType);
            DealerTabTableMap::addInstanceToPool($obj, $key);
        }

        return array($obj, $col);
    }

    /**
     * The returned array will contain objects of the default type or
     * objects that inherit from the default.
     *
     * @param DataFetcherInterface $dataFetcher
     * @return array
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
    public static function populateObjects(DataFetcherInterface $dataFetcher)
    {
        $results = array();

        // set the class once to avoid overhead in the loop
        $cls = static::getOMClass(false);
        // populate the object(s)
        while ($row = $dataFetcher->fetch()) {
            $key = DealerTabTableMap::getPrimaryKeyHashFromRow($row, 0, $dataFetcher->getIndexType());
            if (null !== ($obj = DealerTabTableMap::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                DealerTabTableMap::addInstanceToPool($obj, $key);
            } // if key exists
        }

        return $results;
    }
    /**
     * Add all the columns needed to create a new object.
     *
     * Note: any columns that were marked with lazyLoad="true" in the
     * XML schema will not be added to the select list and only loaded
     * on demand.
     *
     * @param Criteria $criteria object containing the columns to add.
     * @param string   $alias    optional table alias
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
    public static function addSelectColumns(Criteria $criteria, $alias = null)
    {
        if (null === $alias) {
            $criteria->addSelectColumn(DealerTabTableMap::ID);
            $criteria->addSelectColumn(DealerTabTableMap::COMPANY);
            $criteria->addSelectColumn(DealerTabTableMap::ADDRESS1);
            $criteria->addSelectColumn(DealerTabTableMap::ADDRESS2);
            $criteria->addSelectColumn(DealerTabTableMap::ZIPCODE);
            $criteria->addSelectColumn(DealerTabTableMap::CITY);
            $criteria->addSelectColumn(DealerTabTableMap::DESCRIPTION);
            $criteria->addSelectColumn(DealerTabTableMap::SCHEDULE);
            $criteria->addSelectColumn(DealerTabTableMap::PHONE_NUMBER);
            $criteria->addSelectColumn(DealerTabTableMap::WEB_SITE);
            $criteria->addSelectColumn(DealerTabTableMap::LATITUDE);
            $criteria->addSelectColumn(DealerTabTableMap::LONGITUDE);
            $criteria->addSelectColumn(DealerTabTableMap::CREATED_AT);
            $criteria->addSelectColumn(DealerTabTableMap::UPDATED_AT);
        } else {
            $criteria->addSelectColumn($alias . '.ID');
            $criteria->addSelectColumn($alias . '.COMPANY');
            $criteria->addSelectColumn($alias . '.ADDRESS1');
            $criteria->addSelectColumn($alias . '.ADDRESS2');
            $criteria->addSelectColumn($alias . '.ZIPCODE');
            $criteria->addSelectColumn($alias . '.CITY');
            $criteria->addSelectColumn($alias . '.DESCRIPTION');
            $criteria->addSelectColumn($alias . '.SCHEDULE');
            $criteria->addSelectColumn($alias . '.PHONE_NUMBER');
            $criteria->addSelectColumn($alias . '.WEB_SITE');
            $criteria->addSelectColumn($alias . '.LATITUDE');
            $criteria->addSelectColumn($alias . '.LONGITUDE');
            $criteria->addSelectColumn($alias . '.CREATED_AT');
            $criteria->addSelectColumn($alias . '.UPDATED_AT');
        }
    }

    /**
     * Returns the TableMap related to this object.
     * This method is not needed for general use but a specific application could have a need.
     * @return TableMap
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
    public static function getTableMap()
    {
        return Propel::getServiceContainer()->getDatabaseMap(DealerTabTableMap::DATABASE_NAME)->getTable(DealerTabTableMap::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this tableMap class.
     */
    public static function buildTableMap()
    {
      $dbMap = Propel::getServiceContainer()->getDatabaseMap(DealerTabTableMap::DATABASE_NAME);
      if (!$dbMap->hasTable(DealerTabTableMap::TABLE_NAME)) {
        $dbMap->addTableObject(new DealerTabTableMap());
      }
    }

    /**
     * Performs a DELETE on the database, given a DealerTab or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or DealerTab object or primary key or array of primary keys
     *              which is used to create the DELETE statement
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *                if supported by native driver or if emulated using Propel.
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
     public static function doDelete($values, ConnectionInterface $con = null)
     {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(DealerTabTableMap::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            // rename for clarity
            $criteria = $values;
        } elseif ($values instanceof \Dealer\Model\DealerTab) { // it's a model object
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(DealerTabTableMap::DATABASE_NAME);
            $criteria->add(DealerTabTableMap::ID, (array) $values, Criteria::IN);
        }

        $query = DealerTabQuery::create()->mergeWith($criteria);

        if ($values instanceof Criteria) { DealerTabTableMap::clearInstancePool();
        } elseif (!is_object($values)) { // it's a primary key, or an array of pks
            foreach ((array) $values as $singleval) { DealerTabTableMap::removeInstanceFromPool($singleval);
            }
        }

        return $query->delete($con);
    }

    /**
     * Deletes all rows from the dealer_tab table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public static function doDeleteAll(ConnectionInterface $con = null)
    {
        return DealerTabQuery::create()->doDeleteAll($con);
    }

    /**
     * Performs an INSERT on the database, given a DealerTab or Criteria object.
     *
     * @param mixed               $criteria Criteria or DealerTab object containing data that is used to create the INSERT statement.
     * @param ConnectionInterface $con the ConnectionInterface connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
    public static function doInsert($criteria, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(DealerTabTableMap::DATABASE_NAME);
        }

        if ($criteria instanceof Criteria) {
            $criteria = clone $criteria; // rename for clarity
        } else {
            $criteria = $criteria->buildCriteria(); // build Criteria from DealerTab object
        }

        if ($criteria->containsKey(DealerTabTableMap::ID) && $criteria->keyContainsValue(DealerTabTableMap::ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.DealerTabTableMap::ID.')');
        }


        // Set the correct dbName
        $query = DealerTabQuery::create()->mergeWith($criteria);

        try {
            // use transaction because $criteria could contain info
            // for more than one table (I guess, conceivably)
            $con->beginTransaction();
            $pk = $query->doInsert($con);
            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $pk;
    }

} // DealerTabTableMap
// This is the static code needed to register the TableMap for this table with the main Propel class.
//
DealerTabTableMap::buildTableMap();
