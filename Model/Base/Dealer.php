<?php

namespace Dealer\Model\Base;

use \DateTime;
use \Exception;
use \PDO;
use Dealer\Model\Dealer as ChildDealer;
use Dealer\Model\DealerContact as ChildDealerContact;
use Dealer\Model\DealerContactInfo as ChildDealerContactInfo;
use Dealer\Model\DealerContactInfoQuery as ChildDealerContactInfoQuery;
use Dealer\Model\DealerContactInfoVersionQuery as ChildDealerContactInfoVersionQuery;
use Dealer\Model\DealerContactQuery as ChildDealerContactQuery;
use Dealer\Model\DealerContactVersionQuery as ChildDealerContactVersionQuery;
use Dealer\Model\DealerI18n as ChildDealerI18n;
use Dealer\Model\DealerI18nQuery as ChildDealerI18nQuery;
use Dealer\Model\DealerQuery as ChildDealerQuery;
use Dealer\Model\DealerShedules as ChildDealerShedules;
use Dealer\Model\DealerShedulesQuery as ChildDealerShedulesQuery;
use Dealer\Model\DealerShedulesVersionQuery as ChildDealerShedulesVersionQuery;
use Dealer\Model\DealerVersion as ChildDealerVersion;
use Dealer\Model\DealerVersionQuery as ChildDealerVersionQuery;
use Dealer\Model\Map\DealerContactInfoVersionTableMap;
use Dealer\Model\Map\DealerContactVersionTableMap;
use Dealer\Model\Map\DealerShedulesVersionTableMap;
use Dealer\Model\Map\DealerTableMap;
use Dealer\Model\Map\DealerVersionTableMap;
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
use Thelia\Model\Country as ChildCountry;
use Thelia\Model\CountryQuery;

abstract class Dealer implements ActiveRecordInterface
{
    /**
     * TableMap class name
     */
    const TABLE_MAP = '\\Dealer\\Model\\Map\\DealerTableMap';


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
     * @var        Country
     */
    protected $aCountry;

    /**
     * @var        ObjectCollection|ChildDealerShedules[] Collection to store aggregation of ChildDealerShedules objects.
     */
    protected $collDealerSheduless;
    protected $collDealerShedulessPartial;

    /**
     * @var        ObjectCollection|ChildDealerContact[] Collection to store aggregation of ChildDealerContact objects.
     */
    protected $collDealerContacts;
    protected $collDealerContactsPartial;

    /**
     * @var        ObjectCollection|ChildDealerContactInfo[] Collection to store aggregation of ChildDealerContactInfo objects.
     */
    protected $collDealerContactInfos;
    protected $collDealerContactInfosPartial;

    /**
     * @var        ObjectCollection|ChildDealerI18n[] Collection to store aggregation of ChildDealerI18n objects.
     */
    protected $collDealerI18ns;
    protected $collDealerI18nsPartial;

