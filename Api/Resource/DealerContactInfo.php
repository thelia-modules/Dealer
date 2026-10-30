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
use Dealer\Model\DealerContactInfo as DealerContactInfoModel;
use Dealer\Model\DealerContactInfoQuery;
use Dealer\Model\DealerContactQuery;
use Dealer\Model\Map\DealerContactInfoTableMap;
use Dealer\Api\Scope\DealerScopedResourceInterface;
use Dealer\Api\Translation\PartialTranslationsTrait;
use Dealer\Dealer as DealerModule;
use Dealer\Api\State\Processor\DealerScopedPersistProcessor;
use Dealer\Api\Validator\ContactInScope;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\Map\TableMap;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Thelia\Api\Resource\AbstractTranslatableResource;
use Thelia\Api\Resource\I18nCollection;

/**
 * One contact detail of a store contact: a type (phone, e-mail, social network...) and a
 * translated value.
 */
#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/admin/dealer_contact_infos',
            openapi: new OpenApiOperation(tags: [DealerModule::API_DOC_TAG_CONTACT_INFO], summary: 'Contact details of the stores in scope'),
        ),
        new Post(
            uriTemplate: '/admin/dealer_contact_infos',
            openapi: new OpenApiOperation(
                tags: [DealerModule::API_DOC_TAG_CONTACT_INFO],
                summary: 'Add a contact detail to a contact',
                description: 'contactId must be a contact of a store in scope (422 otherwise).',
            ),
            processor: DealerScopedPersistProcessor::class,
        ),
        new Get(
            uriTemplate: '/admin/dealer_contact_infos/{id}',
            openapi: new OpenApiOperation(tags: [DealerModule::API_DOC_TAG_CONTACT_INFO], summary: 'Contact detail'),
        ),
        new Patch(
            uriTemplate: '/admin/dealer_contact_infos/{id}',
            openapi: new OpenApiOperation(
                tags: [DealerModule::API_DOC_TAG_CONTACT_INFO],
                summary: 'Update a contact detail',
                description: 'Partial update (`Content-Type: application/merge-patch+json`); only the locales sent in `i18ns` are written.',
            ),
            processor: DealerScopedPersistProcessor::class,
        ),
        new Delete(
            uriTemplate: '/admin/dealer_contact_infos/{id}',
            openapi: new OpenApiOperation(tags: [DealerModule::API_DOC_TAG_CONTACT_INFO], summary: 'Delete a contact detail'),
        ),
    ],
    normalizationContext: ['groups' => [self::GROUP_ADMIN_READ]],
    denormalizationContext: ['groups' => [self::GROUP_ADMIN_WRITE]],
    validationContext: ['groups' => [self::GROUP_ADMIN_WRITE]],
)]
class DealerContactInfo extends AbstractTranslatableResource implements DealerScopedResourceInterface
{
    use PartialTranslationsTrait;

    public const GROUP_ADMIN_READ = 'admin:dealer_contact_info:read';
    public const GROUP_ADMIN_WRITE = 'admin:dealer_contact_info:write';

    /** Documentation only: the validation reads the value set of the Dealer schema (contactTypes()). */
    public const CONTACT_TYPES = ['EMAIL', 'TEL', 'FAX', 'URL', 'FACEBOOK', 'TWITTER', 'PINTEREST', 'GOOGLE', 'YOUTUB', 'INSTAGRAM'];

    private const PHONE_TYPES = ['TEL', 'FAX'];
    private const PHONE_PATTERN = '#^\+?[0-9 ().\/-]{4,30}$#';

    private const REQUIRED_I18N_FIELD = 'value';

    #[Groups([self::GROUP_ADMIN_READ, DealerContact::GROUP_ADMIN_READ, Dealer::GROUP_ADMIN_READ_SINGLE])]
    public ?int $id = null;

    #[Assert\NotBlank(groups: [self::GROUP_ADMIN_WRITE])]
    #[ContactInScope(groups: [self::GROUP_ADMIN_WRITE])]
    #[Groups([self::GROUP_ADMIN_READ, self::GROUP_ADMIN_WRITE])]
    public ?int $contactId = null;

