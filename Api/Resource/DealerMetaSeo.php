<?php

declare(strict_types=1);

namespace Dealer\Api\Resource;

use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use Dealer\Model\Map\DealerMetaSeoTableMap;
use Propel\Runtime\Map\TableMap;
use Symfony\Component\Serializer\Annotation\Groups;
use Thelia\Api\Bridge\Propel\Attribute\Relation;
use Thelia\Api\Bridge\Propel\Filter\SearchFilter;
use Thelia\Api\Resource\AbstractTranslatableResource;
use Thelia\Api\Resource\I18nCollection;
use Thelia\Api\Resource\Product;
use Thelia\Api\Resource\PropelResourceInterface;
use Thelia\Api\Resource\PropelResourceTrait;

#[ApiResource(
    operations: [],
    normalizationContext: ['groups' => [self::GROUP_FRONT_READ]],
)]
class DealerMetaSeo extends AbstractTranslatableResource
{
    use PropelResourceTrait;

    public const string GROUP_FRONT_READ = 'front:dealer_meta_seo:read';
    public const string GROUP_FRONT_READ_SINGLE = 'front:dealer_meta_seo:read:single';

    #[Groups([self::GROUP_FRONT_READ])]
    public ?int $id = null;

    #[Groups([self::GROUP_FRONT_READ, Dealer::GROUP_FRONT_READ_SINGLE])]
    public ?string $slug = null;

    #[Groups([self::GROUP_FRONT_READ, Dealer::GROUP_FRONT_READ_SINGLE])]
    public ?string $json = null;

    #[Groups([self::GROUP_FRONT_READ, Dealer::GROUP_FRONT_READ_SINGLE])]
    public I18nCollection $i18ns;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;
        return $this;
    }

    public function getJson(): ?string
    {
        return $this->json;
    }

    public function setJson(?string $json): self
    {
        $this->json = $json;
        return $this;
    }

    public static function getPropelRelatedTableMap(): ?TableMap
    {
        return new DealerMetaSeoTableMap();
    }

    public static function getI18nResourceClass(): string
    {
        return DealerMetaSeoI18n::class;
    }
}