    /**
     * @var        ObjectCollection|ChildDealerVersion[] Collection to store aggregation of ChildDealerVersion objects.
     */
    protected $collDealerVersions;
    protected $collDealerVersionsPartial;

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
     * @var        array[ChildDealerI18n]
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
    protected $dealerShedulessScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $dealerContactsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $dealerContactInfosScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $dealerI18nsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $dealerVersionsScheduledForDeletion = null;

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
     * Initializes internal state of Dealer\Model\Base\Dealer object.
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
     * Compares this with another <code>Dealer</code> instance.  If
     * <code>obj</code> is an instance of <code>Dealer</code>, delegates to
     * <code>equals(Dealer)</code>.  Otherwise, returns <code>false</code>.
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
     * @return Dealer The current object, for fluid interface
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
     * @return Dealer The current object, for fluid interface
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
     * Set the value of [id] column.
     *
     * @param      int $v new value
     * @return   \Dealer\Model\Dealer The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[DealerTableMap::ID] = true;
        }


        return $this;
    } // setId()

    /**
     * Set the value of [address1] column.
     *
     * @param      string $v new value
     * @return   \Dealer\Model\Dealer The current object (for fluent API support)
     */
    public function setAddress1($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->address1 !== $v) {
            $this->address1 = $v;
            $this->modifiedColumns[DealerTableMap::ADDRESS1] = true;
        }


        return $this;
    } // setAddress1()

    /**
     * Set the value of [address2] column.
     *
     * @param      string $v new value
     * @return   \Dealer\Model\Dealer The current object (for fluent API support)
     */
    public function setAddress2($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->address2 !== $v) {
            $this->address2 = $v;
            $this->modifiedColumns[DealerTableMap::ADDRESS2] = true;
        }


        return $this;
    } // setAddress2()

    /**
     * Set the value of [address3] column.
     *
     * @param      string $v new value
     * @return   \Dealer\Model\Dealer The current object (for fluent API support)
     */
    public function setAddress3($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->address3 !== $v) {
            $this->address3 = $v;
            $this->modifiedColumns[DealerTableMap::ADDRESS3] = true;
        }


        return $this;
    } // setAddress3()

    /**
     * Set the value of [zipcode] column.
     *
     * @param      string $v new value
     * @return   \Dealer\Model\Dealer The current object (for fluent API support)
     */
    public function setZipcode($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->zipcode !== $v) {
            $this->zipcode = $v;
            $this->modifiedColumns[DealerTableMap::ZIPCODE] = true;
        }


        return $this;
    } // setZipcode()

    /**
     * Set the value of [city] column.
     *
     * @param      string $v new value
     * @return   \Dealer\Model\Dealer The current object (for fluent API support)
     */
    public function setCity($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->city !== $v) {
            $this->city = $v;
            $this->modifiedColumns[DealerTableMap::CITY] = true;
        }


        return $this;
    } // setCity()

    /**
     * Set the value of [country_id] column.
     *
     * @param      int $v new value
     * @return   \Dealer\Model\Dealer The current object (for fluent API support)
     */
    public function setCountryId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->country_id !== $v) {
            $this->country_id = $v;
            $this->modifiedColumns[DealerTableMap::COUNTRY_ID] = true;
        }

        if ($this->aCountry !== null && $this->aCountry->getId() !== $v) {
            $this->aCountry = null;
        }


        return $this;
    } // setCountryId()

    /**
     * Set the value of [latitude] column.
     *
     * @param      string $v new value
     * @return   \Dealer\Model\Dealer The current object (for fluent API support)
     */
    public function setLatitude($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->latitude !== $v) {
            $this->latitude = $v;
            $this->modifiedColumns[DealerTableMap::LATITUDE] = true;
        }


        return $this;
    } // setLatitude()

    /**
     * Set the value of [longitude] column.
     *
     * @param      string $v new value
     * @return   \Dealer\Model\Dealer The current object (for fluent API support)
     */
    public function setLongitude($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->longitude !== $v) {
            $this->longitude = $v;
            $this->modifiedColumns[DealerTableMap::LONGITUDE] = true;
        }


        return $this;
    } // setLongitude()

    /**
     * Sets the value of [created_at] column to a normalized version of the date/time value specified.
     *
     * @param      mixed $v string, integer (timestamp), or \DateTime value.
     *               Empty strings are treated as NULL.
     * @return   \Dealer\Model\Dealer The current object (for fluent API support)
     */
    public function setCreatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, '\DateTime');
        if ($this->created_at !== null || $dt !== null) {
            if ($dt !== $this->created_at) {
                $this->created_at = $dt;
                $this->modifiedColumns[DealerTableMap::CREATED_AT] = true;
            }
        } // if either are not null


        return $this;
    } // setCreatedAt()

    /**
     * Sets the value of [updated_at] column to a normalized version of the date/time value specified.
     *
     * @param      mixed $v string, integer (timestamp), or \DateTime value.
     *               Empty strings are treated as NULL.
     * @return   \Dealer\Model\Dealer The current object (for fluent API support)
     */
    public function setUpdatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, '\DateTime');
        if ($this->updated_at !== null || $dt !== null) {
            if ($dt !== $this->updated_at) {
                $this->updated_at = $dt;
                $this->modifiedColumns[DealerTableMap::UPDATED_AT] = true;
            }
        } // if either are not null


        return $this;
    } // setUpdatedAt()

    /**
     * Set the value of [version] column.
     *
     * @param      int $v new value
     * @return   \Dealer\Model\Dealer The current object (for fluent API support)
     */
    public function setVersion($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->version !== $v) {
            $this->version = $v;
            $this->modifiedColumns[DealerTableMap::VERSION] = true;
        }


        return $this;
    } // setVersion()

    /**
     * Sets the value of [version_created_at] column to a normalized version of the date/time value specified.
     *
     * @param      mixed $v string, integer (timestamp), or \DateTime value.
     *               Empty strings are treated as NULL.
     * @return   \Dealer\Model\Dealer The current object (for fluent API support)
     */
    public function setVersionCreatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, '\DateTime');
        if ($this->version_created_at !== null || $dt !== null) {
            if ($dt !== $this->version_created_at) {
                $this->version_created_at = $dt;
                $this->modifiedColumns[DealerTableMap::VERSION_CREATED_AT] = true;
            }
        } // if either are not null


        return $this;
    } // setVersionCreatedAt()

    /**
     * Set the value of [version_created_by] column.
     *
     * @param      string $v new value
     * @return   \Dealer\Model\Dealer The current object (for fluent API support)
     */
    public function setVersionCreatedBy($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->version_created_by !== $v) {
            $this->version_created_by = $v;
            $this->modifiedColumns[DealerTableMap::VERSION_CREATED_BY] = true;
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


            $col = $row[TableMap::TYPE_NUM == $indexType ? 0 + $startcol : DealerTableMap::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
            $this->id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 1 + $startcol : DealerTableMap::translateFieldName('Address1', TableMap::TYPE_PHPNAME, $indexType)];
            $this->address1 = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 2 + $startcol : DealerTableMap::translateFieldName('Address2', TableMap::TYPE_PHPNAME, $indexType)];
            $this->address2 = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 3 + $startcol : DealerTableMap::translateFieldName('Address3', TableMap::TYPE_PHPNAME, $indexType)];
            $this->address3 = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 4 + $startcol : DealerTableMap::translateFieldName('Zipcode', TableMap::TYPE_PHPNAME, $indexType)];
            $this->zipcode = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 5 + $startcol : DealerTableMap::translateFieldName('City', TableMap::TYPE_PHPNAME, $indexType)];
            $this->city = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 6 + $startcol : DealerTableMap::translateFieldName('CountryId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->country_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 7 + $startcol : DealerTableMap::translateFieldName('Latitude', TableMap::TYPE_PHPNAME, $indexType)];
            $this->latitude = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 8 + $startcol : DealerTableMap::translateFieldName('Longitude', TableMap::TYPE_PHPNAME, $indexType)];
            $this->longitude = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 9 + $startcol : DealerTableMap::translateFieldName('CreatedAt', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00 00:00:00') {
                $col = null;
            }
            $this->created_at = (null !== $col) ? PropelDateTime::newInstance($col, null, '\DateTime') : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 10 + $startcol : DealerTableMap::translateFieldName('UpdatedAt', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00 00:00:00') {
                $col = null;
            }
            $this->updated_at = (null !== $col) ? PropelDateTime::newInstance($col, null, '\DateTime') : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 11 + $startcol : DealerTableMap::translateFieldName('Version', TableMap::TYPE_PHPNAME, $indexType)];
            $this->version = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 12 + $startcol : DealerTableMap::translateFieldName('VersionCreatedAt', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00 00:00:00') {
                $col = null;
            }
            $this->version_created_at = (null !== $col) ? PropelDateTime::newInstance($col, null, '\DateTime') : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 13 + $startcol : DealerTableMap::translateFieldName('VersionCreatedBy', TableMap::TYPE_PHPNAME, $indexType)];
            $this->version_created_by = (null !== $col) ? (string) $col : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }

            return $startcol + 14; // 14 = DealerTableMap::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating \Dealer\Model\Dealer object", 0, $e);
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
        if ($this->aCountry !== null && $this->country_id !== $this->aCountry->getId()) {
            $this->aCountry = null;
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
            $con = Propel::getServiceContainer()->getReadConnection(DealerTableMap::DATABASE_NAME);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $dataFetcher = ChildDealerQuery::create(null, $this->buildPkeyCriteria())->setFormatter(ModelCriteria::FORMAT_STATEMENT)->find($con);
        $row = $dataFetcher->fetch();
        $dataFetcher->close();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true, $dataFetcher->getIndexType()); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aCountry = null;
            $this->collDealerSheduless = null;

            $this->collDealerContacts = null;

            $this->collDealerContactInfos = null;

            $this->collDealerI18ns = null;

            $this->collDealerVersions = null;

        } // if (deep)
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param      ConnectionInterface $con
     * @return void
     * @throws PropelException
     * @see Dealer::setDeleted()
     * @see Dealer::isDeleted()
     */
    public function delete(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(DealerTableMap::DATABASE_NAME);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = ChildDealerQuery::create()
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
            $con = Propel::getServiceContainer()->getWriteConnection(DealerTableMap::DATABASE_NAME);
        }

        $con->beginTransaction();
        $isInsert = $this->isNew();
        try {
            $ret = $this->preSave($con);
            // versionable behavior
            if ($this->isVersioningNecessary()) {
                $this->setVersion($this->isNew() ? 1 : $this->getLastVersionNumber($con) + 1);
                if (!$this->isColumnModified(DealerTableMap::VERSION_CREATED_AT)) {
                    $this->setVersionCreatedAt(time());
                }
                $createVersion = true; // for postSave hook
            }
            if ($isInsert) {
                $ret = $ret && $this->preInsert($con);
                // timestampable behavior
                if (!$this->isColumnModified(DealerTableMap::CREATED_AT)) {
                    $this->setCreatedAt(time());
                }
                if (!$this->isColumnModified(DealerTableMap::UPDATED_AT)) {
                    $this->setUpdatedAt(time());
                }
            } else {
                $ret = $ret && $this->preUpdate($con);
                // timestampable behavior
                if ($this->isModified() && !$this->isColumnModified(DealerTableMap::UPDATED_AT)) {
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
                DealerTableMap::addInstanceToPool($this);
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

            if ($this->aCountry !== null) {
                if ($this->aCountry->isModified() || $this->aCountry->isNew()) {
                    $affectedRows += $this->aCountry->save($con);
                }
                $this->setCountry($this->aCountry);
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

            if ($this->dealerShedulessScheduledForDeletion !== null) {
                if (!$this->dealerShedulessScheduledForDeletion->isEmpty()) {
                    \Dealer\Model\DealerShedulesQuery::create()
                        ->filterByPrimaryKeys($this->dealerShedulessScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->dealerShedulessScheduledForDeletion = null;
                }
            }

                if ($this->collDealerSheduless !== null) {
            foreach ($this->collDealerSheduless as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->dealerContactsScheduledForDeletion !== null) {
                if (!$this->dealerContactsScheduledForDeletion->isEmpty()) {
                    \Dealer\Model\DealerContactQuery::create()
                        ->filterByPrimaryKeys($this->dealerContactsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->dealerContactsScheduledForDeletion = null;
                }
            }

                if ($this->collDealerContacts !== null) {
            foreach ($this->collDealerContacts as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->dealerContactInfosScheduledForDeletion !== null) {
                if (!$this->dealerContactInfosScheduledForDeletion->isEmpty()) {
                    \Dealer\Model\DealerContactInfoQuery::create()
                        ->filterByPrimaryKeys($this->dealerContactInfosScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->dealerContactInfosScheduledForDeletion = null;
                }
            }

                if ($this->collDealerContactInfos !== null) {
            foreach ($this->collDealerContactInfos as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->dealerI18nsScheduledForDeletion !== null) {
                if (!$this->dealerI18nsScheduledForDeletion->isEmpty()) {
                    \Dealer\Model\DealerI18nQuery::create()
                        ->filterByPrimaryKeys($this->dealerI18nsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->dealerI18nsScheduledForDeletion = null;
                }
            }

                if ($this->collDealerI18ns !== null) {
            foreach ($this->collDealerI18ns as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->dealerVersionsScheduledForDeletion !== null) {
                if (!$this->dealerVersionsScheduledForDeletion->isEmpty()) {
                    \Dealer\Model\DealerVersionQuery::create()
                        ->filterByPrimaryKeys($this->dealerVersionsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->dealerVersionsScheduledForDeletion = null;
                }
            }

                if ($this->collDealerVersions !== null) {
            foreach ($this->collDealerVersions as $referrerFK) {
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

        $this->modifiedColumns[DealerTableMap::ID] = true;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . DealerTableMap::ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(DealerTableMap::ID)) {
            $modifiedColumns[':p' . $index++]  = 'ID';
        }
        if ($this->isColumnModified(DealerTableMap::ADDRESS1)) {
            $modifiedColumns[':p' . $index++]  = 'ADDRESS1';
        }
        if ($this->isColumnModified(DealerTableMap::ADDRESS2)) {
            $modifiedColumns[':p' . $index++]  = 'ADDRESS2';
        }
        if ($this->isColumnModified(DealerTableMap::ADDRESS3)) {
            $modifiedColumns[':p' . $index++]  = 'ADDRESS3';
        }
        if ($this->isColumnModified(DealerTableMap::ZIPCODE)) {
            $modifiedColumns[':p' . $index++]  = 'ZIPCODE';
        }
        if ($this->isColumnModified(DealerTableMap::CITY)) {
            $modifiedColumns[':p' . $index++]  = 'CITY';
        }
        if ($this->isColumnModified(DealerTableMap::COUNTRY_ID)) {
            $modifiedColumns[':p' . $index++]  = 'COUNTRY_ID';
        }
        if ($this->isColumnModified(DealerTableMap::LATITUDE)) {
            $modifiedColumns[':p' . $index++]  = 'LATITUDE';
        }
        if ($this->isColumnModified(DealerTableMap::LONGITUDE)) {
            $modifiedColumns[':p' . $index++]  = 'LONGITUDE';
        }
        if ($this->isColumnModified(DealerTableMap::CREATED_AT)) {
            $modifiedColumns[':p' . $index++]  = 'CREATED_AT';
        }
        if ($this->isColumnModified(DealerTableMap::UPDATED_AT)) {
            $modifiedColumns[':p' . $index++]  = 'UPDATED_AT';
        }
        if ($this->isColumnModified(DealerTableMap::VERSION)) {
            $modifiedColumns[':p' . $index++]  = 'VERSION';
        }
        if ($this->isColumnModified(DealerTableMap::VERSION_CREATED_AT)) {
            $modifiedColumns[':p' . $index++]  = 'VERSION_CREATED_AT';
        }
        if ($this->isColumnModified(DealerTableMap::VERSION_CREATED_BY)) {
            $modifiedColumns[':p' . $index++]  = 'VERSION_CREATED_BY';
        }

        $sql = sprintf(
            'INSERT INTO dealer (%s) VALUES (%s)',
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
        $pos = DealerTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);
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
        if (isset($alreadyDumpedObjects['Dealer'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['Dealer'][$this->getPrimaryKey()] = true;
        $keys = DealerTableMap::getFieldNames($keyType);
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
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->aCountry) {
                $result['Country'] = $this->aCountry->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->collDealerSheduless) {
                $result['DealerSheduless'] = $this->collDealerSheduless->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collDealerContacts) {
                $result['DealerContacts'] = $this->collDealerContacts->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collDealerContactInfos) {
                $result['DealerContactInfos'] = $this->collDealerContactInfos->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collDealerI18ns) {
                $result['DealerI18ns'] = $this->collDealerI18ns->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collDealerVersions) {
                $result['DealerVersions'] = $this->collDealerVersions->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
        $pos = DealerTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);

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
        $keys = DealerTableMap::getFieldNames($keyType);

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
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(DealerTableMap::DATABASE_NAME);

        if ($this->isColumnModified(DealerTableMap::ID)) $criteria->add(DealerTableMap::ID, $this->id);
        if ($this->isColumnModified(DealerTableMap::ADDRESS1)) $criteria->add(DealerTableMap::ADDRESS1, $this->address1);
        if ($this->isColumnModified(DealerTableMap::ADDRESS2)) $criteria->add(DealerTableMap::ADDRESS2, $this->address2);
        if ($this->isColumnModified(DealerTableMap::ADDRESS3)) $criteria->add(DealerTableMap::ADDRESS3, $this->address3);
        if ($this->isColumnModified(DealerTableMap::ZIPCODE)) $criteria->add(DealerTableMap::ZIPCODE, $this->zipcode);
        if ($this->isColumnModified(DealerTableMap::CITY)) $criteria->add(DealerTableMap::CITY, $this->city);
        if ($this->isColumnModified(DealerTableMap::COUNTRY_ID)) $criteria->add(DealerTableMap::COUNTRY_ID, $this->country_id);
        if ($this->isColumnModified(DealerTableMap::LATITUDE)) $criteria->add(DealerTableMap::LATITUDE, $this->latitude);
        if ($this->isColumnModified(DealerTableMap::LONGITUDE)) $criteria->add(DealerTableMap::LONGITUDE, $this->longitude);
        if ($this->isColumnModified(DealerTableMap::CREATED_AT)) $criteria->add(DealerTableMap::CREATED_AT, $this->created_at);
        if ($this->isColumnModified(DealerTableMap::UPDATED_AT)) $criteria->add(DealerTableMap::UPDATED_AT, $this->updated_at);
        if ($this->isColumnModified(DealerTableMap::VERSION)) $criteria->add(DealerTableMap::VERSION, $this->version);
        if ($this->isColumnModified(DealerTableMap::VERSION_CREATED_AT)) $criteria->add(DealerTableMap::VERSION_CREATED_AT, $this->version_created_at);
        if ($this->isColumnModified(DealerTableMap::VERSION_CREATED_BY)) $criteria->add(DealerTableMap::VERSION_CREATED_BY, $this->version_created_by);

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
        $criteria = new Criteria(DealerTableMap::DATABASE_NAME);
        $criteria->add(DealerTableMap::ID, $this->id);

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
     * @param      object $copyObj An object of \Dealer\Model\Dealer (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
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

        if ($deepCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);

            foreach ($this->getDealerSheduless() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addDealerShedules($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getDealerContacts() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addDealerContact($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getDealerContactInfos() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addDealerContactInfo($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getDealerI18ns() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addDealerI18n($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getDealerVersions() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addDealerVersion($relObj->copy($deepCopy));
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
     * @return                 \Dealer\Model\Dealer Clone of current object.
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
     * Declares an association between this object and a ChildCountry object.
     *
     * @param                  ChildCountry $v
     * @return                 \Dealer\Model\Dealer The current object (for fluent API support)
     * @throws PropelException
     */
    public function setCountry(ChildCountry $v = null)
    {
        if ($v === null) {
            $this->setCountryId(NULL);
        } else {
            $this->setCountryId($v->getId());
        }

        $this->aCountry = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the ChildCountry object, it will not be re-added.
        if ($v !== null) {
            $v->addDealer($this);
        }


        return $this;
    }


    /**
     * Get the associated ChildCountry object
     *
     * @param      ConnectionInterface $con Optional Connection object.
     * @return                 ChildCountry The associated ChildCountry object.
     * @throws PropelException
     */
    public function getCountry(ConnectionInterface $con = null)
    {
        if ($this->aCountry === null && ($this->country_id !== null)) {
            $this->aCountry = CountryQuery::create()->findPk($this->country_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aCountry->addDealers($this);
             */
        }

        return $this->aCountry;
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
        if ('DealerShedules' == $relationName) {
            return $this->initDealerSheduless();
        }
        if ('DealerContact' == $relationName) {
            return $this->initDealerContacts();
        }
        if ('DealerContactInfo' == $relationName) {
            return $this->initDealerContactInfos();
        }
        if ('DealerI18n' == $relationName) {
            return $this->initDealerI18ns();
        }
        if ('DealerVersion' == $relationName) {
            return $this->initDealerVersions();
        }
    }

    /**
     * Clears out the collDealerSheduless collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addDealerSheduless()
     */
    public function clearDealerSheduless()
    {
        $this->collDealerSheduless = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collDealerSheduless collection loaded partially.
     */
    public function resetPartialDealerSheduless($v = true)
    {
        $this->collDealerShedulessPartial = $v;
    }

    /**
     * Initializes the collDealerSheduless collection.
     *
     * By default this just sets the collDealerSheduless collection to an empty array (like clearcollDealerSheduless());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initDealerSheduless($overrideExisting = true)
    {
        if (null !== $this->collDealerSheduless && !$overrideExisting) {
            return;
        }
        $this->collDealerSheduless = new ObjectCollection();
        $this->collDealerSheduless->setModel('\Dealer\Model\DealerShedules');
    }

    /**
     * Gets an array of ChildDealerShedules objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildDealer is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildDealerShedules[] List of ChildDealerShedules objects
     * @throws PropelException
     */
    public function getDealerSheduless($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collDealerShedulessPartial && !$this->isNew();
        if (null === $this->collDealerSheduless || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collDealerSheduless) {
                // return empty collection
                $this->initDealerSheduless();
            } else {
                $collDealerSheduless = ChildDealerShedulesQuery::create(null, $criteria)
                    ->filterByDealer($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collDealerShedulessPartial && count($collDealerSheduless)) {
                        $this->initDealerSheduless(false);

                        foreach ($collDealerSheduless as $obj) {
                            if (false == $this->collDealerSheduless->contains($obj)) {
                                $this->collDealerSheduless->append($obj);
                            }
                        }

                        $this->collDealerShedulessPartial = true;
                    }

                    reset($collDealerSheduless);

                    return $collDealerSheduless;
                }

                if ($partial && $this->collDealerSheduless) {
                    foreach ($this->collDealerSheduless as $obj) {
                        if ($obj->isNew()) {
                            $collDealerSheduless[] = $obj;
                        }
                    }
                }

                $this->collDealerSheduless = $collDealerSheduless;
                $this->collDealerShedulessPartial = false;
            }
        }

        return $this->collDealerSheduless;
    }

    /**
     * Sets a collection of DealerShedules objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $dealerSheduless A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildDealer The current object (for fluent API support)
     */
    public function setDealerSheduless(Collection $dealerSheduless, ConnectionInterface $con = null)
    {
        $dealerShedulessToDelete = $this->getDealerSheduless(new Criteria(), $con)->diff($dealerSheduless);


        $this->dealerShedulessScheduledForDeletion = $dealerShedulessToDelete;

        foreach ($dealerShedulessToDelete as $dealerShedulesRemoved) {
            $dealerShedulesRemoved->setDealer(null);
        }

        $this->collDealerSheduless = null;
        foreach ($dealerSheduless as $dealerShedules) {
            $this->addDealerShedules($dealerShedules);
        }

        $this->collDealerSheduless = $dealerSheduless;
        $this->collDealerShedulessPartial = false;

        return $this;
    }

    /**
     * Returns the number of related DealerShedules objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related DealerShedules objects.
     * @throws PropelException
     */
    public function countDealerSheduless(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collDealerShedulessPartial && !$this->isNew();
        if (null === $this->collDealerSheduless || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collDealerSheduless) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getDealerSheduless());
            }

            $query = ChildDealerShedulesQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByDealer($this)
                ->count($con);
        }

        return count($this->collDealerSheduless);
    }

    /**
     * Method called to associate a ChildDealerShedules object to this object
     * through the ChildDealerShedules foreign key attribute.
     *
     * @param    ChildDealerShedules $l ChildDealerShedules
     * @return   \Dealer\Model\Dealer The current object (for fluent API support)
     */
    public function addDealerShedules(ChildDealerShedules $l)
    {
        if ($this->collDealerSheduless === null) {
            $this->initDealerSheduless();
            $this->collDealerShedulessPartial = true;
        }

        if (!in_array($l, $this->collDealerSheduless->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddDealerShedules($l);
        }

        return $this;
    }

    /**
     * @param DealerShedules $dealerShedules The dealerShedules object to add.
     */
    protected function doAddDealerShedules($dealerShedules)
    {
        $this->collDealerSheduless[]= $dealerShedules;
        $dealerShedules->setDealer($this);
    }

    /**
     * @param  DealerShedules $dealerShedules The dealerShedules object to remove.
     * @return ChildDealer The current object (for fluent API support)
     */
    public function removeDealerShedules($dealerShedules)
    {
        if ($this->getDealerSheduless()->contains($dealerShedules)) {
            $this->collDealerSheduless->remove($this->collDealerSheduless->search($dealerShedules));
            if (null === $this->dealerShedulessScheduledForDeletion) {
                $this->dealerShedulessScheduledForDeletion = clone $this->collDealerSheduless;
                $this->dealerShedulessScheduledForDeletion->clear();
            }
            $this->dealerShedulessScheduledForDeletion[]= clone $dealerShedules;
            $dealerShedules->setDealer(null);
        }

        return $this;
    }

    /**
     * Clears out the collDealerContacts collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addDealerContacts()
     */
    public function clearDealerContacts()
    {
        $this->collDealerContacts = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collDealerContacts collection loaded partially.
     */
    public function resetPartialDealerContacts($v = true)
    {
        $this->collDealerContactsPartial = $v;
    }

    /**
     * Initializes the collDealerContacts collection.
     *
     * By default this just sets the collDealerContacts collection to an empty array (like clearcollDealerContacts());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initDealerContacts($overrideExisting = true)
    {
        if (null !== $this->collDealerContacts && !$overrideExisting) {
            return;
        }
        $this->collDealerContacts = new ObjectCollection();
        $this->collDealerContacts->setModel('\Dealer\Model\DealerContact');
    }

    /**
     * Gets an array of ChildDealerContact objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildDealer is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildDealerContact[] List of ChildDealerContact objects
     * @throws PropelException
     */
    public function getDealerContacts($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collDealerContactsPartial && !$this->isNew();
        if (null === $this->collDealerContacts || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collDealerContacts) {
                // return empty collection
                $this->initDealerContacts();
            } else {
                $collDealerContacts = ChildDealerContactQuery::create(null, $criteria)
                    ->filterByDealer($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collDealerContactsPartial && count($collDealerContacts)) {
                        $this->initDealerContacts(false);

                        foreach ($collDealerContacts as $obj) {
                            if (false == $this->collDealerContacts->contains($obj)) {
                                $this->collDealerContacts->append($obj);
                            }
                        }

                        $this->collDealerContactsPartial = true;
                    }

                    reset($collDealerContacts);

                    return $collDealerContacts;
                }

                if ($partial && $this->collDealerContacts) {
                    foreach ($this->collDealerContacts as $obj) {
                        if ($obj->isNew()) {
                            $collDealerContacts[] = $obj;
                        }
                    }
                }

                $this->collDealerContacts = $collDealerContacts;
                $this->collDealerContactsPartial = false;
            }
        }

        return $this->collDealerContacts;
    }

    /**
     * Sets a collection of DealerContact objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $dealerContacts A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildDealer The current object (for fluent API support)
     */
    public function setDealerContacts(Collection $dealerContacts, ConnectionInterface $con = null)
    {
        $dealerContactsToDelete = $this->getDealerContacts(new Criteria(), $con)->diff($dealerContacts);


        $this->dealerContactsScheduledForDeletion = $dealerContactsToDelete;

        foreach ($dealerContactsToDelete as $dealerContactRemoved) {
            $dealerContactRemoved->setDealer(null);
        }

        $this->collDealerContacts = null;
        foreach ($dealerContacts as $dealerContact) {
            $this->addDealerContact($dealerContact);
        }

        $this->collDealerContacts = $dealerContacts;
        $this->collDealerContactsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related DealerContact objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related DealerContact objects.
     * @throws PropelException
     */
    public function countDealerContacts(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collDealerContactsPartial && !$this->isNew();
        if (null === $this->collDealerContacts || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collDealerContacts) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getDealerContacts());
            }

            $query = ChildDealerContactQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByDealer($this)
                ->count($con);
        }

        return count($this->collDealerContacts);
    }

    /**
     * Method called to associate a ChildDealerContact object to this object
     * through the ChildDealerContact foreign key attribute.
     *
     * @param    ChildDealerContact $l ChildDealerContact
     * @return   \Dealer\Model\Dealer The current object (for fluent API support)
     */
    public function addDealerContact(ChildDealerContact $l)
    {
        if ($this->collDealerContacts === null) {
            $this->initDealerContacts();
            $this->collDealerContactsPartial = true;
        }

        if (!in_array($l, $this->collDealerContacts->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddDealerContact($l);
        }

        return $this;
    }

    /**
     * @param DealerContact $dealerContact The dealerContact object to add.
     */
    protected function doAddDealerContact($dealerContact)
    {
        $this->collDealerContacts[]= $dealerContact;
        $dealerContact->setDealer($this);
    }

    /**
     * @param  DealerContact $dealerContact The dealerContact object to remove.
     * @return ChildDealer The current object (for fluent API support)
     */
    public function removeDealerContact($dealerContact)
    {
        if ($this->getDealerContacts()->contains($dealerContact)) {
            $this->collDealerContacts->remove($this->collDealerContacts->search($dealerContact));
            if (null === $this->dealerContactsScheduledForDeletion) {
                $this->dealerContactsScheduledForDeletion = clone $this->collDealerContacts;
                $this->dealerContactsScheduledForDeletion->clear();
            }
            $this->dealerContactsScheduledForDeletion[]= clone $dealerContact;
            $dealerContact->setDealer(null);
        }

        return $this;
    }

    /**
     * Clears out the collDealerContactInfos collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addDealerContactInfos()
     */
    public function clearDealerContactInfos()
    {
        $this->collDealerContactInfos = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collDealerContactInfos collection loaded partially.
     */
    public function resetPartialDealerContactInfos($v = true)
    {
        $this->collDealerContactInfosPartial = $v;
    }

    /**
     * Initializes the collDealerContactInfos collection.
     *
     * By default this just sets the collDealerContactInfos collection to an empty array (like clearcollDealerContactInfos());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initDealerContactInfos($overrideExisting = true)
    {
        if (null !== $this->collDealerContactInfos && !$overrideExisting) {
            return;
        }
        $this->collDealerContactInfos = new ObjectCollection();
        $this->collDealerContactInfos->setModel('\Dealer\Model\DealerContactInfo');
    }

    /**
     * Gets an array of ChildDealerContactInfo objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildDealer is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildDealerContactInfo[] List of ChildDealerContactInfo objects
     * @throws PropelException
     */
    public function getDealerContactInfos($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collDealerContactInfosPartial && !$this->isNew();
        if (null === $this->collDealerContactInfos || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collDealerContactInfos) {
                // return empty collection
                $this->initDealerContactInfos();
            } else {
                $collDealerContactInfos = ChildDealerContactInfoQuery::create(null, $criteria)
                    ->filterByDealer($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collDealerContactInfosPartial && count($collDealerContactInfos)) {
                        $this->initDealerContactInfos(false);

                        foreach ($collDealerContactInfos as $obj) {
                            if (false == $this->collDealerContactInfos->contains($obj)) {
                                $this->collDealerContactInfos->append($obj);
                            }
                        }

                        $this->collDealerContactInfosPartial = true;
                    }

                    reset($collDealerContactInfos);

                    return $collDealerContactInfos;
                }

                if ($partial && $this->collDealerContactInfos) {
                    foreach ($this->collDealerContactInfos as $obj) {
                        if ($obj->isNew()) {
                            $collDealerContactInfos[] = $obj;
                        }
                    }
                }

                $this->collDealerContactInfos = $collDealerContactInfos;
                $this->collDealerContactInfosPartial = false;
            }
        }

        return $this->collDealerContactInfos;
    }

    /**
     * Sets a collection of DealerContactInfo objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $dealerContactInfos A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildDealer The current object (for fluent API support)
     */
    public function setDealerContactInfos(Collection $dealerContactInfos, ConnectionInterface $con = null)
    {
        $dealerContactInfosToDelete = $this->getDealerContactInfos(new Criteria(), $con)->diff($dealerContactInfos);


        $this->dealerContactInfosScheduledForDeletion = $dealerContactInfosToDelete;

        foreach ($dealerContactInfosToDelete as $dealerContactInfoRemoved) {
            $dealerContactInfoRemoved->setDealer(null);
        }

        $this->collDealerContactInfos = null;
        foreach ($dealerContactInfos as $dealerContactInfo) {
            $this->addDealerContactInfo($dealerContactInfo);
        }

        $this->collDealerContactInfos = $dealerContactInfos;
        $this->collDealerContactInfosPartial = false;

        return $this;
    }

    /**
     * Returns the number of related DealerContactInfo objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related DealerContactInfo objects.
     * @throws PropelException
     */
    public function countDealerContactInfos(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collDealerContactInfosPartial && !$this->isNew();
        if (null === $this->collDealerContactInfos || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collDealerContactInfos) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getDealerContactInfos());
            }

            $query = ChildDealerContactInfoQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByDealer($this)
                ->count($con);
        }

        return count($this->collDealerContactInfos);
    }

    /**
     * Method called to associate a ChildDealerContactInfo object to this object
     * through the ChildDealerContactInfo foreign key attribute.
     *
     * @param    ChildDealerContactInfo $l ChildDealerContactInfo
     * @return   \Dealer\Model\Dealer The current object (for fluent API support)
     */
    public function addDealerContactInfo(ChildDealerContactInfo $l)
    {
        if ($this->collDealerContactInfos === null) {
            $this->initDealerContactInfos();
            $this->collDealerContactInfosPartial = true;
        }

        if (!in_array($l, $this->collDealerContactInfos->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddDealerContactInfo($l);
        }

        return $this;
    }

    /**
     * @param DealerContactInfo $dealerContactInfo The dealerContactInfo object to add.
     */
    protected function doAddDealerContactInfo($dealerContactInfo)
    {
        $this->collDealerContactInfos[]= $dealerContactInfo;
        $dealerContactInfo->setDealer($this);
    }

    /**
     * @param  DealerContactInfo $dealerContactInfo The dealerContactInfo object to remove.
     * @return ChildDealer The current object (for fluent API support)
     */
    public function removeDealerContactInfo($dealerContactInfo)
    {
        if ($this->getDealerContactInfos()->contains($dealerContactInfo)) {
            $this->collDealerContactInfos->remove($this->collDealerContactInfos->search($dealerContactInfo));
            if (null === $this->dealerContactInfosScheduledForDeletion) {
                $this->dealerContactInfosScheduledForDeletion = clone $this->collDealerContactInfos;
                $this->dealerContactInfosScheduledForDeletion->clear();
            }
            $this->dealerContactInfosScheduledForDeletion[]= clone $dealerContactInfo;
            $dealerContactInfo->setDealer(null);
        }

        return $this;
    }

    /**
     * Clears out the collDealerI18ns collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addDealerI18ns()
     */
    public function clearDealerI18ns()
    {
        $this->collDealerI18ns = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collDealerI18ns collection loaded partially.
     */
    public function resetPartialDealerI18ns($v = true)
    {
        $this->collDealerI18nsPartial = $v;
    }

    /**
     * Initializes the collDealerI18ns collection.
     *
     * By default this just sets the collDealerI18ns collection to an empty array (like clearcollDealerI18ns());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initDealerI18ns($overrideExisting = true)
    {
        if (null !== $this->collDealerI18ns && !$overrideExisting) {
            return;
        }
        $this->collDealerI18ns = new ObjectCollection();
        $this->collDealerI18ns->setModel('\Dealer\Model\DealerI18n');
    }

    /**
     * Gets an array of ChildDealerI18n objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildDealer is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildDealerI18n[] List of ChildDealerI18n objects
     * @throws PropelException
     */
    public function getDealerI18ns($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collDealerI18nsPartial && !$this->isNew();
        if (null === $this->collDealerI18ns || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collDealerI18ns) {
                // return empty collection
                $this->initDealerI18ns();
            } else {
                $collDealerI18ns = ChildDealerI18nQuery::create(null, $criteria)
                    ->filterByDealer($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collDealerI18nsPartial && count($collDealerI18ns)) {
                        $this->initDealerI18ns(false);

                        foreach ($collDealerI18ns as $obj) {
                            if (false == $this->collDealerI18ns->contains($obj)) {
                                $this->collDealerI18ns->append($obj);
                            }
                        }

                        $this->collDealerI18nsPartial = true;
                    }

                    reset($collDealerI18ns);

                    return $collDealerI18ns;
                }

                if ($partial && $this->collDealerI18ns) {
                    foreach ($this->collDealerI18ns as $obj) {
                        if ($obj->isNew()) {
                            $collDealerI18ns[] = $obj;
                        }
                    }
                }

                $this->collDealerI18ns = $collDealerI18ns;
                $this->collDealerI18nsPartial = false;
            }
        }

        return $this->collDealerI18ns;
    }

    /**
     * Sets a collection of DealerI18n objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $dealerI18ns A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildDealer The current object (for fluent API support)
     */
    public function setDealerI18ns(Collection $dealerI18ns, ConnectionInterface $con = null)
    {
        $dealerI18nsToDelete = $this->getDealerI18ns(new Criteria(), $con)->diff($dealerI18ns);


        //since at least one column in the foreign key is at the same time a PK
        //we can not just set a PK to NULL in the lines below. We have to store
        //a backup of all values, so we are able to manipulate these items based on the onDelete value later.
        $this->dealerI18nsScheduledForDeletion = clone $dealerI18nsToDelete;

        foreach ($dealerI18nsToDelete as $dealerI18nRemoved) {
            $dealerI18nRemoved->setDealer(null);
        }

        $this->collDealerI18ns = null;
        foreach ($dealerI18ns as $dealerI18n) {
            $this->addDealerI18n($dealerI18n);
        }

        $this->collDealerI18ns = $dealerI18ns;
        $this->collDealerI18nsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related DealerI18n objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related DealerI18n objects.
     * @throws PropelException
     */
    public function countDealerI18ns(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collDealerI18nsPartial && !$this->isNew();
        if (null === $this->collDealerI18ns || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collDealerI18ns) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getDealerI18ns());
            }

            $query = ChildDealerI18nQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByDealer($this)
                ->count($con);
        }

        return count($this->collDealerI18ns);
    }

    /**
     * Method called to associate a ChildDealerI18n object to this object
     * through the ChildDealerI18n foreign key attribute.
     *
     * @param    ChildDealerI18n $l ChildDealerI18n
     * @return   \Dealer\Model\Dealer The current object (for fluent API support)
     */
    public function addDealerI18n(ChildDealerI18n $l)
    {
        if ($l && $locale = $l->getLocale()) {
            $this->setLocale($locale);
            $this->currentTranslations[$locale] = $l;
        }
        if ($this->collDealerI18ns === null) {
            $this->initDealerI18ns();
            $this->collDealerI18nsPartial = true;
        }

        if (!in_array($l, $this->collDealerI18ns->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddDealerI18n($l);
        }

        return $this;
    }

    /**
     * @param DealerI18n $dealerI18n The dealerI18n object to add.
     */
    protected function doAddDealerI18n($dealerI18n)
    {
        $this->collDealerI18ns[]= $dealerI18n;
        $dealerI18n->setDealer($this);
    }

    /**
     * @param  DealerI18n $dealerI18n The dealerI18n object to remove.
     * @return ChildDealer The current object (for fluent API support)
     */
    public function removeDealerI18n($dealerI18n)
    {
        if ($this->getDealerI18ns()->contains($dealerI18n)) {
            $this->collDealerI18ns->remove($this->collDealerI18ns->search($dealerI18n));
            if (null === $this->dealerI18nsScheduledForDeletion) {
                $this->dealerI18nsScheduledForDeletion = clone $this->collDealerI18ns;
                $this->dealerI18nsScheduledForDeletion->clear();
            }
            $this->dealerI18nsScheduledForDeletion[]= clone $dealerI18n;
            $dealerI18n->setDealer(null);
        }

        return $this;
    }

    /**
     * Clears out the collDealerVersions collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addDealerVersions()
     */
    public function clearDealerVersions()
    {
        $this->collDealerVersions = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collDealerVersions collection loaded partially.
     */
    public function resetPartialDealerVersions($v = true)
    {
        $this->collDealerVersionsPartial = $v;
    }

    /**
     * Initializes the collDealerVersions collection.
     *
     * By default this just sets the collDealerVersions collection to an empty array (like clearcollDealerVersions());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initDealerVersions($overrideExisting = true)
    {
        if (null !== $this->collDealerVersions && !$overrideExisting) {
            return;
        }
        $this->collDealerVersions = new ObjectCollection();
        $this->collDealerVersions->setModel('\Dealer\Model\DealerVersion');
    }

    /**
     * Gets an array of ChildDealerVersion objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildDealer is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildDealerVersion[] List of ChildDealerVersion objects
     * @throws PropelException
     */
    public function getDealerVersions($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collDealerVersionsPartial && !$this->isNew();
        if (null === $this->collDealerVersions || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collDealerVersions) {
                // return empty collection
                $this->initDealerVersions();
            } else {
                $collDealerVersions = ChildDealerVersionQuery::create(null, $criteria)
                    ->filterByDealer($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collDealerVersionsPartial && count($collDealerVersions)) {
                        $this->initDealerVersions(false);

                        foreach ($collDealerVersions as $obj) {
                            if (false == $this->collDealerVersions->contains($obj)) {
                                $this->collDealerVersions->append($obj);
                            }
                        }

                        $this->collDealerVersionsPartial = true;
                    }

                    reset($collDealerVersions);

                    return $collDealerVersions;
                }

                if ($partial && $this->collDealerVersions) {
                    foreach ($this->collDealerVersions as $obj) {
                        if ($obj->isNew()) {
                            $collDealerVersions[] = $obj;
                        }
                    }
                }

                $this->collDealerVersions = $collDealerVersions;
                $this->collDealerVersionsPartial = false;
            }
        }

        return $this->collDealerVersions;
    }

    /**
     * Sets a collection of DealerVersion objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $dealerVersions A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildDealer The current object (for fluent API support)
     */
    public function setDealerVersions(Collection $dealerVersions, ConnectionInterface $con = null)
    {
        $dealerVersionsToDelete = $this->getDealerVersions(new Criteria(), $con)->diff($dealerVersions);


        //since at least one column in the foreign key is at the same time a PK
        //we can not just set a PK to NULL in the lines below. We have to store
        //a backup of all values, so we are able to manipulate these items based on the onDelete value later.
        $this->dealerVersionsScheduledForDeletion = clone $dealerVersionsToDelete;

        foreach ($dealerVersionsToDelete as $dealerVersionRemoved) {
            $dealerVersionRemoved->setDealer(null);
        }

        $this->collDealerVersions = null;
        foreach ($dealerVersions as $dealerVersion) {
            $this->addDealerVersion($dealerVersion);
        }

        $this->collDealerVersions = $dealerVersions;
        $this->collDealerVersionsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related DealerVersion objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related DealerVersion objects.
     * @throws PropelException
     */
    public function countDealerVersions(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collDealerVersionsPartial && !$this->isNew();
        if (null === $this->collDealerVersions || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collDealerVersions) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getDealerVersions());
            }

            $query = ChildDealerVersionQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByDealer($this)
                ->count($con);
        }

        return count($this->collDealerVersions);
    }

    /**
     * Method called to associate a ChildDealerVersion object to this object
     * through the ChildDealerVersion foreign key attribute.
     *
     * @param    ChildDealerVersion $l ChildDealerVersion
     * @return   \Dealer\Model\Dealer The current object (for fluent API support)
     */
    public function addDealerVersion(ChildDealerVersion $l)
    {
        if ($this->collDealerVersions === null) {
            $this->initDealerVersions();
            $this->collDealerVersionsPartial = true;
        }

        if (!in_array($l, $this->collDealerVersions->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddDealerVersion($l);
        }

        return $this;
    }

    /**
     * @param DealerVersion $dealerVersion The dealerVersion object to add.
     */
    protected function doAddDealerVersion($dealerVersion)
    {
        $this->collDealerVersions[]= $dealerVersion;
        $dealerVersion->setDealer($this);
    }

    /**
     * @param  DealerVersion $dealerVersion The dealerVersion object to remove.
     * @return ChildDealer The current object (for fluent API support)
     */
    public function removeDealerVersion($dealerVersion)
    {
        if ($this->getDealerVersions()->contains($dealerVersion)) {
            $this->collDealerVersions->remove($this->collDealerVersions->search($dealerVersion));
            if (null === $this->dealerVersionsScheduledForDeletion) {
                $this->dealerVersionsScheduledForDeletion = clone $this->collDealerVersions;
                $this->dealerVersionsScheduledForDeletion->clear();
            }
            $this->dealerVersionsScheduledForDeletion[]= clone $dealerVersion;
            $dealerVersion->setDealer(null);
        }

        return $this;
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
            if ($this->collDealerSheduless) {
                foreach ($this->collDealerSheduless as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collDealerContacts) {
                foreach ($this->collDealerContacts as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collDealerContactInfos) {
                foreach ($this->collDealerContactInfos as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collDealerI18ns) {
                foreach ($this->collDealerI18ns as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collDealerVersions) {
                foreach ($this->collDealerVersions as $o) {
                    $o->clearAllReferences($deep);
                }
            }
        } // if ($deep)

        // i18n behavior
        $this->currentLocale = 'en_US';
        $this->currentTranslations = null;

        $this->collDealerSheduless = null;
        $this->collDealerContacts = null;
        $this->collDealerContactInfos = null;
        $this->collDealerI18ns = null;
        $this->collDealerVersions = null;
        $this->aCountry = null;
    }

    /**
     * Return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(DealerTableMap::DEFAULT_STRING_FORMAT);
    }

    // timestampable behavior

    /**
     * Mark the current object so that the update date doesn't get updated during next save
     *
     * @return     ChildDealer The current object (for fluent API support)
     */
    public function keepUpdateDateUnchanged()
    {
        $this->modifiedColumns[DealerTableMap::UPDATED_AT] = true;

        return $this;
    }

    // i18n behavior

    /**
     * Sets the locale for translations
     *
     * @param     string $locale Locale to use for the translation, e.g. 'fr_FR'
     *
     * @return    ChildDealer The current object (for fluent API support)
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
     * @return ChildDealerI18n */
    public function getTranslation($locale = 'en_US', ConnectionInterface $con = null)
    {
        if (!isset($this->currentTranslations[$locale])) {
            if (null !== $this->collDealerI18ns) {
                foreach ($this->collDealerI18ns as $translation) {
                    if ($translation->getLocale() == $locale) {
                        $this->currentTranslations[$locale] = $translation;

                        return $translation;
                    }
                }
            }
            if ($this->isNew()) {
                $translation = new ChildDealerI18n();
                $translation->setLocale($locale);
            } else {
                $translation = ChildDealerI18nQuery::create()
                    ->filterByPrimaryKey(array($this->getPrimaryKey(), $locale))
                    ->findOneOrCreate($con);
                $this->currentTranslations[$locale] = $translation;
            }
            $this->addDealerI18n($translation);
        }

        return $this->currentTranslations[$locale];
    }

    /**
     * Remove the translation for a given locale
     *
     * @param     string $locale Locale to use for the translation, e.g. 'fr_FR'
     * @param     ConnectionInterface $con an optional connection object
     *
     * @return    ChildDealer The current object (for fluent API support)
     */
    public function removeTranslation($locale = 'en_US', ConnectionInterface $con = null)
    {
        if (!$this->isNew()) {
            ChildDealerI18nQuery::create()
                ->filterByPrimaryKey(array($this->getPrimaryKey(), $locale))
                ->delete($con);
        }
        if (isset($this->currentTranslations[$locale])) {
            unset($this->currentTranslations[$locale]);
        }
        foreach ($this->collDealerI18ns as $key => $translation) {
            if ($translation->getLocale() == $locale) {
                unset($this->collDealerI18ns[$key]);
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
     * @return ChildDealerI18n */
    public function getCurrentTranslation(ConnectionInterface $con = null)
    {
        return $this->getTranslation($this->getLocale(), $con);
    }


        /**
         * Get the [title] column value.
         *
         * @return   string
         */
        public function getTitle()
        {
        return $this->getCurrentTranslation()->getTitle();
    }


        /**
         * Set the value of [title] column.
         *
         * @param      string $v new value
         * @return   \Dealer\Model\DealerI18n The current object (for fluent API support)
         */
        public function setTitle($v)
        {    $this->getCurrentTranslation()->setTitle($v);

        return $this;
    }


        /**
         * Get the [description] column value.
         *
         * @return   string
         */
        public function getDescription()
        {
        return $this->getCurrentTranslation()->getDescription();
    }


        /**
         * Set the value of [description] column.
         *
         * @param      string $v new value
         * @return   \Dealer\Model\DealerI18n The current object (for fluent API support)
         */
        public function setDescription($v)
        {    $this->getCurrentTranslation()->setDescription($v);

        return $this;
    }

    // versionable behavior

    /**
     * Enforce a new Version of this object upon next save.
     *
     * @return \Dealer\Model\Dealer
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

        if (ChildDealerQuery::isVersioningEnabled() && ($this->isNew() || $this->isModified()) || $this->isDeleted()) {
            return true;
        }
        // to avoid infinite loops, emulate in save
        $this->alreadyInSave = true;
        foreach ($this->getDealerSheduless(null, $con) as $relatedObject) {
            if ($relatedObject->isVersioningNecessary($con)) {
                $this->alreadyInSave = false;

                return true;
            }
        }
        $this->alreadyInSave = false;

        // to avoid infinite loops, emulate in save
        $this->alreadyInSave = true;
        foreach ($this->getDealerContacts(null, $con) as $relatedObject) {
            if ($relatedObject->isVersioningNecessary($con)) {
                $this->alreadyInSave = false;

                return true;
            }
        }
        $this->alreadyInSave = false;

        // to avoid infinite loops, emulate in save
        $this->alreadyInSave = true;
        foreach ($this->getDealerContactInfos(null, $con) as $relatedObject) {
            if ($relatedObject->isVersioningNecessary($con)) {
                $this->alreadyInSave = false;

                return true;
            }
        }
        $this->alreadyInSave = false;


        return false;
    }

    /**
     * Creates a version of the current object and saves it.
     *
     * @param   ConnectionInterface $con the connection to use
     *
     * @return  ChildDealerVersion A version object
     */
    public function addVersion($con = null)
    {
        $this->enforceVersion = false;

        $version = new ChildDealerVersion();
        $version->setId($this->getId());
        $version->setAddress1($this->getAddress1());
        $version->setAddress2($this->getAddress2());
        $version->setAddress3($this->getAddress3());
        $version->setZipcode($this->getZipcode());
        $version->setCity($this->getCity());
        $version->setCountryId($this->getCountryId());
        $version->setLatitude($this->getLatitude());
        $version->setLongitude($this->getLongitude());
        $version->setCreatedAt($this->getCreatedAt());
        $version->setUpdatedAt($this->getUpdatedAt());
        $version->setVersion($this->getVersion());
        $version->setVersionCreatedAt($this->getVersionCreatedAt());
        $version->setVersionCreatedBy($this->getVersionCreatedBy());
        $version->setDealer($this);
        if ($relateds = $this->getDealerSheduless($con)->toKeyValue('Id', 'Version')) {
            $version->setDealerShedulesIds(array_keys($relateds));
            $version->setDealerShedulesVersions(array_values($relateds));
        }
        if ($relateds = $this->getDealerContacts($con)->toKeyValue('Id', 'Version')) {
            $version->setDealerContactIds(array_keys($relateds));
            $version->setDealerContactVersions(array_values($relateds));
        }
        if ($relateds = $this->getDealerContactInfos($con)->toKeyValue('Id', 'Version')) {
            $version->setDealerContactInfoIds(array_keys($relateds));
            $version->setDealerContactInfoVersions(array_values($relateds));
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
     * @return  ChildDealer The current object (for fluent API support)
     */
    public function toVersion($versionNumber, $con = null)
    {
        $version = $this->getOneVersion($versionNumber, $con);
        if (!$version) {
            throw new PropelException(sprintf('No ChildDealer object found with version %d', $version));
        }
        $this->populateFromVersion($version, $con);

        return $this;
    }

    /**
     * Sets the properties of the current object to the value they had at a specific version
     *
     * @param ChildDealerVersion $version The version object to use
     * @param ConnectionInterface   $con the connection to use
     * @param array                 $loadedObjects objects that been loaded in a chain of populateFromVersion calls on referrer or fk objects.
     *
     * @return ChildDealer The current object (for fluent API support)
     */
    public function populateFromVersion($version, $con = null, &$loadedObjects = array())
    {
        $loadedObjects['ChildDealer'][$version->getId()][$version->getVersion()] = $this;
        $this->setId($version->getId());
        $this->setAddress1($version->getAddress1());
        $this->setAddress2($version->getAddress2());
        $this->setAddress3($version->getAddress3());
        $this->setZipcode($version->getZipcode());
        $this->setCity($version->getCity());
        $this->setCountryId($version->getCountryId());
        $this->setLatitude($version->getLatitude());
        $this->setLongitude($version->getLongitude());
        $this->setCreatedAt($version->getCreatedAt());
        $this->setUpdatedAt($version->getUpdatedAt());
        $this->setVersion($version->getVersion());
        $this->setVersionCreatedAt($version->getVersionCreatedAt());
        $this->setVersionCreatedBy($version->getVersionCreatedBy());
        if ($fkValues = $version->getDealerShedulesIds()) {
            $this->clearDealerSheduless();
            $fkVersions = $version->getDealerShedulesVersions();
            $query = ChildDealerShedulesVersionQuery::create();
            foreach ($fkValues as $key => $value) {
                $c1 = $query->getNewCriterion(DealerShedulesVersionTableMap::ID, $value);
                $c2 = $query->getNewCriterion(DealerShedulesVersionTableMap::VERSION, $fkVersions[$key]);
                $c1->addAnd($c2);
                $query->addOr($c1);
            }
            foreach ($query->find($con) as $relatedVersion) {
                if (isset($loadedObjects['ChildDealerShedules']) && isset($loadedObjects['ChildDealerShedules'][$relatedVersion->getId()]) && isset($loadedObjects['ChildDealerShedules'][$relatedVersion->getId()][$relatedVersion->getVersion()])) {
                    $related = $loadedObjects['ChildDealerShedules'][$relatedVersion->getId()][$relatedVersion->getVersion()];
                } else {
                    $related = new ChildDealerShedules();
                    $related->populateFromVersion($relatedVersion, $con, $loadedObjects);
                    $related->setNew(false);
                }
                $this->addDealerShedules($related);
                $this->collDealerShedulessPartial = false;
            }
        }
        if ($fkValues = $version->getDealerContactIds()) {
            $this->clearDealerContact();
            $fkVersions = $version->getDealerContactVersions();
            $query = ChildDealerContactVersionQuery::create();
            foreach ($fkValues as $key => $value) {
                $c1 = $query->getNewCriterion(DealerContactVersionTableMap::ID, $value);
                $c2 = $query->getNewCriterion(DealerContactVersionTableMap::VERSION, $fkVersions[$key]);
                $c1->addAnd($c2);
                $query->addOr($c1);
            }
            foreach ($query->find($con) as $relatedVersion) {
                if (isset($loadedObjects['ChildDealerContact']) && isset($loadedObjects['ChildDealerContact'][$relatedVersion->getId()]) && isset($loadedObjects['ChildDealerContact'][$relatedVersion->getId()][$relatedVersion->getVersion()])) {
                    $related = $loadedObjects['ChildDealerContact'][$relatedVersion->getId()][$relatedVersion->getVersion()];
                } else {
                    $related = new ChildDealerContact();
                    $related->populateFromVersion($relatedVersion, $con, $loadedObjects);
                    $related->setNew(false);
                }
                $this->addDealerContact($related);
                $this->collDealerContactPartial = false;
            }
        }
        if ($fkValues = $version->getDealerContactInfoIds()) {
            $this->clearDealerContactInfo();
            $fkVersions = $version->getDealerContactInfoVersions();
            $query = ChildDealerContactInfoVersionQuery::create();
            foreach ($fkValues as $key => $value) {
                $c1 = $query->getNewCriterion(DealerContactInfoVersionTableMap::ID, $value);
                $c2 = $query->getNewCriterion(DealerContactInfoVersionTableMap::VERSION, $fkVersions[$key]);
                $c1->addAnd($c2);
                $query->addOr($c1);
            }
            foreach ($query->find($con) as $relatedVersion) {
                if (isset($loadedObjects['ChildDealerContactInfo']) && isset($loadedObjects['ChildDealerContactInfo'][$relatedVersion->getId()]) && isset($loadedObjects['ChildDealerContactInfo'][$relatedVersion->getId()][$relatedVersion->getVersion()])) {
                    $related = $loadedObjects['ChildDealerContactInfo'][$relatedVersion->getId()][$relatedVersion->getVersion()];
                } else {
                    $related = new ChildDealerContactInfo();
                    $related->populateFromVersion($relatedVersion, $con, $loadedObjects);
                    $related->setNew(false);
                }
                $this->addDealerContactInfo($related);
                $this->collDealerContactInfoPartial = false;
            }
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
        $v = ChildDealerVersionQuery::create()
            ->filterByDealer($this)
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
     * @return  ChildDealerVersion A version object
     */
    public function getOneVersion($versionNumber, $con = null)
    {
        return ChildDealerVersionQuery::create()
            ->filterByDealer($this)
            ->filterByVersion($versionNumber)
            ->findOne($con);
    }

    /**
     * Gets all the versions of this object, in incremental order
     *
     * @param   ConnectionInterface $con the connection to use
     *
     * @return  ObjectCollection A list of ChildDealerVersion objects
     */
    public function getAllVersions($con = null)
    {
        $criteria = new Criteria();
        $criteria->addAscendingOrderByColumn(DealerVersionTableMap::VERSION);

        return $this->getDealerVersions($criteria, $con);
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
     * @return PropelCollection|array \Dealer\Model\DealerVersion[] List of \Dealer\Model\DealerVersion objects
     */
    public function getLastVersions($number = 10, $criteria = null, $con = null)
    {
        $criteria = ChildDealerVersionQuery::create(null, $criteria);
        $criteria->addDescendingOrderByColumn(DealerVersionTableMap::VERSION);
        $criteria->limit($number);

        return $this->getDealerVersions($criteria, $con);
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
