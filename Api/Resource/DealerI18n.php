<?php

declare(strict_types=1);

namespace Dealer\Api\Resource;

use Symfony\Component\Serializer\Annotation\Groups;
use Thelia\Api\Resource\I18n;

class DealerI18n extends I18n
{
    #[Groups([Dealer::GROUP_FRONT_READ])]
    protected ?string $title = null;

    #[Groups([Dealer::GROUP_FRONT_READ])]
    protected ?string $description = null;

    #[Groups([Dealer::GROUP_FRONT_READ])]
    protected ?string $access = null;

    #[Groups([Dealer::GROUP_FRONT_READ])]
    protected ?string $bigDescription = null;

    #[Groups([Dealer::GROUP_FRONT_READ])]
    protected ?string $hardOpenHour = null;

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getAccess(): ?string
    {
        return $this->access;
    }

    public function setAccess(?string $access): self
    {
        $this->access = $access;

        return $this;
    }

    public function getBigDescription(): ?string
    {
        return $this->bigDescription;
    }

    public function setBigDescription(?string $bigDescription): self
    {
        $this->bigDescription = $bigDescription;

        return $this;
    }

    public function getHardOpenHour(): ?string
    {
        return $this->hardOpenHour;
    }

    public function setHardOpenHour(?string $hardOpenHour): self
    {
        $this->hardOpenHour = $hardOpenHour;

        return $this;
    }
}
