<?php

declare(strict_types=1);

namespace Dealer\Api\Resource;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\OpenApi\Model\Operation as OpenApiOperation;
use Dealer\Model\Dealer as DealerModel;
use Dealer\Model\DealerQuery;
use Dealer\Model\Map\DealerTableMap;
use Dealer\Service\DealerPickupConfigService;
use Dealer\Api\Scope\DealerScopedResourceInterface;
use Dealer\Api\Translation\PartialTranslationsTrait;
use Dealer\Dealer as DealerModule;
use Dealer\Api\State\Processor\DealerPersistProcessor;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\Map\TableMap;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Thelia\Api\Bridge\Propel\Attribute\Relation;
use Thelia\Api\Resource\AbstractTranslatableResource;
use Thelia\Api\Resource\I18nCollection;
use Thelia\Model\CountryQuery;

/**
 * A store, as the back-office tabs "General" and "Location" show it, plus the read-only view of
 * its contacts and opening hours (each with the right of its tab) and pickup settings. The other tabs (SEO, images, associations,
 * users, modules) are not exposed.
 */
#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/admin/dealers',
            openapi: new OpenApiOperation(
                tags: [DealerModule::API_DOC_TAG_DEALER],
                summary: 'Stores',
                description: 'The stores in scope: every store for a superadmin, the stores linked to the administrator otherwise.',
            ),
        ),
        new Get(
            uriTemplate: '/admin/dealers/{id}',
            normalizationContext: ['groups' => self::GROUPS_ADMIN_READ_SINGLE],
            openapi: new OpenApiOperation(
                tags: [DealerModule::API_DOC_TAG_DEALER],
                summary: 'Store detail',
                description: 'General information, location, contacts, opening hours and pickup settings of the store. '
                    .'The contacts and the opening hours are left out without the right of their tab.',
            ),
        ),
        new Patch(
            uriTemplate: '/admin/dealers/{id}',
            normalizationContext: ['groups' => self::GROUPS_ADMIN_READ_SINGLE],
            openapi: new OpenApiOperation(
                tags: [DealerModule::API_DOC_TAG_DEALER],
                summary: 'Update the general information and location of a store',
                description: 'Partial update (`Content-Type: application/merge-patch+json`). Translated fields '
                    .'go in `i18ns`, keyed by locale: only the locales sent are written. Contacts, opening '
                    .'hours and pickup settings have their own endpoints.',
            ),
            processor: DealerPersistProcessor::class,
        ),
    ],
    normalizationContext: ['groups' => [self::GROUP_ADMIN_READ]],
    denormalizationContext: ['groups' => [self::GROUP_ADMIN_WRITE]],
    validationContext: ['groups' => [self::GROUP_ADMIN_WRITE]],
)]
class Dealer extends AbstractTranslatableResource implements DealerScopedResourceInterface
{
    use PartialTranslationsTrait;

    public const GROUP_ADMIN_READ = 'admin:dealer:read';
    public const GROUP_ADMIN_READ_SINGLE = 'admin:dealer:read:single';
    public const GROUP_ADMIN_READ_CONTACTS = 'admin:dealer:read:contacts';
    public const GROUP_ADMIN_READ_SCHEDULES = 'admin:dealer:read:schedules';
    public const GROUP_ADMIN_WRITE = 'admin:dealer:write';

    /**
     * The detail of a store. The contacts and the opening hours are dropped from it when the
     * administrator lacks the right of their tab (DealerTabRightsContextBuilder).
     */
    public const GROUPS_ADMIN_READ_SINGLE = [
        self::GROUP_ADMIN_READ,
        self::GROUP_ADMIN_READ_SINGLE,
        self::GROUP_ADMIN_READ_CONTACTS,
        self::GROUP_ADMIN_READ_SCHEDULES,
    ];

    private const REQUIRED_I18N_FIELD = 'title';

    #[Groups([self::GROUP_ADMIN_READ])]
    public ?int $id = null;

    #[Groups([self::GROUP_ADMIN_READ, self::GROUP_ADMIN_WRITE])]
    public bool $visible = false;

    #[Assert\NotBlank(groups: [self::GROUP_ADMIN_WRITE])]
    #[Assert\Length(max: 255, groups: [self::GROUP_ADMIN_WRITE])]
    #[Groups([self::GROUP_ADMIN_READ, self::GROUP_ADMIN_WRITE])]
    public ?string $address1 = null;

    #[Assert\Length(max: 255, groups: [self::GROUP_ADMIN_WRITE])]
    #[Groups([self::GROUP_ADMIN_READ, self::GROUP_ADMIN_WRITE])]
    public ?string $address2 = null;

