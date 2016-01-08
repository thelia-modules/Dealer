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

namespace Dealer\Loop;

use Dealer\Model\Map\DealerBrandTableMap;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\Join;
use Thelia\Core\Template\Loop\Argument\Argument;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;
use Thelia\Core\Template\Loop\Brand;
use Thelia\Model\Map\BrandTableMap;

/**
 * Class BrandLoop
 * @package Dealer\Loop
 */
class BrandLoop extends Brand
{
    /**
     * @inheritDoc
     */
    protected function getArgDefinitions()
    {
        /** @var ArgumentCollection $arguments */
        $arguments = parent::getArgDefinitions();

        $arguments->addArgument(
            Argument::createIntListTypeArgument("dealer_id")
        );

        return $arguments;
    }

    public function buildModelCriteria()
    {
        $query = parent::buildModelCriteria();

        if (null != $id = $this->getDealerId()) {
            if (is_array($id)) {
                $id = implode(",", $id);
            }

            $dealerJoin = new Join(BrandTableMap::ID, DealerBrandTableMap::BRAND_ID, Criteria::LEFT_JOIN);
            $query
                ->addJoinObject($dealerJoin, "dealerJoin")
                ->where(DealerBrandTableMap::DEALER_ID . " " . Criteria::IN . " (" . $id . ")");

        }

        return $query;
    }
}