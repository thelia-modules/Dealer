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

use Dealer\Model\DealerContent;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class DealerContentLinkEvent
 * @package Dealer\Event
 */
class DealerContentLinkEvent extends Event
{
    /** @var  DealerContent $dealerContentLink */
    protected $dealerContentLink;

    /**
     * @return DealerContent
     */
    public function getDealerContentLink()
    {
        return $this->dealerContentLink;
    }

    /**
     * @param DealerContent $dealerContentLink
     */
    public function setDealerContentLink($dealerContentLink)
    {
        $this->dealerContentLink = $dealerContentLink;
    }


}