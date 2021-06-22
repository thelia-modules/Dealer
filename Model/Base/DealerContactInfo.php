<?php

namespace Dealer\Model\Base;

use \DateTime;
use \Exception;
use \PDO;
use Dealer\Model\DealerContact as ChildDealerContact;
use Dealer\Model\DealerContactInfo as ChildDealerContactInfo;
use Dealer\Model\DealerContactInfoI18n as ChildDealerContactInfoI18n;
use Dealer\Model\DealerContactInfoI18nQuery as ChildDealerContactInfoI18nQuery;
use Dealer\Model\DealerContactInfoQuery as ChildDealerContactInfoQuery;
use Dealer\Model\DealerContactInfoVersion as ChildDealerContactInfoVersion;
use Dealer\Model\DealerContactInfoVersionQuery as ChildDealerContactInfoVersionQuery;
use Dealer\Model\DealerContactQuery as ChildDealerContactQuery;
use Dealer\Model\DealerContactVersionQuery as ChildDealerContactVersionQuery;
use Dealer\Model\Map\DealerContactInfoTableMap;
use Dealer\Model\Map\DealerContactInfoVersionTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveRecord\ActiveRecordInterface;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\BadMethodCallException;
use Propel\Runtime\Exception\PropelException;
use Propel\Runtime\Map\TableMap;
use Propel\Runtime\Parser\AbstractParser;
use Propel\Runtime\Util\PropelDateTime;

abstract class DealerContactInfo implements ActiveRecordInterface
{
    /**
     * TableMap class name
     */
    const TABLE_MAP = '\\Dealer\\Model\\Map\\DealerContactInfoTableMap';


    /**
     * attribute to determine if this object has previously been saved.
     * @var boolean
     */
    protected $new = true;

    /**
     * attribute to determine whether this object has been deleted.
     * @var boolean
     */
    protected $deleted = false;

    /**
     * The columns that have been modified in current object.
     * Tracking modified columns allows us to only update modified columns.
     * @var array
     */
    protected $modifiedColumns = array();

    /**
     * The (virtual) columns that are added at runtime
     * The formatters can add supplementary columns based on a resultset
     * @var array
     */
    protected $virtualColumns = array();

    /**
     * The value for the id field.
     * @var        int
     */
    protected $id;

    /**
     * The value for the contact_id field.
     * @var        int
     */
    protected $contact_id;

    /**
     * The value for the contact_type field.
     * @var        int
     */
    protected $contact_type;

    /**
     * The value for the created_at field.
     * @var        string
     */
    protected $created_at;

    /**
     * The value for the updated_at field.
     * @var        string
     */
    protected $updated_at;

    /**
     * The value for the version field.
     * Note: this column has a database default value of: 0
     * @var        int
     */
    protected $version;

    /**
     * The value for the version_created_at field.
     * @var        string
     */
    protected $version_created_at;

    /**
     * The value for the version_created_by field.
     * @var        string
     */
    protected $version_created_by;

    /**
     * @var        DealerContact
     */
    protected $aDealerContact;

    /**
     * @var        ObjectCollection|ChildDealerContactInfoI18n[] Collection to store aggregation of ChildDealerContactInfoI18n objects.
     */
    protected $collDealerContactInfoI18ns;
    protected $collDealerContactInfoI18nsPartial;

    /**
     * @var        ObjectCollection|ChildDealerContactInfoVersion[] Collection to store aggregation of ChildDealerContactInfoVersion objects.
     */
    protected $collDealerContactInfoVersions;
    protected $collDealerContactInfoVersionsPartial;

    /**
     * Flag to prevent endless save loop, if this object is referenced
     * by another object which falls in this transaction.
     *
     * @var boolean
     */
    protected $alreadyInSave = false;

    // i18n behavior

    /**
     * Current locale
     * @var        string
     */
    protected $currentLocale = 'en_US';

    /**
     * Current translation objects
     * @var        array[ChildDealerContactInfoI18n]
     */
    protected $currentTranslations;

    // versionable behavior


    /**
     * @var bool
     */
    protected $enforceVersion = false;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $dealerContactInfoI18nsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $dealerContactInfoVersionsScheduledForDeletion = null;

    /**
     * Applies default values to this object.
     * This method should be called from the object's constructor (or
     * equivalent initialization method).
     * @see __construct()
     */
    public function applyDefaultValues()
    {
        $this->version = 0;
    }

    /**
     * Initializes internal state of Dealer\Model\Base\DealerContactInfo object.
     * @see applyDefaults()
     */
    public function __construct()
    {
        $this->applyDefaultValues();
    }

    /**
     * Returns whether the object has been modified.
     *
     * @return boolean True if the object has been modified.
     */
    public function isModified()
    {
        return !!$this->modifiedColumns;
    }

    /**
     * Has specified column been modified?
     *
     * @param  string  $col column fully qualified name (TableMap::TYPE_COLNAME), e.g. Book::AUTHOR_ID
     * @return boolean True if $col has been modified.
     */
    public function isColumnModified($col)
    {
        return $this->modifiedColumns && isset($this->modifiedColumns[$col]);
    }

    /**
     * Get the columns that have been modified in this object.
     * @return array A unique list of the modified column names for this object.
     */
    public function getModifiedColumns()
    {
        return $this->modifiedColumns ? array_keys($this->modifiedColumns) : [];
    }

    /**
     * Returns whether the object has ever been saved.  This will
     * be false, if the object was retrieved from storage or was created
     * and then saved.
     *
     * @return boolean true, if the object has never been persisted.
     */
    public function isNew()
    {
        return $this->new;
    }

    /**
     * Setter for the isNew attribute.  This method will be called
     * by Propel-generated children and objects.
     *
     * @param boolean $b the state of the object.
     */
    public function setNew($b)
    {
        $this->new = (Boolean) $b;
    }

    /**
     * Whether this object has been deleted.
     * @return boolean The deleted state of this object.
     */
    public function isDeleted()
    {
        return $this->deleted;
    }

    /**
     * Specify whether this object has been deleted.
     * @param  boolean $b The deleted state of this object.
     * @return void
     */
    public function setDeleted($b)
    {
        $this->deleted = (Boolean) $b;
    }

    /**
     * Sets the modified state for the object to be false.
     * @param  string $col If supplied, only the specified column is reset.
     * @return void
     */
    public function resetModified($col = null)
    {
        if (null !== $col) {
            if (isset($this->modifiedColumns[$col])) {
                unset($this->modifiedColumns[$col]);
            }
        } else {
            $this->modifiedColumns = array();
        }
    }

    /**
     * Compares this with another <code>DealerContactInfo</code> instance.  If
     * <code>obj</code> is an instance of <code>DealerContactInfo</code>, delegates to
     * <code>equals(DealerContactInfo)</code>.  Otherwise, returns <code>false</code>.
     *
     * @param  mixed   $obj The object to compare to.
     * @return boolean Whether equal to the object specified.
     */
    public function equals($obj)
    {
        $thisclazz = get_class($this);
        if (!is_object($obj) || !($obj instanceof $thisclazz)) {
            return false;
        }

        if ($this === $obj) {
            return true;
        }

        if (null === $this->getPrimaryKey()
            || null === $obj->getPrimaryKey())  {
            return false;
        }

        return $this->getPrimaryKey() === $obj->getPrimaryKey();
    }

    /**
     * If the primary key is not null, return the hashcode of the
     * primary key. Otherwise, return the hash code of the object.
     *
     * @return int Hashcode
     */
    public function hashCode()
    {
        if (null !== $this->getPrimaryKey()) {
            return crc32(serialize($this->getPrimaryKey()));
        }

        return crc32(serialize(clone $this));
    }

    /**
     * Get the associative array of the virtual columns in this object
     *
     * @return array
     */
    public function getVirtualColumns()
    {
        return $this->virtualColumns;
    }

    /**
     * Checks the existence of a virtual column in this object
     *
     * @param  string  $name The virtual column name
     * @return boolean
     */
    public function hasVirtualColumn($name)
    {
        return array_key_exists($name, $this->virtualColumns);
    }

    /**
     * Get the value of a virtual column in this object
     *
     * @param  string $name The virtual column name
     * @return mixed
     *
     * @throws PropelException
     */
    public function getVirtualColumn($name)
    {
        if (!$this->hasVirtualColumn($name)) {
            throw new PropelException(sprintf('Cannot get value of inexistent virtual column %s.', $name));
        }

        return $this->virtualColumns[$name];
    }

    /**
     * Set the value of a virtual column in this object
     *
     * @param string $name  The virtual column name
     * @param mixed  $value The value to give to the virtual column
     *
     * @return DealerContactInfo The current object, for fluid interface
     */
    public function setVirtualColumn($name, $value)
    {
        $this->virtualColumns[$name] = $value;

        return $this;
    }

    /**
     * Logs a message using Propel::log().
     *
     * @param  string  $msg
     * @param  int     $priority One of the Propel::LOG_* logging levels
     * @return boolean
     */
    protected function log($msg, $priority = Propel::LOG_INFO)
    {
        return Propel::log(get_class($this) . ': ' . $msg, $priority);
    }

