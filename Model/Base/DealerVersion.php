<?php

namespace Dealer\Model\Base;

use \DateTime;
use \Exception;
use \PDO;
use Dealer\Model\Dealer as ChildDealer;
use Dealer\Model\DealerQuery as ChildDealerQuery;
use Dealer\Model\DealerVersionQuery as ChildDealerVersionQuery;
use Dealer\Model\Map\DealerVersionTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveRecord\ActiveRecordInterface;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\BadMethodCallException;
use Propel\Runtime\Exception\PropelException;
use Propel\Runtime\Map\TableMap;
use Propel\Runtime\Parser\AbstractParser;
use Propel\Runtime\Util\PropelDateTime;

abstract class DealerVersion implements ActiveRecordInterface
{
    /**
     * TableMap class name
     */
    const TABLE_MAP = '\\Dealer\\Model\\Map\\DealerVersionTableMap';


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
     * The value for the address1 field.
     * @var        string
     */
    protected $address1;

    /**
     * The value for the address2 field.
     * @var        string
     */
    protected $address2;

    /**
     * The value for the address3 field.
     * @var        string
     */
    protected $address3;

    /**
     * The value for the zipcode field.
     * @var        string
     */
    protected $zipcode;

    /**
     * The value for the city field.
     * @var        string
     */
    protected $city;

    /**
     * The value for the country_id field.
     * @var        int
     */
    protected $country_id;

    /**
     * The value for the latitude field.
     * Note: this column has a database default value of: '0'
     * @var        string
     */
    protected $latitude;

    /**
     * The value for the longitude field.
     * Note: this column has a database default value of: '0'
     * @var        string
     */
    protected $longitude;

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
     * The value for the dealer_content_ids field.
     * @var        array
     */
    protected $dealer_content_ids;

    /**
     * The unserialized $dealer_content_ids value - i.e. the persisted object.
     * This is necessary to avoid repeated calls to unserialize() at runtime.
     * @var object
     */
    protected $dealer_content_ids_unserialized;

    /**
     * The value for the dealer_content_versions field.
     * @var        array
     */
    protected $dealer_content_versions;

    /**
     * The unserialized $dealer_content_versions value - i.e. the persisted object.
     * This is necessary to avoid repeated calls to unserialize() at runtime.
     * @var object
     */
    protected $dealer_content_versions_unserialized;

    /**
     * The value for the dealer_shedules_ids field.
     * @var        array
     */
    protected $dealer_shedules_ids;

    /**
     * The unserialized $dealer_shedules_ids value - i.e. the persisted object.
     * This is necessary to avoid repeated calls to unserialize() at runtime.
     * @var object
     */
    protected $dealer_shedules_ids_unserialized;

    /**
     * The value for the dealer_shedules_versions field.
     * @var        array
     */
    protected $dealer_shedules_versions;

    /**
     * The unserialized $dealer_shedules_versions value - i.e. the persisted object.
     * This is necessary to avoid repeated calls to unserialize() at runtime.
     * @var object
     */
    protected $dealer_shedules_versions_unserialized;

    /**
     * The value for the dealer_contact_ids field.
     * @var        array
     */
    protected $dealer_contact_ids;

    /**
     * The unserialized $dealer_contact_ids value - i.e. the persisted object.
     * This is necessary to avoid repeated calls to unserialize() at runtime.
     * @var object
     */
    protected $dealer_contact_ids_unserialized;

    /**
     * The value for the dealer_contact_versions field.
     * @var        array
     */
    protected $dealer_contact_versions;

    /**
     * The unserialized $dealer_contact_versions value - i.e. the persisted object.
     * This is necessary to avoid repeated calls to unserialize() at runtime.
     * @var object
     */
    protected $dealer_contact_versions_unserialized;

    /**
     * @var        Dealer
     */
    protected $aDealer;

    /**
     * Flag to prevent endless save loop, if this object is referenced
     * by another object which falls in this transaction.
     *
     * @var boolean
     */
    protected $alreadyInSave = false;

    /**
     * Applies default values to this object.
     * This method should be called from the object's constructor (or
     * equivalent initialization method).
     * @see __construct()
     */
    public function applyDefaultValues()
    {
        $this->latitude = '0';
        $this->longitude = '0';
        $this->version = 0;
    }

    /**
     * Initializes internal state of Dealer\Model\Base\DealerVersion object.
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
     * Compares this with another <code>DealerVersion</code> instance.  If
     * <code>obj</code> is an instance of <code>DealerVersion</code>, delegates to
     * <code>equals(DealerVersion)</code>.  Otherwise, returns <code>false</code>.
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
     * @return DealerVersion The current object, for fluid interface
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
     * @return DealerVersion The current object, for fluid interface
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
     * Get the [address1] column value.
     *
     * @return   string
     */
    public function getAddress1()
    {

        return $this->address1;
    }

    /**
     * Get the [address2] column value.
     *
     * @return   string
     */
    public function getAddress2()
    {

        return $this->address2;
    }

    /**
     * Get the [address3] column value.
     *
     * @return   string
     */
    public function getAddress3()
    {

        return $this->address3;
    }

    /**
     * Get the [zipcode] column value.
     *
     * @return   string
     */
    public function getZipcode()
    {

        return $this->zipcode;
    }

    /**
     * Get the [city] column value.
     *
     * @return   string
     */
    public function getCity()
    {

        return $this->city;
    }

    /**
     * Get the [country_id] column value.
     *
     * @return   int
     */
    public function getCountryId()
    {

        return $this->country_id;
    }

    /**
     * Get the [latitude] column value.
     *
     * @return   string
     */
    public function getLatitude()
    {

        return $this->latitude;
    }

