<?php

declare(strict_types=1);

namespace Dealer\Service\Admin;

enum DealerVisibilityFilter: string
{
    case Online = 'online';
    case Offline = 'offline';

    public function storedValue(): int
    {
        return $this === self::Online ? 1 : 0;
    }
}
