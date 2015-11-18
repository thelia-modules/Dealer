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

use Dealer\Model\Dealer;
use Dealer\Model\DealerQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Thelia\Core\Template\Element\BaseI18nLoop;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Element\LoopResultRow;
use Thelia\Core\Template\Element\PropelSearchLoopInterface;
use Thelia\Core\Template\Loop\Argument\Argument;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;

/**
 * Class DealerLoop
 * @package Dealer\Loop
 */
class DealerLoop extends BaseI18nLoop implements PropelSearchLoopInterface
{

    /**
     * @param LoopResult $loopResult
     *
     * @return LoopResult
     */
    public function parseResults(LoopResult $loopResult)
    {
        /** @var Dealer $dealer */
        foreach ($loopResult->getResultDataCollection() as $dealer) {
            $loopResultRow = new LoopResultRow($dealer);

            $loopResultRow
                ->set('ID', $dealer->getId())
                ->set("ADDRESS1", $dealer->getAddress1())
                ->set("ADDRESS2", $dealer->getAddress2())
                ->set("ADDRESS3", $dealer->getAddress3())
                ->set("ZIPCODE", $dealer->getZipcode())
                ->set("CITY", $dealer->getCity())
                ->set("COUNTRY_ID", $dealer->getCountryId())
                ->set("LAT", $dealer->getLatitude())
                ->set("LON", $dealer->getLongitude());

            if ($dealer->hasVirtualColumn('i18n_TITLE')) {
                $loopResultRow->set("TITLE", $dealer->getVirtualColumn('i18n_TITLE'));
            }

            if ($dealer->hasVirtualColumn('i18n_DESCRIPTION')) {
                $loopResultRow->set("DESCRIPTION", $dealer->getVirtualColumn('i18n_DESCRIPTION'));
            }

            $loopResult->addRow($loopResultRow);
        }

        return $loopResult;
    }

    /**
     * @inheritdoc
     */
    protected function getArgDefinitions()
    {
        return new ArgumentCollection(
            Argument::createIntListTypeArgument('id'),
            Argument::createIntListTypeArgument('country_id'),
            Argument::createAnyListTypeArgument('city'),
            Argument::createEnumListTypeArgument('order', [
                'id',
                'id-reverse',
                'date',
                'date-reverse'
            ])
        );
    }

    /**
     * @inheritdoc
     */
    public function buildModelCriteria()
    {
        $query = DealerQuery::create();

        // manage translations
        $this->configureI18nProcessing(
            $query,
            [
                'TITLE',
                'DESCRIPTION'
            ]
        );

        if ($id = $this->getId()) {
            $query->filterById($id);
        }

        if ($country_id = $this->getCountryId()) {
            $query->filterByCountryId($country_id);
        }

        if ($city = $this->getCity()) {
            $query->filterByCity($city);
        }

        foreach ($this->getOrder() as $order) {
            switch ($order) {
                case 'id' :
                    $query->orderById();
                    break;
                case 'id-reverse' :
                    $query->orderById(Criteria::DESC);
                    break;
                case 'date' :
                    $query->orderByCreatedAt();
                    break;
                case 'date-reverse' :
                    $query->orderByCreatedAt(Criteria::DESC);
                    break;
                default:
                    break;
            }
        }

        return $query;
    }
}