    /**
     * Populate the current object from a string, using a given parser format
     * <code>
     * $book = new Book();
     * $book->importFrom('JSON', '{"Id":9012,"Title":"Don Juan","ISBN":"0140422161","Price":12.99,"PublisherId":1234,"AuthorId":5678}');
     * </code>
     *
     * @param mixed $parser A AbstractParser instance,
     *                       or a format name ('XML', 'YAML', 'JSON', 'CSV')
     * @param string $data The source data to import from
     *
     * @return DealerContactInfo The current object, for fluid interface
     */
    public function importFrom($parser, $data)
    {
        if (!$parser instanceof AbstractParser) {
            $parser = AbstractParser::getParser($parser);
        }

        $this->fromArray($parser->toArray($data), TableMap::TYPE_PHPNAME);

        return $this;
    }

    /**
     * Export the current object properties to a string, using a given parser format
     * <code>
     * $book = BookQuery::create()->findPk(9012);
     * echo $book->exportTo('JSON');
     *  => {"Id":9012,"Title":"Don Juan","ISBN":"0140422161","Price":12.99,"PublisherId":1234,"AuthorId":5678}');
     * </code>
     *
     * @param  mixed   $parser                 A AbstractParser instance, or a format name ('XML', 'YAML', 'JSON', 'CSV')
     * @param  boolean $includeLazyLoadColumns (optional) Whether to include lazy load(ed) columns. Defaults to TRUE.
     * @return string  The exported data
     */
    public function exportTo($parser, $includeLazyLoadColumns = true)
    {
        if (!$parser instanceof AbstractParser) {
            $parser = AbstractParser::getParser($parser);
        }

        return $parser->fromArray($this->toArray(TableMap::TYPE_PHPNAME, $includeLazyLoadColumns, array(), true));
    }

    /**
     * Clean up internal collections prior to serializing
     * Avoids recursive loops that turn into segmentation faults when serializing
     */
    public function __sleep()
    {
        $this->clearAllReferences();

        return array_keys(get_object_vars($this));
    }

    /**
     * Get the [id] column value.
     *
     * @return   int
     */
    public function getId()
    {

        return $this->id;
    }

    /**
     * Get the [contact_id] column value.
     *
     * @return   int
     */
    public function getContactId()
    {

        return $this->contact_id;
    }

    /**
     * Get the [contact_type] column value.
     *
     * @return   int
     */
    public function getContactType()
    {
        if (null === $this->contact_type) {
            return null;
        }
        $valueSet = DealerContactInfoTableMap::getValueSet(DealerContactInfoTableMap::CONTACT_TYPE);
        if (!isset($valueSet[$this->contact_type])) {
            throw new PropelException('Unknown stored enum key: ' . $this->contact_type);
        }

        return $valueSet[$this->contact_type];
    }

    /**
     * Get the [optionally formatted] temporal [created_at] column value.
     *
     *
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                            If format is NULL, then the raw \DateTime object will be returned.
     *
     * @return mixed Formatted date/time value as string or \DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
     *
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getCreatedAt($format = NULL)
    {
        if ($format === null) {
            return $this->created_at;
        } else {
            return $this->created_at instanceof \DateTime ? $this->created_at->format($format) : null;
        }
    }

    /**
     * Get the [optionally formatted] temporal [updated_at] column value.
     *
     *
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                            If format is NULL, then the raw \DateTime object will be returned.
     *
     * @return mixed Formatted date/time value as string or \DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
     *
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getUpdatedAt($format = NULL)
    {
        if ($format === null) {
            return $this->updated_at;
        } else {
            return $this->updated_at instanceof \DateTime ? $this->updated_at->format($format) : null;
        }
    }

    /**
     * Get the [version] column value.
     *
     * @return   int
     */
    public function getVersion()
    {

        return $this->version;
    }

    /**
     * Get the [optionally formatted] temporal [version_created_at] column value.
     *
     *
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                            If format is NULL, then the raw \DateTime object will be returned.
     *
     * @return mixed Formatted date/time value as string or \DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
     *
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getVersionCreatedAt($format = NULL)
    {
        if ($format === null) {
            return $this->version_created_at;
        } else {
            return $this->version_created_at instanceof \DateTime ? $this->version_created_at->format($format) : null;
        }
    }

    /**
     * Get the [version_created_by] column value.
     *
     * @return   string
     */
    public function getVersionCreatedBy()
    {

        return $this->version_created_by;
    }

