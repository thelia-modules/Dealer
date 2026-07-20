<?php

declare(strict_types=1);

namespace Dealer\Api\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use Dealer\Model\Map\DealerImageTableMap;
use Propel\Runtime\Map\TableMap;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Serializer\Annotation\Groups;
use Thelia\Api\Bridge\Propel\Attribute\Relation;
use Thelia\Api\Resource\AbstractTranslatableResource;
use Thelia\Api\Resource\I18nCollection;
use Thelia\Api\Resource\ItemFileResourceInterface;

#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/front/dealer_images/{id}',
            normalizationContext: ['groups' => [self::GROUP_FRONT_READ, self::GROUP_FRONT_READ_SINGLE]],
        ),
    ],
    normalizationContext: ['groups' => [self::GROUP_FRONT_READ]],
)]
class DealerImage extends AbstractTranslatableResource implements ItemFileResourceInterface
{
    public const GROUP_FRONT_READ = 'front:dealer_image:read';
    public const GROUP_FRONT_READ_SINGLE = 'front:dealer_image:read:single';

    #[Groups([self::GROUP_FRONT_READ])]
    public ?int $id = null;

    #[Groups([self::GROUP_FRONT_READ])]
    public ?int $type = null;

    #[Groups([self::GROUP_FRONT_READ])]
    public string $file;

    #[Groups([self::GROUP_FRONT_READ])]
    public ?string $fileUrl = null;

    #[Groups([self::GROUP_FRONT_READ])]
    public ?\DateTime $createdAt = null;

    #[Groups([self::GROUP_FRONT_READ])]
    public ?\DateTime $updatedAt = null;

    #[Groups([self::GROUP_FRONT_READ])]
    public I18nCollection $i18ns;

    public UploadedFile $fileToUpload;

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

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(?int $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getFile(): string
    {
        return $this->file;
    }

    public function setFile(string $file): self
    {
        $this->file = $file;

        return $this;
    }

    public function getFileUrl(): ?string
    {
        return $this->fileUrl;
    }

    public function setFileUrl(?string $fileUrl): self
    {
        $this->fileUrl = $fileUrl;

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

    public function getFileToUpload(): UploadedFile
    {
        return $this->fileToUpload;
    }

    public function setFileToUpload(UploadedFile $fileToUpload): self
    {
        $this->fileToUpload = $fileToUpload;

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
        return new DealerImageTableMap();
    }

    public static function getI18nResourceClass(): string
    {
        return DealerImageI18n::class;
    }

    public static function getItemType(): string
    {
        return 'dealer';
    }

    public static function getFileType(): string
    {
        return 'image';
    }

    public function getItemId(): string
    {
        return isset($this->dealer) ? (string) $this->dealer->getId() : '';
    }
}
