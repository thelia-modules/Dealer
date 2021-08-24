<?php
/*************************************************************************************/
/*      This file is part of the Thelia package.                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : dev@thelia.net                                                       */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/
/*************************************************************************************/

namespace Dealer\Event;

use Dealer\Model\DealerContactInfo;
use Thelia\Core\Event\ActionEvent;

/**
 * Class DealerContactInfoEvent
 * @package Dealer\Event
 */
class DealerContactInfoEvent extends ActionEvent
{
    /**
     * @var DealerContactInfo
     */
    protected $dealer_contact_info;

    /**
     * @return DealerContactInfo
     */
    public function getDealerContactInfo()
    {
        return $this->dealer_contact_info;
    }

    /**
     * @param DealerContactInfo $dealer_contact_info
     */
    public function setDealerContactInfo($dealer_contact_info)
    {
        $this->dealer_contact_info = $dealer_contact_info;
    }
}
