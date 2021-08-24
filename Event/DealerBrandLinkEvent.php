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

use Dealer\Model\DealerBrand;
use Thelia\Core\Event\ActionEvent;

/**
 * Class DealerBrandLinkEvent
 * @package Dealer\Event
 */
class DealerBrandLinkEvent extends ActionEvent
{

    /**
     * @var DealerBrand $dealerBrandLink
     */
    protected $dealerBrandLink;

    /**
     * @return DealerBrand
     */
    public function getDealerBrandLink()
    {
        return $this->dealerBrandLink;
    }

    /**
     * @param DealerBrand $dealerBrandLink
     */
    public function setDealerBrandLink($dealerBrandLink)
    {
        $this->dealerBrandLink = $dealerBrandLink;
    }
}
