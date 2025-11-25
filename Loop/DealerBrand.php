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
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Thelia\Core\Template\Loop\Argument\Argument;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;
use Thelia\Core\Template\Loop\Brand;
use Thelia\Model\Map\BrandTableMap;

/**
 * Class BrandLoop
 * @package Dealer\Loop
 */
class DealerBrand extends Brand
{
    /**
     * @inheritDoc
     */
    protected function getArgDefinitions(): ArgumentCollection
    {
        /** @var ArgumentCollection $arguments */
        $arguments = parent::getArgDefinitions();

        $arguments->addArgument(
            Argument::createIntListTypeArgument("dealer_id")
        );

        return $arguments;
    }

    public function buildModelCriteria(): ModelCriteria
    {
        $query = parent::buildModelCriteria();

        if (null != $id = $this->getDealerId()) {
            if (is_array($id)) {
                $id = implode(",", $id);
            }

            $dealerJoin = new Join(BrandTableMap::COL_ID, DealerBrandTableMap::COL_BRAND_ID, Criteria::LEFT_JOIN);
            $query
                ->addJoinObject($dealerJoin, "dealerJoin")
                ->where(DealerBrandTableMap::COL_DEALER_ID . " " . Criteria::IN . " (" . $id . ")");
        }

        return $query;
    }
}
