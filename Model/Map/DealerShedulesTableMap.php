<?php

namespace Dealer\Model\Map;

use Dealer\Model\DealerShedules;
use Dealer\Model\DealerShedulesQuery;
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
 * This class defines the structure of the 'dealer_shedules' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 */
class DealerShedulesTableMap extends TableMap
{
    use InstancePoolTrait;
    use TableMapTrait;
    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'Dealer.Model.Map.DealerShedulesTableMap';

    /**
     * The default database name for this class
     */
    const DATABASE_NAME = 'thelia';

    /**
     * The table name for this class
     */
    const TABLE_NAME = 'dealer_shedules';

    /**
     * The related Propel class for this table
     */
    const OM_CLASS = '\\Dealer\\Model\\DealerShedules';

    /**
     * A class that can be returned by this tableMap
     */
    const CLASS_DEFAULT = 'Dealer.Model.DealerShedules';

    /**
     * The total number of columns
     */
    const NUM_COLUMNS = 13;

    /**
     * The number of lazy-loaded columns
     */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /**
     * The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS)
     */
    const NUM_HYDRATE_COLUMNS = 13;

    /**
     * the column name for the ID field
     */
    const ID = 'dealer_shedules.ID';

    /**
     * the column name for the DEALER_ID field
     */
    const DEALER_ID = 'dealer_shedules.DEALER_ID';

    /**
     * the column name for the DAY field
     */
    const DAY = 'dealer_shedules.DAY';

    /**
     * the column name for the BEGIN field
     */
    const BEGIN = 'dealer_shedules.BEGIN';

    /**
     * the column name for the END field
     */
    const END = 'dealer_shedules.END';

    /**
     * the column name for the CLOSED field
     */
    const CLOSED = 'dealer_shedules.CLOSED';

    /**
     * the column name for the PERIOD_BEGIN field
     */
    const PERIOD_BEGIN = 'dealer_shedules.PERIOD_BEGIN';

    /**
     * the column name for the PERIOD_END field
     */
    const PERIOD_END = 'dealer_shedules.PERIOD_END';

    /**
     * the column name for the CREATED_AT field
     */
    const CREATED_AT = 'dealer_shedules.CREATED_AT';

    /**
     * the column name for the UPDATED_AT field
     */
    const UPDATED_AT = 'dealer_shedules.UPDATED_AT';

    /**
     * the column name for the VERSION field
     */
    const VERSION = 'dealer_shedules.VERSION';

    /**
     * the column name for the VERSION_CREATED_AT field
     */
    const VERSION_CREATED_AT = 'dealer_shedules.VERSION_CREATED_AT';

