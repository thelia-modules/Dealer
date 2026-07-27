<?php

declare(strict_types=1);

namespace Dealer\Api\Resource;

use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use Dealer\Model\Map\DealerContactInfoTableMap;
use Propel\Runtime\Map\TableMap;
use Symfony\Component\Serializer\Annotation\Groups;
use Thelia\Api\Bridge\Propel\Attribute\Relation;
use Thelia\Api\Bridge\Propel\Filter\OrderFilter;
use Thelia\Api\Bridge\Propel\Filter\SearchFilter;
use Thelia\Api\Resource\AbstractTranslatableResource;
use Thelia\Api\Resource\I18nCollection;

#[ApiResource(
    operations: [],
    normalizationContext: ['groups' => [self::GROUP_FRONT_READ]],
)]
class DealerContactInfo extends AbstractTranslatableResource
{
    public const string GROUP_FRONT_READ = 'front:dealer_contact_info:read';
    public const string GROUP_FRONT_READ_SINGLE = 'front:dealer_contact_info:read:single';

    #[Groups([self::GROUP_FRONT_READ])]
    public ?int $id = null;

    #[Groups([self::GROUP_FRONT_READ])]
    public ?string $contactType = null;

    #[Groups([self::GROUP_FRONT_READ])]
    public ?\DateTime $createdAt = null;

    #[Groups([self::GROUP_FRONT_READ])]
    public ?\DateTime $updatedAt = null;

    #[Groups([self::GROUP_FRONT_READ])]
    public I18nCollection $i18ns;

    #[Relation(targetResource: DealerContact::class)]
    #[Groups([self::GROUP_FRONT_READ_SINGLE])]
    public DealerContact $contact;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;

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

    public function getContact(): DealerContact
    {
        return $this->contact;
    }

    public function setContact(DealerContact $contact): self
    {
        $this->contact = $contact;

        return $this;
    }

    public static function getPropelRelatedTableMap(): ?TableMap
    {
        return new DealerContactInfoTableMap();
    }

    public static function getI18nResourceClass(): string
    {
        return DealerContactInfoI18n::class;
    }
}
