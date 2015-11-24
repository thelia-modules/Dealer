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

use Dealer\Model\DealerContact;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class DealerContactEvent
 * @package Dealer\Event
 */
class DealerContactEvent extends Event
{
    /**
     * @var DealerContact
     */
    protected $dealer_contact;

    /**
     * @return DealerContact
     */
    public function getDealerContact()
    {
        return $this->dealer_contact;
    }

    /**
     * @param DealerContact $dealer_contact
     */
    public function setDealerContact($dealer_contact)
    {
        $this->dealer_contact = $dealer_contact;
    }
}