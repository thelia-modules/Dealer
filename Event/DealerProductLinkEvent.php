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

use Dealer\Model\DealerProduct;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class DealerProductLinkEvent
 * @package Dealer\Event
 */
class DealerProductLinkEvent extends Event
{

    /**
     * @var DealerProduct $dealerProductLink
     */
    protected $dealerProductLink;

    /**
     * @return DealerProduct
     */
    public function getDealerProductLink()
    {
        return $this->dealerProductLink;
    }

    /**
     * @param DealerProduct $dealerProductLink
     */
    public function setDealerProductLink($dealerProductLink)
    {
        $this->dealerProductLink = $dealerProductLink;
    }


}