    #[Assert\NotBlank(groups: [self::GROUP_ADMIN_WRITE])]
    #[Assert\Choice(callback: [self::class, 'contactTypes'], groups: [self::GROUP_ADMIN_WRITE])]
    #[ApiProperty(schema: ['type' => 'string', 'enum' => self::CONTACT_TYPES])]
    #[Groups([self::GROUP_ADMIN_READ, self::GROUP_ADMIN_WRITE, DealerContact::GROUP_ADMIN_READ, Dealer::GROUP_ADMIN_READ_SINGLE])]
    public ?string $contactType = null;

    #[ApiProperty(types: 'object')]
    #[Groups([self::GROUP_ADMIN_READ, self::GROUP_ADMIN_WRITE, DealerContact::GROUP_ADMIN_READ, Dealer::GROUP_ADMIN_READ_SINGLE])]
    public I18nCollection $i18ns;

    public static function getPropelRelatedTableMap(): ?TableMap
    {
        return new DealerContactInfoTableMap();
    }

    public static function getI18nResourceClass(): string
    {
        return DealerContactInfoI18n::class;
    }

    /** @return list<string> */
    public static function contactTypes(): array
    {
        return array_values(DealerContactInfoTableMap::getValueSet(DealerContactInfoTableMap::COL_CONTACT_TYPE));
    }

    /**
     * The store page renders an e-mail as mailto:, a phone as tel: and every other type as a
     * link (Molecules/StoreSocialLinks): a value that is not what its type says would be a
     * broken link, or a javascript: one.
     */
    #[Assert\Callback(groups: [self::GROUP_ADMIN_WRITE])]
    public function validateValueForType(ExecutionContextInterface $context): void
    {
        // A PATCH reads only the locales its payload names: a type changed alone has to match
        // the values already stored in the others.
        $values = [];
        $stored = $this->getPropelModel();

        if ($stored instanceof DealerContactInfoModel) {
            foreach ($stored->getDealerContactInfoI18ns() as $storedI18n) {
                $values[(string) $storedI18n->getLocale()] = (string) $storedI18n->getValue();
            }
        }

        foreach ($this->i18ns as $locale => $i18n) {
            $values[(string) $locale] = (string) $i18n->getValue();
        }

        foreach ($values as $locale => $value) {
            $value = trim($value);

            if ('' === $value || null === $this->contactType) {
                continue;
            }

            $valid = match (true) {
                'EMAIL' === $this->contactType => false !== filter_var($value, \FILTER_VALIDATE_EMAIL),
                \in_array($this->contactType, self::PHONE_TYPES, true) => 1 === preg_match(self::PHONE_PATTERN, $value),
                default => 1 === preg_match('#^https?://#i', $value) && false !== filter_var($value, \FILTER_VALIDATE_URL),
            };

            if (!$valid) {
                $context->buildViolation(sprintf('Not a valid %s value.', $this->contactType))
                    ->atPath('i18ns['.$locale.'].value')
                    ->addViolation();
            }
        }
    }

    public static function restrictToDealers(ModelCriteria $query, array $dealerIds): void
    {
        if (!$query instanceof DealerContactInfoQuery) {
            throw new \LogicException(sprintf('Cannot bound a %s to a store.', $query::class));
        }

        $query->useDealerContactQuery()
            ->filterByDealerId($dealerIds, Criteria::IN)
            ->endUse();
    }

    /**
     * The store of the contact the detail belongs to. ContactInScope has already refused a contact
     * that does not exist or belongs to a store out of scope, with one answer for both.
     */
    public function owningDealerId(): ?int
    {
        $dealerId = DealerContactQuery::create()->filterById((int) $this->contactId)->select(['DealerId'])->findOne();

        if (null === $dealerId) {
            throw new NotFoundHttpException(sprintf('Contact %d not found.', (int) $this->contactId));
        }

        return (int) $dealerId;
    }

    public function assignDealer(int $dealerId): void
    {
        // The store follows from the contact.
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

    public function getContactId(): ?int
    {
        return $this->contactId;
    }

    public function setContactId(?int $contactId): self
    {
        $this->contactId = $contactId;

        return $this;
    }

    public function getContactType(): ?string
    {
        return $this->contactType;
    }

    public function setContactType(?string $contactType): self
    {
        $this->contactType = $contactType;

        return $this;
    }
}
