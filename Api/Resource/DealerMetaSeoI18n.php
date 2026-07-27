<?php

declare(strict_types=1);

namespace Dealer\Api\Resource;

use Symfony\Component\Serializer\Annotation\Groups;
use Thelia\Api\Resource\I18n;

class DealerMetaSeoI18n extends I18n
{
    #[Groups([DealerMetaSeo::GROUP_FRONT_READ, Dealer::GROUP_FRONT_READ_SINGLE])]
    protected ?string $metaTitle = null;

    #[Groups([DealerMetaSeo::GROUP_FRONT_READ, Dealer::GROUP_FRONT_READ_SINGLE])]
    protected ?string $metaDescription = null;

    #[Groups([DealerMetaSeo::GROUP_FRONT_READ, Dealer::GROUP_FRONT_READ_SINGLE])]
    protected ?string $metaKeywords = null;

    public function getMetaTitle(): ?string
    {
        return $this->metaTitle;
    }

    public function setMetaTitle(?string $metaTitle): self
    {
        $this->metaTitle = $metaTitle;

        return $this;
    }

    public function getMetaDescription(): ?string
    {
        return $this->metaDescription;
    }

    public function setMetaDescription(?string $metaDescription): self
    {
        $this->metaDescription = $metaDescription;

        return $this;
    }

    public function getMetaKeywords(): ?string
    {
        return $this->metaKeywords;
    }

    public function setMetaKeywords(?string $metaKeywords): self
    {
        $this->metaKeywords = $metaKeywords;

        return $this;
    }
}
