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

use Dealer\Model\Dealer;
use Thelia\Core\Event\ActionEvent;

/**
 * Class DealerEvent
 */
class DealerEvent extends ActionEvent
{
    /**
     * @var Dealer
     */
    protected $dealer;

    /**
     * @return Dealer
     */
    public function getDealer()
    {
        return $this->dealer;
    }

    /**
     * @param Dealer $dealer
     */
    public function setDealer($dealer)
    {
        $this->dealer = $dealer;
    }
}
