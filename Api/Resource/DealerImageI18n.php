<?php

declare(strict_types=1);

namespace Dealer\Api\Resource;

use Symfony\Component\Serializer\Annotation\Groups;
use Thelia\Api\Resource\I18n;

class DealerImageI18n extends I18n
{
    #[Groups([DealerImage::GROUP_FRONT_READ])]
    protected ?string $title = null;

    #[Groups([DealerImage::GROUP_FRONT_READ])]
    protected ?string $description = null;

    #[Groups([DealerImage::GROUP_FRONT_READ])]
    protected ?string $chapo = null;

    #[Groups([DealerImage::GROUP_FRONT_READ])]
    protected ?string $postscriptum = null;

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

    public function getChapo(): ?string
    {
        return $this->chapo;
    }

    public function setChapo(?string $chapo): self
    {
        $this->chapo = $chapo;

        return $this;
    }

    public function getPostscriptum(): ?string
    {
        return $this->postscriptum;
    }

    public function setPostscriptum(?string $postscriptum): self
    {
        $this->postscriptum = $postscriptum;

        return $this;
    }
}
