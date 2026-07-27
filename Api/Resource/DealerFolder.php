<?php

declare(strict_types=1);

namespace Dealer\Api\Resource;

use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use Dealer\Model\Map\DealerFolderTableMap;
use Propel\Runtime\Map\TableMap;
use Symfony\Component\Serializer\Annotation\Groups;
use Thelia\Api\Bridge\Propel\Attribute\Relation;
use Thelia\Api\Bridge\Propel\Filter\SearchFilter;
use Thelia\Api\Resource\Folder;
use Thelia\Api\Resource\PropelResourceInterface;
use Thelia\Api\Resource\PropelResourceTrait;

#[ApiResource(
    operations: [],
    normalizationContext: ['groups' => [self::GROUP_FRONT_READ]],
)]
class DealerFolder implements PropelResourceInterface
{
    use PropelResourceTrait;

    public const GROUP_FRONT_READ = 'front:dealer_folder:read';
    public const GROUP_FRONT_READ_SINGLE = 'front:dealer_folder:read:single';

    #[Groups([self::GROUP_FRONT_READ])]
    public ?int $id = null;

    #[Relation(targetResource: Folder::class)]
    #[Groups([self::GROUP_FRONT_READ, Dealer::GROUP_FRONT_READ_SINGLE])]
    public Folder $folder;

    #[Relation(targetResource: Dealer::class)]
    #[Groups([self::GROUP_FRONT_READ_SINGLE])]
    public Dealer $dealer;

    #[Groups([self::GROUP_FRONT_READ_SINGLE, Dealer::GROUP_FRONT_READ_SINGLE])]
    public ?int $folderId = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getFolder(): Folder
    {
        return $this->folder;
    }

    public function setFolder(Folder $folder): self
    {
        $this->folder = $folder;

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

    public function getFolderId(): ?int
    {
        return $this->folderId;
    }

    public function setFolderId(?int $folderId): self
    {
        $this->folderId = $folderId;
        return $this;
    }

    public static function getPropelRelatedTableMap(): ?TableMap
    {
        return new DealerFolderTableMap();
    }
}
