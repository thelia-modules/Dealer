<?php

namespace Dealer\Model\Base;

use \DateTime;
use \Exception;
use \PDO;
use Dealer\Model\Dealer as ChildDealer;
use Dealer\Model\DealerAdmin as ChildDealerAdmin;
use Dealer\Model\DealerAdminQuery as ChildDealerAdminQuery;
use Dealer\Model\DealerBrand as ChildDealerBrand;
use Dealer\Model\DealerBrandQuery as ChildDealerBrandQuery;
use Dealer\Model\DealerContact as ChildDealerContact;
use Dealer\Model\DealerContactQuery as ChildDealerContactQuery;
use Dealer\Model\DealerContent as ChildDealerContent;
use Dealer\Model\DealerContentQuery as ChildDealerContentQuery;
use Dealer\Model\DealerFolder as ChildDealerFolder;
use Dealer\Model\DealerFolderQuery as ChildDealerFolderQuery;
use Dealer\Model\DealerI18n as ChildDealerI18n;
use Dealer\Model\DealerI18nQuery as ChildDealerI18nQuery;
use Dealer\Model\DealerProduct as ChildDealerProduct;
use Dealer\Model\DealerProductQuery as ChildDealerProductQuery;
use Dealer\Model\DealerQuery as ChildDealerQuery;
use Dealer\Model\DealerShedules as ChildDealerShedules;
use Dealer\Model\DealerShedulesQuery as ChildDealerShedulesQuery;
use Dealer\Model\Map\DealerTableMap;
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
     * The value for the visible field.
     * Note: this column has a database default value of: 0
     * @var        int
     */
    protected $visible;

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
     * @var        ObjectCollection|ChildDealerContent[] Collection to store aggregation of ChildDealerContent objects.
     */
    protected $collDealerContents;
    protected $collDealerContentsPartial;

    /**
     * @var        ObjectCollection|ChildDealerFolder[] Collection to store aggregation of ChildDealerFolder objects.
     */
    protected $collDealerFolders;
    protected $collDealerFoldersPartial;

    /**
     * @var        ObjectCollection|ChildDealerBrand[] Collection to store aggregation of ChildDealerBrand objects.
     */
    protected $collDealerBrands;
    protected $collDealerBrandsPartial;

    /**
     * @var        ObjectCollection|ChildDealerProduct[] Collection to store aggregation of ChildDealerProduct objects.
     */
    protected $collDealerProducts;
    protected $collDealerProductsPartial;

    /**
     * @var        ObjectCollection|ChildDealerAdmin[] Collection to store aggregation of ChildDealerAdmin objects.
     */
    protected $collDealerAdmins;
    protected $collDealerAdminsPartial;

    /**
     * @var        ObjectCollection|ChildDealerI18n[] Collection to store aggregation of ChildDealerI18n objects.
     */
    protected $collDealerI18ns;
    protected $collDealerI18nsPartial;

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
    protected $dealerContentsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $dealerFoldersScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $dealerBrandsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $dealerProductsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $dealerAdminsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection
     */
    protected $dealerI18nsScheduledForDeletion = null;

    /**
     * Applies default values to this object.
     * This method should be called from the object's constructor (or
     * equivalent initialization method).
     * @see __construct()
     */
    public function applyDefaultValues()
    {
        $this->visible = 0;
        $this->latitude = '0';
        $this->longitude = '0';
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
     * Get the [visible] column value.
     *
     * @return   int
     */
    public function getVisible()
    {

        return $this->visible;
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
     * Set the value of [visible] column.
     *
     * @param      int $v new value
     * @return   \Dealer\Model\Dealer The current object (for fluent API support)
     */
    public function setVisible($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->visible !== $v) {
            $this->visible = $v;
            $this->modifiedColumns[DealerTableMap::VISIBLE] = true;
        }


        return $this;
    } // setVisible()

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
     * Indicates whether the columns in this object are only set to default values.
     *
     * This method can be used in conjunction with isModified() to indicate whether an object is both
     * modified _and_ has some values set which are non-default.
     *
     * @return boolean Whether the columns in this object are only been set with default values.
     */
    public function hasOnlyDefaultValues()
    {
            if ($this->visible !== 0) {
                return false;
            }

            if ($this->latitude !== '0') {
                return false;
            }

            if ($this->longitude !== '0') {
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

            $col = $row[TableMap::TYPE_NUM == $indexType ? 1 + $startcol : DealerTableMap::translateFieldName('Visible', TableMap::TYPE_PHPNAME, $indexType)];
            $this->visible = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 2 + $startcol : DealerTableMap::translateFieldName('Address1', TableMap::TYPE_PHPNAME, $indexType)];
            $this->address1 = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 3 + $startcol : DealerTableMap::translateFieldName('Address2', TableMap::TYPE_PHPNAME, $indexType)];
            $this->address2 = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 4 + $startcol : DealerTableMap::translateFieldName('Address3', TableMap::TYPE_PHPNAME, $indexType)];
            $this->address3 = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 5 + $startcol : DealerTableMap::translateFieldName('Zipcode', TableMap::TYPE_PHPNAME, $indexType)];
            $this->zipcode = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 6 + $startcol : DealerTableMap::translateFieldName('City', TableMap::TYPE_PHPNAME, $indexType)];
            $this->city = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 7 + $startcol : DealerTableMap::translateFieldName('CountryId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->country_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 8 + $startcol : DealerTableMap::translateFieldName('Latitude', TableMap::TYPE_PHPNAME, $indexType)];
            $this->latitude = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 9 + $startcol : DealerTableMap::translateFieldName('Longitude', TableMap::TYPE_PHPNAME, $indexType)];
            $this->longitude = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 10 + $startcol : DealerTableMap::translateFieldName('CreatedAt', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00 00:00:00') {
                $col = null;
            }
            $this->created_at = (null !== $col) ? PropelDateTime::newInstance($col, null, '\DateTime') : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 11 + $startcol : DealerTableMap::translateFieldName('UpdatedAt', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00 00:00:00') {
                $col = null;
            }
            $this->updated_at = (null !== $col) ? PropelDateTime::newInstance($col, null, '\DateTime') : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }

            return $startcol + 12; // 12 = DealerTableMap::NUM_HYDRATE_COLUMNS.

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

            $this->collDealerContents = null;

            $this->collDealerFolders = null;

            $this->collDealerBrands = null;

            $this->collDealerProducts = null;

            $this->collDealerAdmins = null;

            $this->collDealerI18ns = null;

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

            if ($this->dealerContentsScheduledForDeletion !== null) {
                if (!$this->dealerContentsScheduledForDeletion->isEmpty()) {
                    \Dealer\Model\DealerContentQuery::create()
                        ->filterByPrimaryKeys($this->dealerContentsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->dealerContentsScheduledForDeletion = null;
                }
            }

                if ($this->collDealerContents !== null) {
            foreach ($this->collDealerContents as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->dealerFoldersScheduledForDeletion !== null) {
                if (!$this->dealerFoldersScheduledForDeletion->isEmpty()) {
                    \Dealer\Model\DealerFolderQuery::create()
                        ->filterByPrimaryKeys($this->dealerFoldersScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->dealerFoldersScheduledForDeletion = null;
                }
            }

                if ($this->collDealerFolders !== null) {
            foreach ($this->collDealerFolders as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->dealerBrandsScheduledForDeletion !== null) {
                if (!$this->dealerBrandsScheduledForDeletion->isEmpty()) {
                    \Dealer\Model\DealerBrandQuery::create()
                        ->filterByPrimaryKeys($this->dealerBrandsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->dealerBrandsScheduledForDeletion = null;
                }
            }

                if ($this->collDealerBrands !== null) {
            foreach ($this->collDealerBrands as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->dealerProductsScheduledForDeletion !== null) {
                if (!$this->dealerProductsScheduledForDeletion->isEmpty()) {
                    \Dealer\Model\DealerProductQuery::create()
                        ->filterByPrimaryKeys($this->dealerProductsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->dealerProductsScheduledForDeletion = null;
                }
            }

                if ($this->collDealerProducts !== null) {
            foreach ($this->collDealerProducts as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->dealerAdminsScheduledForDeletion !== null) {
                if (!$this->dealerAdminsScheduledForDeletion->isEmpty()) {
                    \Dealer\Model\DealerAdminQuery::create()
                        ->filterByPrimaryKeys($this->dealerAdminsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->dealerAdminsScheduledForDeletion = null;
                }
            }

                if ($this->collDealerAdmins !== null) {
            foreach ($this->collDealerAdmins as $referrerFK) {
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
        if ($this->isColumnModified(DealerTableMap::VISIBLE)) {
            $modifiedColumns[':p' . $index++]  = 'VISIBLE';
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
                    case 'VISIBLE':
                        $stmt->bindValue($identifier, $this->visible, PDO::PARAM_INT);
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
                return $this->getVisible();
                break;
            case 2:
                return $this->getAddress1();
                break;
            case 3:
                return $this->getAddress2();
                break;
            case 4:
                return $this->getAddress3();
                break;
            case 5:
                return $this->getZipcode();
                break;
            case 6:
                return $this->getCity();
                break;
            case 7:
                return $this->getCountryId();
                break;
            case 8:
                return $this->getLatitude();
                break;
            case 9:
                return $this->getLongitude();
                break;
            case 10:
                return $this->getCreatedAt();
                break;
            case 11:
                return $this->getUpdatedAt();
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
            $keys[1] => $this->getVisible(),
            $keys[2] => $this->getAddress1(),
            $keys[3] => $this->getAddress2(),
            $keys[4] => $this->getAddress3(),
            $keys[5] => $this->getZipcode(),
            $keys[6] => $this->getCity(),
            $keys[7] => $this->getCountryId(),
            $keys[8] => $this->getLatitude(),
            $keys[9] => $this->getLongitude(),
            $keys[10] => $this->getCreatedAt(),
            $keys[11] => $this->getUpdatedAt(),
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
            if (null !== $this->collDealerContents) {
                $result['DealerContents'] = $this->collDealerContents->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collDealerFolders) {
                $result['DealerFolders'] = $this->collDealerFolders->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collDealerBrands) {
                $result['DealerBrands'] = $this->collDealerBrands->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collDealerProducts) {
                $result['DealerProducts'] = $this->collDealerProducts->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collDealerAdmins) {
                $result['DealerAdmins'] = $this->collDealerAdmins->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collDealerI18ns) {
                $result['DealerI18ns'] = $this->collDealerI18ns->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
                $this->setVisible($value);
                break;
            case 2:
                $this->setAddress1($value);
                break;
            case 3:
                $this->setAddress2($value);
                break;
            case 4:
                $this->setAddress3($value);
                break;
            case 5:
                $this->setZipcode($value);
                break;
            case 6:
                $this->setCity($value);
                break;
            case 7:
                $this->setCountryId($value);
                break;
            case 8:
                $this->setLatitude($value);
                break;
            case 9:
                $this->setLongitude($value);
                break;
            case 10:
                $this->setCreatedAt($value);
                break;
            case 11:
                $this->setUpdatedAt($value);
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
        if (array_key_exists($keys[1], $arr)) $this->setVisible($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setAddress1($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setAddress2($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setAddress3($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setZipcode($arr[$keys[5]]);
        if (array_key_exists($keys[6], $arr)) $this->setCity($arr[$keys[6]]);
        if (array_key_exists($keys[7], $arr)) $this->setCountryId($arr[$keys[7]]);
        if (array_key_exists($keys[8], $arr)) $this->setLatitude($arr[$keys[8]]);
        if (array_key_exists($keys[9], $arr)) $this->setLongitude($arr[$keys[9]]);
        if (array_key_exists($keys[10], $arr)) $this->setCreatedAt($arr[$keys[10]]);
        if (array_key_exists($keys[11], $arr)) $this->setUpdatedAt($arr[$keys[11]]);
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
        if ($this->isColumnModified(DealerTableMap::VISIBLE)) $criteria->add(DealerTableMap::VISIBLE, $this->visible);
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
        $copyObj->setVisible($this->getVisible());
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

            foreach ($this->getDealerContents() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addDealerContent($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getDealerFolders() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addDealerFolder($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getDealerBrands() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addDealerBrand($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getDealerProducts() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addDealerProduct($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getDealerAdmins() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addDealerAdmin($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getDealerI18ns() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addDealerI18n($relObj->copy($deepCopy));
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
        if ('DealerContent' == $relationName) {
            return $this->initDealerContents();
        }
        if ('DealerFolder' == $relationName) {
            return $this->initDealerFolders();
        }
        if ('DealerBrand' == $relationName) {
            return $this->initDealerBrands();
        }
        if ('DealerProduct' == $relationName) {
            return $this->initDealerProducts();
        }
        if ('DealerAdmin' == $relationName) {
            return $this->initDealerAdmins();
        }
        if ('DealerI18n' == $relationName) {
            return $this->initDealerI18ns();
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
     * Clears out the collDealerContents collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addDealerContents()
     */
    public function clearDealerContents()
    {
        $this->collDealerContents = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collDealerContents collection loaded partially.
     */
    public function resetPartialDealerContents($v = true)
    {
        $this->collDealerContentsPartial = $v;
    }

    /**
     * Initializes the collDealerContents collection.
     *
     * By default this just sets the collDealerContents collection to an empty array (like clearcollDealerContents());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initDealerContents($overrideExisting = true)
    {
        if (null !== $this->collDealerContents && !$overrideExisting) {
            return;
        }
        $this->collDealerContents = new ObjectCollection();
        $this->collDealerContents->setModel('\Dealer\Model\DealerContent');
    }

    /**
     * Gets an array of ChildDealerContent objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildDealer is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildDealerContent[] List of ChildDealerContent objects
     * @throws PropelException
     */
    public function getDealerContents($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collDealerContentsPartial && !$this->isNew();
        if (null === $this->collDealerContents || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collDealerContents) {
                // return empty collection
                $this->initDealerContents();
            } else {
                $collDealerContents = ChildDealerContentQuery::create(null, $criteria)
                    ->filterByDealer($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collDealerContentsPartial && count($collDealerContents)) {
                        $this->initDealerContents(false);

                        foreach ($collDealerContents as $obj) {
                            if (false == $this->collDealerContents->contains($obj)) {
                                $this->collDealerContents->append($obj);
                            }
                        }

                        $this->collDealerContentsPartial = true;
                    }

                    reset($collDealerContents);

                    return $collDealerContents;
                }

                if ($partial && $this->collDealerContents) {
                    foreach ($this->collDealerContents as $obj) {
                        if ($obj->isNew()) {
                            $collDealerContents[] = $obj;
                        }
                    }
                }

                $this->collDealerContents = $collDealerContents;
                $this->collDealerContentsPartial = false;
            }
        }

        return $this->collDealerContents;
    }

    /**
     * Sets a collection of DealerContent objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $dealerContents A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildDealer The current object (for fluent API support)
     */
    public function setDealerContents(Collection $dealerContents, ConnectionInterface $con = null)
    {
        $dealerContentsToDelete = $this->getDealerContents(new Criteria(), $con)->diff($dealerContents);


        $this->dealerContentsScheduledForDeletion = $dealerContentsToDelete;

        foreach ($dealerContentsToDelete as $dealerContentRemoved) {
            $dealerContentRemoved->setDealer(null);
        }

        $this->collDealerContents = null;
        foreach ($dealerContents as $dealerContent) {
            $this->addDealerContent($dealerContent);
        }

        $this->collDealerContents = $dealerContents;
        $this->collDealerContentsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related DealerContent objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related DealerContent objects.
     * @throws PropelException
     */
    public function countDealerContents(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collDealerContentsPartial && !$this->isNew();
        if (null === $this->collDealerContents || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collDealerContents) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getDealerContents());
            }

            $query = ChildDealerContentQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByDealer($this)
                ->count($con);
        }

        return count($this->collDealerContents);
    }

    /**
     * Method called to associate a ChildDealerContent object to this object
     * through the ChildDealerContent foreign key attribute.
     *
     * @param    ChildDealerContent $l ChildDealerContent
     * @return   \Dealer\Model\Dealer The current object (for fluent API support)
     */
    public function addDealerContent(ChildDealerContent $l)
    {
        if ($this->collDealerContents === null) {
            $this->initDealerContents();
            $this->collDealerContentsPartial = true;
        }

        if (!in_array($l, $this->collDealerContents->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddDealerContent($l);
        }

        return $this;
    }

    /**
     * @param DealerContent $dealerContent The dealerContent object to add.
     */
    protected function doAddDealerContent($dealerContent)
    {
        $this->collDealerContents[]= $dealerContent;
        $dealerContent->setDealer($this);
    }

    /**
     * @param  DealerContent $dealerContent The dealerContent object to remove.
     * @return ChildDealer The current object (for fluent API support)
     */
    public function removeDealerContent($dealerContent)
    {
        if ($this->getDealerContents()->contains($dealerContent)) {
            $this->collDealerContents->remove($this->collDealerContents->search($dealerContent));
            if (null === $this->dealerContentsScheduledForDeletion) {
                $this->dealerContentsScheduledForDeletion = clone $this->collDealerContents;
                $this->dealerContentsScheduledForDeletion->clear();
            }
            $this->dealerContentsScheduledForDeletion[]= clone $dealerContent;
            $dealerContent->setDealer(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Dealer is new, it will return
     * an empty collection; or if this Dealer has previously
     * been saved, it will retrieve related DealerContents from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Dealer.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildDealerContent[] List of ChildDealerContent objects
     */
    public function getDealerContentsJoinContent($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildDealerContentQuery::create(null, $criteria);
        $query->joinWith('Content', $joinBehavior);

        return $this->getDealerContents($query, $con);
    }

    /**
     * Clears out the collDealerFolders collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addDealerFolders()
     */
    public function clearDealerFolders()
    {
        $this->collDealerFolders = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collDealerFolders collection loaded partially.
     */
    public function resetPartialDealerFolders($v = true)
    {
        $this->collDealerFoldersPartial = $v;
    }

    /**
     * Initializes the collDealerFolders collection.
     *
     * By default this just sets the collDealerFolders collection to an empty array (like clearcollDealerFolders());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initDealerFolders($overrideExisting = true)
    {
        if (null !== $this->collDealerFolders && !$overrideExisting) {
            return;
        }
        $this->collDealerFolders = new ObjectCollection();
        $this->collDealerFolders->setModel('\Dealer\Model\DealerFolder');
    }

    /**
     * Gets an array of ChildDealerFolder objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildDealer is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildDealerFolder[] List of ChildDealerFolder objects
     * @throws PropelException
     */
    public function getDealerFolders($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collDealerFoldersPartial && !$this->isNew();
        if (null === $this->collDealerFolders || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collDealerFolders) {
                // return empty collection
                $this->initDealerFolders();
            } else {
                $collDealerFolders = ChildDealerFolderQuery::create(null, $criteria)
                    ->filterByDealer($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collDealerFoldersPartial && count($collDealerFolders)) {
                        $this->initDealerFolders(false);

                        foreach ($collDealerFolders as $obj) {
                            if (false == $this->collDealerFolders->contains($obj)) {
                                $this->collDealerFolders->append($obj);
                            }
                        }

                        $this->collDealerFoldersPartial = true;
                    }

                    reset($collDealerFolders);

                    return $collDealerFolders;
                }

                if ($partial && $this->collDealerFolders) {
                    foreach ($this->collDealerFolders as $obj) {
                        if ($obj->isNew()) {
                            $collDealerFolders[] = $obj;
                        }
                    }
                }

                $this->collDealerFolders = $collDealerFolders;
                $this->collDealerFoldersPartial = false;
            }
        }

        return $this->collDealerFolders;
    }

    /**
     * Sets a collection of DealerFolder objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $dealerFolders A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildDealer The current object (for fluent API support)
     */
    public function setDealerFolders(Collection $dealerFolders, ConnectionInterface $con = null)
    {
        $dealerFoldersToDelete = $this->getDealerFolders(new Criteria(), $con)->diff($dealerFolders);


        $this->dealerFoldersScheduledForDeletion = $dealerFoldersToDelete;

        foreach ($dealerFoldersToDelete as $dealerFolderRemoved) {
            $dealerFolderRemoved->setDealer(null);
        }

        $this->collDealerFolders = null;
        foreach ($dealerFolders as $dealerFolder) {
            $this->addDealerFolder($dealerFolder);
        }

        $this->collDealerFolders = $dealerFolders;
        $this->collDealerFoldersPartial = false;

        return $this;
    }

    /**
     * Returns the number of related DealerFolder objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related DealerFolder objects.
     * @throws PropelException
     */
    public function countDealerFolders(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collDealerFoldersPartial && !$this->isNew();
        if (null === $this->collDealerFolders || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collDealerFolders) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getDealerFolders());
            }

            $query = ChildDealerFolderQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByDealer($this)
                ->count($con);
        }

        return count($this->collDealerFolders);
    }

    /**
     * Method called to associate a ChildDealerFolder object to this object
     * through the ChildDealerFolder foreign key attribute.
     *
     * @param    ChildDealerFolder $l ChildDealerFolder
     * @return   \Dealer\Model\Dealer The current object (for fluent API support)
     */
    public function addDealerFolder(ChildDealerFolder $l)
    {
        if ($this->collDealerFolders === null) {
            $this->initDealerFolders();
            $this->collDealerFoldersPartial = true;
        }

        if (!in_array($l, $this->collDealerFolders->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddDealerFolder($l);
        }

        return $this;
    }

    /**
     * @param DealerFolder $dealerFolder The dealerFolder object to add.
     */
    protected function doAddDealerFolder($dealerFolder)
    {
        $this->collDealerFolders[]= $dealerFolder;
        $dealerFolder->setDealer($this);
    }

    /**
     * @param  DealerFolder $dealerFolder The dealerFolder object to remove.
     * @return ChildDealer The current object (for fluent API support)
     */
    public function removeDealerFolder($dealerFolder)
    {
        if ($this->getDealerFolders()->contains($dealerFolder)) {
            $this->collDealerFolders->remove($this->collDealerFolders->search($dealerFolder));
            if (null === $this->dealerFoldersScheduledForDeletion) {
                $this->dealerFoldersScheduledForDeletion = clone $this->collDealerFolders;
                $this->dealerFoldersScheduledForDeletion->clear();
            }
            $this->dealerFoldersScheduledForDeletion[]= clone $dealerFolder;
            $dealerFolder->setDealer(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Dealer is new, it will return
     * an empty collection; or if this Dealer has previously
     * been saved, it will retrieve related DealerFolders from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Dealer.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildDealerFolder[] List of ChildDealerFolder objects
     */
    public function getDealerFoldersJoinFolder($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildDealerFolderQuery::create(null, $criteria);
        $query->joinWith('Folder', $joinBehavior);

        return $this->getDealerFolders($query, $con);
    }

    /**
     * Clears out the collDealerBrands collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addDealerBrands()
     */
    public function clearDealerBrands()
    {
        $this->collDealerBrands = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collDealerBrands collection loaded partially.
     */
    public function resetPartialDealerBrands($v = true)
    {
        $this->collDealerBrandsPartial = $v;
    }

    /**
     * Initializes the collDealerBrands collection.
     *
     * By default this just sets the collDealerBrands collection to an empty array (like clearcollDealerBrands());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initDealerBrands($overrideExisting = true)
    {
        if (null !== $this->collDealerBrands && !$overrideExisting) {
            return;
        }
        $this->collDealerBrands = new ObjectCollection();
        $this->collDealerBrands->setModel('\Dealer\Model\DealerBrand');
    }

    /**
     * Gets an array of ChildDealerBrand objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildDealer is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildDealerBrand[] List of ChildDealerBrand objects
     * @throws PropelException
     */
    public function getDealerBrands($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collDealerBrandsPartial && !$this->isNew();
        if (null === $this->collDealerBrands || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collDealerBrands) {
                // return empty collection
                $this->initDealerBrands();
            } else {
                $collDealerBrands = ChildDealerBrandQuery::create(null, $criteria)
                    ->filterByDealer($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collDealerBrandsPartial && count($collDealerBrands)) {
                        $this->initDealerBrands(false);

                        foreach ($collDealerBrands as $obj) {
                            if (false == $this->collDealerBrands->contains($obj)) {
                                $this->collDealerBrands->append($obj);
                            }
                        }

                        $this->collDealerBrandsPartial = true;
                    }

                    reset($collDealerBrands);

                    return $collDealerBrands;
                }

                if ($partial && $this->collDealerBrands) {
                    foreach ($this->collDealerBrands as $obj) {
                        if ($obj->isNew()) {
                            $collDealerBrands[] = $obj;
                        }
                    }
                }

                $this->collDealerBrands = $collDealerBrands;
                $this->collDealerBrandsPartial = false;
            }
        }

        return $this->collDealerBrands;
    }

    /**
     * Sets a collection of DealerBrand objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $dealerBrands A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildDealer The current object (for fluent API support)
     */
    public function setDealerBrands(Collection $dealerBrands, ConnectionInterface $con = null)
    {
        $dealerBrandsToDelete = $this->getDealerBrands(new Criteria(), $con)->diff($dealerBrands);


        $this->dealerBrandsScheduledForDeletion = $dealerBrandsToDelete;

        foreach ($dealerBrandsToDelete as $dealerBrandRemoved) {
            $dealerBrandRemoved->setDealer(null);
        }

        $this->collDealerBrands = null;
        foreach ($dealerBrands as $dealerBrand) {
            $this->addDealerBrand($dealerBrand);
        }

        $this->collDealerBrands = $dealerBrands;
        $this->collDealerBrandsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related DealerBrand objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related DealerBrand objects.
     * @throws PropelException
     */
    public function countDealerBrands(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collDealerBrandsPartial && !$this->isNew();
        if (null === $this->collDealerBrands || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collDealerBrands) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getDealerBrands());
            }

            $query = ChildDealerBrandQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByDealer($this)
                ->count($con);
        }

        return count($this->collDealerBrands);
    }

    /**
     * Method called to associate a ChildDealerBrand object to this object
     * through the ChildDealerBrand foreign key attribute.
     *
     * @param    ChildDealerBrand $l ChildDealerBrand
     * @return   \Dealer\Model\Dealer The current object (for fluent API support)
     */
    public function addDealerBrand(ChildDealerBrand $l)
    {
        if ($this->collDealerBrands === null) {
            $this->initDealerBrands();
            $this->collDealerBrandsPartial = true;
        }

        if (!in_array($l, $this->collDealerBrands->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddDealerBrand($l);
        }

        return $this;
    }

    /**
     * @param DealerBrand $dealerBrand The dealerBrand object to add.
     */
    protected function doAddDealerBrand($dealerBrand)
    {
        $this->collDealerBrands[]= $dealerBrand;
        $dealerBrand->setDealer($this);
    }

    /**
     * @param  DealerBrand $dealerBrand The dealerBrand object to remove.
     * @return ChildDealer The current object (for fluent API support)
     */
    public function removeDealerBrand($dealerBrand)
    {
        if ($this->getDealerBrands()->contains($dealerBrand)) {
            $this->collDealerBrands->remove($this->collDealerBrands->search($dealerBrand));
            if (null === $this->dealerBrandsScheduledForDeletion) {
                $this->dealerBrandsScheduledForDeletion = clone $this->collDealerBrands;
                $this->dealerBrandsScheduledForDeletion->clear();
            }
            $this->dealerBrandsScheduledForDeletion[]= clone $dealerBrand;
            $dealerBrand->setDealer(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Dealer is new, it will return
     * an empty collection; or if this Dealer has previously
     * been saved, it will retrieve related DealerBrands from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Dealer.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildDealerBrand[] List of ChildDealerBrand objects
     */
    public function getDealerBrandsJoinBrand($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildDealerBrandQuery::create(null, $criteria);
        $query->joinWith('Brand', $joinBehavior);

        return $this->getDealerBrands($query, $con);
    }

    /**
     * Clears out the collDealerProducts collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addDealerProducts()
     */
    public function clearDealerProducts()
    {
        $this->collDealerProducts = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collDealerProducts collection loaded partially.
     */
    public function resetPartialDealerProducts($v = true)
    {
        $this->collDealerProductsPartial = $v;
    }

    /**
     * Initializes the collDealerProducts collection.
     *
     * By default this just sets the collDealerProducts collection to an empty array (like clearcollDealerProducts());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initDealerProducts($overrideExisting = true)
    {
        if (null !== $this->collDealerProducts && !$overrideExisting) {
            return;
        }
        $this->collDealerProducts = new ObjectCollection();
        $this->collDealerProducts->setModel('\Dealer\Model\DealerProduct');
    }

    /**
     * Gets an array of ChildDealerProduct objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildDealer is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildDealerProduct[] List of ChildDealerProduct objects
     * @throws PropelException
     */
    public function getDealerProducts($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collDealerProductsPartial && !$this->isNew();
        if (null === $this->collDealerProducts || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collDealerProducts) {
                // return empty collection
                $this->initDealerProducts();
            } else {
                $collDealerProducts = ChildDealerProductQuery::create(null, $criteria)
                    ->filterByDealer($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collDealerProductsPartial && count($collDealerProducts)) {
                        $this->initDealerProducts(false);

                        foreach ($collDealerProducts as $obj) {
                            if (false == $this->collDealerProducts->contains($obj)) {
                                $this->collDealerProducts->append($obj);
                            }
                        }

                        $this->collDealerProductsPartial = true;
                    }

                    reset($collDealerProducts);

                    return $collDealerProducts;
                }

                if ($partial && $this->collDealerProducts) {
                    foreach ($this->collDealerProducts as $obj) {
                        if ($obj->isNew()) {
                            $collDealerProducts[] = $obj;
                        }
                    }
                }

                $this->collDealerProducts = $collDealerProducts;
                $this->collDealerProductsPartial = false;
            }
        }

        return $this->collDealerProducts;
    }

    /**
     * Sets a collection of DealerProduct objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $dealerProducts A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildDealer The current object (for fluent API support)
     */
    public function setDealerProducts(Collection $dealerProducts, ConnectionInterface $con = null)
    {
        $dealerProductsToDelete = $this->getDealerProducts(new Criteria(), $con)->diff($dealerProducts);


        $this->dealerProductsScheduledForDeletion = $dealerProductsToDelete;

        foreach ($dealerProductsToDelete as $dealerProductRemoved) {
            $dealerProductRemoved->setDealer(null);
        }

        $this->collDealerProducts = null;
        foreach ($dealerProducts as $dealerProduct) {
            $this->addDealerProduct($dealerProduct);
        }

        $this->collDealerProducts = $dealerProducts;
        $this->collDealerProductsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related DealerProduct objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related DealerProduct objects.
     * @throws PropelException
     */
    public function countDealerProducts(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collDealerProductsPartial && !$this->isNew();
        if (null === $this->collDealerProducts || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collDealerProducts) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getDealerProducts());
            }

            $query = ChildDealerProductQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByDealer($this)
                ->count($con);
        }

        return count($this->collDealerProducts);
    }

    /**
     * Method called to associate a ChildDealerProduct object to this object
     * through the ChildDealerProduct foreign key attribute.
     *
     * @param    ChildDealerProduct $l ChildDealerProduct
     * @return   \Dealer\Model\Dealer The current object (for fluent API support)
     */
    public function addDealerProduct(ChildDealerProduct $l)
    {
        if ($this->collDealerProducts === null) {
            $this->initDealerProducts();
            $this->collDealerProductsPartial = true;
        }

        if (!in_array($l, $this->collDealerProducts->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddDealerProduct($l);
        }

        return $this;
    }

    /**
     * @param DealerProduct $dealerProduct The dealerProduct object to add.
     */
    protected function doAddDealerProduct($dealerProduct)
    {
        $this->collDealerProducts[]= $dealerProduct;
        $dealerProduct->setDealer($this);
    }

    /**
     * @param  DealerProduct $dealerProduct The dealerProduct object to remove.
     * @return ChildDealer The current object (for fluent API support)
     */
    public function removeDealerProduct($dealerProduct)
    {
        if ($this->getDealerProducts()->contains($dealerProduct)) {
            $this->collDealerProducts->remove($this->collDealerProducts->search($dealerProduct));
            if (null === $this->dealerProductsScheduledForDeletion) {
                $this->dealerProductsScheduledForDeletion = clone $this->collDealerProducts;
                $this->dealerProductsScheduledForDeletion->clear();
            }
            $this->dealerProductsScheduledForDeletion[]= clone $dealerProduct;
            $dealerProduct->setDealer(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Dealer is new, it will return
     * an empty collection; or if this Dealer has previously
     * been saved, it will retrieve related DealerProducts from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Dealer.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildDealerProduct[] List of ChildDealerProduct objects
     */
    public function getDealerProductsJoinProduct($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildDealerProductQuery::create(null, $criteria);
        $query->joinWith('Product', $joinBehavior);

        return $this->getDealerProducts($query, $con);
    }

    /**
     * Clears out the collDealerAdmins collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addDealerAdmins()
     */
    public function clearDealerAdmins()
    {
        $this->collDealerAdmins = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collDealerAdmins collection loaded partially.
     */
    public function resetPartialDealerAdmins($v = true)
    {
        $this->collDealerAdminsPartial = $v;
    }

    /**
     * Initializes the collDealerAdmins collection.
     *
     * By default this just sets the collDealerAdmins collection to an empty array (like clearcollDealerAdmins());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initDealerAdmins($overrideExisting = true)
    {
        if (null !== $this->collDealerAdmins && !$overrideExisting) {
            return;
        }
        $this->collDealerAdmins = new ObjectCollection();
        $this->collDealerAdmins->setModel('\Dealer\Model\DealerAdmin');
    }

    /**
     * Gets an array of ChildDealerAdmin objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildDealer is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return Collection|ChildDealerAdmin[] List of ChildDealerAdmin objects
     * @throws PropelException
     */
    public function getDealerAdmins($criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collDealerAdminsPartial && !$this->isNew();
        if (null === $this->collDealerAdmins || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collDealerAdmins) {
                // return empty collection
                $this->initDealerAdmins();
            } else {
                $collDealerAdmins = ChildDealerAdminQuery::create(null, $criteria)
                    ->filterByDealer($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collDealerAdminsPartial && count($collDealerAdmins)) {
                        $this->initDealerAdmins(false);

                        foreach ($collDealerAdmins as $obj) {
                            if (false == $this->collDealerAdmins->contains($obj)) {
                                $this->collDealerAdmins->append($obj);
                            }
                        }

                        $this->collDealerAdminsPartial = true;
                    }

                    reset($collDealerAdmins);

                    return $collDealerAdmins;
                }

                if ($partial && $this->collDealerAdmins) {
                    foreach ($this->collDealerAdmins as $obj) {
                        if ($obj->isNew()) {
                            $collDealerAdmins[] = $obj;
                        }
                    }
                }

                $this->collDealerAdmins = $collDealerAdmins;
                $this->collDealerAdminsPartial = false;
            }
        }

        return $this->collDealerAdmins;
    }

    /**
     * Sets a collection of DealerAdmin objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $dealerAdmins A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return   ChildDealer The current object (for fluent API support)
     */
    public function setDealerAdmins(Collection $dealerAdmins, ConnectionInterface $con = null)
    {
        $dealerAdminsToDelete = $this->getDealerAdmins(new Criteria(), $con)->diff($dealerAdmins);


        $this->dealerAdminsScheduledForDeletion = $dealerAdminsToDelete;

        foreach ($dealerAdminsToDelete as $dealerAdminRemoved) {
            $dealerAdminRemoved->setDealer(null);
        }

        $this->collDealerAdmins = null;
        foreach ($dealerAdmins as $dealerAdmin) {
            $this->addDealerAdmin($dealerAdmin);
        }

        $this->collDealerAdmins = $dealerAdmins;
        $this->collDealerAdminsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related DealerAdmin objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related DealerAdmin objects.
     * @throws PropelException
     */
    public function countDealerAdmins(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collDealerAdminsPartial && !$this->isNew();
        if (null === $this->collDealerAdmins || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collDealerAdmins) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getDealerAdmins());
            }

            $query = ChildDealerAdminQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByDealer($this)
                ->count($con);
        }

        return count($this->collDealerAdmins);
    }

    /**
     * Method called to associate a ChildDealerAdmin object to this object
     * through the ChildDealerAdmin foreign key attribute.
     *
     * @param    ChildDealerAdmin $l ChildDealerAdmin
     * @return   \Dealer\Model\Dealer The current object (for fluent API support)
     */
    public function addDealerAdmin(ChildDealerAdmin $l)
    {
        if ($this->collDealerAdmins === null) {
            $this->initDealerAdmins();
            $this->collDealerAdminsPartial = true;
        }

        if (!in_array($l, $this->collDealerAdmins->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddDealerAdmin($l);
        }

        return $this;
    }

    /**
     * @param DealerAdmin $dealerAdmin The dealerAdmin object to add.
     */
    protected function doAddDealerAdmin($dealerAdmin)
    {
        $this->collDealerAdmins[]= $dealerAdmin;
        $dealerAdmin->setDealer($this);
    }

    /**
     * @param  DealerAdmin $dealerAdmin The dealerAdmin object to remove.
     * @return ChildDealer The current object (for fluent API support)
     */
    public function removeDealerAdmin($dealerAdmin)
    {
        if ($this->getDealerAdmins()->contains($dealerAdmin)) {
            $this->collDealerAdmins->remove($this->collDealerAdmins->search($dealerAdmin));
            if (null === $this->dealerAdminsScheduledForDeletion) {
                $this->dealerAdminsScheduledForDeletion = clone $this->collDealerAdmins;
                $this->dealerAdminsScheduledForDeletion->clear();
            }
            $this->dealerAdminsScheduledForDeletion[]= clone $dealerAdmin;
            $dealerAdmin->setDealer(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Dealer is new, it will return
     * an empty collection; or if this Dealer has previously
     * been saved, it will retrieve related DealerAdmins from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Dealer.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return Collection|ChildDealerAdmin[] List of ChildDealerAdmin objects
     */
    public function getDealerAdminsJoinAdmin($criteria = null, $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildDealerAdminQuery::create(null, $criteria);
        $query->joinWith('Admin', $joinBehavior);

        return $this->getDealerAdmins($query, $con);
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
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->visible = null;
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
            if ($this->collDealerContents) {
                foreach ($this->collDealerContents as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collDealerFolders) {
                foreach ($this->collDealerFolders as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collDealerBrands) {
                foreach ($this->collDealerBrands as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collDealerProducts) {
                foreach ($this->collDealerProducts as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collDealerAdmins) {
                foreach ($this->collDealerAdmins as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collDealerI18ns) {
                foreach ($this->collDealerI18ns as $o) {
                    $o->clearAllReferences($deep);
                }
            }
        } // if ($deep)

        // i18n behavior
        $this->currentLocale = 'en_US';
        $this->currentTranslations = null;

        $this->collDealerSheduless = null;
        $this->collDealerContacts = null;
        $this->collDealerContents = null;
        $this->collDealerFolders = null;
        $this->collDealerBrands = null;
        $this->collDealerProducts = null;
        $this->collDealerAdmins = null;
        $this->collDealerI18ns = null;
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


        /**
         * Get the [access] column value.
         *
         * @return   string
         */
        public function getAccess()
        {
        return $this->getCurrentTranslation()->getAccess();
    }


        /**
         * Set the value of [access] column.
         *
         * @param      string $v new value
         * @return   \Dealer\Model\DealerI18n The current object (for fluent API support)
         */
        public function setAccess($v)
        {    $this->getCurrentTranslation()->setAccess($v);

        return $this;
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
