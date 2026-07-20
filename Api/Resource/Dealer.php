<?php

declare(strict_types=1);

namespace Dealer\Api\Resource;

use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use Dealer\Model\Map\DealerTableMap;
use Propel\Runtime\Map\TableMap;
use Symfony\Component\Serializer\Annotation\Groups;
use Thelia\Api\Bridge\Propel\Attribute\Relation;
use Thelia\Api\Bridge\Propel\Filter\BooleanFilter;
use Thelia\Api\Bridge\Propel\Filter\OrderFilter;
use Thelia\Api\Bridge\Propel\Filter\SearchFilter;
use Thelia\Api\Resource\AbstractTranslatableResource;
use Thelia\Api\Resource\Country;
use Thelia\Api\Resource\I18nCollection;

#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/front/dealers',
        ),
        new Get(
            uriTemplate: '/front/dealers/{id}',
            normalizationContext: ['groups' => [
                self::GROUP_FRONT_READ,
                self::GROUP_FRONT_READ_SINGLE,
                Country::GROUP_FRONT_READ,
                DealerContact::GROUP_FRONT_READ,
                DealerContent::GROUP_FRONT_READ,
                DealerFolder::GROUP_FRONT_READ,
                DealerBrand::GROUP_FRONT_READ,
                DealerProduct::GROUP_FRONT_READ,
                DealerImage::GROUP_FRONT_READ,
                DealerSchedule::GROUP_FRONT_READ,
            ]],
        ),
    ],
    normalizationContext: ['groups' => [self::GROUP_FRONT_READ]],
)]
#[ApiFilter(
    filterClass: SearchFilter::class,
    properties: [
        'id',
        'city',
        'country.id',
    ],
)]
#[ApiFilter(
    filterClass: BooleanFilter::class,
    properties: [
        'visible',
    ],
)]
#[ApiFilter(
    filterClass: OrderFilter::class,
    properties: [
        'id',
        'createdAt',
    ],
)]
class Dealer extends AbstractTranslatableResource
{
    public const GROUP_FRONT_READ = 'front:dealer:read';
    public const GROUP_FRONT_READ_SINGLE = 'front:dealer:read:single';

    #[Groups([self::GROUP_FRONT_READ])]
    public ?int $id = null;

    #[Groups([self::GROUP_FRONT_READ])]
    public bool $visible;

    #[Groups([self::GROUP_FRONT_READ])]
    public ?string $address1 = null;

    #[Groups([self::GROUP_FRONT_READ])]
    public ?string $address2 = null;

    #[Groups([self::GROUP_FRONT_READ])]
    public ?string $address3 = null;

    #[Groups([self::GROUP_FRONT_READ])]
    public ?string $zipcode = null;

    #[Groups([self::GROUP_FRONT_READ])]
    public ?string $city = null;

    #[Groups([self::GROUP_FRONT_READ])]
    public ?string $latitude = null;

    #[Groups([self::GROUP_FRONT_READ])]
    public ?string $longitude = null;

    #[Groups([self::GROUP_FRONT_READ])]
    public ?\DateTime $createdAt = null;

    #[Groups([self::GROUP_FRONT_READ])]
    public ?\DateTime $updatedAt = null;

    #[Groups([self::GROUP_FRONT_READ])]
    public I18nCollection $i18ns;

    #[Relation(targetResource: Country::class)]
    #[Groups([self::GROUP_FRONT_READ])]
    public Country $country;

    /**
     * Contacts attached to this dealer, through the dealer_contact table.
     *
     * @var DealerContact[]
     */
    #[Relation(targetResource: DealerContact::class)]
    #[Groups([self::GROUP_FRONT_READ_SINGLE])]
    public array $dealerContacts = [];

    /**
     * Contents linked to this dealer, through the dealer_content table.
     *
     * @var DealerContent[]
     */
    #[Relation(targetResource: DealerContent::class)]
    #[Groups([self::GROUP_FRONT_READ_SINGLE])]
    public array $dealerContents = [];

    /**
     * Folders linked to this dealer, through the dealer_folder table.
     *
     * @var DealerFolder[]
     */
    #[Relation(targetResource: DealerFolder::class)]
    #[Groups([self::GROUP_FRONT_READ_SINGLE])]
    public array $dealerFolders = [];

    /**
     * Brands linked to this dealer, through the dealer_brand table.
     *
     * @var DealerBrand[]
     */
    #[Relation(targetResource: DealerBrand::class)]
    #[Groups([self::GROUP_FRONT_READ_SINGLE])]
    public array $dealerBrands = [];