    /**
     * Set the value of [id] column.
     *
     * @param      int $v new value
     * @return   \Dealer\Model\DealerContactInfo The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[DealerContactInfoTableMap::ID] = true;
        }


        return $this;
    } // setId()

    /**
     * Set the value of [contact_id] column.
     *
     * @param      int $v new value
     * @return   \Dealer\Model\DealerContactInfo The current object (for fluent API support)
     */
    public function setContactId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->contact_id !== $v) {
            $this->contact_id = $v;
            $this->modifiedColumns[DealerContactInfoTableMap::CONTACT_ID] = true;
        }

        if ($this->aDealerContact !== null && $this->aDealerContact->getId() !== $v) {
            $this->aDealerContact = null;
        }


        return $this;
    } // setContactId()

    /**
     * Set the value of [contact_type] column.
     *
     * @param      int $v new value
     * @return   \Dealer\Model\DealerContactInfo The current object (for fluent API support)
     */
    public function setContactType($v)
    {
        if ($v !== null) {
            $valueSet = DealerContactInfoTableMap::getValueSet(DealerContactInfoTableMap::CONTACT_TYPE);
            if (!in_array($v, $valueSet)) {
                throw new PropelException(sprintf('Value "%s" is not accepted in this enumerated column', $v));
            }
            $v = array_search($v, $valueSet);
        }

        if ($this->contact_type !== $v) {
            $this->contact_type = $v;
            $this->modifiedColumns[DealerContactInfoTableMap::CONTACT_TYPE] = true;
        }


        return $this;
    } // setContactType()

    /**
     * Sets the value of [created_at] column to a normalized version of the date/time value specified.
     *
     * @param      mixed $v string, integer (timestamp), or \DateTime value.
     *               Empty strings are treated as NULL.
     * @return   \Dealer\Model\DealerContactInfo The current object (for fluent API support)
     */
    public function setCreatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, '\DateTime');
        if ($this->created_at !== null || $dt !== null) {
            if ($dt !== $this->created_at) {
                $this->created_at = $dt;
                $this->modifiedColumns[DealerContactInfoTableMap::CREATED_AT] = true;
            }
        } // if either are not null


        return $this;
    } // setCreatedAt()

    /**
     * Sets the value of [updated_at] column to a normalized version of the date/time value specified.
     *
     * @param      mixed $v string, integer (timestamp), or \DateTime value.
     *               Empty strings are treated as NULL.
     * @return   \Dealer\Model\DealerContactInfo The current object (for fluent API support)
     */
    public function setUpdatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, '\DateTime');
        if ($this->updated_at !== null || $dt !== null) {
            if ($dt !== $this->updated_at) {
                $this->updated_at = $dt;
                $this->modifiedColumns[DealerContactInfoTableMap::UPDATED_AT] = true;
            }
        } // if either are not null


        return $this;
    } // setUpdatedAt()

    /**
     * Set the value of [version] column.
     *
     * @param      int $v new value
     * @return   \Dealer\Model\DealerContactInfo The current object (for fluent API support)
     */
    public function setVersion($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->version !== $v) {
            $this->version = $v;
            $this->modifiedColumns[DealerContactInfoTableMap::VERSION] = true;
        }


        return $this;
    } // setVersion()

    /**
     * Sets the value of [version_created_at] column to a normalized version of the date/time value specified.
     *
     * @param      mixed $v string, integer (timestamp), or \DateTime value.
     *               Empty strings are treated as NULL.
     * @return   \Dealer\Model\DealerContactInfo The current object (for fluent API support)
     */
    public function setVersionCreatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, '\DateTime');
        if ($this->version_created_at !== null || $dt !== null) {
            if ($dt !== $this->version_created_at) {
                $this->version_created_at = $dt;
                $this->modifiedColumns[DealerContactInfoTableMap::VERSION_CREATED_AT] = true;
            }
        } // if either are not null


        return $this;
    } // setVersionCreatedAt()

    /**
     * Set the value of [version_created_by] column.
     *
     * @param      string $v new value
     * @return   \Dealer\Model\DealerContactInfo The current object (for fluent API support)
     */
    public function setVersionCreatedBy($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->version_created_by !== $v) {
            $this->version_created_by = $v;
            $this->modifiedColumns[DealerContactInfoTableMap::VERSION_CREATED_BY] = true;
        }


        return $this;
    } // setVersionCreatedBy()

    /**
     * Indicates whether the columns in this object are only set to default values.
     *
     * This method can be used in conjunction with isModified() to indicate whether an object is both
     * modified _and_ has some values set which are non-default.
     *
     * @return boolean Whether the columns in this object are only been set with default values.
     */
    public function hasOnlyDefaultValues()
    {
            if ($this->version !== 0) {
                return false;
            }

        // otherwise, everything was equal, so return TRUE
        return true;
    } // hasOnlyDefaultValues()

    /**
     * Hydrates (populates) the object variables with values from the database resultset.
     *
     * An offset (0-based "start column") is specified so that objects can be hydrated
     * with a subset of the columns in the resultset rows.  This is needed, for example,
     * for results of JOIN queries where the resultset row includes columns from two or
     * more tables.
     *
     * @param array   $row       The row returned by DataFetcher->fetch().
     * @param int     $startcol  0-based offset column which indicates which restultset column to start with.
     * @param boolean $rehydrate Whether this object is being re-hydrated from the database.
     * @param string  $indexType The index type of $row. Mostly DataFetcher->getIndexType().
                                  One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME
     *                            TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *
     * @return int             next starting column
     * @throws PropelException - Any caught Exception will be rewrapped as a PropelException.
     */
    public function hydrate($row, $startcol = 0, $rehydrate = false, $indexType = TableMap::TYPE_NUM)
    {
        try {


            $col = $row[TableMap::TYPE_NUM == $indexType ? 0 + $startcol : DealerContactInfoTableMap::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
            $this->id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 1 + $startcol : DealerContactInfoTableMap::translateFieldName('ContactId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->contact_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 2 + $startcol : DealerContactInfoTableMap::translateFieldName('ContactType', TableMap::TYPE_PHPNAME, $indexType)];
            $this->contact_type = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 3 + $startcol : DealerContactInfoTableMap::translateFieldName('CreatedAt', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00 00:00:00') {
                $col = null;
            }
            $this->created_at = (null !== $col) ? PropelDateTime::newInstance($col, null, '\DateTime') : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 4 + $startcol : DealerContactInfoTableMap::translateFieldName('UpdatedAt', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00 00:00:00') {
                $col = null;
            }
            $this->updated_at = (null !== $col) ? PropelDateTime::newInstance($col, null, '\DateTime') : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 5 + $startcol : DealerContactInfoTableMap::translateFieldName('Version', TableMap::TYPE_PHPNAME, $indexType)];
            $this->version = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 6 + $startcol : DealerContactInfoTableMap::translateFieldName('VersionCreatedAt', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00 00:00:00') {
                $col = null;
            }
            $this->version_created_at = (null !== $col) ? PropelDateTime::newInstance($col, null, '\DateTime') : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 7 + $startcol : DealerContactInfoTableMap::translateFieldName('VersionCreatedBy', TableMap::TYPE_PHPNAME, $indexType)];
            $this->version_created_by = (null !== $col) ? (string) $col : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }

            return $startcol + 8; // 8 = DealerContactInfoTableMap::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating \Dealer\Model\DealerContactInfo object", 0, $e);
        }
    }

    /**
     * Checks and repairs the internal consistency of the object.
     *
     * This method is executed after an already-instantiated object is re-hydrated
     * from the database.  It exists to check any foreign keys to make sure that
     * the objects related to the current object are correct based on foreign key.
     *
     * You can override this method in the stub class, but you should always invoke
     * the base method from the overridden method (i.e. parent::ensureConsistency()),
     * in case your model changes.
     *
     * @throws PropelException
     */
    public function ensureConsistency()
    {
        if ($this->aDealerContact !== null && $this->contact_id !== $this->aDealerContact->getId()) {
            $this->aDealerContact = null;
        }
    } // ensureConsistency

    /**
     * Reloads this object from datastore based on primary key and (optionally) resets all associated objects.
     *
     * This will only work if the object has been saved and has a valid primary key set.
     *
     * @param      boolean $deep (optional) Whether to also de-associated any related objects.
     * @param      ConnectionInterface $con (optional) The ConnectionInterface connection to use.
     * @return void
     * @throws PropelException - if this object is deleted, unsaved or doesn't have pk match in db
     */
    public function reload($deep = false, ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("Cannot reload a deleted object.");
        }

        if ($this->isNew()) {
            throw new PropelException("Cannot reload an unsaved object.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(DealerContactInfoTableMap::DATABASE_NAME);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $dataFetcher = ChildDealerContactInfoQuery::create(null, $this->buildPkeyCriteria())->setFormatter(ModelCriteria::FORMAT_STATEMENT)->find($con);
        $row = $dataFetcher->fetch();
        $dataFetcher->close();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true, $dataFetcher->getIndexType()); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aDealerContact = null;
            $this->collDealerContactInfoI18ns = null;

            $this->collDealerContactInfoVersions = null;

        } // if (deep)
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param      ConnectionInterface $con
     * @return void
     * @throws PropelException
     * @see DealerContactInfo::setDeleted()
     * @see DealerContactInfo::isDeleted()
     */
    public function delete(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(DealerContactInfoTableMap::DATABASE_NAME);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = ChildDealerContactInfoQuery::create()
                ->filterByPrimaryKey($this->getPrimaryKey());
            $ret = $this->preDelete($con);
            if ($ret) {
                $deleteQuery->delete($con);
                $this->postDelete($con);
                $con->commit();
                $this->setDeleted(true);
            } else {
                $con->commit();
            }
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Persists this object to the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All modified related objects will also be persisted in the doSave()
     * method.  This method wraps all precipitate database operations in a
     * single transaction.
     *
     * @param      ConnectionInterface $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @see doSave()
     */
    public function save(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("You cannot save an object that has been deleted.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(DealerContactInfoTableMap::DATABASE_NAME);
        }

        $con->beginTransaction();
        $isInsert = $this->isNew();
        try {
            $ret = $this->preSave($con);
            // versionable behavior
            if ($this->isVersioningNecessary()) {
                $this->setVersion($this->isNew() ? 1 : $this->getLastVersionNumber($con) + 1);
                if (!$this->isColumnModified(DealerContactInfoTableMap::VERSION_CREATED_AT)) {
                    $this->setVersionCreatedAt(time());
                }
                $createVersion = true; // for postSave hook
            }
            if ($isInsert) {
                $ret = $ret && $this->preInsert($con);
                // timestampable behavior
                if (!$this->isColumnModified(DealerContactInfoTableMap::CREATED_AT)) {
                    $this->setCreatedAt(time());
                }
                if (!$this->isColumnModified(DealerContactInfoTableMap::UPDATED_AT)) {
                    $this->setUpdatedAt(time());
                }
            } else {
                $ret = $ret && $this->preUpdate($con);
                // timestampable behavior
                if ($this->isModified() && !$this->isColumnModified(DealerContactInfoTableMap::UPDATED_AT)) {
                    $this->setUpdatedAt(time());
                }
            }
            if ($ret) {
                $affectedRows = $this->doSave($con);
                if ($isInsert) {
                    $this->postInsert($con);
                } else {
                    $this->postUpdate($con);
                }
                $this->postSave($con);
                // versionable behavior
                if (isset($createVersion)) {
                    $this->addVersion($con);
                }
                DealerContactInfoTableMap::addInstanceToPool($this);
            } else {
                $affectedRows = 0;
            }
            $con->commit();

            return $affectedRows;
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Performs the work of inserting or updating the row in the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All related objects are also updated in this method.
     *
     * @param      ConnectionInterface $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @see save()
     */
    protected function doSave(ConnectionInterface $con)
    {
        $affectedRows = 0; // initialize var to track total num of affected rows
        if (!$this->alreadyInSave) {
            $this->alreadyInSave = true;

            // We call the save method on the following object(s) if they
            // were passed to this object by their corresponding set
            // method.  This object relates to these object(s) by a
            // foreign key reference.

            if ($this->aDealerContact !== null) {
                if ($this->aDealerContact->isModified() || $this->aDealerContact->isNew()) {
                    $affectedRows += $this->aDealerContact->save($con);
                }
                $this->setDealerContact($this->aDealerContact);
            }

            if ($this->isNew() || $this->isModified()) {
                // persist changes
                if ($this->isNew()) {
                    $this->doInsert($con);
                } else {
                    $this->doUpdate($con);
                }
                $affectedRows += 1;
                $this->resetModified();
            }

            if ($this->dealerContactInfoI18nsScheduledForDeletion !== null) {
                if (!$this->dealerContactInfoI18nsScheduledForDeletion->isEmpty()) {
                    \Dealer\Model\DealerContactInfoI18nQuery::create()
                        ->filterByPrimaryKeys($this->dealerContactInfoI18nsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->dealerContactInfoI18nsScheduledForDeletion = null;
                }
            }

                if ($this->collDealerContactInfoI18ns !== null) {
            foreach ($this->collDealerContactInfoI18ns as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->dealerContactInfoVersionsScheduledForDeletion !== null) {
                if (!$this->dealerContactInfoVersionsScheduledForDeletion->isEmpty()) {
                    \Dealer\Model\DealerContactInfoVersionQuery::create()
                        ->filterByPrimaryKeys($this->dealerContactInfoVersionsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->dealerContactInfoVersionsScheduledForDeletion = null;
                }
            }

                if ($this->collDealerContactInfoVersions !== null) {
            foreach ($this->collDealerContactInfoVersions as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            $this->alreadyInSave = false;

        }

        return $affectedRows;
    } // doSave()

    /**
     * Insert the row in the database.
     *
     * @param      ConnectionInterface $con
     *
     * @throws PropelException
     * @see doSave()
     */
    protected function doInsert(ConnectionInterface $con)
    {
        $modifiedColumns = array();
        $index = 0;

        $this->modifiedColumns[DealerContactInfoTableMap::ID] = true;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . DealerContactInfoTableMap::ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(DealerContactInfoTableMap::ID)) {
            $modifiedColumns[':p' . $index++]  = 'ID';
        }
        if ($this->isColumnModified(DealerContactInfoTableMap::CONTACT_ID)) {
            $modifiedColumns[':p' . $index++]  = 'CONTACT_ID';
        }
        if ($this->isColumnModified(DealerContactInfoTableMap::CONTACT_TYPE)) {
            $modifiedColumns[':p' . $index++]  = 'CONTACT_TYPE';
        }
        if ($this->isColumnModified(DealerContactInfoTableMap::CREATED_AT)) {
            $modifiedColumns[':p' . $index++]  = 'CREATED_AT';
        }
        if ($this->isColumnModified(DealerContactInfoTableMap::UPDATED_AT)) {
            $modifiedColumns[':p' . $index++]  = 'UPDATED_AT';
        }
        if ($this->isColumnModified(DealerContactInfoTableMap::VERSION)) {
            $modifiedColumns[':p' . $index++]  = 'VERSION';
        }
        if ($this->isColumnModified(DealerContactInfoTableMap::VERSION_CREATED_AT)) {
            $modifiedColumns[':p' . $index++]  = 'VERSION_CREATED_AT';
        }
        if ($this->isColumnModified(DealerContactInfoTableMap::VERSION_CREATED_BY)) {
            $modifiedColumns[':p' . $index++]  = 'VERSION_CREATED_BY';
        }

        $sql = sprintf(
            'INSERT INTO dealer_contact_info (%s) VALUES (%s)',
            implode(', ', $modifiedColumns),
            implode(', ', array_keys($modifiedColumns))
        );

        try {
            $stmt = $con->prepare($sql);
            foreach ($modifiedColumns as $identifier => $columnName) {
                switch ($columnName) {
                    case 'ID':
                        $stmt->bindValue($identifier, $this->id, PDO::PARAM_INT);
                        break;
                    case 'CONTACT_ID':
                        $stmt->bindValue($identifier, $this->contact_id, PDO::PARAM_INT);
                        break;
                    case 'CONTACT_TYPE':
                        $stmt->bindValue($identifier, $this->contact_type, PDO::PARAM_INT);
                        break;
                    case 'CREATED_AT':
                        $stmt->bindValue($identifier, $this->created_at ? $this->created_at->format("Y-m-d H:i:s") : null, PDO::PARAM_STR);
                        break;
                    case 'UPDATED_AT':
                        $stmt->bindValue($identifier, $this->updated_at ? $this->updated_at->format("Y-m-d H:i:s") : null, PDO::PARAM_STR);
                        break;
                    case 'VERSION':
                        $stmt->bindValue($identifier, $this->version, PDO::PARAM_INT);
                        break;
                    case 'VERSION_CREATED_AT':
                        $stmt->bindValue($identifier, $this->version_created_at ? $this->version_created_at->format("Y-m-d H:i:s") : null, PDO::PARAM_STR);
                        break;
                    case 'VERSION_CREATED_BY':
                        $stmt->bindValue($identifier, $this->version_created_by, PDO::PARAM_STR);
                        break;
                }
            }
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute INSERT statement [%s]', $sql), 0, $e);
        }

        try {
            $pk = $con->lastInsertId();
        } catch (Exception $e) {
            throw new PropelException('Unable to get autoincrement id.', 0, $e);
        }
        $this->setId($pk);

        $this->setNew(false);
    }

    /**
     * Update the row in the database.
     *
     * @param      ConnectionInterface $con
     *
     * @return Integer Number of updated rows
     * @see doSave()
     */
    protected function doUpdate(ConnectionInterface $con)
    {
        $selectCriteria = $this->buildPkeyCriteria();
        $valuesCriteria = $this->buildCriteria();

        return $selectCriteria->doUpdate($valuesCriteria, $con);
    }

    /**
     * Retrieves a field from the object by name passed in as a string.
     *
     * @param      string $name name
     * @param      string $type The type of fieldname the $name is of:
     *                     one of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME
     *                     TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *                     Defaults to TableMap::TYPE_PHPNAME.
     * @return mixed Value of field.
     */
    public function getByName($name, $type = TableMap::TYPE_PHPNAME)
    {
        $pos = DealerContactInfoTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);
        $field = $this->getByPosition($pos);

        return $field;
    }

    /**
     * Retrieves a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param      int $pos position in xml schema
     * @return mixed Value of field at $pos
     */
    public function getByPosition($pos)
    {
        switch ($pos) {
            case 0:
                return $this->getId();
                break;
            case 1:
                return $this->getContactId();
                break;
            case 2:
                return $this->getContactType();
                break;
            case 3:
                return $this->getCreatedAt();
                break;
            case 4:
                return $this->getUpdatedAt();
                break;
            case 5:
                return $this->getVersion();
                break;
            case 6:
                return $this->getVersionCreatedAt();
                break;
            case 7:
                return $this->getVersionCreatedBy();
                break;
            default:
                return null;
                break;
        } // switch()
    }

    /**
     * Exports the object as an array.
     *
     * You can specify the key type of the array by passing one of the class
     * type constants.
     *
     * @param     string  $keyType (optional) One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME,
     *                    TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *                    Defaults to TableMap::TYPE_PHPNAME.
     * @param     boolean $includeLazyLoadColumns (optional) Whether to include lazy loaded columns. Defaults to TRUE.
     * @param     array $alreadyDumpedObjects List of objects to skip to avoid recursion
     * @param     boolean $includeForeignObjects (optional) Whether to include hydrated related objects. Default to FALSE.
     *
     * @return array an associative array containing the field names (as keys) and field values
     */
    public function toArray($keyType = TableMap::TYPE_PHPNAME, $includeLazyLoadColumns = true, $alreadyDumpedObjects = array(), $includeForeignObjects = false)
    {
        if (isset($alreadyDumpedObjects['DealerContactInfo'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['DealerContactInfo'][$this->getPrimaryKey()] = true;
        $keys = DealerContactInfoTableMap::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getContactId(),
            $keys[2] => $this->getContactType(),
            $keys[3] => $this->getCreatedAt(),
            $keys[4] => $this->getUpdatedAt(),
            $keys[5] => $this->getVersion(),
            $keys[6] => $this->getVersionCreatedAt(),
            $keys[7] => $this->getVersionCreatedBy(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->aDealerContact) {
                $result['DealerContact'] = $this->aDealerContact->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->collDealerContactInfoI18ns) {
                $result['DealerContactInfoI18ns'] = $this->collDealerContactInfoI18ns->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collDealerContactInfoVersions) {
                $result['DealerContactInfoVersions'] = $this->collDealerContactInfoVersions->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
        }

        return $result;
    }

    /**
     * Sets a field from the object by name passed in as a string.
     *
     * @param      string $name
     * @param      mixed  $value field value
     * @param      string $type The type of fieldname the $name is of:
     *                     one of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME
     *                     TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *                     Defaults to TableMap::TYPE_PHPNAME.
     * @return void
     */
    public function setByName($name, $value, $type = TableMap::TYPE_PHPNAME)
    {
        $pos = DealerContactInfoTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);

        return $this->setByPosition($pos, $value);
    }

    /**
     * Sets a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param      int $pos position in xml schema
     * @param      mixed $value field value
     * @return void
     */
    public function setByPosition($pos, $value)
    {
        switch ($pos) {
            case 0:
                $this->setId($value);
                break;
            case 1:
                $this->setContactId($value);
                break;
            case 2:
                $valueSet = DealerContactInfoTableMap::getValueSet(DealerContactInfoTableMap::CONTACT_TYPE);
                if (isset($valueSet[$value])) {
                    $value = $valueSet[$value];
                }
                $this->setContactType($value);
                break;
            case 3:
                $this->setCreatedAt($value);
                break;
            case 4:
                $this->setUpdatedAt($value);
                break;
            case 5:
                $this->setVersion($value);
                break;
            case 6:
                $this->setVersionCreatedAt($value);
                break;
            case 7:
                $this->setVersionCreatedBy($value);
                break;
        } // switch()
    }

    /**
     * Populates the object using an array.
     *
     * This is particularly useful when populating an object from one of the
     * request arrays (e.g. $_POST).  This method goes through the column
     * names, checking to see whether a matching key exists in populated
     * array. If so the setByName() method is called for that column.
     *
     * You can specify the key type of the array by additionally passing one
     * of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_STUDLYPHPNAME,
     * TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     * The default key type is the column's TableMap::TYPE_PHPNAME.
     *
     * @param      array  $arr     An array to populate the object from.
     * @param      string $keyType The type of keys the array uses.
     * @return void
     */
    public function fromArray($arr, $keyType = TableMap::TYPE_PHPNAME)
    {
        $keys = DealerContactInfoTableMap::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setContactId($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setContactType($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setCreatedAt($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setUpdatedAt($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setVersion($arr[$keys[5]]);
        if (array_key_exists($keys[6], $arr)) $this->setVersionCreatedAt($arr[$keys[6]]);
        if (array_key_exists($keys[7], $arr)) $this->setVersionCreatedBy($arr[$keys[7]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(DealerContactInfoTableMap::DATABASE_NAME);

        if ($this->isColumnModified(DealerContactInfoTableMap::ID)) $criteria->add(DealerContactInfoTableMap::ID, $this->id);
        if ($this->isColumnModified(DealerContactInfoTableMap::CONTACT_ID)) $criteria->add(DealerContactInfoTableMap::CONTACT_ID, $this->contact_id);
        if ($this->isColumnModified(DealerContactInfoTableMap::CONTACT_TYPE)) $criteria->add(DealerContactInfoTableMap::CONTACT_TYPE, $this->contact_type);
        if ($this->isColumnModified(DealerContactInfoTableMap::CREATED_AT)) $criteria->add(DealerContactInfoTableMap::CREATED_AT, $this->created_at);
        if ($this->isColumnModified(DealerContactInfoTableMap::UPDATED_AT)) $criteria->add(DealerContactInfoTableMap::UPDATED_AT, $this->updated_at);
        if ($this->isColumnModified(DealerContactInfoTableMap::VERSION)) $criteria->add(DealerContactInfoTableMap::VERSION, $this->version);
        if ($this->isColumnModified(DealerContactInfoTableMap::VERSION_CREATED_AT)) $criteria->add(DealerContactInfoTableMap::VERSION_CREATED_AT, $this->version_created_at);
        if ($this->isColumnModified(DealerContactInfoTableMap::VERSION_CREATED_BY)) $criteria->add(DealerContactInfoTableMap::VERSION_CREATED_BY, $this->version_created_by);

        return $criteria;
    }

    /**
     * Builds a Criteria object containing the primary key for this object.
     *
     * Unlike buildCriteria() this method includes the primary key values regardless
     * of whether or not they have been modified.
     *
     * @return Criteria The Criteria object containing value(s) for primary key(s).
     */
    public function buildPkeyCriteria()
    {
        $criteria = new Criteria(DealerContactInfoTableMap::DATABASE_NAME);
        $criteria->add(DealerContactInfoTableMap::ID, $this->id);

        return $criteria;
    }

    /**
     * Returns the primary key for this object (row).
     * @return   int
     */
    public function getPrimaryKey()
    {
        return $this->getId();
    }

    /**
     * Generic method to set the primary key (id column).
     *
     * @param       int $key Primary key.
     * @return void
     */
    public function setPrimaryKey($key)
    {
        $this->setId($key);
    }

    /**
     * Returns true if the primary key for this object is null.
     * @return boolean
     */
    public function isPrimaryKeyNull()
    {

        return null === $this->getId();
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      object $copyObj An object of \Dealer\Model\DealerContactInfo (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setContactId($this->getContactId());
        $copyObj->setContactType($this->getContactType());
        $copyObj->setCreatedAt($this->getCreatedAt());
        $copyObj->setUpdatedAt($this->getUpdatedAt());
        $copyObj->setVersion($this->getVersion());
        $copyObj->setVersionCreatedAt($this->getVersionCreatedAt());
        $copyObj->setVersionCreatedBy($this->getVersionCreatedBy());

        if ($deepCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);

            foreach ($this->getDealerContactInfoI18ns() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addDealerContactInfoI18n($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getDealerContactInfoVersions() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addDealerContactInfoVersion($relObj->copy($deepCopy));
                }
            }

        } // if ($deepCopy)

        if ($makeNew) {
            $copyObj->setNew(true);
            $copyObj->setId(NULL); // this is a auto-increment column, so set to default value
        }
    }

    /**
     * Makes a copy of this object that will be inserted as a new row in table when saved.
     * It creates a new object filling in the simple attributes, but skipping any primary
     * keys that are defined for the table.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @return                 \Dealer\Model\DealerContactInfo Clone of current object.
     * @throws PropelException
     */
    public function copy($deepCopy = false)
    {
        // we use get_class(), because this might be a subclass
        $clazz = get_class($this);
        $copyObj = new $clazz();
        $this->copyInto($copyObj, $deepCopy);

        return $copyObj;
    }

    /**
     * Declares an association between this object and a ChildDealerContact object.
     *
     * @param                  ChildDealerContact $v
     * @return                 \Dealer\Model\DealerContactInfo The current object (for fluent API support)
     * @throws PropelException
     */
    public function setDealerContact(ChildDealerContact $v = null)
    {
        if ($v === null) {
            $this->setContactId(NULL);
        } else {
            $this->setContactId($v->getId());
        }

        $this->aDealerContact = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the ChildDealerContact object, it will not be re-added.
        if ($v !== null) {
            $v->addDealerContactInfo($this);
        }


        return $this;
    }


    /**
     * Get the associated ChildDealerContact object
     *
     * @param      ConnectionInterface $con Optional Connection object.
     * @return                 ChildDealerContact The associated ChildDealerContact object.
     * @throws PropelException
     */
    public function getDealerContact(ConnectionInterface $con = null)
    {
        if ($this->aDealerContact === null && ($this->contact_id !== null)) {
            $this->aDealerContact = ChildDealerContactQuery::create()->findPk($this->contact_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aDealerContact->addDealerContactInfos($this);
             */
        }

        return $this->aDealerContact;
    }


    /**
     * Initializes a collection based on the name of a relation.
     * Avoids crafting an 'init[$relationName]s' method name
     * that wouldn't work when StandardEnglishPluralizer is used.
     *
     * @param      string $relationName The name of the relation to initialize
     * @return void
     */
    public function initRelation($relationName)
    {
        if ('DealerContactInfoI18n' == $relationName) {
            return $this->initDealerContactInfoI18ns();
        }
        if ('DealerContactInfoVersion' == $relationName) {
            return $this->initDealerContactInfoVersions();
        }
    }

    /**
     * Clears out the collDealerContactInfoI18ns collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addDealerContactInfoI18ns()
     */
    public function clearDealerContactInfoI18ns()
    {
        $this->collDealerContactInfoI18ns = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collDealerContactInfoI18ns collection loaded partially.
     */
    public function resetPartialDealerContactInfoI18ns($v = true)
    {
        $this->collDealerContactInfoI18nsPartial = $v;
    }

    /**
     * Initializes the collDealerContactInfoI18ns collection.
     *
     * By default this just sets the collDealerContactInfoI18ns collection to an empty array (like clearcollDealerContactInfoI18ns());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initDealerContactInfoI18ns($overrideExisting = true)
    {
        if (null !== $this->collDealerContactInfoI18ns && !$overrideExisting) {
            return;
        }
        $this->collDealerContactInfoI18ns = new ObjectCollection();
        $this->collDealerContactInfoI18ns->setModel('\Dealer\Model\DealerContactInfoI18n');
    }

    /**
     * Gets an array of ChildDealerContactInfoI18n objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildDealerContactInfo is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildDealerContactInfoI18n[] List of ChildDealerContactInfoI18n objects
     * @throws PropelException
     */
    public function getDealerContactInfoI18ns($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collDealerContactInfoI18nsPartial && !$this->isNew();
        if (null === $this->collDealerContactInfoI18ns || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collDealerContactInfoI18ns) {
                // return empty collection
                $this->initDealerContactInfoI18ns();
            } else {
                $collDealerContactInfoI18ns = ChildDealerContactInfoI18nQuery::create(null, $criteria)
                    ->filterByDealerContactInfo($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collDealerContactInfoI18nsPartial && count($collDealerContactInfoI18ns)) {
                        $this->initDealerContactInfoI18ns(false);

                        foreach ($collDealerContactInfoI18ns as $obj) {
                            if (false == $this->collDealerContactInfoI18ns->contains($obj)) {
                                $this->collDealerContactInfoI18ns->append($obj);
                            }
                        }

                        $this->collDealerContactInfoI18nsPartial = true;
                    }

                    reset($collDealerContactInfoI18ns);

                    return $collDealerContactInfoI18ns;
                }

                if ($partial && $this->collDealerContactInfoI18ns) {
                    foreach ($this->collDealerContactInfoI18ns as $obj) {
                        if ($obj->isNew()) {
                            $collDealerContactInfoI18ns[] = $obj;
                        }
                    }
                }

                $this->collDealerContactInfoI18ns = $collDealerContactInfoI18ns;
                $this->collDealerContactInfoI18nsPartial = false;
            }
        }

        return $this->collDealerContactInfoI18ns;
    }

    /**
     * Sets a collection of DealerContactInfoI18n objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $dealerContactInfoI18ns A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildDealerContactInfo The current object (for fluent API support)
     */
    public function setDealerContactInfoI18ns(Collection $dealerContactInfoI18ns, ConnectionInterface $con = null)
    {
        $dealerContactInfoI18nsToDelete = $this->getDealerContactInfoI18ns(new Criteria(), $con)->diff($dealerContactInfoI18ns);


        //since at least one column in the foreign key is at the same time a PK
        //we can not just set a PK to NULL in the lines below. We have to store
        //a backup of all values, so we are able to manipulate these items based on the onDelete value later.
        $this->dealerContactInfoI18nsScheduledForDeletion = clone $dealerContactInfoI18nsToDelete;

        foreach ($dealerContactInfoI18nsToDelete as $dealerContactInfoI18nRemoved) {
            $dealerContactInfoI18nRemoved->setDealerContactInfo(null);
        }

        $this->collDealerContactInfoI18ns = null;
        foreach ($dealerContactInfoI18ns as $dealerContactInfoI18n) {
            $this->addDealerContactInfoI18n($dealerContactInfoI18n);
        }

        $this->collDealerContactInfoI18ns = $dealerContactInfoI18ns;
        $this->collDealerContactInfoI18nsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related DealerContactInfoI18n objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related DealerContactInfoI18n objects.
     * @throws PropelException
     */
    public function countDealerContactInfoI18ns(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collDealerContactInfoI18nsPartial && !$this->isNew();
        if (null === $this->collDealerContactInfoI18ns || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collDealerContactInfoI18ns) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getDealerContactInfoI18ns());
            }

            $query = ChildDealerContactInfoI18nQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByDealerContactInfo($this)
                ->count($con);
        }

        return count($this->collDealerContactInfoI18ns);
    }

    /**
     * Method called to associate a ChildDealerContactInfoI18n object to this object
     * through the ChildDealerContactInfoI18n foreign key attribute.
     *
     * @param    ChildDealerContactInfoI18n $l ChildDealerContactInfoI18n
     * @return   \Dealer\Model\DealerContactInfo The current object (for fluent API support)
     */
    public function addDealerContactInfoI18n(ChildDealerContactInfoI18n $l)
    {
        if ($l && $locale = $l->getLocale()) {
            $this->setLocale($locale);
            $this->currentTranslations[$locale] = $l;
        }
        if ($this->collDealerContactInfoI18ns === null) {
            $this->initDealerContactInfoI18ns();
            $this->collDealerContactInfoI18nsPartial = true;
        }

        if (!in_array($l, $this->collDealerContactInfoI18ns->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddDealerContactInfoI18n($l);
        }

        return $this;
    }

    /**
     * @param DealerContactInfoI18n $dealerContactInfoI18n The dealerContactInfoI18n object to add.
     */
    protected function doAddDealerContactInfoI18n($dealerContactInfoI18n)
    {
        $this->collDealerContactInfoI18ns[]= $dealerContactInfoI18n;
        $dealerContactInfoI18n->setDealerContactInfo($this);
    }

    /**
     * @param  DealerContactInfoI18n $dealerContactInfoI18n The dealerContactInfoI18n object to remove.
     * @return ChildDealerContactInfo The current object (for fluent API support)
     */
    public function removeDealerContactInfoI18n($dealerContactInfoI18n)
    {
        if ($this->getDealerContactInfoI18ns()->contains($dealerContactInfoI18n)) {
            $this->collDealerContactInfoI18ns->remove($this->collDealerContactInfoI18ns->search($dealerContactInfoI18n));
            if (null === $this->dealerContactInfoI18nsScheduledForDeletion) {
                $this->dealerContactInfoI18nsScheduledForDeletion = clone $this->collDealerContactInfoI18ns;
                $this->dealerContactInfoI18nsScheduledForDeletion->clear();
            }
            $this->dealerContactInfoI18nsScheduledForDeletion[]= clone $dealerContactInfoI18n;
            $dealerContactInfoI18n->setDealerContactInfo(null);
        }

        return $this;
    }

    /**
     * Clears out the collDealerContactInfoVersions collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addDealerContactInfoVersions()
     */
    public function clearDealerContactInfoVersions()
    {
        $this->collDealerContactInfoVersions = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collDealerContactInfoVersions collection loaded partially.
     */
    public function resetPartialDealerContactInfoVersions($v = true)
    {
        $this->collDealerContactInfoVersionsPartial = $v;
    }

    /**
     * Initializes the collDealerContactInfoVersions collection.
     *
     * By default this just sets the collDealerContactInfoVersions collection to an empty array (like clearcollDealerContactInfoVersions());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initDealerContactInfoVersions($overrideExisting = true)
    {
        if (null !== $this->collDealerContactInfoVersions && !$overrideExisting) {
            return;
        }
        $this->collDealerContactInfoVersions = new ObjectCollection();
        $this->collDealerContactInfoVersions->setModel('\Dealer\Model\DealerContactInfoVersion');
    }

    /**
     * Gets an array of ChildDealerContactInfoVersion objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildDealerContactInfo is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildDealerContactInfoVersion[] List of ChildDealerContactInfoVersion objects
     * @throws PropelException
     */
    public function getDealerContactInfoVersions($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collDealerContactInfoVersionsPartial && !$this->isNew();
        if (null === $this->collDealerContactInfoVersions || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collDealerContactInfoVersions) {
                // return empty collection
                $this->initDealerContactInfoVersions();
            } else {
                $collDealerContactInfoVersions = ChildDealerContactInfoVersionQuery::create(null, $criteria)
                    ->filterByDealerContactInfo($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collDealerContactInfoVersionsPartial && count($collDealerContactInfoVersions)) {
                        $this->initDealerContactInfoVersions(false);

                        foreach ($collDealerContactInfoVersions as $obj) {
                            if (false == $this->collDealerContactInfoVersions->contains($obj)) {
                                $this->collDealerContactInfoVersions->append($obj);
                            }
                        }

                        $this->collDealerContactInfoVersionsPartial = true;
                    }

                    reset($collDealerContactInfoVersions);

                    return $collDealerContactInfoVersions;
                }

                if ($partial && $this->collDealerContactInfoVersions) {
                    foreach ($this->collDealerContactInfoVersions as $obj) {
                        if ($obj->isNew()) {
                            $collDealerContactInfoVersions[] = $obj;
                        }
                    }
                }

                $this->collDealerContactInfoVersions = $collDealerContactInfoVersions;
                $this->collDealerContactInfoVersionsPartial = false;
            }
        }

        return $this->collDealerContactInfoVersions;
    }

    /**
     * Sets a collection of DealerContactInfoVersion objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $dealerContactInfoVersions A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildDealerContactInfo The current object (for fluent API support)
     */
    public function setDealerContactInfoVersions(Collection $dealerContactInfoVersions, ConnectionInterface $con = null)
    {
        $dealerContactInfoVersionsToDelete = $this->getDealerContactInfoVersions(new Criteria(), $con)->diff($dealerContactInfoVersions);


        //since at least one column in the foreign key is at the same time a PK
        //we can not just set a PK to NULL in the lines below. We have to store
        //a backup of all values, so we are able to manipulate these items based on the onDelete value later.
        $this->dealerContactInfoVersionsScheduledForDeletion = clone $dealerContactInfoVersionsToDelete;

        foreach ($dealerContactInfoVersionsToDelete as $dealerContactInfoVersionRemoved) {
            $dealerContactInfoVersionRemoved->setDealerContactInfo(null);
        }

        $this->collDealerContactInfoVersions = null;
        foreach ($dealerContactInfoVersions as $dealerContactInfoVersion) {
            $this->addDealerContactInfoVersion($dealerContactInfoVersion);
        }

        $this->collDealerContactInfoVersions = $dealerContactInfoVersions;
        $this->collDealerContactInfoVersionsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related DealerContactInfoVersion objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related DealerContactInfoVersion objects.
     * @throws PropelException
     */
    public function countDealerContactInfoVersions(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collDealerContactInfoVersionsPartial && !$this->isNew();
        if (null === $this->collDealerContactInfoVersions || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collDealerContactInfoVersions) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getDealerContactInfoVersions());
            }

            $query = ChildDealerContactInfoVersionQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByDealerContactInfo($this)
                ->count($con);
        }

        return count($this->collDealerContactInfoVersions);
    }

    /**
     * Method called to associate a ChildDealerContactInfoVersion object to this object
     * through the ChildDealerContactInfoVersion foreign key attribute.
     *
     * @param    ChildDealerContactInfoVersion $l ChildDealerContactInfoVersion
     * @return   \Dealer\Model\DealerContactInfo The current object (for fluent API support)
     */
    public function addDealerContactInfoVersion(ChildDealerContactInfoVersion $l)
    {
        if ($this->collDealerContactInfoVersions === null) {
            $this->initDealerContactInfoVersions();
            $this->collDealerContactInfoVersionsPartial = true;
        }

        if (!in_array($l, $this->collDealerContactInfoVersions->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddDealerContactInfoVersion($l);
        }

        return $this;
    }

    /**
     * @param DealerContactInfoVersion $dealerContactInfoVersion The dealerContactInfoVersion object to add.
     */
    protected function doAddDealerContactInfoVersion($dealerContactInfoVersion)
    {
        $this->collDealerContactInfoVersions[]= $dealerContactInfoVersion;
        $dealerContactInfoVersion->setDealerContactInfo($this);
    }

    /**
     * @param  DealerContactInfoVersion $dealerContactInfoVersion The dealerContactInfoVersion object to remove.
     * @return ChildDealerContactInfo The current object (for fluent API support)
     */
    public function removeDealerContactInfoVersion($dealerContactInfoVersion)
    {
        if ($this->getDealerContactInfoVersions()->contains($dealerContactInfoVersion)) {
            $this->collDealerContactInfoVersions->remove($this->collDealerContactInfoVersions->search($dealerContactInfoVersion));
            if (null === $this->dealerContactInfoVersionsScheduledForDeletion) {
                $this->dealerContactInfoVersionsScheduledForDeletion = clone $this->collDealerContactInfoVersions;
                $this->dealerContactInfoVersionsScheduledForDeletion->clear();
            }
            $this->dealerContactInfoVersionsScheduledForDeletion[]= clone $dealerContactInfoVersion;
            $dealerContactInfoVersion->setDealerContactInfo(null);
        }

        return $this;
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->contact_id = null;
        $this->contact_type = null;
        $this->created_at = null;
        $this->updated_at = null;
        $this->version = null;
        $this->version_created_at = null;
        $this->version_created_by = null;
        $this->alreadyInSave = false;
        $this->clearAllReferences();
        $this->applyDefaultValues();
        $this->resetModified();
        $this->setNew(true);
        $this->setDeleted(false);
    }

    /**
     * Resets all references to other model objects or collections of model objects.
     *
     * This method is a user-space workaround for PHP's inability to garbage collect
     * objects with circular references (even in PHP 5.3). This is currently necessary
     * when using Propel in certain daemon or large-volume/high-memory operations.
     *
     * @param      boolean $deep Whether to also clear the references on all referrer objects.
     */
    public function clearAllReferences($deep = false)
    {
        if ($deep) {
            if ($this->collDealerContactInfoI18ns) {
                foreach ($this->collDealerContactInfoI18ns as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collDealerContactInfoVersions) {
                foreach ($this->collDealerContactInfoVersions as $o) {
                    $o->clearAllReferences($deep);
                }
            }
        } // if ($deep)

        // i18n behavior
        $this->currentLocale = 'en_US';
        $this->currentTranslations = null;

        $this->collDealerContactInfoI18ns = null;
        $this->collDealerContactInfoVersions = null;
        $this->aDealerContact = null;
    }

    /**
     * Return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(DealerContactInfoTableMap::DEFAULT_STRING_FORMAT);
    }

    // timestampable behavior

    /**
     * Mark the current object so that the update date doesn't get updated during next save
     *
     * @return     ChildDealerContactInfo The current object (for fluent API support)
     */
    public function keepUpdateDateUnchanged()
    {
        $this->modifiedColumns[DealerContactInfoTableMap::UPDATED_AT] = true;

        return $this;
    }

    // i18n behavior

    /**
     * Sets the locale for translations
     *
     * @param     string $locale Locale to use for the translation, e.g. 'fr_FR'
     *
     * @return    ChildDealerContactInfo The current object (for fluent API support)
     */
    public function setLocale($locale = 'en_US')
    {
        $this->currentLocale = $locale;

        return $this;
    }

    /**
     * Gets the locale for translations
     *
     * @return    string $locale Locale to use for the translation, e.g. 'fr_FR'
     */
    public function getLocale()
    {
        return $this->currentLocale;
    }

    /**
     * Returns the current translation for a given locale
     *
     * @param     string $locale Locale to use for the translation, e.g. 'fr_FR'
     * @param     ConnectionInterface $con an optional connection object
     *
     * @return ChildDealerContactInfoI18n */
    public function getTranslation($locale = 'en_US', ConnectionInterface $con = null)
    {
        if (!isset($this->currentTranslations[$locale])) {
            if (null !== $this->collDealerContactInfoI18ns) {
                foreach ($this->collDealerContactInfoI18ns as $translation) {
                    if ($translation->getLocale() == $locale) {
                        $this->currentTranslations[$locale] = $translation;

                        return $translation;
                    }
                }
            }
            if ($this->isNew()) {
                $translation = new ChildDealerContactInfoI18n();
                $translation->setLocale($locale);
            } else {
                $translation = ChildDealerContactInfoI18nQuery::create()
                    ->filterByPrimaryKey(array($this->getPrimaryKey(), $locale))
                    ->findOneOrCreate($con);
                $this->currentTranslations[$locale] = $translation;
            }
            $this->addDealerContactInfoI18n($translation);
        }

        return $this->currentTranslations[$locale];
    }

    /**
     * Remove the translation for a given locale
     *
     * @param     string $locale Locale to use for the translation, e.g. 'fr_FR'
     * @param     ConnectionInterface $con an optional connection object
     *
     * @return    ChildDealerContactInfo The current object (for fluent API support)
     */
    public function removeTranslation($locale = 'en_US', ConnectionInterface $con = null)
    {
        if (!$this->isNew()) {
            ChildDealerContactInfoI18nQuery::create()
                ->filterByPrimaryKey(array($this->getPrimaryKey(), $locale))
                ->delete($con);
        }
        if (isset($this->currentTranslations[$locale])) {
            unset($this->currentTranslations[$locale]);
        }
        foreach ($this->collDealerContactInfoI18ns as $key => $translation) {
            if ($translation->getLocale() == $locale) {
                unset($this->collDealerContactInfoI18ns[$key]);
                break;
            }
        }

        return $this;
    }

    /**
     * Returns the current translation
     *
     * @param     ConnectionInterface $con an optional connection object
     *
     * @return ChildDealerContactInfoI18n */
    public function getCurrentTranslation(ConnectionInterface $con = null)
    {
        return $this->getTranslation($this->getLocale(), $con);
    }


        /**
         * Get the [value] column value.
         *
         * @return   string
         */
        public function getValue()
        {
        return $this->getCurrentTranslation()->getValue();
    }


        /**
         * Set the value of [value] column.
         *
         * @param      string $v new value
         * @return   \Dealer\Model\DealerContactInfoI18n The current object (for fluent API support)
         */
        public function setValue($v)
        {    $this->getCurrentTranslation()->setValue($v);

        return $this;
    }

    // versionable behavior

    /**
     * Enforce a new Version of this object upon next save.
     *
     * @return \Dealer\Model\DealerContactInfo
     */
    public function enforceVersioning()
    {
        $this->enforceVersion = true;

        return $this;
    }

    /**
     * Checks whether the current state must be recorded as a version
     *
     * @return  boolean
     */
    public function isVersioningNecessary($con = null)
    {
        if ($this->alreadyInSave) {
            return false;
        }

        if ($this->enforceVersion) {
            return true;
        }

        if (ChildDealerContactInfoQuery::isVersioningEnabled() && ($this->isNew() || $this->isModified()) || $this->isDeleted()) {
            return true;
        }
        if (null !== ($object = $this->getDealerContact($con)) && $object->isVersioningNecessary($con)) {
            return true;
        }


        return false;
    }

    /**
     * Creates a version of the current object and saves it.
     *
     * @param   ConnectionInterface $con the connection to use
     *
     * @return  ChildDealerContactInfoVersion A version object
     */
    public function addVersion($con = null)
    {
        $this->enforceVersion = false;

        $version = new ChildDealerContactInfoVersion();
        $version->setId($this->getId());
        $version->setContactId($this->getContactId());
        $version->setContactType($this->getContactType());
        $version->setCreatedAt($this->getCreatedAt());
        $version->setUpdatedAt($this->getUpdatedAt());
        $version->setVersion($this->getVersion());
        $version->setVersionCreatedAt($this->getVersionCreatedAt());
        $version->setVersionCreatedBy($this->getVersionCreatedBy());
        $version->setDealerContactInfo($this);
        if (($related = $this->getDealerContact($con)) && $related->getVersion()) {
            $version->setContactIdVersion($related->getVersion());
        }
        $version->save($con);

        return $version;
    }

    /**
     * Sets the properties of the current object to the value they had at a specific version
     *
     * @param   integer $versionNumber The version number to read
     * @param   ConnectionInterface $con The connection to use
     *
     * @return  ChildDealerContactInfo The current object (for fluent API support)
     */
    public function toVersion($versionNumber, $con = null)
    {
        $version = $this->getOneVersion($versionNumber, $con);
        if (!$version) {
            throw new PropelException(sprintf('No ChildDealerContactInfo object found with version %d', $version));
        }
        $this->populateFromVersion($version, $con);

        return $this;
    }

    /**
     * Sets the properties of the current object to the value they had at a specific version
     *
     * @param ChildDealerContactInfoVersion $version The version object to use
     * @param ConnectionInterface   $con the connection to use
     * @param array                 $loadedObjects objects that been loaded in a chain of populateFromVersion calls on referrer or fk objects.
     *
     * @return ChildDealerContactInfo The current object (for fluent API support)
     */
    public function populateFromVersion($version, $con = null, &$loadedObjects = array())
    {
        $loadedObjects['ChildDealerContactInfo'][$version->getId()][$version->getVersion()] = $this;
        $this->setId($version->getId());
        $this->setContactId($version->getContactId());
        $this->setContactType($version->getContactType());
        $this->setCreatedAt($version->getCreatedAt());
        $this->setUpdatedAt($version->getUpdatedAt());
        $this->setVersion($version->getVersion());
        $this->setVersionCreatedAt($version->getVersionCreatedAt());
        $this->setVersionCreatedBy($version->getVersionCreatedBy());
        if ($fkValue = $version->getContactId()) {
            if (isset($loadedObjects['ChildDealerContact']) && isset($loadedObjects['ChildDealerContact'][$fkValue]) && isset($loadedObjects['ChildDealerContact'][$fkValue][$version->getContactIdVersion()])) {
                $related = $loadedObjects['ChildDealerContact'][$fkValue][$version->getContactIdVersion()];
            } else {
                $related = new ChildDealerContact();
                $relatedVersion = ChildDealerContactVersionQuery::create()
                    ->filterById($fkValue)
                    ->filterByVersion($version->getContactIdVersion())
                    ->findOne($con);
                $related->populateFromVersion($relatedVersion, $con, $loadedObjects);
                $related->setNew(false);
            }
            $this->setDealerContact($related);
        }

        return $this;
    }

    /**
     * Gets the latest persisted version number for the current object
     *
     * @param   ConnectionInterface $con the connection to use
     *
     * @return  integer
     */
    public function getLastVersionNumber($con = null)
    {
        $v = ChildDealerContactInfoVersionQuery::create()
            ->filterByDealerContactInfo($this)
            ->orderByVersion('desc')
            ->findOne($con);
        if (!$v) {
            return 0;
        }

        return $v->getVersion();
    }

    /**
     * Checks whether the current object is the latest one
     *
     * @param   ConnectionInterface $con the connection to use
     *
     * @return  Boolean
     */
    public function isLastVersion($con = null)
    {
        return $this->getLastVersionNumber($con) == $this->getVersion();
    }

    /**
     * Retrieves a version object for this entity and a version number
     *
     * @param   integer $versionNumber The version number to read
     * @param   ConnectionInterface $con the connection to use
     *
     * @return  ChildDealerContactInfoVersion A version object
     */
    public function getOneVersion($versionNumber, $con = null)
    {
        return ChildDealerContactInfoVersionQuery::create()
            ->filterByDealerContactInfo($this)
            ->filterByVersion($versionNumber)
            ->findOne($con);
    }

    /**
     * Gets all the versions of this object, in incremental order
     *
     * @param   ConnectionInterface $con the connection to use
     *
     * @return  ObjectCollection A list of ChildDealerContactInfoVersion objects
     */
    public function getAllVersions($con = null)
    {
        $criteria = new Criteria();
        $criteria->addAscendingOrderByColumn(DealerContactInfoVersionTableMap::VERSION);

        return $this->getDealerContactInfoVersions($criteria, $con);
    }

    /**
     * Compares the current object with another of its version.
     * <code>
     * print_r($book->compareVersion(1));
     * => array(
     *   '1' => array('Title' => 'Book title at version 1'),
     *   '2' => array('Title' => 'Book title at version 2')
     * );
     * </code>
     *
     * @param   integer             $versionNumber
     * @param   string              $keys Main key used for the result diff (versions|columns)
     * @param   ConnectionInterface $con the connection to use
     * @param   array               $ignoredColumns  The columns to exclude from the diff.
     *
     * @return  array A list of differences
     */
    public function compareVersion($versionNumber, $keys = 'columns', $con = null, $ignoredColumns = array())
    {
        $fromVersion = $this->toArray();
        $toVersion = $this->getOneVersion($versionNumber, $con)->toArray();

        return $this->computeDiff($fromVersion, $toVersion, $keys, $ignoredColumns);
    }

    /**
     * Compares two versions of the current object.
     * <code>
     * print_r($book->compareVersions(1, 2));
     * => array(
     *   '1' => array('Title' => 'Book title at version 1'),
     *   '2' => array('Title' => 'Book title at version 2')
     * );
     * </code>
     *
     * @param   integer             $fromVersionNumber
     * @param   integer             $toVersionNumber
     * @param   string              $keys Main key used for the result diff (versions|columns)
     * @param   ConnectionInterface $con the connection to use
     * @param   array               $ignoredColumns  The columns to exclude from the diff.
     *
     * @return  array A list of differences
     */
    public function compareVersions($fromVersionNumber, $toVersionNumber, $keys = 'columns', $con = null, $ignoredColumns = array())
    {
        $fromVersion = $this->getOneVersion($fromVersionNumber, $con)->toArray();
        $toVersion = $this->getOneVersion($toVersionNumber, $con)->toArray();

        return $this->computeDiff($fromVersion, $toVersion, $keys, $ignoredColumns);
    }

    /**
     * Computes the diff between two versions.
     * <code>
     * print_r($book->computeDiff(1, 2));
     * => array(
     *   '1' => array('Title' => 'Book title at version 1'),
     *   '2' => array('Title' => 'Book title at version 2')
     * );
     * </code>
     *
     * @param   array     $fromVersion     An array representing the original version.
     * @param   array     $toVersion       An array representing the destination version.
     * @param   string    $keys            Main key used for the result diff (versions|columns).
     * @param   array     $ignoredColumns  The columns to exclude from the diff.
     *
     * @return  array A list of differences
     */
    protected function computeDiff($fromVersion, $toVersion, $keys = 'columns', $ignoredColumns = array())
    {
        $fromVersionNumber = $fromVersion['Version'];
        $toVersionNumber = $toVersion['Version'];
        $ignoredColumns = array_merge(array(
            'Version',
            'VersionCreatedAt',
            'VersionCreatedBy',
        ), $ignoredColumns);
        $diff = array();
        foreach ($fromVersion as $key => $value) {
            if (in_array($key, $ignoredColumns)) {
                continue;
            }
            if ($toVersion[$key] != $value) {
                switch ($keys) {
                    case 'versions':
                        $diff[$fromVersionNumber][$key] = $value;
                        $diff[$toVersionNumber][$key] = $toVersion[$key];
                        break;
                    default:
                        $diff[$key] = array(
                            $fromVersionNumber => $value,
                            $toVersionNumber => $toVersion[$key],
                        );
                        break;
                }
            }
        }

        return $diff;
    }
    /**
     * retrieve the last $number versions.
     *
     * @param Integer $number the number of record to return.
     * @return PropelCollection|array \Dealer\Model\DealerContactInfoVersion[] List of \Dealer\Model\DealerContactInfoVersion objects
     */
    public function getLastVersions($number = 10, $criteria = null, $con = null)
    {
        $criteria = ChildDealerContactInfoVersionQuery::create(null, $criteria);
        $criteria->addDescendingOrderByColumn(DealerContactInfoVersionTableMap::VERSION);
        $criteria->limit($number);

        return $this->getDealerContactInfoVersions($criteria, $con);
    }
    /**
     * Code to be run before persisting the object
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preSave(ConnectionInterface $con = null)
    {
        return true;
    }

    /**
     * Code to be run after persisting the object
     * @param ConnectionInterface $con
     */
    public function postSave(ConnectionInterface $con = null)
    {

    }

    /**
     * Code to be run before inserting to database
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preInsert(ConnectionInterface $con = null)
    {
        return true;
    }

    /**
     * Code to be run after inserting to database
     * @param ConnectionInterface $con
     */
    public function postInsert(ConnectionInterface $con = null)
    {

    }

    /**
     * Code to be run before updating the object in database
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preUpdate(ConnectionInterface $con = null)
    {
        return true;
    }

    /**
     * Code to be run after updating the object in database
     * @param ConnectionInterface $con
     */
    public function postUpdate(ConnectionInterface $con = null)
    {

    }

    /**
     * Code to be run before deleting the object in database
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preDelete(ConnectionInterface $con = null)
    {
        return true;
    }

    /**
     * Code to be run after deleting the object in database
     * @param ConnectionInterface $con
     */
    public function postDelete(ConnectionInterface $con = null)
    {

    }


    /**
     * Derived method to catches calls to undefined methods.
     *
     * Provides magic import/export method support (fromXML()/toXML(), fromYAML()/toYAML(), etc.).
     * Allows to define default __call() behavior if you overwrite __call()
     *
     * @param string $name
     * @param mixed  $params
     *
     * @return array|string
     */
    public function __call($name, $params)
    {
        if (0 === strpos($name, 'get')) {
            $virtualColumn = substr($name, 3);
            if ($this->hasVirtualColumn($virtualColumn)) {
                return $this->getVirtualColumn($virtualColumn);
            }

            $virtualColumn = lcfirst($virtualColumn);
            if ($this->hasVirtualColumn($virtualColumn)) {
                return $this->getVirtualColumn($virtualColumn);
            }
        }

        if (0 === strpos($name, 'from')) {
            $format = substr($name, 4);

            return $this->importFrom($format, reset($params));
        }

        if (0 === strpos($name, 'to')) {
            $format = substr($name, 2);
            $includeLazyLoadColumns = isset($params[0]) ? $params[0] : true;

            return $this->exportTo($format, $includeLazyLoadColumns);
        }

        throw new BadMethodCallException(sprintf('Call to undefined method: %s.', $name));
    }

}
