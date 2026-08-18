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
use Dealer\Service\ScheduleApplicability;
use Propel\Runtime\ActiveQuery\Criteria;
use Thelia\Core\Template\Element\ArraySearchLoopInterface;
use Thelia\Core\Template\Element\BaseLoop;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Element\LoopResultRow;
use Thelia\Core\Template\Loop\Argument\Argument;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;

class DealerExtraSchedules extends BaseLoop implements ArraySearchLoopInterface
{

    public function parseResults(LoopResult $loopResult): LoopResult
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
                ->set('RECURRING', $schedules['RECURRING'])
                ->set('TITLE', $schedules['TITLE'])
            ;


            $loopResult->addRow($loopResultRow);
        }

        return $loopResult;
    }

    protected function getArgDefinitions(): ArgumentCollection
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

    public function buildArray(): array
    {
        $results = array();

        $query = DealerShedulesQuery::create();

        // Extra schedules = the exceptional entries (dated, periodic, weekly or yearly).
        // The kind is carried by the `exception` column: an exceptional entry may have no
        // period bound at all, so filtering on period_begin/period_end would miss it.
        // `hide_past` is applied in PHP below, once the rows are loaded.
        $query->filterByException(true);

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

        if ($this->getHidePast()) {
            // Recurring-aware "hide past": a yearly entry always comes back, an entry with
            // no period end never expires, and a dated one survives while its end covers
            // today. ScheduleApplicability::periodCovers() holds that open-bound rule.
            $today = new \DateTimeImmutable('today');
            $stillRelevant = [];

            foreach ($dealerSchedules as $dealerSchedule) {
                if ($dealerSchedule->getRecurring()
                    || ScheduleApplicability::periodCovers(null, $dealerSchedule->getPeriodEnd(), $today)) {
                    $stillRelevant[] = $dealerSchedule;
                }
            }

            $dealerSchedules->setData($stillRelevant);
        }

        if ($this->getMergeDay()) {
            $dealerCount = count($dealerSchedules);

            for ($i = 0; $i < $dealerCount; $i++) {

                if (isset($dealerSchedules[$i])) {

                    $formattedHours = null;

                    // if the next result has the same dates, same day, then concat the morning and afternoon hours
                    if (
                        // isset(): reading a missing offset of a Propel collection raises a
                        // "Only variable references should be returned by reference" notice.
                        isset($dealerSchedules[$i+1])
                        && ($dealerSchedules[$i]->getDay() == $dealerSchedules[$i+1]->getDay())
                        && ($dealerSchedules[$i]->getDealerId() == $dealerSchedules[$i+1]->getDealerId())
                        && ($dealerSchedules[$i]->getPeriodBegin() == $dealerSchedules[$i+1]->getPeriodBegin())
                        && ($dealerSchedules[$i]->getPeriodEnd() == $dealerSchedules[$i+1]->getPeriodEnd())
                        // A yearly entry and a dated one never describe the same occurrence.
                        && ($dealerSchedules[$i]->getRecurring() == $dealerSchedules[$i+1]->getRecurring())
                        // A full-day closure has no hours to concatenate.
                        && $dealerSchedules[$i]->getBegin() && $dealerSchedules[$i]->getEnd()
                        && $dealerSchedules[$i+1]->getBegin() && $dealerSchedules[$i+1]->getEnd()
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
                        'FORMATTED_HOURS' => $formattedHours,
                        'RECURRING' => $dealerSchedules[$i]->getRecurring(),
                        'TITLE' => $dealerSchedules[$i]->getTitle()
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
                    // A full-day closure carries no hours.
                    'FORMATTED_HOURS' => ($dealerSchedule->getBegin() && $dealerSchedule->getEnd())
                        ? date_format($dealerSchedule->getBegin(), 'H\hi') . $this->getHourSeparator() . date_format($dealerSchedule->getEnd(), 'H\hi')
                        : null,
                    'RECURRING' => $dealerSchedule->getRecurring(),
                    'TITLE' => $dealerSchedule->getTitle()
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
