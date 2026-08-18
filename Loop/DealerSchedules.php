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

use Dealer\Dealer;
use Dealer\Model\DealerShedules;
use Dealer\Model\DealerShedulesQuery;
use Dealer\Model\Map\DealerShedulesTableMap;
use Propel\Runtime\ActiveQuery\Criteria;
use Thelia\Core\Template\Element\BaseLoop;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Element\LoopResultRow;
use Thelia\Core\Template\Element\PropelSearchLoopInterface;
use Thelia\Core\Template\Loop\Argument\Argument;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;

/**
 * Class ShedulesLoop
 * @package Dealer\Loop
 */
class DealerSchedules extends BaseLoop implements PropelSearchLoopInterface
{

    /**
     * @param LoopResult $loopResult
     *
     * @return LoopResult
     */
    public function parseResults(LoopResult $loopResult): LoopResult
    {
        /** @var DealerShedules $schedules */
        foreach ($loopResult->getResultDataCollection() as $schedules) {
            $loopResultRow = new LoopResultRow($schedules);

            $loopResultRow
                ->set('ID', $schedules->getId())
                ->set('DEALER_ID', $schedules->getDealerId())
                ->set('DAY', $schedules->getDay())
                // An exceptional entry may target a date without any weekday.
                ->set('DAY_LABEL', $schedules->getDay() === null ? null : $this->getDayLabel($schedules->getDay()))
                ->set('BEGIN', $schedules->getBegin())
                ->set('END', $schedules->getEnd())
                ->set('PERIOD_BEGIN', $schedules->getPeriodBegin())
                ->set('PERIOD_END', $schedules->getPeriodEnd())
                ->set('CLOSED', $schedules->getClosed())
                ->set('EXCEPTION', $schedules->getException())
                ->set('RECURRING', $schedules->getRecurring())
                ->set('TITLE', $schedules->getTitle());


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
    protected function getArgDefinitions(): ArgumentCollection
    {
        return new ArgumentCollection(

            Argument::createIntListTypeArgument('id'),
            Argument::createIntListTypeArgument('dealer_id'),
            Argument::createBooleanTypeArgument('default_period'),
            Argument::createBooleanTypeArgument('hide_past', false),
            Argument::createBooleanTypeArgument('closed', false),
            Argument::createIntListTypeArgument('day'),
            Argument::createEnumListTypeArgument('order', [
                'id',
                'id-reverse',
                'day',
                'day-reverse',
                'begin',
                'begin-reverse',
                'period-begin',
                'period-begin-reverse'
            ], 'id')

        );
    }

    /**
     * this method returns a Propel ModelCriteria
     *
     * @return \Propel\Runtime\ActiveQuery\ModelCriteria
     */
    public function buildModelCriteria(): \Propel\Runtime\ActiveQuery\ModelCriteria
    {
        $query = DealerShedulesQuery::create();

        if ($id = $this->getId()) {
            $query->filterById($id);
        }

        if ($day = $this->getDay()) {
            $query->filterByDay($day);
        }

        if ($dealer_id = $this->getDealerId()) {
            $query->filterByDealerId($dealer_id);
        }

        // default_period selects the base weekly hours, its opposite the exceptional
        // entries: the distinction is carried by the `exception` column, not by the
        // presence of a period (an exceptional entry may have no period bound at all).
        if ($this->getDefaultPeriod()) {
            $query->filterByException(false);
        } else {
            $query->filterByException(true);
            if ($this->getHidePast()) {
                // An entry is still relevant when its period has no end (open-ended) or
                // when it recurs every year; only dated entries already over are hidden.
                $query
                    ->condition(
                        'period_end_future',
                        DealerShedulesTableMap::COL_PERIOD_END . ' > ?',
                        (new \DateTime())->format('Y-m-d H:i:s'),
                        \PDO::PARAM_STR
                    )
                    ->condition('period_end_open', DealerShedulesTableMap::COL_PERIOD_END . ' ' . Criteria::ISNULL)
                    ->condition('period_recurring', DealerShedulesTableMap::COL_RECURRING . ' = ?', true, \PDO::PARAM_BOOL)
                    ->combine(
                        ['period_end_future', 'period_end_open', 'period_recurring'],
                        Criteria::LOGICAL_OR
                    );
            }
        }

        $query->filterByClosed($this->getClosed());

        foreach ($this->getOrder() as $order) {
            switch ($order) {
                case 'id':
                    $query->orderById();
                    break;
                case 'id-reverse':
                    $query->orderById(Criteria::DESC);
                    break;
                case 'day':
                    $query->orderByDay();
                    break;
                case 'day-reverse':
                    $query->orderByDay(Criteria::DESC);
                    break;
                case 'begin':
                    $query->orderByBegin();
                    break;
                case 'begin-reverse':
                    $query->orderByBegin(Criteria::DESC);
                    break;
                case 'period-begin':
                    $query->orderByPeriodBegin();
                    break;
                case 'period-begin-reverse':
                    $query->orderByPeriodBegin(Criteria::DESC);
                    break;
                default:
                    break;
            }
        }

        return $query;
    }

    protected function getDayLabel($int = 0)
    {
        return [
            $this->translator->trans("Monday", [], Dealer::MESSAGE_DOMAIN),
            $this->translator->trans("Tuesday", [], Dealer::MESSAGE_DOMAIN),
            $this->translator->trans("Wednesday", [], Dealer::MESSAGE_DOMAIN),
            $this->translator->trans("Thursday", [], Dealer::MESSAGE_DOMAIN),
            $this->translator->trans("Friday", [], Dealer::MESSAGE_DOMAIN),
            $this->translator->trans("Saturday", [], Dealer::MESSAGE_DOMAIN),
            $this->translator->trans("Sunday", [], Dealer::MESSAGE_DOMAIN)
        ][$int];
    }
}