    /**
     * Products linked to this dealer, through the dealer_product table.
     *
     * @var DealerProduct[]
     */
    #[Relation(targetResource: DealerProduct::class)]
    #[Groups([self::GROUP_FRONT_READ_SINGLE])]
    public array $dealerProducts = [];

    /**
     * Images of this dealer, through the dealer_image table.
     *
     * @var DealerImage[]
     */
    #[Relation(targetResource: DealerImage::class)]
    #[Groups([self::GROUP_FRONT_READ_SINGLE])]
    public array $dealerImages = [];

    /**
     * Opening schedules of this dealer, through the dealer_shedules table.
     *
     * @var DealerSchedule[]
     */
    #[Relation(targetResource: DealerSchedule::class, relationAlias: 'dealerSheduless')]
    #[Groups([self::GROUP_FRONT_READ_SINGLE])]
    public array $dealerSchedules = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function isVisible(): bool
    {
        return $this->visible;
    }

    public function setVisible(bool $visible): self
    {
        $this->visible = $visible;

        return $this;
    }

    public function getAddress1(): ?string
    {
        return $this->address1;
    }

    public function setAddress1(?string $address1): self
    {
        $this->address1 = $address1;

        return $this;
    }

    public function getAddress2(): ?string
    {
        return $this->address2;
    }

    public function setAddress2(?string $address2): self
    {
        $this->address2 = $address2;

        return $this;
    }

    public function getAddress3(): ?string
    {
        return $this->address3;
    }

    public function setAddress3(?string $address3): self
    {
        $this->address3 = $address3;

        return $this;
    }

    public function getZipcode(): ?string
    {
        return $this->zipcode;
    }

    public function setZipcode(?string $zipcode): self
    {
        $this->zipcode = $zipcode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(?string $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(?string $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getCountry(): Country
    {
        return $this->country;
    }

    public function setCountry(Country $country): self
    {
        $this->country = $country;

        return $this;
    }

    /**
     * @return DealerContact[]
     */
    public function getDealerContacts(): array
    {
        return $this->dealerContacts;
    }

    /**
     * @param DealerContact[] $dealerContacts
     */
    public function setDealerContacts(array $dealerContacts): self
    {
        $this->dealerContacts = $dealerContacts;

        return $this;
    }

    /**
     * @return DealerContent[]
     */
    public function getDealerContents(): array
    {
        return $this->dealerContents;
    }

    /**
     * @param DealerContent[] $dealerContents
     */
    public function setDealerContents(array $dealerContents): self
    {
        $this->dealerContents = $dealerContents;

        return $this;
    }

    /**
     * @return DealerFolder[]
     */
    public function getDealerFolders(): array
    {
        return $this->dealerFolders;
    }

    /**
     * @param DealerFolder[] $dealerFolders
     */
    public function setDealerFolders(array $dealerFolders): self
    {
        $this->dealerFolders = $dealerFolders;

        return $this;
    }

    /**
     * @return DealerBrand[]
     */
    public function getDealerBrands(): array
    {
        return $this->dealerBrands;
    }

    /**
     * @param DealerBrand[] $dealerBrands
     */
    public function setDealerBrands(array $dealerBrands): self
    {
        $this->dealerBrands = $dealerBrands;

        return $this;
    }

    /**
     * @return DealerProduct[]
     */
    public function getDealerProducts(): array
    {
        return $this->dealerProducts;
    }

    /**
     * @param DealerProduct[] $dealerProducts
     */
    public function setDealerProducts(array $dealerProducts): self
    {
        $this->dealerProducts = $dealerProducts;

        return $this;
    }

    /**
     * @return DealerImage[]
     */
    public function getDealerImages(): array
    {
        return $this->dealerImages;
    }

    /**
     * @param DealerImage[] $dealerImages
     */
    public function setDealerImages(array $dealerImages): self
    {
        $this->dealerImages = $dealerImages;

        return $this;
    }

    /**
     * @return DealerSchedule[]
     */
    public function getDealerSchedules(): array
    {
        return $this->dealerSchedules;
    }

    /**
     * @param DealerSchedule[] $dealerSchedules
     */
    public function setDealerSchedules(array $dealerSchedules): self
    {
        $this->dealerSchedules = $dealerSchedules;

        return $this;
    }

    public static function getPropelRelatedTableMap(): ?TableMap
    {
        return new DealerTableMap();
    }

    public static function getI18nResourceClass(): string
    {
        return DealerI18n::class;
    }
}
