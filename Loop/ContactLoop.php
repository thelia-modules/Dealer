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

use Dealer\Model\DealerContact;
use Dealer\Model\DealerContactQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Thelia\Core\Template\Element\BaseI18nLoop;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Element\LoopResultRow;
use Thelia\Core\Template\Element\PropelSearchLoopInterface;
use Thelia\Core\Template\Loop\Argument\Argument;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;

/**
 * Class ContactLoop
 * @package Dealer\Loop
 */
class ContactLoop extends BaseI18nLoop implements PropelSearchLoopInterface
{

    /**
     * @param LoopResult $loopResult
     *
     * @return LoopResult
     */
    public function parseResults(LoopResult $loopResult)
    {
        /** @var DealerContact $contact */
        foreach ($loopResult->getResultDataCollection() as $contact) {
            $loopResultRow = new LoopResultRow($contact);

            $loopResultRow
                ->set('ID', $contact->getId())
                ->set('DEALER_ID', $contact->getDealerId())
                ->set('IS_DEFAULT', $contact->getIsDefault())
            ;

            if ($contact->hasVirtualColumn('i18n_LABEL')) {
                $loopResultRow->set("LABEL", $contact->getVirtualColumn('i18n_LABEL'));
            }

            $loopResult->addRow($loopResultRow);
        }

        return $loopResult;
    }

    /**
     * Definition of loop arguments
     *
     * example :
     *
     * public function getArgDefinitions()
     * {
     *  return new ArgumentCollection(
     *
     *       Argument::createIntListTypeArgument('id'),
     *           new Argument(
     *           'ref',
     *           new TypeCollection(
     *               new Type\AlphaNumStringListType()
     *           )
     *       ),
     *       Argument::createIntListTypeArgument('category'),
     *       Argument::createBooleanTypeArgument('new'),
     *       ...
     *   );
     * }
     *
     * @return \Thelia\Core\Template\Loop\Argument\ArgumentCollection
     */
    protected function getArgDefinitions()
    {
        return new ArgumentCollection(

            Argument::createIntListTypeArgument('id'),
            Argument::createIntListTypeArgument('dealer_id'),
            Argument::createEnumListTypeArgument('order', [
                'id',
                'id-reverse',
                'label',
                'label-reverse',
            ], 'id')

        );
    }

    /**
     * this method returns a Propel ModelCriteria
     *
     * @return \Propel\Runtime\ActiveQuery\ModelCriteria
     */
    public function buildModelCriteria()
    {
        $query = DealerContactQuery::create();

        // manage translations
        $this->configureI18nProcessing(
            $query,
            [
                'LABEL',
            ],
            null,
            'ID',
            $this->getForceReturn()
        );

        if($id = $this->getId()){
            $query->filterById($id);
        }

        if($dealer_id = $this->getDealerId()){
            $query->filterByDealerId($dealer_id);
        }

        foreach($this->getOrder() as $order){
            switch($order){
                case 'id':
                    $query->orderById();
                    break;
                case 'id-reverse':
                    $query->orderById(Criteria::DESC);
                    break;
                case 'label':
                    $query->orderByLabel();
                    break;
                case 'label-reverse':
                    $query->orderByLabel(Criteria::DESC);
                    break;
                default:break;
            }
        }

        return $query;
    }
}