    /**
     * Get the [longitude] column value.
     *
     * @return   string
     */
    public function getLongitude()
    {

        return $this->longitude;
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
     * Get the [dealer_content_ids] column value.
     *
     * @return   array
     */
    public function getDealerContentIds()
    {
        if (null === $this->dealer_content_ids_unserialized) {
            $this->dealer_content_ids_unserialized = array();
        }
        if (!$this->dealer_content_ids_unserialized && null !== $this->dealer_content_ids) {
            $dealer_content_ids_unserialized = substr($this->dealer_content_ids, 2, -2);
            $this->dealer_content_ids_unserialized = $dealer_content_ids_unserialized ? explode(' | ', $dealer_content_ids_unserialized) : array();
        }

        return $this->dealer_content_ids_unserialized;
    }

    /**
     * Test the presence of a value in the [dealer_content_ids] array column value.
     * @param      mixed $value
     *
     * @return boolean
     */
    public function hasDealerContentId($value)
    {
        return in_array($value, $this->getDealerContentIds());
    } // hasDealerContentId()

    /**
     * Get the [dealer_content_versions] column value.
     *
     * @return   array
     */
    public function getDealerContentVersions()
    {
        if (null === $this->dealer_content_versions_unserialized) {
            $this->dealer_content_versions_unserialized = array();
        }
        if (!$this->dealer_content_versions_unserialized && null !== $this->dealer_content_versions) {
            $dealer_content_versions_unserialized = substr($this->dealer_content_versions, 2, -2);
            $this->dealer_content_versions_unserialized = $dealer_content_versions_unserialized ? explode(' | ', $dealer_content_versions_unserialized) : array();
        }

        return $this->dealer_content_versions_unserialized;
    }

    /**
     * Test the presence of a value in the [dealer_content_versions] array column value.
     * @param      mixed $value
     *
     * @return boolean
     */
    public function hasDealerContentVersion($value)
    {
        return in_array($value, $this->getDealerContentVersions());
    } // hasDealerContentVersion()

    /**
     * Get the [dealer_shedules_ids] column value.
     *
     * @return   array
     */
    public function getDealerShedulesIds()
    {
        if (null === $this->dealer_shedules_ids_unserialized) {
            $this->dealer_shedules_ids_unserialized = array();
        }
        if (!$this->dealer_shedules_ids_unserialized && null !== $this->dealer_shedules_ids) {
            $dealer_shedules_ids_unserialized = substr($this->dealer_shedules_ids, 2, -2);
            $this->dealer_shedules_ids_unserialized = $dealer_shedules_ids_unserialized ? explode(' | ', $dealer_shedules_ids_unserialized) : array();
        }

        return $this->dealer_shedules_ids_unserialized;
    }

    /**
     * Test the presence of a value in the [dealer_shedules_ids] array column value.
     * @param      mixed $value
     *
     * @return boolean
     */
    public function hasDealerShedulesId($value)
    {
        return in_array($value, $this->getDealerShedulesIds());
    } // hasDealerShedulesId()

    /**
     * Get the [dealer_shedules_versions] column value.
     *
     * @return   array
     */
    public function getDealerShedulesVersions()
    {
        if (null === $this->dealer_shedules_versions_unserialized) {
            $this->dealer_shedules_versions_unserialized = array();
        }
        if (!$this->dealer_shedules_versions_unserialized && null !== $this->dealer_shedules_versions) {
            $dealer_shedules_versions_unserialized = substr($this->dealer_shedules_versions, 2, -2);
            $this->dealer_shedules_versions_unserialized = $dealer_shedules_versions_unserialized ? explode(' | ', $dealer_shedules_versions_unserialized) : array();
        }

        return $this->dealer_shedules_versions_unserialized;
    }

    /**
     * Test the presence of a value in the [dealer_shedules_versions] array column value.
     * @param      mixed $value
     *
     * @return boolean
     */
    public function hasDealerShedulesVersion($value)
    {
        return in_array($value, $this->getDealerShedulesVersions());
    } // hasDealerShedulesVersion()

    /**
     * Get the [dealer_contact_ids] column value.
     *
     * @return   array
     */
    public function getDealerContactIds()
    {
        if (null === $this->dealer_contact_ids_unserialized) {
            $this->dealer_contact_ids_unserialized = array();
        }
        if (!$this->dealer_contact_ids_unserialized && null !== $this->dealer_contact_ids) {
            $dealer_contact_ids_unserialized = substr($this->dealer_contact_ids, 2, -2);
            $this->dealer_contact_ids_unserialized = $dealer_contact_ids_unserialized ? explode(' | ', $dealer_contact_ids_unserialized) : array();
        }

        return $this->dealer_contact_ids_unserialized;
    }

    /**
     * Test the presence of a value in the [dealer_contact_ids] array column value.
     * @param      mixed $value
     *
     * @return boolean
     */
    public function hasDealerContactId($value)
    {
        return in_array($value, $this->getDealerContactIds());
    } // hasDealerContactId()

    /**
     * Get the [dealer_contact_versions] column value.
     *
     * @return   array
     */
    public function getDealerContactVersions()
    {
        if (null === $this->dealer_contact_versions_unserialized) {
            $this->dealer_contact_versions_unserialized = array();
        }
        if (!$this->dealer_contact_versions_unserialized && null !== $this->dealer_contact_versions) {
            $dealer_contact_versions_unserialized = substr($this->dealer_contact_versions, 2, -2);
            $this->dealer_contact_versions_unserialized = $dealer_contact_versions_unserialized ? explode(' | ', $dealer_contact_versions_unserialized) : array();
        }

        return $this->dealer_contact_versions_unserialized;
    }

    /**
     * Test the presence of a value in the [dealer_contact_versions] array column value.
     * @param      mixed $value
     *
     * @return boolean
     */
    public function hasDealerContactVersion($value)
    {
        return in_array($value, $this->getDealerContactVersions());
    } // hasDealerContactVersion()

    /**
     * Set the value of [id] column.
     *
     * @param      int $v new value
     * @return   \Dealer\Model\DealerVersion The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[DealerVersionTableMap::ID] = true;
        }

        if ($this->aDealer !== null && $this->aDealer->getId() !== $v) {
            $this->aDealer = null;
        }


        return $this;
    } // setId()

    /**
     * Set the value of [address1] column.
     *
     * @param      string $v new value
     * @return   \Dealer\Model\DealerVersion The current object (for fluent API support)
     */
    public function setAddress1($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->address1 !== $v) {
            $this->address1 = $v;
            $this->modifiedColumns[DealerVersionTableMap::ADDRESS1] = true;
        }


        return $this;
    } // setAddress1()

    /**
     * Set the value of [address2] column.
     *
     * @param      string $v new value
     * @return   \Dealer\Model\DealerVersion The current object (for fluent API support)
     */
    public function setAddress2($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->address2 !== $v) {
            $this->address2 = $v;
            $this->modifiedColumns[DealerVersionTableMap::ADDRESS2] = true;
        }


