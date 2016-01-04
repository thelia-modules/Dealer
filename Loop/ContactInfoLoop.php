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

use Dealer\Model\Base\DealerContactInfoQuery;
use Dealer\Model\DealerContactInfo;
use Dealer\Model\Map\DealerContactInfoTableMap;
use Propel\Runtime\ActiveQuery\Criteria;
use Thelia\Core\Template\Element\BaseI18nLoop;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Element\LoopResultRow;
use Thelia\Core\Template\Element\PropelSearchLoopInterface;
use Thelia\Core\Template\Loop\Argument\Argument;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;

/**
 * Class ContactInfoLoop
 * @package Dealer\Loop
 */
class ContactInfoLoop extends BaseI18nLoop implements PropelSearchLoopInterface
{

    /**
     * @param LoopResult $loopResult
     *
     * @return LoopResult
     */
    public function parseResults(LoopResult $loopResult)
    {
        /** @var DealerContactInfo $contact */
        foreach ($loopResult->getResultDataCollection() as $contact) {
            $loopResultRow = new LoopResultRow($contact);

            $loopResultRow
                ->set('ID', $contact->getId())
                ->set('CONTACT_ID', $contact->getContactId())
                ->set('CONTACT_TYPE', $contact->getContactType())
                ->set('CONTACT_TYPE_ID', $contact->getContactTypeId());

            if ($contact->hasVirtualColumn('i18n_VALUE')) {
                $loopResultRow->set("VALUE", $contact->getVirtualColumn('i18n_VALUE'));
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
            Argument::createIntListTypeArgument('contact_id'),
            Argument::createEnumListTypeArgument('contact_type', DealerContactInfoTableMap::getValueSet(DealerContactInfoTableMap::CONTACT_TYPE),null),
            Argument::createEnumListTypeArgument('order', [
                'id',
                'id-reverse',
                'value',
                'value-reverse',
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
        $query = DealerContactInfoQuery::create();

        $this->configureI18nProcessing(
            $query,
            [
                'VALUE',
            ],
            null,
            'ID',
            $this->getForceReturn()
        );

        if ($id = $this->getId()) {
            $query->filterById($id);
        }

        if(null != $type = $this->getContactType()){
            $query->filterByContactType($type);
        }

        if ($contact_id = $this->getContactId()) {
            $query->filterByContactId($contact_id);
        }

        foreach ($this->getOrder() as $order) {
            switch ($order) {
                case 'id':
                    $query->orderById();
                    break;
                case 'id-reverse':
                    $query->orderById(Criteria::DESC);
                    break;
                case 'value':
                    $query->orderByValue();
                    break;
                case 'value-reverse':
                    $query->orderByValue(Criteria::DESC);
                    break;
                default:
                    break;
            }
        }

        return $query;
    }
}