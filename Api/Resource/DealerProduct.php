<?php

declare(strict_types=1);

namespace Dealer\Api\Resource;

use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use Dealer\Model\Map\DealerProductTableMap;
use Propel\Runtime\Map\TableMap;
use Symfony\Component\Serializer\Annotation\Groups;
use Thelia\Api\Bridge\Propel\Attribute\Relation;
use Thelia\Api\Bridge\Propel\Filter\SearchFilter;
use Thelia\Api\Resource\Product;
use Thelia\Api\Resource\PropelResourceInterface;
use Thelia\Api\Resource\PropelResourceTrait;

#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/front/dealer_products',
        ),
        new Get(
            uriTemplate: '/front/dealer_products/{id}',
            normalizationContext: ['groups' => [
                self::GROUP_FRONT_READ,
                self::GROUP_FRONT_READ_SINGLE,
                Dealer::GROUP_FRONT_READ,
            ]],
        ),
    ],
    normalizationContext: ['groups' => [self::GROUP_FRONT_READ]],
)]
#[ApiFilter(
    filterClass: SearchFilter::class,
    properties: [
        'id',
        'dealer.id',
    ],
)]
class DealerProduct implements PropelResourceInterface
{
    use PropelResourceTrait;

    public const string GROUP_FRONT_READ = 'front:dealer_product:read';
    public const string GROUP_FRONT_READ_SINGLE = 'front:dealer_product:read:single';

    #[Groups([self::GROUP_FRONT_READ, Dealer::GROUP_FRONT_READ_SINGLE])]
    public ?int $id = null;

    #[Relation(targetResource: Product::class)]
    #[Groups([self::GROUP_FRONT_READ, Dealer::GROUP_FRONT_READ_SINGLE])]
    public ?Product $product = null;

    #[Groups([self::GROUP_FRONT_READ, Dealer::GROUP_FRONT_READ_SINGLE])]
    public ?int $productId = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function setProduct(Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getProductId(): ?int
    {
        return $this->productId;
    }

    public function setProductId(?int $productId): self
    {
        $this->productId = $productId;

        return $this;
    }

    public static function getPropelRelatedTableMap(): ?TableMap
    {
        return new DealerProductTableMap();
    }
}
