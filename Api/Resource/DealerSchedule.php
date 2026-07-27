<?php

declare(strict_types=1);

namespace Dealer\Api\Resource;

use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use Dealer\Model\Map\DealerShedulesTableMap;
use Propel\Runtime\Map\TableMap;
use Symfony\Component\Serializer\Annotation\Groups;
use Thelia\Api\Bridge\Propel\Attribute\Relation;
use Thelia\Api\Bridge\Propel\Filter\BooleanFilter;
use Thelia\Api\Bridge\Propel\Filter\OrderFilter;
use Thelia\Api\Bridge\Propel\Filter\SearchFilter;
use Thelia\Api\Resource\PropelResourceInterface;
use Thelia\Api\Resource\PropelResourceTrait;

#[ApiResource(
    operations: [],
    normalizationContext: ['groups' => [self::GROUP_FRONT_READ]],
)]
class DealerSchedule implements PropelResourceInterface
{
    use PropelResourceTrait;

    public const GROUP_FRONT_READ = 'front:dealer_schedule:read';
    public const GROUP_FRONT_READ_SINGLE = 'front:dealer_schedule:read:single';

    #[Groups([self::GROUP_FRONT_READ])]
    public ?int $id = null;

    #[Groups([self::GROUP_FRONT_READ])]
    public ?int $day = null;

    #[Groups([self::GROUP_FRONT_READ])]
    public ?\DateTime $begin = null;

    #[Groups([self::GROUP_FRONT_READ])]
    public ?\DateTime $end = null;

    #[Groups([self::GROUP_FRONT_READ])]
    public ?bool $closed = null;

    #[Groups([self::GROUP_FRONT_READ])]
    public ?\DateTime $periodBegin = null;

    #[Groups([self::GROUP_FRONT_READ])]
    public ?\DateTime $periodEnd = null;

    #[Groups([self::GROUP_FRONT_READ])]
    public ?\DateTime $createdAt = null;

    #[Groups([self::GROUP_FRONT_READ])]
    public ?\DateTime $updatedAt = null;

    #[Relation(targetResource: Dealer::class)]
    #[Groups([self::GROUP_FRONT_READ_SINGLE])]
    public Dealer $dealer;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getDay(): ?int
    {
        return $this->day;
    }

    public function setDay(?int $day): self
    {
        $this->day = $day;

        return $this;
    }

    public function getBegin(): ?\DateTime
    {
        return $this->begin;
    }

    public function setBegin(?\DateTime $begin): self
    {
        $this->begin = $begin;

        return $this;
    }

    public function getEnd(): ?\DateTime
    {
        return $this->end;
    }

    public function setEnd(?\DateTime $end): self
    {
        $this->end = $end;

        return $this;
    }

    public function getClosed(): ?bool
    {
        return $this->closed;
    }

    public function setClosed(?bool $closed): self
    {
        $this->closed = $closed;

        return $this;
    }

    public function getPeriodBegin(): ?\DateTime
    {
        return $this->periodBegin;
    }

    public function setPeriodBegin(?\DateTime $periodBegin): self
    {
        $this->periodBegin = $periodBegin;

        return $this;
    }

    public function getPeriodEnd(): ?\DateTime
    {
        return $this->periodEnd;
    }

    public function setPeriodEnd(?\DateTime $periodEnd): self
    {
        $this->periodEnd = $periodEnd;

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

    public static function getPropelRelatedTableMap(): ?TableMap
    {
        return new DealerShedulesTableMap();
    }
}
