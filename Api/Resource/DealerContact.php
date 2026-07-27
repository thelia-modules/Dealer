<?php

declare(strict_types=1);

namespace Dealer\Api\Resource;

use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use Dealer\Model\Map\DealerContactTableMap;
use Propel\Runtime\Map\TableMap;
use Symfony\Component\Serializer\Annotation\Groups;
use Thelia\Api\Bridge\Propel\Attribute\Relation;
use Thelia\Api\Bridge\Propel\Filter\BooleanFilter;
use Thelia\Api\Bridge\Propel\Filter\OrderFilter;
use Thelia\Api\Bridge\Propel\Filter\SearchFilter;
use Thelia\Api\Resource\AbstractTranslatableResource;
use Thelia\Api\Resource\I18nCollection;

#[ApiResource(
    operations: [],
    normalizationContext: ['groups' => [self::GROUP_FRONT_READ]],
)]
#[ApiFilter(
    filterClass: BooleanFilter::class,
    properties: [
        'isDefault',
    ],
)]
#[ApiFilter(
    filterClass: OrderFilter::class,
    properties: [
        'id',
    ],
)]
class DealerContact extends AbstractTranslatableResource
{
    public const string GROUP_FRONT_READ = 'front:dealer_contact:read';
    public const string GROUP_FRONT_READ_SINGLE = 'front:dealer_contact:read:single';

    #[Groups([self::GROUP_FRONT_READ])]
    public ?int $id = null;

    #[Groups([self::GROUP_FRONT_READ])]
    public bool $isDefault;

    #[Groups([self::GROUP_FRONT_READ])]
    public ?\DateTime $createdAt = null;

    #[Groups([self::GROUP_FRONT_READ])]
    public ?\DateTime $updatedAt = null;

    #[Groups([self::GROUP_FRONT_READ])]
    public I18nCollection $i18ns;

    #[Relation(targetResource: Dealer::class)]
    #[Groups([self::GROUP_FRONT_READ_SINGLE])]
    public Dealer $dealer;

    #[Relation(targetResource: DealerContactInfo::class, relationAlias: 'dealerContactInfos')]
    #[Groups([self::GROUP_FRONT_READ_SINGLE, Dealer::GROUP_FRONT_READ_SINGLE])]
    public array $infos = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function isDefault(): bool
    {
        return $this->isDefault;
    }

    public function setIsDefault(bool $isDefault): self
    {
        $this->isDefault = $isDefault;

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

    public function getDealer(): Dealer
    {
        return $this->dealer;
    }

    public function setDealer(Dealer $dealer): self
    {
        $this->dealer = $dealer;

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

    public static function getPropelRelatedTableMap(): ?TableMap
    {
        return new DealerContactTableMap();
    }

    public static function getI18nResourceClass(): string
    {
        return DealerContactI18n::class;
    }
}
