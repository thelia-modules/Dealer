<?php

declare(strict_types=1);

namespace Dealer\Api\Resource;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model\Operation as OpenApiOperation;
use Dealer\Model\DealerContactQuery;
use Dealer\Model\Map\DealerContactTableMap;
use Dealer\Api\Scope\DealerScopedResourceInterface;
use Dealer\Api\Translation\PartialTranslationsTrait;
use Dealer\Dealer as DealerModule;
use Dealer\Api\State\Processor\DealerContactPersistProcessor;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\Map\TableMap;
use Symfony\Component\Serializer\Annotation\Groups;
use Thelia\Api\Bridge\Propel\Attribute\Relation;
use Thelia\Api\Resource\AbstractTranslatableResource;
use Thelia\Api\Resource\I18nCollection;

/**
 * A contact of the store ("Contacts" tab): a translated label, a default flag, and its contact
 * details (phone, e-mail...), which are written through /admin/dealer_contact_infos.
 */
#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/admin/dealer_contacts',
            openapi: new OpenApiOperation(tags: [DealerModule::API_DOC_TAG_CONTACT], summary: 'Contacts of the stores in scope'),
        ),
        new Post(
            uriTemplate: '/admin/dealer_contacts',
            openapi: new OpenApiOperation(
                tags: [DealerModule::API_DOC_TAG_CONTACT],
                summary: 'Add a contact to a store',
                description: 'dealerId names the store; it may be left out when the request is bounded to one store. '
                    .'isDefault true takes the default flag off the store\'s other contacts.',
            ),
            denormalizationContext: ['groups' => [self::GROUP_ADMIN_WRITE, self::GROUP_ADMIN_CREATE]],
            processor: DealerContactPersistProcessor::class,
        ),
        new Get(
            uriTemplate: '/admin/dealer_contacts/{id}',
            openapi: new OpenApiOperation(tags: [DealerModule::API_DOC_TAG_CONTACT], summary: 'Contact detail'),
        ),
        new Patch(
            uriTemplate: '/admin/dealer_contacts/{id}',
            openapi: new OpenApiOperation(
                tags: [DealerModule::API_DOC_TAG_CONTACT],
                summary: 'Update a contact',
                description: 'Partial update (`Content-Type: application/merge-patch+json`); only the locales sent in `i18ns` are written.',
            ),
            processor: DealerContactPersistProcessor::class,
        ),
        new Delete(
            uriTemplate: '/admin/dealer_contacts/{id}',
            openapi: new OpenApiOperation(tags: [DealerModule::API_DOC_TAG_CONTACT], summary: 'Delete a contact and its contact details'),
        ),
    ],
    normalizationContext: ['groups' => [self::GROUP_ADMIN_READ]],
    denormalizationContext: ['groups' => [self::GROUP_ADMIN_WRITE]],
    validationContext: ['groups' => [self::GROUP_ADMIN_WRITE]],
)]
class DealerContact extends AbstractTranslatableResource implements DealerScopedResourceInterface
{
    use PartialTranslationsTrait;

    public const GROUP_ADMIN_READ = 'admin:dealer_contact:read';
    public const GROUP_ADMIN_WRITE = 'admin:dealer_contact:write';
    /** Written on creation only: a contact never moves to another store. */
    public const GROUP_ADMIN_CREATE = 'admin:dealer_contact:create';

    private const REQUIRED_I18N_FIELD = 'label';

    #[Groups([self::GROUP_ADMIN_READ, Dealer::GROUP_ADMIN_READ_SINGLE])]
    public ?int $id = null;

    #[Groups([self::GROUP_ADMIN_READ, self::GROUP_ADMIN_CREATE])]
    public ?int $dealerId = null;

    #[Groups([self::GROUP_ADMIN_READ, self::GROUP_ADMIN_WRITE, Dealer::GROUP_ADMIN_READ_SINGLE])]
    public bool $isDefault = false;

    #[ApiProperty(types: 'object')]
    #[Groups([self::GROUP_ADMIN_READ, self::GROUP_ADMIN_WRITE, Dealer::GROUP_ADMIN_READ_SINGLE])]
    public I18nCollection $i18ns;

    #[Relation(targetResource: DealerContactInfo::class, relationAlias: 'DealerContactInfos')]
    #[Groups([self::GROUP_ADMIN_READ, Dealer::GROUP_ADMIN_READ_SINGLE])]
    public array $infos = [];

    public static function getPropelRelatedTableMap(): ?TableMap
    {
        return new DealerContactTableMap();
    }

    public static function getI18nResourceClass(): string
    {
        return DealerContactI18n::class;
    }

    public static function restrictToDealers(ModelCriteria $query, array $dealerIds): void
    {
        if (!$query instanceof DealerContactQuery) {
            throw new \LogicException(sprintf('Cannot bound a %s to a store.', $query::class));
        }

        $query->filterByDealerId($dealerIds, Criteria::IN);
    }

    public function owningDealerId(): ?int
    {
        return $this->dealerId;
    }

    public function assignDealer(int $dealerId): void
    {
        $this->dealerId = $dealerId;
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

    public function getDealerId(): ?int
    {
        return $this->dealerId;
    }

    public function setDealerId(?int $dealerId): self
    {
        $this->dealerId = $dealerId;

        return $this;
    }

    public function getIsDefault(): bool
    {
        return $this->isDefault;
    }

    public function setIsDefault(bool $isDefault): self
    {
        $this->isDefault = $isDefault;

        return $this;
    }

    public function getInfos(): array
    {
        return $this->infos;
    }

    public function setInfos(array $infos): self
    {
        $this->infos = $infos;

        return $this;
    }
}
