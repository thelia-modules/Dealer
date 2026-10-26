<?php

declare(strict_types=1);

namespace Dealer\Api\Resource;

use Symfony\Component\Serializer\Annotation\Groups;
use Thelia\Api\Resource\I18n;

class DealerContactI18n extends I18n
{
    #[Groups([DealerContact::GROUP_ADMIN_READ, DealerContact::GROUP_ADMIN_WRITE, Dealer::GROUP_ADMIN_READ_SINGLE])]
    protected ?string $label = null;

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(?string $label): self
    {
        $this->label = $label;

        return $this;
    }
}