    #[Assert\Length(max: 255, groups: [self::GROUP_ADMIN_WRITE])]
    #[Groups([self::GROUP_ADMIN_READ, self::GROUP_ADMIN_WRITE])]
    public ?string $address3 = null;

    #[Assert\NotBlank(groups: [self::GROUP_ADMIN_WRITE])]
    #[Assert\Length(max: 10, groups: [self::GROUP_ADMIN_WRITE])]
    #[Groups([self::GROUP_ADMIN_READ, self::GROUP_ADMIN_WRITE])]
    public ?string $zipcode = null;

    #[Assert\NotBlank(groups: [self::GROUP_ADMIN_WRITE])]
    #[Assert\Length(max: 255, groups: [self::GROUP_ADMIN_WRITE])]
    #[Groups([self::GROUP_ADMIN_READ, self::GROUP_ADMIN_WRITE])]
    public ?string $city = null;

    #[Assert\NotBlank(groups: [self::GROUP_ADMIN_WRITE])]
    #[Groups([self::GROUP_ADMIN_READ, self::GROUP_ADMIN_WRITE])]
    public ?int $countryId = null;

    #[Assert\Range(min: -90, max: 90, groups: [self::GROUP_ADMIN_WRITE])]
    #[Groups([self::GROUP_ADMIN_READ, self::GROUP_ADMIN_WRITE])]
    public ?float $latitude = null;

    #[Assert\Range(min: -180, max: 180, groups: [self::GROUP_ADMIN_WRITE])]
    #[Groups([self::GROUP_ADMIN_READ, self::GROUP_ADMIN_WRITE])]
    public ?float $longitude = null;

    #[ApiProperty(types: 'object')]
    #[Groups([self::GROUP_ADMIN_READ, self::GROUP_ADMIN_WRITE])]
    public I18nCollection $i18ns;

    #[Relation(targetResource: DealerContact::class, relationAlias: 'DealerContacts')]
    #[Groups([self::GROUP_ADMIN_READ_CONTACTS])]
    public array $contacts = [];

    #[Relation(targetResource: DealerSchedule::class, relationAlias: 'DealerSheduless')]
    #[Groups([self::GROUP_ADMIN_READ_SCHEDULES])]
    public array $schedules = [];

    public static function getPropelRelatedTableMap(): ?TableMap
    {
        return new DealerTableMap();
    }

    public static function getI18nResourceClass(): string
    {
        return DealerI18n::class;
    }

    public static function restrictToDealers(ModelCriteria $query, array $dealerIds): void
    {
        if (!$query instanceof DealerQuery) {
            throw new \LogicException(sprintf('Cannot bound a %s to a store.', $query::class));
        }

        $query->filterById($dealerIds, Criteria::IN);
    }

    public function owningDealerId(): ?int
    {
        return $this->id;
    }

    public function assignDealer(int $dealerId): void
    {
        // A store is only ever updated, after a read bounded to the stores in scope.
    }

    #[Assert\Callback(groups: [self::GROUP_ADMIN_WRITE])]
    public function validateCountry(ExecutionContextInterface $context): void
    {
        if (null !== $this->countryId && null === CountryQuery::create()->findPk($this->countryId)) {
            $context->buildViolation('Unknown country.')->atPath('countryId')->addViolation();
        }
    }

    /**
     * The pickup settings, defaults included when the store has never saved any: what the
     * "Pickup config" tab shows. Written through /admin/dealer_pickup_configs/{dealerId}.
     *
     * @return array{timezone: ?string, prepDelayMinutes: int, orderableDays: int, slotDurationMinutes: int, maxOrdersPerSlot: int}|null
     */
    #[Groups([self::GROUP_ADMIN_READ_SINGLE])]
    public function getPickupConfig(): ?array
    {
        $store = $this->getPropelModel();

        if (!$store instanceof DealerModel) {
            return null;
        }

        // A resource is not injected: the service is a plain reader of the pickup row, with
        // the defaults the back office shows when the store never saved any.
        return DealerPickupConfig::fromStore($store, (new DealerPickupConfigService())->get((int) $store->getId()))->toArray();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
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

    public function getCountryId(): ?int
    {
        return $this->countryId;
    }

    public function setCountryId(?int $countryId): self
    {
        $this->countryId = $countryId;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(?float $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(?float $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getContacts(): array
    {
        return $this->contacts;
    }

    public function setContacts(array $contacts): self
    {
        $this->contacts = $contacts;

        return $this;
    }

    public function getSchedules(): array
    {
        return $this->schedules;
    }

    public function setSchedules(array $schedules): self
    {
        $this->schedules = $schedules;

        return $this;
    }
}
