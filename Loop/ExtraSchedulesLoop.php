<?php
/**
 * Created by PhpStorm.
 * User: tompradat
 * Date: 01/07/2016
 * Time: 16:18
 */

namespace Dealer\Loop;


use Dealer\Dealer;
use Dealer\Model\DealerShedulesQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Thelia\Core\Template\Element\ArraySearchLoopInterface;
use Thelia\Core\Template\Element\BaseLoop;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Element\LoopResultRow;
use Thelia\Core\Template\Loop\Argument\Argument;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;

class ExtraSchedulesLoop extends BaseLoop implements ArraySearchLoopInterface
{

    public function parseResults(LoopResult $loopResult)
    {
        foreach ($loopResult->getResultDataCollection() as $schedules) {
            $loopResultRow = new LoopResultRow($schedules);

            $loopResultRow
                ->set('DEALER_ID', $schedules['DEALER_ID'])
                ->set('DAY', $schedules['DAY'])
                ->set('DAY_LABEL', $schedules['DAY_LABEL'])
                ->set('FORMATTED_HOURS', $schedules['FORMATTED_HOURS'])
                ->set('PERIOD_BEGIN', $schedules['PERIOD_BEGIN'])
                ->set('PERIOD_END', $schedules['PERIOD_END'])
                ->set('BEGIN', $schedules['BEGIN'])
                ->set('END', $schedules['END'])
                ->set('ID', $schedules['ID'])
            ;


            $loopResult->addRow($loopResultRow);
        }

        return $loopResult;
    }

    protected function getArgDefinitions()
    {
        return new ArgumentCollection(

            Argument::createIntListTypeArgument('id'),
            Argument::createIntListTypeArgument('dealer_id'),
            Argument::createBooleanTypeArgument('hide_past', false),
            Argument::createBooleanTypeArgument('closed', false),
            Argument::createAnyTypeArgument('hour_separator', ' - '),
            Argument::createAnyTypeArgument('half_day_separator', ' / '),
            Argument::createBooleanTypeArgument('merge_day', true),
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

    public function buildArray()
    {
        $results = array();

        $query = DealerShedulesQuery::create();

        $query->filterByPeriodNotNull();
        if ($this->getHidePast()) {
            $query->filterByPeriodEnd((new \DateTime())->add(\DateInterval::createFromDateString('yesterday')), Criteria::GREATER_THAN);
        }

        if ($id = $this->getId()) {
            $query->filterById($id);
        }

        if ($day = $this->getDay()) {
            $query->filterByDay($day);
        }

        if ($dealer_id = $this->getDealerId()) {
            $query->filterByDealerId($dealer_id);
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
        
        if ($this->getMergeDay()) {
            $query->orderByBegin();
        }

        $dealerSchedules = $query->find();

        if ($this->getMergeDay()) {
            $dealerCount = count($dealerSchedules);

            for ($i = 0; $i < $dealerCount; $i++) {

                if (isset($dealerSchedules[$i])) {

                    $formattedHours = null;

                    // if the next result has the same dates, same day, then concat the morning and afternoon hours
                    if (
                        ($dealerSchedules[$i+1] !== null)
                        && ($dealerSchedules[$i]->getDay() == $dealerSchedules[$i+1]->getDay())
                        && ($dealerSchedules[$i]->getDealerId() == $dealerSchedules[$i+1]->getDealerId())
                        && ($dealerSchedules[$i]->getPeriodBegin() == $dealerSchedules[$i+1]->getPeriodBegin())
                        && ($dealerSchedules[$i]->getPeriodEnd() == $dealerSchedules[$i+1]->getPeriodEnd())
                    ) {
                        $end = $dealerSchedules[$i+1]->getEnd();
                        if ($dealerSchedules[$i]->getEnd()->format('H\hi') === $dealerSchedules[$i+1]->getBegin()->format('H\hi')) {
                            $formattedHours = date_format($dealerSchedules[$i]->getBegin(), 'H\hi') . $this->getHourSeparator() . date_format($dealerSchedules[$i+1]->getEnd(), 'H\hi');
                        } else {
                            $formattedHours = date_format($dealerSchedules[$i]->getBegin(), 'H\hi') . $this->getHourSeparator() . date_format($dealerSchedules[$i]->getEnd(), 'H\hi') . $this->getHalfDaySeparator() . date_format($dealerSchedules[$i+1]->getBegin(), 'H\hi') . $this->getHourSeparator() . date_format($dealerSchedules[$i+1]->getEnd(), 'H\hi');
                        }
                        unset($dealerSchedules[$i+1]);
                    } else {
                        $end = $dealerSchedules[$i]->getEnd();
                        if ($dealerSchedules[$i]->getBegin() && $dealerSchedules[$i]->getEnd()) {
                            $formattedHours = date_format($dealerSchedules[$i]->getBegin(), 'H\hi') . $this->getHourSeparator() . date_format($dealerSchedules[$i]->getEnd(), 'H\hi');
                        }
                    }

                    $results[] = array(
                        'ID' => $dealerSchedules[$i]->getId(),
                        'DEALER_ID' => $dealerSchedules[$i]->getDealerId(),
                        'DAY' => $dealerSchedules[$i]->getDay(),
                        'DAY_LABEL' => ($dealerSchedules[$i]->getDay() === null) ? null : $this->getDayLabel($dealerSchedules[$i]->getDay()),
                        'PERIOD_BEGIN' => $dealerSchedules[$i]->getPeriodBegin(),
                        'PERIOD_END' => $dealerSchedules[$i]->getPeriodEnd(),
                        'BEGIN' => $dealerSchedules[$i]->getBegin(),
                        'END' => $end,
                        'FORMATTED_HOURS' => $formattedHours
                    );
                }
            }
        } else {
            foreach ($dealerSchedules as $dealerSchedule) {
                $results[] = array(
                    'DEALER_ID' => $dealerSchedule->getDealerId(),
                    'ID' => $dealerSchedule->getId(),
                    'DAY' => $dealerSchedule->getDay(),
                    'DAY_LABEL' => ($dealerSchedule->getDay() === null) ? null : $this->getDayLabel($dealerSchedule->getDay()),
                    'BEGIN' => $dealerSchedule->getBegin(),
                    'END' => $dealerSchedule->getEnd(),
                    'PERIOD_BEGIN' => $dealerSchedule->getPeriodBegin(),
                    'PERIOD_END' => $dealerSchedule->getPeriodEnd(),
                    'FORMATTED_HOURS' => date_format($dealerSchedule->getBegin(), 'H\hi') . $this->getHourSeparator() . date_format($dealerSchedule->getEnd(), 'H\hi')
                );
            }
        }

        return $results;
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