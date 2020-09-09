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

use Thelia\Core\FileFormat\FormatType;
use Thelia\ImportExport\Export\ExportHandler;
use Thelia\Model\Lang;
use Dealer\Model\Map\DealerTableMap;
use Dealer\Model\Map\DealerI18nTableMap;
use Dealer\Model\DealerQuery;
use Thelia\Model\Map\CountryI18nTableMap;
use Thelia\Model\Map\CountryTableMap;
use Thelia\Tools\I18n;

/**
 * Class DealerExport
 * @package Thelia\ImportExport\Export
 * @author  Zzuutt zzuutt34@free.fr
 */
class DealerExport extends ExportHandler
{
    /**
     * @param  Lang                                            $lang
     * @return array|\Propel\Runtime\ActiveQuery\ModelCriteria
     */
    public function buildDataSet(Lang $lang)
    {
        $locale = $lang->getLocale();

        $dealer = DealerQuery::create()
            ->useCountryQuery()
                ->useCountryI18nQuery()
                    ->addAsColumn("address_COUNTRY", CountryI18nTableMap::TITLE)
                ->endUse()
            ->endUse()
            ->useDealerI18nQuery()
                ->addAsColumn("dealer_TITLE", DealerI18nTableMap::TITLE)
                ->addAsColumn("dealer_DESCRIPTION", DealerI18nTableMap::DESCRIPTION)
                ->addAsColumn("dealer_ACCESS", DealerI18nTableMap::ACCESS)
            ->endUse()
            ->select([
                DealerTableMap::ID,
                //DealerTableMap::VERSION,
                DealerTableMap::VISIBLE,
                'dealer_TITLE',
                'dealer_DESCRIPTION',
                'dealer_ACCESS',
                DealerTableMap::ADDRESS1,
                DealerTableMap::ADDRESS2,
                DealerTableMap::ADDRESS3,
                DealerTableMap::ZIPCODE,
                DealerTableMap::CITY,
                DealerTableMap::COUNTRY_ID,
                'address_COUNTRY',
                DealerTableMap::LATITUDE,
                DealerTableMap::LONGITUDE
            ])
            ->orderById()
        ;

        I18n::addI18nCondition(
            $dealer,
            CountryI18nTableMap::TABLE_NAME,
            CountryTableMap::ID,
            CountryI18nTableMap::ID,
            CountryI18nTableMap::LOCALE,
            $locale
        );

        $dealer
            ->find()
            ->toArray()
        ;

        return $dealer;
    }

    protected function getAliases()
    {

        return [
            //DealerTableMap::VERSION     => 'version',
            DealerTableMap::ID          => 'id',
            DealerTableMap::VISIBLE     =>  'visible',
            'dealer_TITLE'              => 'title',
            'dealer_DESCRIPTION'        => 'description',
            'dealer_ACCESS'             => 'access',
            DealerTableMap::ADDRESS1    => 'address1',
            DealerTableMap::ADDRESS2    => 'address2',
            DealerTableMap::ADDRESS3    => 'address3',
            DealerTableMap::ZIPCODE     => 'zipcode',
            DealerTableMap::CITY        => 'city',
            DealerTableMap::COUNTRY_ID  => 'country_id',
            'address_COUNTRY'           => 'country',
            DealerTableMap::LATITUDE    => 'latitude',
            DealerTableMap::LONGITUDE   => 'longitude'
        ];
    }

    public function getOrder()
    {
        return [
            'id',
            //'version',
            'visible',
            'title',
            'description',
            'access',
            'address1',
            'address2',
            'address3',
            'zipcode',
            'city',
            'country_id',
            'country',
            'latitude',
            'longitude'
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
}
