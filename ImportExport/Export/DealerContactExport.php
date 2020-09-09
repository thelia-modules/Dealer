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
use Dealer\Model\Map\DealerTableMap;
use Dealer\Model\Map\DealerI18nTableMap;
use Dealer\Model\DealerQuery;
use Dealer\Model\Map\DealerContactTableMap;
use Dealer\Model\Map\DealerContactI18nTableMap;
use Dealer\Model\Map\DealerContactInfoTableMap;
use Dealer\Model\Map\DealerContactInfoI18nTableMap;

/**
 * Class DealerExport
 * @package Thelia\ImportExport\Export
 * @author  Zzuutt zzuutt34@free.fr
 */
class DealerContactExport extends ExportHandler
{
    /**
     * @param  Lang                                            $lang
     * @return array|\Propel\Runtime\ActiveQuery\ModelCriteria
     */
    public function buildDataSet(Lang $lang)
    {
        $contactInfoType = "CASE ".DealerContactInfoTableMap::CONTACT_TYPE.
                            " WHEN 0 THEN '".DealerContactInfoTableMap::CONTACT_TYPE_EMAIL."'".
                            " WHEN 1 THEN '".DealerContactInfoTableMap::CONTACT_TYPE_TEL."'".
                            " WHEN 2 THEN '".DealerContactInfoTableMap::CONTACT_TYPE_FAX."'".
                            " WHEN 3 THEN '".DealerContactInfoTableMap::CONTACT_TYPE_URL."'".
                            " WHEN 4 THEN '".DealerContactInfoTableMap::CONTACT_TYPE_FACEBOOK."'".
                            " WHEN 5 THEN '".DealerContactInfoTableMap::CONTACT_TYPE_TWITTER."'".
                            " WHEN 6 THEN '".DealerContactInfoTableMap::CONTACT_TYPE_PINTEREST."'".
                            " WHEN 7 THEN '".DealerContactInfoTableMap::CONTACT_TYPE_GOOGLE."'".
                            " WHEN 8 THEN '".DealerContactInfoTableMap::CONTACT_TYPE_YOUTUBE."'".
                            " WHEN 9 THEN '".DealerContactInfoTableMap::CONTACT_TYPE_INSTAGRAM."'".
                            " ELSE '?'".
                            " END";


        $dealer = DealerQuery::create()
            ->useDealerI18nQuery()
                ->addAsColumn("dealer_TITLE", DealerI18nTableMap::TITLE)
            ->endUse()
            ->useDealerContactQuery()
                ->addAsColumn("contact_DEFAULT", DealerContactTableMap::IS_DEFAULT)
                ->useDealerContactI18nQuery()
                    ->addAsColumn("contact_LABEL", DealerContactI18nTableMap::LABEL)
                ->endUse()
                ->useDealerContactInfoQuery()
                    //->addAsColumn("contact_TYPE", DealerContactInfoTableMap::CONTACT_TYPE)
                    ->addAsColumn("contact_TYPE_NAME", $contactInfoType)
                    ->useDealerContactInfoI18nQuery()
                        ->addAsColumn("contact_VALUE", DealerContactInfoI18nTableMap::VALUE)
                    ->endUse()
                ->endUse()
            ->endUse()



            ->select([
                DealerTableMap::ID,
                'dealer_TITLE',
                'contact_LABEL',
                'contact_DEFAULT',
                'contact_TYPE_NAME',
                'contact_VALUE'
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
            DealerTableMap::ID  => 'id',
            'dealer_TITLE'      => 'title',
            'contact_LABEL'     => 'label',
            'contact_DEFAULT'   => 'is_default',
            'contact_TYPE_NAME' => 'type',
            'contact_VALUE'     => 'value'
        ];
    }

    public function getOrder()
    {
        return [
            'id',
            'title',
            'label',
            'is_default',
            'type',
            'value'
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