        return $this;
    } // setAddress2()

    /**
     * Set the value of [address3] column.
     *
     * @param      string $v new value
     * @return   \Dealer\Model\DealerVersion The current object (for fluent API support)
     */
    public function setAddress3($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->address3 !== $v) {
            $this->address3 = $v;
            $this->modifiedColumns[DealerVersionTableMap::ADDRESS3] = true;
        }


        return $this;
    } // setAddress3()

    /**
     * Set the value of [zipcode] column.
     *
     * @param      string $v new value
     * @return   \Dealer\Model\DealerVersion The current object (for fluent API support)
     */
    public function setZipcode($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->zipcode !== $v) {
            $this->zipcode = $v;
            $this->modifiedColumns[DealerVersionTableMap::ZIPCODE] = true;
        }


        return $this;
    } // setZipcode()

    /**
     * Set the value of [city] column.
     *
     * @param      string $v new value
     * @return   \Dealer\Model\DealerVersion The current object (for fluent API support)
     */
    public function setCity($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->city !== $v) {
            $this->city = $v;
            $this->modifiedColumns[DealerVersionTableMap::CITY] = true;
        }


        return $this;
    } // setCity()

    /**
     * Set the value of [country_id] column.
     *
     * @param      int $v new value
     * @return   \Dealer\Model\DealerVersion The current object (for fluent API support)
     */
    public function setCountryId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->country_id !== $v) {
            $this->country_id = $v;
            $this->modifiedColumns[DealerVersionTableMap::COUNTRY_ID] = true;
        }


        return $this;
    } // setCountryId()

    /**
     * Set the value of [latitude] column.
     *
     * @param      string $v new value
     * @return   \Dealer\Model\DealerVersion The current object (for fluent API support)
     */
    public function setLatitude($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->latitude !== $v) {
            $this->latitude = $v;
            $this->modifiedColumns[DealerVersionTableMap::LATITUDE] = true;
        }


        return $this;
    } // setLatitude()

    /**
     * Set the value of [longitude] column.
     *
     * @param      string $v new value
     * @return   \Dealer\Model\DealerVersion The current object (for fluent API support)
     */
    public function setLongitude($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->longitude !== $v) {
            $this->longitude = $v;
            $this->modifiedColumns[DealerVersionTableMap::LONGITUDE] = true;
        }


        return $this;
    } // setLongitude()

    /**
     * Sets the value of [created_at] column to a normalized version of the date/time value specified.
     *
     * @param      mixed $v string, integer (timestamp), or \DateTime value.
     *               Empty strings are treated as NULL.
     * @return   \Dealer\Model\DealerVersion The current object (for fluent API support)
     */
    public function setCreatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, '\DateTime');
        if ($this->created_at !== null || $dt !== null) {
            if ($dt !== $this->created_at) {
                $this->created_at = $dt;
                $this->modifiedColumns[DealerVersionTableMap::CREATED_AT] = true;
            }
        } // if either are not null


        return $this;
    } // setCreatedAt()

    /**
     * Sets the value of [updated_at] column to a normalized version of the date/time value specified.
     *
     * @param      mixed $v string, integer (timestamp), or \DateTime value.
     *               Empty strings are treated as NULL.
     * @return   \Dealer\Model\DealerVersion The current object (for fluent API support)
     */
    public function setUpdatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, '\DateTime');
        if ($this->updated_at !== null || $dt !== null) {
            if ($dt !== $this->updated_at) {
                $this->updated_at = $dt;
                $this->modifiedColumns[DealerVersionTableMap::UPDATED_AT] = true;
            }
        } // if either are not null


        return $this;
    } // setUpdatedAt()

    /**
     * Set the value of [version] column.
     *
     * @param      int $v new value
     * @return   \Dealer\Model\DealerVersion The current object (for fluent API support)
     */
    public function setVersion($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->version !== $v) {
            $this->version = $v;
            $this->modifiedColumns[DealerVersionTableMap::VERSION] = true;
        }


        return $this;
    } // setVersion()

    /**
     * Sets the value of [version_created_at] column to a normalized version of the date/time value specified.
     *
     * @param      mixed $v string, integer (timestamp), or \DateTime value.
     *               Empty strings are treated as NULL.
     * @return   \Dealer\Model\DealerVersion The current object (for fluent API support)
     */
    public function setVersionCreatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, '\DateTime');
        if ($this->version_created_at !== null || $dt !== null) {
            if ($dt !== $this->version_created_at) {
                $this->version_created_at = $dt;
                $this->modifiedColumns[DealerVersionTableMap::VERSION_CREATED_AT] = true;
            }
        } // if either are not null


        return $this;
    } // setVersionCreatedAt()

    /**
     * Set the value of [version_created_by] column.
     *
     * @param      string $v new value
     * @return   \Dealer\Model\DealerVersion The current object (for fluent API support)
     */
    public function setVersionCreatedBy($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->version_created_by !== $v) {
            $this->version_created_by = $v;
            $this->modifiedColumns[DealerVersionTableMap::VERSION_CREATED_BY] = true;
        }


        return $this;
    } // setVersionCreatedBy()

    /**
     * Set the value of [dealer_content_ids] column.
     *
     * @param      array $v new value
     * @return   \Dealer\Model\DealerVersion The current object (for fluent API support)
     */
    public function setDealerContentIds($v)
    {
        if ($this->dealer_content_ids_unserialized !== $v) {
            $this->dealer_content_ids_unserialized = $v;
            $this->dealer_content_ids = '| ' . implode(' | ', $v) . ' |';
            $this->modifiedColumns[DealerVersionTableMap::DEALER_CONTENT_IDS] = true;
        }


        return $this;
    } // setDealerContentIds()

    /**
     * Adds a value to the [dealer_content_ids] array column value.
     * @param      mixed $value
     *
     * @return   \Dealer\Model\DealerVersion The current object (for fluent API support)
     */
    public function addDealerContentId($value)
    {
        $currentArray = $this->getDealerContentIds();
        $currentArray []= $value;
        $this->setDealerContentIds($currentArray);

        return $this;
    } // addDealerContentId()

    /**
     * Removes a value from the [dealer_content_ids] array column value.
     * @param      mixed $value
     *
     * @return   \Dealer\Model\DealerVersion The current object (for fluent API support)
     */
    public function removeDealerContentId($value)
    {
        $targetArray = array();
        foreach ($this->getDealerContentIds() as $element) {
            if ($element != $value) {
                $targetArray []= $element;
            }
        }
        $this->setDealerContentIds($targetArray);

        return $this;
    } // removeDealerContentId()

    /**
     * Set the value of [dealer_content_versions] column.
     *
     * @param      array $v new value
     * @return   \Dealer\Model\DealerVersion The current object (for fluent API support)
     */
    public function setDealerContentVersions($v)
    {
        if ($this->dealer_content_versions_unserialized !== $v) {
            $this->dealer_content_versions_unserialized = $v;
            $this->dealer_content_versions = '| ' . implode(' | ', $v) . ' |';
            $this->modifiedColumns[DealerVersionTableMap::DEALER_CONTENT_VERSIONS] = true;
        }


        return $this;
    } // setDealerContentVersions()

    /**
     * Adds a value to the [dealer_content_versions] array column value.
     * @param      mixed $value
     *
     * @return   \Dealer\Model\DealerVersion The current object (for fluent API support)
     */
    public function addDealerContentVersion($value)
    {
        $currentArray = $this->getDealerContentVersions();
        $currentArray []= $value;
        $this->setDealerContentVersions($currentArray);

        return $this;
    } // addDealerContentVersion()

    /**
     * Removes a value from the [dealer_content_versions] array column value.
     * @param      mixed $value
     *
     * @return   \Dealer\Model\DealerVersion The current object (for fluent API support)
     */
    public function removeDealerContentVersion($value)
    {
        $targetArray = array();
        foreach ($this->getDealerContentVersions() as $element) {
            if ($element != $value) {
                $targetArray []= $element;
            }
        }
        $this->setDealerContentVersions($targetArray);

        return $this;
    } // removeDealerContentVersion()

    /**
     * Set the value of [dealer_shedules_ids] column.
     *
     * @param      array $v new value
     * @return   \Dealer\Model\DealerVersion The current object (for fluent API support)
     */
    public function setDealerShedulesIds($v)
    {
        if ($this->dealer_shedules_ids_unserialized !== $v) {
            $this->dealer_shedules_ids_unserialized = $v;
            $this->dealer_shedules_ids = '| ' . implode(' | ', $v) . ' |';
            $this->modifiedColumns[DealerVersionTableMap::DEALER_SHEDULES_IDS] = true;
        }


        return $this;
    } // setDealerShedulesIds()

    /**
     * Adds a value to the [dealer_shedules_ids] array column value.
     * @param      mixed $value
     *
     * @return   \Dealer\Model\DealerVersion The current object (for fluent API support)
     */
    public function addDealerShedulesId($value)
    {
        $currentArray = $this->getDealerShedulesIds();
        $currentArray []= $value;
        $this->setDealerShedulesIds($currentArray);

        return $this;
    } // addDealerShedulesId()

    /**
     * Removes a value from the [dealer_shedules_ids] array column value.
     * @param      mixed $value
     *
     * @return   \Dealer\Model\DealerVersion The current object (for fluent API support)
     */
    public function removeDealerShedulesId($value)
    {
        $targetArray = array();
        foreach ($this->getDealerShedulesIds() as $element) {
            if ($element != $value) {
                $targetArray []= $element;
            }
        }
        $this->setDealerShedulesIds($targetArray);

        return $this;
    } // removeDealerShedulesId()

    /**
     * Set the value of [dealer_shedules_versions] column.
     *
     * @param      array $v new value
     * @return   \Dealer\Model\DealerVersion The current object (for fluent API support)
     */
    public function setDealerShedulesVersions($v)
    {
        if ($this->dealer_shedules_versions_unserialized !== $v) {
            $this->dealer_shedules_versions_unserialized = $v;
            $this->dealer_shedules_versions = '| ' . implode(' | ', $v) . ' |';
            $this->modifiedColumns[DealerVersionTableMap::DEALER_SHEDULES_VERSIONS] = true;
        }


        return $this;
    } // setDealerShedulesVersions()

    /**
     * Adds a value to the [dealer_shedules_versions] array column value.
     * @param      mixed $value
     *
     * @return   \Dealer\Model\DealerVersion The current object (for fluent API support)
     */
    public function addDealerShedulesVersion($value)
    {
        $currentArray = $this->getDealerShedulesVersions();
        $currentArray []= $value;
        $this->setDealerShedulesVersions($currentArray);

        return $this;
    } // addDealerShedulesVersion()

    /**
     * Removes a value from the [dealer_shedules_versions] array column value.
     * @param      mixed $value
     *
     * @return   \Dealer\Model\DealerVersion The current object (for fluent API support)
     */
    public function removeDealerShedulesVersion($value)
    {
        $targetArray = array();
        foreach ($this->getDealerShedulesVersions() as $element) {
            if ($element != $value) {
                $targetArray []= $element;
            }
        }
        $this->setDealerShedulesVersions($targetArray);

        return $this;
    } // removeDealerShedulesVersion()

    /**
     * Set the value of [dealer_contact_ids] column.
     *
     * @param      array $v new value
     * @return   \Dealer\Model\DealerVersion The current object (for fluent API support)
     */
    public function setDealerContactIds($v)
    {
        if ($this->dealer_contact_ids_unserialized !== $v) {
            $this->dealer_contact_ids_unserialized = $v;
            $this->dealer_contact_ids = '| ' . implode(' | ', $v) . ' |';
            $this->modifiedColumns[DealerVersionTableMap::DEALER_CONTACT_IDS] = true;
        }


        return $this;
    } // setDealerContactIds()

    /**
     * Adds a value to the [dealer_contact_ids] array column value.
     * @param      mixed $value
     *
     * @return   \Dealer\Model\DealerVersion The current object (for fluent API support)
     */
    public function addDealerContactId($value)
    {
        $currentArray = $this->getDealerContactIds();
        $currentArray []= $value;
        $this->setDealerContactIds($currentArray);

        return $this;
    } // addDealerContactId()

    /**
     * Removes a value from the [dealer_contact_ids] array column value.
     * @param      mixed $value
     *
     * @return   \Dealer\Model\DealerVersion The current object (for fluent API support)
     */
    public function removeDealerContactId($value)
    {
        $targetArray = array();
        foreach ($this->getDealerContactIds() as $element) {
            if ($element != $value) {
                $targetArray []= $element;
            }
        }
        $this->setDealerContactIds($targetArray);

        return $this;
    } // removeDealerContactId()

    /**
     * Set the value of [dealer_contact_versions] column.
     *
     * @param      array $v new value
     * @return   \Dealer\Model\DealerVersion The current object (for fluent API support)
     */
    public function setDealerContactVersions($v)
    {
        if ($this->dealer_contact_versions_unserialized !== $v) {
            $this->dealer_contact_versions_unserialized = $v;
            $this->dealer_contact_versions = '| ' . implode(' | ', $v) . ' |';
            $this->modifiedColumns[DealerVersionTableMap::DEALER_CONTACT_VERSIONS] = true;
        }


        return $this;
    } // setDealerContactVersions()

    /**
     * Adds a value to the [dealer_contact_versions] array column value.
     * @param      mixed $value
     *
     * @return   \Dealer\Model\DealerVersion The current object (for fluent API support)
     */
    public function addDealerContactVersion($value)
    {
        $currentArray = $this->getDealerContactVersions();
        $currentArray []= $value;
        $this->setDealerContactVersions($currentArray);

        return $this;
    } // addDealerContactVersion()

    /**
     * Removes a value from the [dealer_contact_versions] array column value.
     * @param      mixed $value
     *
     * @return   \Dealer\Model\DealerVersion The current object (for fluent API support)
     */
    public function removeDealerContactVersion($value)
    {
        $targetArray = array();
        foreach ($this->getDealerContactVersions() as $element) {
            if ($element != $value) {
                $targetArray []= $element;
            }
        }
        $this->setDealerContactVersions($targetArray);

        return $this;
    } // removeDealerContactVersion()

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
            if ($this->latitude !== '0') {
                return false;
            }

            if ($this->longitude !== '0') {
                return false;
            }

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


            $col = $row[TableMap::TYPE_NUM == $indexType ? 0 + $startcol : DealerVersionTableMap::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
            $this->id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 1 + $startcol : DealerVersionTableMap::translateFieldName('Address1', TableMap::TYPE_PHPNAME, $indexType)];
            $this->address1 = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 2 + $startcol : DealerVersionTableMap::translateFieldName('Address2', TableMap::TYPE_PHPNAME, $indexType)];
            $this->address2 = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 3 + $startcol : DealerVersionTableMap::translateFieldName('Address3', TableMap::TYPE_PHPNAME, $indexType)];
            $this->address3 = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 4 + $startcol : DealerVersionTableMap::translateFieldName('Zipcode', TableMap::TYPE_PHPNAME, $indexType)];
            $this->zipcode = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 5 + $startcol : DealerVersionTableMap::translateFieldName('City', TableMap::TYPE_PHPNAME, $indexType)];
            $this->city = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 6 + $startcol : DealerVersionTableMap::translateFieldName('CountryId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->country_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 7 + $startcol : DealerVersionTableMap::translateFieldName('Latitude', TableMap::TYPE_PHPNAME, $indexType)];
            $this->latitude = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 8 + $startcol : DealerVersionTableMap::translateFieldName('Longitude', TableMap::TYPE_PHPNAME, $indexType)];
            $this->longitude = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 9 + $startcol : DealerVersionTableMap::translateFieldName('CreatedAt', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00 00:00:00') {
                $col = null;
            }
            $this->created_at = (null !== $col) ? PropelDateTime::newInstance($col, null, '\DateTime') : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 10 + $startcol : DealerVersionTableMap::translateFieldName('UpdatedAt', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00 00:00:00') {
                $col = null;
            }
            $this->updated_at = (null !== $col) ? PropelDateTime::newInstance($col, null, '\DateTime') : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 11 + $startcol : DealerVersionTableMap::translateFieldName('Version', TableMap::TYPE_PHPNAME, $indexType)];
            $this->version = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 12 + $startcol : DealerVersionTableMap::translateFieldName('VersionCreatedAt', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00 00:00:00') {
                $col = null;
            }
            $this->version_created_at = (null !== $col) ? PropelDateTime::newInstance($col, null, '\DateTime') : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 13 + $startcol : DealerVersionTableMap::translateFieldName('VersionCreatedBy', TableMap::TYPE_PHPNAME, $indexType)];
            $this->version_created_by = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 14 + $startcol : DealerVersionTableMap::translateFieldName('DealerContentIds', TableMap::TYPE_PHPNAME, $indexType)];
            $this->dealer_content_ids = $col;
            $this->dealer_content_ids_unserialized = null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 15 + $startcol : DealerVersionTableMap::translateFieldName('DealerContentVersions', TableMap::TYPE_PHPNAME, $indexType)];
            $this->dealer_content_versions = $col;
            $this->dealer_content_versions_unserialized = null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 16 + $startcol : DealerVersionTableMap::translateFieldName('DealerShedulesIds', TableMap::TYPE_PHPNAME, $indexType)];
            $this->dealer_shedules_ids = $col;
            $this->dealer_shedules_ids_unserialized = null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 17 + $startcol : DealerVersionTableMap::translateFieldName('DealerShedulesVersions', TableMap::TYPE_PHPNAME, $indexType)];
            $this->dealer_shedules_versions = $col;
            $this->dealer_shedules_versions_unserialized = null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 18 + $startcol : DealerVersionTableMap::translateFieldName('DealerContactIds', TableMap::TYPE_PHPNAME, $indexType)];
            $this->dealer_contact_ids = $col;
            $this->dealer_contact_ids_unserialized = null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 19 + $startcol : DealerVersionTableMap::translateFieldName('DealerContactVersions', TableMap::TYPE_PHPNAME, $indexType)];
            $this->dealer_contact_versions = $col;
            $this->dealer_contact_versions_unserialized = null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }

            return $startcol + 20; // 20 = DealerVersionTableMap::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating \Dealer\Model\DealerVersion object", 0, $e);
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
        if ($this->aDealer !== null && $this->id !== $this->aDealer->getId()) {
            $this->aDealer = null;
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
            $con = Propel::getServiceContainer()->getReadConnection(DealerVersionTableMap::DATABASE_NAME);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $dataFetcher = ChildDealerVersionQuery::create(null, $this->buildPkeyCriteria())->setFormatter(ModelCriteria::FORMAT_STATEMENT)->find($con);
        $row = $dataFetcher->fetch();
        $dataFetcher->close();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true, $dataFetcher->getIndexType()); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aDealer = null;
        } // if (deep)
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param      ConnectionInterface $con
     * @return void
     * @throws PropelException
     * @see DealerVersion::setDeleted()
     * @see DealerVersion::isDeleted()
     */
    public function delete(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(DealerVersionTableMap::DATABASE_NAME);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = ChildDealerVersionQuery::create()
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
            $con = Propel::getServiceContainer()->getWriteConnection(DealerVersionTableMap::DATABASE_NAME);
        }

        $con->beginTransaction();
        $isInsert = $this->isNew();
        try {
            $ret = $this->preSave($con);
            if ($isInsert) {
                $ret = $ret && $this->preInsert($con);
            } else {
                $ret = $ret && $this->preUpdate($con);
            }
            if ($ret) {
                $affectedRows = $this->doSave($con);
                if ($isInsert) {
                    $this->postInsert($con);
                } else {
                    $this->postUpdate($con);
                }
                $this->postSave($con);
                DealerVersionTableMap::addInstanceToPool($this);
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

            if ($this->aDealer !== null) {
                if ($this->aDealer->isModified() || $this->aDealer->isNew()) {
                    $affectedRows += $this->aDealer->save($con);
                }
                $this->setDealer($this->aDealer);
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


         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(DealerVersionTableMap::ID)) {
            $modifiedColumns[':p' . $index++]  = 'ID';
        }
        if ($this->isColumnModified(DealerVersionTableMap::ADDRESS1)) {
            $modifiedColumns[':p' . $index++]  = 'ADDRESS1';
        }
        if ($this->isColumnModified(DealerVersionTableMap::ADDRESS2)) {
            $modifiedColumns[':p' . $index++]  = 'ADDRESS2';
        }
        if ($this->isColumnModified(DealerVersionTableMap::ADDRESS3)) {
            $modifiedColumns[':p' . $index++]  = 'ADDRESS3';
        }
        if ($this->isColumnModified(DealerVersionTableMap::ZIPCODE)) {
            $modifiedColumns[':p' . $index++]  = 'ZIPCODE';
        }
        if ($this->isColumnModified(DealerVersionTableMap::CITY)) {
            $modifiedColumns[':p' . $index++]  = 'CITY';
        }
        if ($this->isColumnModified(DealerVersionTableMap::COUNTRY_ID)) {
            $modifiedColumns[':p' . $index++]  = 'COUNTRY_ID';
        }
        if ($this->isColumnModified(DealerVersionTableMap::LATITUDE)) {
            $modifiedColumns[':p' . $index++]  = 'LATITUDE';
        }
        if ($this->isColumnModified(DealerVersionTableMap::LONGITUDE)) {
            $modifiedColumns[':p' . $index++]  = 'LONGITUDE';
        }
        if ($this->isColumnModified(DealerVersionTableMap::CREATED_AT)) {
            $modifiedColumns[':p' . $index++]  = 'CREATED_AT';
        }
        if ($this->isColumnModified(DealerVersionTableMap::UPDATED_AT)) {
            $modifiedColumns[':p' . $index++]  = 'UPDATED_AT';
        }
        if ($this->isColumnModified(DealerVersionTableMap::VERSION)) {
            $modifiedColumns[':p' . $index++]  = 'VERSION';
        }
        if ($this->isColumnModified(DealerVersionTableMap::VERSION_CREATED_AT)) {
            $modifiedColumns[':p' . $index++]  = 'VERSION_CREATED_AT';
        }
        if ($this->isColumnModified(DealerVersionTableMap::VERSION_CREATED_BY)) {
            $modifiedColumns[':p' . $index++]  = 'VERSION_CREATED_BY';
        }
        if ($this->isColumnModified(DealerVersionTableMap::DEALER_CONTENT_IDS)) {
            $modifiedColumns[':p' . $index++]  = 'DEALER_CONTENT_IDS';
        }
        if ($this->isColumnModified(DealerVersionTableMap::DEALER_CONTENT_VERSIONS)) {
            $modifiedColumns[':p' . $index++]  = 'DEALER_CONTENT_VERSIONS';
        }
        if ($this->isColumnModified(DealerVersionTableMap::DEALER_SHEDULES_IDS)) {
            $modifiedColumns[':p' . $index++]  = 'DEALER_SHEDULES_IDS';
        }
        if ($this->isColumnModified(DealerVersionTableMap::DEALER_SHEDULES_VERSIONS)) {
            $modifiedColumns[':p' . $index++]  = 'DEALER_SHEDULES_VERSIONS';
        }
        if ($this->isColumnModified(DealerVersionTableMap::DEALER_CONTACT_IDS)) {
            $modifiedColumns[':p' . $index++]  = 'DEALER_CONTACT_IDS';
        }
        if ($this->isColumnModified(DealerVersionTableMap::DEALER_CONTACT_VERSIONS)) {
            $modifiedColumns[':p' . $index++]  = 'DEALER_CONTACT_VERSIONS';
        }

        $sql = sprintf(
            'INSERT INTO dealer_version (%s) VALUES (%s)',
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
                    case 'ADDRESS1':
                        $stmt->bindValue($identifier, $this->address1, PDO::PARAM_STR);
                        break;
                    case 'ADDRESS2':
                        $stmt->bindValue($identifier, $this->address2, PDO::PARAM_STR);
                        break;
                    case 'ADDRESS3':
                        $stmt->bindValue($identifier, $this->address3, PDO::PARAM_STR);
                        break;
                    case 'ZIPCODE':
                        $stmt->bindValue($identifier, $this->zipcode, PDO::PARAM_STR);
                        break;
                    case 'CITY':
                        $stmt->bindValue($identifier, $this->city, PDO::PARAM_STR);
                        break;
                    case 'COUNTRY_ID':
                        $stmt->bindValue($identifier, $this->country_id, PDO::PARAM_INT);
                        break;
                    case 'LATITUDE':
                        $stmt->bindValue($identifier, $this->latitude, PDO::PARAM_STR);
                        break;
                    case 'LONGITUDE':
                        $stmt->bindValue($identifier, $this->longitude, PDO::PARAM_STR);
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
                    case 'DEALER_CONTENT_IDS':
                        $stmt->bindValue($identifier, $this->dealer_content_ids, PDO::PARAM_STR);
                        break;
                    case 'DEALER_CONTENT_VERSIONS':
                        $stmt->bindValue($identifier, $this->dealer_content_versions, PDO::PARAM_STR);
                        break;
                    case 'DEALER_SHEDULES_IDS':
                        $stmt->bindValue($identifier, $this->dealer_shedules_ids, PDO::PARAM_STR);
                        break;
                    case 'DEALER_SHEDULES_VERSIONS':
                        $stmt->bindValue($identifier, $this->dealer_shedules_versions, PDO::PARAM_STR);
                        break;
                    case 'DEALER_CONTACT_IDS':
                        $stmt->bindValue($identifier, $this->dealer_contact_ids, PDO::PARAM_STR);
                        break;
                    case 'DEALER_CONTACT_VERSIONS':
                        $stmt->bindValue($identifier, $this->dealer_contact_versions, PDO::PARAM_STR);
                        break;
                }
            }
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute INSERT statement [%s]', $sql), 0, $e);
        }

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
        $pos = DealerVersionTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);
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
                return $this->getAddress1();
                break;
            case 2:
                return $this->getAddress2();
                break;
            case 3:
                return $this->getAddress3();
                break;
            case 4:
                return $this->getZipcode();
                break;
            case 5:
                return $this->getCity();
                break;
            case 6:
                return $this->getCountryId();
                break;
            case 7:
                return $this->getLatitude();
                break;
            case 8:
                return $this->getLongitude();
                break;
            case 9:
                return $this->getCreatedAt();
                break;
            case 10:
                return $this->getUpdatedAt();
                break;
            case 11:
                return $this->getVersion();
                break;
            case 12:
                return $this->getVersionCreatedAt();
                break;
            case 13:
                return $this->getVersionCreatedBy();
                break;
            case 14:
                return $this->getDealerContentIds();
                break;
            case 15:
                return $this->getDealerContentVersions();
                break;
            case 16:
                return $this->getDealerShedulesIds();
                break;
            case 17:
                return $this->getDealerShedulesVersions();
                break;
            case 18:
                return $this->getDealerContactIds();
                break;
            case 19:
                return $this->getDealerContactVersions();
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
        if (isset($alreadyDumpedObjects['DealerVersion'][serialize($this->getPrimaryKey())])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['DealerVersion'][serialize($this->getPrimaryKey())] = true;
        $keys = DealerVersionTableMap::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getAddress1(),
            $keys[2] => $this->getAddress2(),
            $keys[3] => $this->getAddress3(),
            $keys[4] => $this->getZipcode(),
            $keys[5] => $this->getCity(),
            $keys[6] => $this->getCountryId(),
            $keys[7] => $this->getLatitude(),
            $keys[8] => $this->getLongitude(),
            $keys[9] => $this->getCreatedAt(),
            $keys[10] => $this->getUpdatedAt(),
            $keys[11] => $this->getVersion(),
            $keys[12] => $this->getVersionCreatedAt(),
            $keys[13] => $this->getVersionCreatedBy(),
            $keys[14] => $this->getDealerContentIds(),
            $keys[15] => $this->getDealerContentVersions(),
            $keys[16] => $this->getDealerShedulesIds(),
            $keys[17] => $this->getDealerShedulesVersions(),
            $keys[18] => $this->getDealerContactIds(),
            $keys[19] => $this->getDealerContactVersions(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->aDealer) {
                $result['Dealer'] = $this->aDealer->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
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
        $pos = DealerVersionTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);

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
                $this->setAddress1($value);
                break;
            case 2:
                $this->setAddress2($value);
                break;
            case 3:
                $this->setAddress3($value);
                break;
            case 4:
                $this->setZipcode($value);
                break;
            case 5:
                $this->setCity($value);
                break;
            case 6:
                $this->setCountryId($value);
                break;
            case 7:
                $this->setLatitude($value);
                break;
            case 8:
                $this->setLongitude($value);
                break;
            case 9:
                $this->setCreatedAt($value);
                break;
            case 10:
                $this->setUpdatedAt($value);
                break;
            case 11:
                $this->setVersion($value);
                break;
            case 12:
                $this->setVersionCreatedAt($value);
                break;
            case 13:
                $this->setVersionCreatedBy($value);
                break;
            case 14:
                if (!is_array($value)) {
                    $v = trim(substr($value, 2, -2));
                    $value = $v ? explode(' | ', $v) : array();
                }
                $this->setDealerContentIds($value);
                break;
            case 15:
                if (!is_array($value)) {
                    $v = trim(substr($value, 2, -2));
                    $value = $v ? explode(' | ', $v) : array();
                }
                $this->setDealerContentVersions($value);
                break;
            case 16:
                if (!is_array($value)) {
                    $v = trim(substr($value, 2, -2));
                    $value = $v ? explode(' | ', $v) : array();
                }
                $this->setDealerShedulesIds($value);
                break;
            case 17:
                if (!is_array($value)) {
                    $v = trim(substr($value, 2, -2));
                    $value = $v ? explode(' | ', $v) : array();
                }
                $this->setDealerShedulesVersions($value);
                break;
            case 18:
                if (!is_array($value)) {
                    $v = trim(substr($value, 2, -2));
                    $value = $v ? explode(' | ', $v) : array();
                }
                $this->setDealerContactIds($value);
                break;
            case 19:
                if (!is_array($value)) {
                    $v = trim(substr($value, 2, -2));
                    $value = $v ? explode(' | ', $v) : array();
                }
                $this->setDealerContactVersions($value);
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
        $keys = DealerVersionTableMap::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setAddress1($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setAddress2($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setAddress3($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setZipcode($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setCity($arr[$keys[5]]);
        if (array_key_exists($keys[6], $arr)) $this->setCountryId($arr[$keys[6]]);
        if (array_key_exists($keys[7], $arr)) $this->setLatitude($arr[$keys[7]]);
        if (array_key_exists($keys[8], $arr)) $this->setLongitude($arr[$keys[8]]);
        if (array_key_exists($keys[9], $arr)) $this->setCreatedAt($arr[$keys[9]]);
        if (array_key_exists($keys[10], $arr)) $this->setUpdatedAt($arr[$keys[10]]);
        if (array_key_exists($keys[11], $arr)) $this->setVersion($arr[$keys[11]]);
        if (array_key_exists($keys[12], $arr)) $this->setVersionCreatedAt($arr[$keys[12]]);
        if (array_key_exists($keys[13], $arr)) $this->setVersionCreatedBy($arr[$keys[13]]);
        if (array_key_exists($keys[14], $arr)) $this->setDealerContentIds($arr[$keys[14]]);
        if (array_key_exists($keys[15], $arr)) $this->setDealerContentVersions($arr[$keys[15]]);
        if (array_key_exists($keys[16], $arr)) $this->setDealerShedulesIds($arr[$keys[16]]);
        if (array_key_exists($keys[17], $arr)) $this->setDealerShedulesVersions($arr[$keys[17]]);
        if (array_key_exists($keys[18], $arr)) $this->setDealerContactIds($arr[$keys[18]]);
        if (array_key_exists($keys[19], $arr)) $this->setDealerContactVersions($arr[$keys[19]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(DealerVersionTableMap::DATABASE_NAME);

        if ($this->isColumnModified(DealerVersionTableMap::ID)) $criteria->add(DealerVersionTableMap::ID, $this->id);
        if ($this->isColumnModified(DealerVersionTableMap::ADDRESS1)) $criteria->add(DealerVersionTableMap::ADDRESS1, $this->address1);
        if ($this->isColumnModified(DealerVersionTableMap::ADDRESS2)) $criteria->add(DealerVersionTableMap::ADDRESS2, $this->address2);
        if ($this->isColumnModified(DealerVersionTableMap::ADDRESS3)) $criteria->add(DealerVersionTableMap::ADDRESS3, $this->address3);
        if ($this->isColumnModified(DealerVersionTableMap::ZIPCODE)) $criteria->add(DealerVersionTableMap::ZIPCODE, $this->zipcode);
        if ($this->isColumnModified(DealerVersionTableMap::CITY)) $criteria->add(DealerVersionTableMap::CITY, $this->city);
        if ($this->isColumnModified(DealerVersionTableMap::COUNTRY_ID)) $criteria->add(DealerVersionTableMap::COUNTRY_ID, $this->country_id);
        if ($this->isColumnModified(DealerVersionTableMap::LATITUDE)) $criteria->add(DealerVersionTableMap::LATITUDE, $this->latitude);
        if ($this->isColumnModified(DealerVersionTableMap::LONGITUDE)) $criteria->add(DealerVersionTableMap::LONGITUDE, $this->longitude);
        if ($this->isColumnModified(DealerVersionTableMap::CREATED_AT)) $criteria->add(DealerVersionTableMap::CREATED_AT, $this->created_at);
        if ($this->isColumnModified(DealerVersionTableMap::UPDATED_AT)) $criteria->add(DealerVersionTableMap::UPDATED_AT, $this->updated_at);
        if ($this->isColumnModified(DealerVersionTableMap::VERSION)) $criteria->add(DealerVersionTableMap::VERSION, $this->version);
        if ($this->isColumnModified(DealerVersionTableMap::VERSION_CREATED_AT)) $criteria->add(DealerVersionTableMap::VERSION_CREATED_AT, $this->version_created_at);
        if ($this->isColumnModified(DealerVersionTableMap::VERSION_CREATED_BY)) $criteria->add(DealerVersionTableMap::VERSION_CREATED_BY, $this->version_created_by);
        if ($this->isColumnModified(DealerVersionTableMap::DEALER_CONTENT_IDS)) $criteria->add(DealerVersionTableMap::DEALER_CONTENT_IDS, $this->dealer_content_ids);
        if ($this->isColumnModified(DealerVersionTableMap::DEALER_CONTENT_VERSIONS)) $criteria->add(DealerVersionTableMap::DEALER_CONTENT_VERSIONS, $this->dealer_content_versions);
        if ($this->isColumnModified(DealerVersionTableMap::DEALER_SHEDULES_IDS)) $criteria->add(DealerVersionTableMap::DEALER_SHEDULES_IDS, $this->dealer_shedules_ids);
        if ($this->isColumnModified(DealerVersionTableMap::DEALER_SHEDULES_VERSIONS)) $criteria->add(DealerVersionTableMap::DEALER_SHEDULES_VERSIONS, $this->dealer_shedules_versions);
        if ($this->isColumnModified(DealerVersionTableMap::DEALER_CONTACT_IDS)) $criteria->add(DealerVersionTableMap::DEALER_CONTACT_IDS, $this->dealer_contact_ids);
        if ($this->isColumnModified(DealerVersionTableMap::DEALER_CONTACT_VERSIONS)) $criteria->add(DealerVersionTableMap::DEALER_CONTACT_VERSIONS, $this->dealer_contact_versions);

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
        $criteria = new Criteria(DealerVersionTableMap::DATABASE_NAME);
        $criteria->add(DealerVersionTableMap::ID, $this->id);
        $criteria->add(DealerVersionTableMap::VERSION, $this->version);

        return $criteria;
    }

    /**
     * Returns the composite primary key for this object.
     * The array elements will be in same order as specified in XML.
     * @return array
     */
    public function getPrimaryKey()
    {
        $pks = array();
        $pks[0] = $this->getId();
        $pks[1] = $this->getVersion();

        return $pks;
    }

    /**
     * Set the [composite] primary key.
     *
     * @param      array $keys The elements of the composite key (order must match the order in XML file).
     * @return void
     */
    public function setPrimaryKey($keys)
    {
        $this->setId($keys[0]);
        $this->setVersion($keys[1]);
    }

    /**
     * Returns true if the primary key for this object is null.
     * @return boolean
     */
    public function isPrimaryKeyNull()
    {

        return (null === $this->getId()) && (null === $this->getVersion());
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      object $copyObj An object of \Dealer\Model\DealerVersion (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setId($this->getId());
        $copyObj->setAddress1($this->getAddress1());
        $copyObj->setAddress2($this->getAddress2());
        $copyObj->setAddress3($this->getAddress3());
        $copyObj->setZipcode($this->getZipcode());
        $copyObj->setCity($this->getCity());
        $copyObj->setCountryId($this->getCountryId());
        $copyObj->setLatitude($this->getLatitude());
        $copyObj->setLongitude($this->getLongitude());
        $copyObj->setCreatedAt($this->getCreatedAt());
        $copyObj->setUpdatedAt($this->getUpdatedAt());
        $copyObj->setVersion($this->getVersion());
        $copyObj->setVersionCreatedAt($this->getVersionCreatedAt());
        $copyObj->setVersionCreatedBy($this->getVersionCreatedBy());
        $copyObj->setDealerContentIds($this->getDealerContentIds());
        $copyObj->setDealerContentVersions($this->getDealerContentVersions());
        $copyObj->setDealerShedulesIds($this->getDealerShedulesIds());
        $copyObj->setDealerShedulesVersions($this->getDealerShedulesVersions());
        $copyObj->setDealerContactIds($this->getDealerContactIds());
        $copyObj->setDealerContactVersions($this->getDealerContactVersions());
        if ($makeNew) {
            $copyObj->setNew(true);
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
     * @return                 \Dealer\Model\DealerVersion Clone of current object.
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
     * Declares an association between this object and a ChildDealer object.
     *
     * @param                  ChildDealer $v
     * @return                 \Dealer\Model\DealerVersion The current object (for fluent API support)
     * @throws PropelException
     */
    public function setDealer(ChildDealer $v = null)
    {
        if ($v === null) {
            $this->setId(NULL);
        } else {
            $this->setId($v->getId());
        }

        $this->aDealer = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the ChildDealer object, it will not be re-added.
        if ($v !== null) {
            $v->addDealerVersion($this);
        }


        return $this;
    }


    /**
     * Get the associated ChildDealer object
     *
     * @param      ConnectionInterface $con Optional Connection object.
     * @return                 ChildDealer The associated ChildDealer object.
     * @throws PropelException
     */
    public function getDealer(ConnectionInterface $con = null)
    {
        if ($this->aDealer === null && ($this->id !== null)) {
            $this->aDealer = ChildDealerQuery::create()->findPk($this->id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aDealer->addDealerVersions($this);
             */
        }

        return $this->aDealer;
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->address1 = null;
        $this->address2 = null;
        $this->address3 = null;
        $this->zipcode = null;
        $this->city = null;
        $this->country_id = null;
        $this->latitude = null;
        $this->longitude = null;
        $this->created_at = null;
        $this->updated_at = null;
        $this->version = null;
        $this->version_created_at = null;
        $this->version_created_by = null;
        $this->dealer_content_ids = null;
        $this->dealer_content_ids_unserialized = null;
        $this->dealer_content_versions = null;
        $this->dealer_content_versions_unserialized = null;
        $this->dealer_shedules_ids = null;
        $this->dealer_shedules_ids_unserialized = null;
        $this->dealer_shedules_versions = null;
        $this->dealer_shedules_versions_unserialized = null;
        $this->dealer_contact_ids = null;
        $this->dealer_contact_ids_unserialized = null;
        $this->dealer_contact_versions = null;
        $this->dealer_contact_versions_unserialized = null;
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
        } // if ($deep)

        $this->aDealer = null;
    }

    /**
     * Return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(DealerVersionTableMap::DEFAULT_STRING_FORMAT);
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