    /**
     * the column name for the VERSION_CREATED_BY field
     */
    const VERSION_CREATED_BY = 'dealer_shedules.VERSION_CREATED_BY';

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
        self::TYPE_PHPNAME       => array('Id', 'DealerId', 'Day', 'Begin', 'End', 'Closed', 'PeriodBegin', 'PeriodEnd', 'CreatedAt', 'UpdatedAt', 'Version', 'VersionCreatedAt', 'VersionCreatedBy', ),
        self::TYPE_STUDLYPHPNAME => array('id', 'dealerId', 'day', 'begin', 'end', 'closed', 'periodBegin', 'periodEnd', 'createdAt', 'updatedAt', 'version', 'versionCreatedAt', 'versionCreatedBy', ),
        self::TYPE_COLNAME       => array(DealerShedulesTableMap::ID, DealerShedulesTableMap::DEALER_ID, DealerShedulesTableMap::DAY, DealerShedulesTableMap::BEGIN, DealerShedulesTableMap::END, DealerShedulesTableMap::CLOSED, DealerShedulesTableMap::PERIOD_BEGIN, DealerShedulesTableMap::PERIOD_END, DealerShedulesTableMap::CREATED_AT, DealerShedulesTableMap::UPDATED_AT, DealerShedulesTableMap::VERSION, DealerShedulesTableMap::VERSION_CREATED_AT, DealerShedulesTableMap::VERSION_CREATED_BY, ),
        self::TYPE_RAW_COLNAME   => array('ID', 'DEALER_ID', 'DAY', 'BEGIN', 'END', 'CLOSED', 'PERIOD_BEGIN', 'PERIOD_END', 'CREATED_AT', 'UPDATED_AT', 'VERSION', 'VERSION_CREATED_AT', 'VERSION_CREATED_BY', ),
        self::TYPE_FIELDNAME     => array('id', 'dealer_id', 'day', 'begin', 'end', 'closed', 'period_begin', 'period_end', 'created_at', 'updated_at', 'version', 'version_created_at', 'version_created_by', ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldKeys[self::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        self::TYPE_PHPNAME       => array('Id' => 0, 'DealerId' => 1, 'Day' => 2, 'Begin' => 3, 'End' => 4, 'Closed' => 5, 'PeriodBegin' => 6, 'PeriodEnd' => 7, 'CreatedAt' => 8, 'UpdatedAt' => 9, 'Version' => 10, 'VersionCreatedAt' => 11, 'VersionCreatedBy' => 12, ),
        self::TYPE_STUDLYPHPNAME => array('id' => 0, 'dealerId' => 1, 'day' => 2, 'begin' => 3, 'end' => 4, 'closed' => 5, 'periodBegin' => 6, 'periodEnd' => 7, 'createdAt' => 8, 'updatedAt' => 9, 'version' => 10, 'versionCreatedAt' => 11, 'versionCreatedBy' => 12, ),
        self::TYPE_COLNAME       => array(DealerShedulesTableMap::ID => 0, DealerShedulesTableMap::DEALER_ID => 1, DealerShedulesTableMap::DAY => 2, DealerShedulesTableMap::BEGIN => 3, DealerShedulesTableMap::END => 4, DealerShedulesTableMap::CLOSED => 5, DealerShedulesTableMap::PERIOD_BEGIN => 6, DealerShedulesTableMap::PERIOD_END => 7, DealerShedulesTableMap::CREATED_AT => 8, DealerShedulesTableMap::UPDATED_AT => 9, DealerShedulesTableMap::VERSION => 10, DealerShedulesTableMap::VERSION_CREATED_AT => 11, DealerShedulesTableMap::VERSION_CREATED_BY => 12, ),
        self::TYPE_RAW_COLNAME   => array('ID' => 0, 'DEALER_ID' => 1, 'DAY' => 2, 'BEGIN' => 3, 'END' => 4, 'CLOSED' => 5, 'PERIOD_BEGIN' => 6, 'PERIOD_END' => 7, 'CREATED_AT' => 8, 'UPDATED_AT' => 9, 'VERSION' => 10, 'VERSION_CREATED_AT' => 11, 'VERSION_CREATED_BY' => 12, ),
        self::TYPE_FIELDNAME     => array('id' => 0, 'dealer_id' => 1, 'day' => 2, 'begin' => 3, 'end' => 4, 'closed' => 5, 'period_begin' => 6, 'period_end' => 7, 'created_at' => 8, 'updated_at' => 9, 'version' => 10, 'version_created_at' => 11, 'version_created_by' => 12, ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, )
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
        $this->setName('dealer_shedules');
        $this->setPhpName('DealerShedules');
        $this->setClassName('\\Dealer\\Model\\DealerShedules');
        $this->setPackage('Dealer.Model');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
        $this->addForeignKey('DEALER_ID', 'DealerId', 'INTEGER', 'dealer', 'ID', true, null, null);
        $this->addColumn('DAY', 'Day', 'INTEGER', false, null, null);
        $this->addColumn('BEGIN', 'Begin', 'TIME', false, null, null);
        $this->addColumn('END', 'End', 'TIME', false, null, null);
        $this->addColumn('CLOSED', 'Closed', 'BOOLEAN', false, 1, false);
        $this->addColumn('PERIOD_BEGIN', 'PeriodBegin', 'DATE', false, null, null);
        $this->addColumn('PERIOD_END', 'PeriodEnd', 'DATE', false, null, null);
        $this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
        $this->addColumn('UPDATED_AT', 'UpdatedAt', 'TIMESTAMP', false, null, null);
        $this->addColumn('VERSION', 'Version', 'INTEGER', false, null, 0);
        $this->addColumn('VERSION_CREATED_AT', 'VersionCreatedAt', 'TIMESTAMP', false, null, null);
        $this->addColumn('VERSION_CREATED_BY', 'VersionCreatedBy', 'VARCHAR', false, 100, null);
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Dealer', '\\Dealer\\Model\\Dealer', RelationMap::MANY_TO_ONE, array('dealer_id' => 'id', ), 'CASCADE', null);
        $this->addRelation('DealerShedulesVersion', '\\Dealer\\Model\\DealerShedulesVersion', RelationMap::ONE_TO_MANY, array('id' => 'id', ), 'CASCADE', null, 'DealerShedulesVersions');
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
            'versionable' => array('version_column' => 'version', 'version_table' => '', 'log_created_at' => 'true', 'log_created_by' => 'true', 'log_comment' => 'false', 'version_created_at_column' => 'version_created_at', 'version_created_by_column' => 'version_created_by', 'version_comment_column' => 'version_comment', ),
        );
    } // getBehaviors()
    /**
     * Method to invalidate the instance pool of all tables related to dealer_shedules     * by a foreign key with ON DELETE CASCADE
     */
    public static function clearRelatedInstancePool()
    {
        // Invalidate objects in ".$this->getClassNameFromBuilder($joinedTableTableMapBuilder)." instance pool,
        // since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
                DealerShedulesVersionTableMap::clearInstancePool();
            }

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
        return $withPrefix ? DealerShedulesTableMap::CLASS_DEFAULT : DealerShedulesTableMap::OM_CLASS;
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
     * @return array (DealerShedules object, last column rank)
     */
    public static function populateObject($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        $key = DealerShedulesTableMap::getPrimaryKeyHashFromRow($row, $offset, $indexType);
        if (null !== ($obj = DealerShedulesTableMap::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $offset, true); // rehydrate
            $col = $offset + DealerShedulesTableMap::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = DealerShedulesTableMap::OM_CLASS;
            $obj = new $cls();
            $col = $obj->hydrate($row, $offset, false, $indexType);
            DealerShedulesTableMap::addInstanceToPool($obj, $key);
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
            $key = DealerShedulesTableMap::getPrimaryKeyHashFromRow($row, 0, $dataFetcher->getIndexType());
            if (null !== ($obj = DealerShedulesTableMap::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                DealerShedulesTableMap::addInstanceToPool($obj, $key);
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
            $criteria->addSelectColumn(DealerShedulesTableMap::ID);
            $criteria->addSelectColumn(DealerShedulesTableMap::DEALER_ID);
            $criteria->addSelectColumn(DealerShedulesTableMap::DAY);
            $criteria->addSelectColumn(DealerShedulesTableMap::BEGIN);
            $criteria->addSelectColumn(DealerShedulesTableMap::END);
            $criteria->addSelectColumn(DealerShedulesTableMap::CLOSED);
            $criteria->addSelectColumn(DealerShedulesTableMap::PERIOD_BEGIN);
            $criteria->addSelectColumn(DealerShedulesTableMap::PERIOD_END);
            $criteria->addSelectColumn(DealerShedulesTableMap::CREATED_AT);
            $criteria->addSelectColumn(DealerShedulesTableMap::UPDATED_AT);
            $criteria->addSelectColumn(DealerShedulesTableMap::VERSION);
            $criteria->addSelectColumn(DealerShedulesTableMap::VERSION_CREATED_AT);
            $criteria->addSelectColumn(DealerShedulesTableMap::VERSION_CREATED_BY);
        } else {
            $criteria->addSelectColumn($alias . '.ID');
            $criteria->addSelectColumn($alias . '.DEALER_ID');
            $criteria->addSelectColumn($alias . '.DAY');
            $criteria->addSelectColumn($alias . '.BEGIN');
            $criteria->addSelectColumn($alias . '.END');
            $criteria->addSelectColumn($alias . '.CLOSED');
            $criteria->addSelectColumn($alias . '.PERIOD_BEGIN');
            $criteria->addSelectColumn($alias . '.PERIOD_END');
            $criteria->addSelectColumn($alias . '.CREATED_AT');
            $criteria->addSelectColumn($alias . '.UPDATED_AT');
            $criteria->addSelectColumn($alias . '.VERSION');
            $criteria->addSelectColumn($alias . '.VERSION_CREATED_AT');
            $criteria->addSelectColumn($alias . '.VERSION_CREATED_BY');
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
        return Propel::getServiceContainer()->getDatabaseMap(DealerShedulesTableMap::DATABASE_NAME)->getTable(DealerShedulesTableMap::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this tableMap class.
     */
    public static function buildTableMap()
    {
      $dbMap = Propel::getServiceContainer()->getDatabaseMap(DealerShedulesTableMap::DATABASE_NAME);
      if (!$dbMap->hasTable(DealerShedulesTableMap::TABLE_NAME)) {
        $dbMap->addTableObject(new DealerShedulesTableMap());
      }
    }

    /**
     * Performs a DELETE on the database, given a DealerShedules or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or DealerShedules object or primary key or array of primary keys
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
            $con = Propel::getServiceContainer()->getWriteConnection(DealerShedulesTableMap::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            // rename for clarity
            $criteria = $values;
        } elseif ($values instanceof \Dealer\Model\DealerShedules) { // it's a model object
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(DealerShedulesTableMap::DATABASE_NAME);
            $criteria->add(DealerShedulesTableMap::ID, (array) $values, Criteria::IN);
        }

        $query = DealerShedulesQuery::create()->mergeWith($criteria);

        if ($values instanceof Criteria) { DealerShedulesTableMap::clearInstancePool();
        } elseif (!is_object($values)) { // it's a primary key, or an array of pks
            foreach ((array) $values as $singleval) { DealerShedulesTableMap::removeInstanceFromPool($singleval);
            }
        }

        return $query->delete($con);
    }

    /**
     * Deletes all rows from the dealer_shedules table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public static function doDeleteAll(ConnectionInterface $con = null)
    {
        return DealerShedulesQuery::create()->doDeleteAll($con);
    }

    /**
     * Performs an INSERT on the database, given a DealerShedules or Criteria object.
     *
     * @param mixed               $criteria Criteria or DealerShedules object containing data that is used to create the INSERT statement.
     * @param ConnectionInterface $con the ConnectionInterface connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *         rethrown wrapped into a PropelException.
     */
    public static function doInsert($criteria, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(DealerShedulesTableMap::DATABASE_NAME);
        }

        if ($criteria instanceof Criteria) {
            $criteria = clone $criteria; // rename for clarity
        } else {
            $criteria = $criteria->buildCriteria(); // build Criteria from DealerShedules object
        }

        if ($criteria->containsKey(DealerShedulesTableMap::ID) && $criteria->keyContainsValue(DealerShedulesTableMap::ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.DealerShedulesTableMap::ID.')');
        }


        // Set the correct dbName
        $query = DealerShedulesQuery::create()->mergeWith($criteria);

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

} // DealerShedulesTableMap
// This is the static code needed to register the TableMap for this table with the main Propel class.
//
DealerShedulesTableMap::buildTableMap();
