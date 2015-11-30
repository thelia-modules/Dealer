<?php

namespace Dealer\Model;

use Dealer\Model\Base\DealerContactInfo as BaseDealerContactInfo;

class DealerContactInfo extends BaseDealerContactInfo
{
    public function getContactTypeId(){
        return $this->contact_type;
    }
}
