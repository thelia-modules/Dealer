<?php namespace Dealer\Form\Type\Base;

use Thelia\Core\Form\Type\Field\AbstractIdType;
use Dealer\Model\DealerTabQuery;

class DealerTabIdType extends AbstractIdType
{
    const TYPE_NAME = "dealer_tab_id";

    protected function getQuery()
    {
        return new DealerTabQuery();
    }

    public function getName()
    {
        return static::TYPE_NAME;
    }
}
