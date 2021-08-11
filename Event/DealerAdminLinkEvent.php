<?php
/**
 * Created by PhpStorm.
 * User: apenalver
 * Date: 25/03/2016
 * Time: 11:02
 */

namespace Dealer\Event;

use Dealer\Model\DealerAdmin;
use Thelia\Core\Event\ActionEvent;

/**
 * Class DealerAdminLinkEvent
 * @package Dealer\Event
 */
class DealerAdminLinkEvent extends ActionEvent
{
    /**
     * @var DealerAdmin $dealerAdminLink
     */
    protected $dealerAdminLink;

    /**
     * @return DealerAdmin
     */
    public function getDealerAdminLink()
    {
        return $this->dealerAdminLink;
    }

    /**
     * @param DealerAdmin $dealerAdminLink
     */
    public function setDealerAdminLink($dealerAdminLink)
    {
        $this->dealerAdminLink = $dealerAdminLink;
    }
}
