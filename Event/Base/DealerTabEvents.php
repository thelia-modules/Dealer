<?php namespace Dealer\Event\Base;

use Dealer\Event\Module\DealerEvents as ChildDealerEvents;

class DealerTabEvents
{
    const CREATE = ChildDealerEvents::DEALER_TAB_CREATE;
    const UPDATE = ChildDealerEvents::DEALER_TAB_UPDATE;
    const DELETE = ChildDealerEvents::DEALER_TAB_DELETE;
}
