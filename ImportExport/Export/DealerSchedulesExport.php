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

namespace Dealer\ImportExport\Export;

use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\Join;
use Thelia\Core\FileFormat\FormatType;
use Thelia\ImportExport\Export\ExportHandler;
use Thelia\Model\Lang;
use Thelia\Model\LangQuery;
use Dealer\Model\Map\DealerTableMap;
use Dealer\Model\Map\DealerI18nTableMap;
use Dealer\Model\DealerQuery;
use Dealer\Model\Map\DealerShedulesTableMap;
use Dealer\Dealer;
use Thelia\Core\Translation\Translator;

/**
 * Class DealerExport
 * @package Thelia\ImportExport\Export
 * @author  Zzuutt zzuutt34@free.fr
 */
class DealerSchedulesExport extends ExportHandler
{

    /**
     * @param  Lang                                            $lang
     * @return array|\Propel\Runtime\ActiveQuery\ModelCriteria
     */
    public function buildDataSet(Lang $lang)
    {
        $locale = $lang->getLocale($lang);

        $schedulesDay = "CASE ".DealerShedulesTableMap::DAY.
            " WHEN 0 THEN '".$this->getDayLabel(0, $locale)."'".
            " WHEN 1 THEN '".$this->getDayLabel(1, $locale)."'".
            " WHEN 2 THEN '".$this->getDayLabel(2, $locale)."'".
            " WHEN 3 THEN '".$this->getDayLabel(3, $locale)."'".
            " WHEN 4 THEN '".$this->getDayLabel(4, $locale)."'".
            " WHEN 5 THEN '".$this->getDayLabel(5, $locale)."'".
            " WHEN 6 THEN '".$this->getDayLabel(6, $locale)."'".
            " ELSE '?'".
            " END";


        $dealer = DealerQuery::create()
            ->useDealerI18nQuery()
            ->addAsColumn("dealer_TITLE", DealerI18nTableMap::TITLE)
            ->endUse()
            ->useDealerShedulesQuery()
                ->addAsColumn("schedules_DAY", $schedulesDay)
                ->addAsColumn("schedules_BEGIN", DealerShedulesTableMap::BEGIN)
                ->addAsColumn("schedules_END", DealerShedulesTableMap::END)
                ->addAsColumn("schedules_CLOSED", DealerShedulesTableMap::CLOSED)
                ->addAsColumn("schedules_PERIOD_BEGIN", DealerShedulesTableMap::PERIOD_BEGIN)
                ->addAsColumn("schedules_PERIOD_END", DealerShedulesTableMap::PERIOD_END)
            ->endUse()

            ->select([
                DealerTableMap::ID,
                'dealer_TITLE',
                'schedules_DAY',
                'schedules_BEGIN',
                'schedules_END',
                'schedules_CLOSED',
                'schedules_PERIOD_BEGIN',
                'schedules_PERIOD_END'
            ])
            ->orderById()
            ->find()
            ->toArray()
        ;

        return $dealer;
    }

    protected function getAliases()
    {

        return [
            DealerTableMap::ID      => 'id',
            'dealer_TITLE'          => 'title',
            'schedules_DAY'          => 'day',
            'schedules_BEGIN'        => 'begin',
            'schedules_END'          => 'end',
            'schedules_CLOSED'       => 'closed',
            'schedules_PERIOD_BEGIN' => 'period_begin',
            'schedules_PERIOD_END'   => 'period_end'
        ];
    }

    public function getOrder()
    {
        return [
            'id',
            'title',
            'day',
            'begin',
            'end',
            'closed',
            'period_begin',
            'period_end'
        ];
    }
    /**
     * @return string|array
     *
     * Define all the type of export/formatters that this can handle
     * return a string if it handle a single type ( specific exports ),
     * or an array if multiple.
     *
     * Thelia types are defined in \Thelia\Core\FileFormat\FormatType
     *
     * example:
     * return array(
     *     FormatType::TABLE,
     *     FormatType::UNBOUNDED,
     * );
     */
    public function getHandledTypes()
    {
        return array(
            FormatType::TABLE,
            FormatType::UNBOUNDED,
        );
    }

    protected function getDayLabel($int = 0, $locale = null)
    {
        return [
            Translator::getInstance()->trans("Monday", [], Dealer::MESSAGE_DOMAIN, $locale),
            Translator::getInstance()->trans("Tuesday", [], Dealer::MESSAGE_DOMAIN, $locale),
            Translator::getInstance()->trans("Wednesday", [], Dealer::MESSAGE_DOMAIN, $locale),
            Translator::getInstance()->trans("Thursday", [], Dealer::MESSAGE_DOMAIN, $locale),
            Translator::getInstance()->trans("Friday", [], Dealer::MESSAGE_DOMAIN, $locale),
            Translator::getInstance()->trans("Saturday", [], Dealer::MESSAGE_DOMAIN, $locale),
            Translator::getInstance()->trans("Sunday", [], Dealer::MESSAGE_DOMAIN, $locale)
        ][$int];
    }
}
