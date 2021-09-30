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

use Dealer\Model\CustomerFavoriteDealerQuery;
use Dealer\Model\Dealer;
use Dealer\Model\DealerQuery;
use Dealer\Model\Map\DealerBrandTableMap;
use Dealer\Model\Map\DealerContentTableMap;
use Dealer\Model\Map\DealerFolderTableMap;
use Dealer\Model\Map\DealerProductTableMap;
use Dealer\Model\Map\DealerTableMap;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\Join;
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
        $favoriteDealerId = null;
        $customer = $this->getCurrentRequest()->getSession()->getCustomerUser();
        if ($customer !== null) {
            $favoriteDealerId = (int) $this->getCurrentRequest()->getSession()->get("favoriteDealer");
        }
        /** @var Dealer $dealer */
        foreach ($loopResult->getResultDataCollection() as $dealer) {
            $loopResultRow = new LoopResultRow($dealer);

            $isFavorite = false;
            if ($favoriteDealerId !== null && $favoriteDealerId === $dealer->getId()) {
                $isFavorite = true;
            }

            $loopResultRow
                ->set('ID', $dealer->getId())
                ->set("ADDRESS1", $dealer->getAddress1())
                ->set("ADDRESS2", $dealer->getAddress2())
                ->set("ADDRESS3", $dealer->getAddress3())
                ->set("ZIPCODE", $dealer->getZipcode())
                ->set("CITY", $dealer->getCity())
                ->set("COUNTRY_ID", $dealer->getCountryId())
                ->set("LAT", $dealer->getLatitude())
                ->set("LON", $dealer->getLongitude())
                ->set("CREATE_DATE", $dealer->getCreatedAt())
                ->set("UPDATE_DATE", $dealer->getUpdatedAt())
                ->set("VISIBLE", $dealer->getVisible())
                ->set("FAVORITE", $isFavorite)
            ;

            if ($dealer->hasVirtualColumn('i18n_TITLE')) {
                $loopResultRow->set("TITLE", $dealer->getVirtualColumn('i18n_TITLE'));
            }

            if ($dealer->hasVirtualColumn('i18n_ACCESS')) {
                $loopResultRow->set("ACCESS", $dealer->getVirtualColumn('i18n_ACCESS'));
            }

            if ($dealer->hasVirtualColumn('i18n_DESCRIPTION')) {
                $loopResultRow->set("DESCRIPTION", $dealer->getVirtualColumn('i18n_DESCRIPTION'));
            }

            if ($dealer->hasVirtualColumn('i18n_BIG_DESCRIPTION')) {
                $loopResultRow->set("BIG_DESCRIPTION", $dealer->getVirtualColumn('i18n_BIG_DESCRIPTION'));
            }

            if ($dealer->hasVirtualColumn('i18n_HARD_OPEN_HOUR')) {
                $loopResultRow->set("HARD_OPEN_HOUR", $dealer->getVirtualColumn('i18n_HARD_OPEN_HOUR'));
            }

            if ($this->getWithPrevNextInfo()) {
                $previous = $this->getPrevious($dealer);
                $next = $this->getNext($dealer);
                $loopResultRow
                    ->set("HAS_PREVIOUS", $previous != null ? 1 : 0)
                    ->set("HAS_NEXT", $next != null ? 1 : 0)
                    ->set("PREVIOUS", $previous != null ? $previous->getId() : -1)
                    ->set("NEXT", $next != null ? $next->getId() : -1);
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
            Argument::createIntListTypeArgument('content_id'),
            Argument::createIntListTypeArgument('folder_id'),
            Argument::createIntListTypeArgument('brand_id'),
            Argument::createIntListTypeArgument('product_id'),
            Argument::createBooleanTypeArgument('visible'),
            Argument::createAnyListTypeArgument('city'),
            Argument::createBooleanTypeArgument('with_prev_next_info', false),
            Argument::createEnumListTypeArgument('order', [
                'id',
                'id-reverse',
                'date',
                'date-reverse',
                'title',
                'title-reverse',
            ], 'id')
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
                'DESCRIPTION',
                'ACCESS',
                'BIG_DESCRIPTION',
                'HARD_OPEN_HOUR'
            ],
            null,
            'ID',
            $this->getForceReturn()
        );

        if (null != $id = $this->getId()) {
            $query->filterById($id);
        }

        if (null != $country_id = $this->getCountryId()) {
            $query->filterByCountryId($country_id);
        }

        if (null != $city = $this->getCity()) {
            $query->filterByCity($city);
        }

        if (null != $visible = $this->getVisible()) {
            $query->filterByVisible($visible);
        }

        if ($content = $this->getContentId()) {
            if (is_array($content)) {
                $content = implode(",", $content);
            }
            $contentJoin = new Join(DealerTableMap::ID, DealerContentTableMap::DEALER_ID, Criteria::LEFT_JOIN);
            $query
                ->addJoinObject($contentJoin)
                ->where(DealerContentTableMap::CONTENT_ID." ".Criteria::IN." (".$content.")");
            ;
        }

        if ($folder = $this->getFolderId()) {
            if (is_array($folder)) {
                $folder = implode(",", $folder);
            }
            $contentJoin = new Join(DealerTableMap::ID, DealerFolderTableMap::DEALER_ID, Criteria::LEFT_JOIN);
            $query
                ->addJoinObject($contentJoin)
                ->where(DealerFolderTableMap::FOLDER_ID." ".Criteria::IN." (".$folder.")");
            ;
        }

        if ($brand = $this->getBrandId()) {
            if (is_array($brand)) {
                $brand = implode(",", $brand);
            }
            $contentJoin = new Join(DealerTableMap::ID, DealerBrandTableMap::DEALER_ID, Criteria::LEFT_JOIN);
            $query
                ->addJoinObject($contentJoin)
                ->where(DealerBrandTableMap::BRAND_ID." ".Criteria::IN." (".$brand.")");
            ;
        }

        if ($product = $this->getProductId()) {
            if (is_array($product)) {
                $product = implode(",", $product);
            }
            $contentJoin = new Join(DealerTableMap::ID, DealerProductTableMap::DEALER_ID, Criteria::LEFT_JOIN);
            $query
                ->addJoinObject($contentJoin)
                ->where(DealerProductTableMap::PRODUCT_ID." ".Criteria::IN." (".$product.")");
            ;
        }

        if ($this->getBackendContext() === true) {
            $query =$this->getAdminDealer($query);
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
                case 'title' :
                    $query->useDealerI18nQuery()->orderByTitle()->endUse();
                    break;
                case 'title-reverse' :
                    $query->useDealerI18nQuery()->orderByTitle(Criteria::DESC)->endUse();
                    break;
                default:
                    break;
            }
        }

        return $query;
    }

    /**
     * @param Dealer $dealer
     * @return Dealer
     */
    protected function getPrevious($dealer)
    {
        $query = DealerQuery::create();

        foreach ($this->getOrder() as $order) {
            switch ($order) {
                case 'id' :
                    $query->orderById(Criteria::DESC);
                    $query->filterById($dealer->getId(), Criteria::LESS_THAN);
                    break;
                case 'id-reverse' :
                    $query->orderById();
                    $query->filterById($dealer->getId(), Criteria::GREATER_THAN);
                    break;
                default:
                    $query->orderById(Criteria::DESC);
                    $query->filterById($dealer->getId(), Criteria::LESS_THAN);
                    break;
            }
        }

        return $query->findOne();
    }

    /**
     * @param Dealer $dealer
     * @return Dealer
     */
    protected function getNext($dealer)
    {
        $query = DealerQuery::create();

        foreach ($this->getOrder() as $order) {
            switch ($order) {
                case 'id' :
                    $query->orderById();
                    $query->filterById($dealer->getId(), Criteria::GREATER_THAN);
                    break;
                case 'id-reverse' :
                    $query->orderById(Criteria::DESC);
                    $query->filterById($dealer->getId(), Criteria::LESS_THAN);
                    break;
                default:
                    $query->orderById();
                    $query->filterById($dealer->getId(), Criteria::GREATER_THAN);
                    break;
            }
        }

        return $query->findOne();
    }

    /**
     * @param DealerQuery $query
     * @return DealerQuery
     */
    protected function getAdminDealer($query)
    {
        /** @var \Thelia\Model\Admin $admin */
        $admin = $this->securityContext->getAdminUser();
    
        if ($admin === null) {
            return $query;
        }

        if ($admin->getProfileId() === null) {
            return $query;
        }

        // If the current admin has an allowed profile_id, let him see all the dealers
        if (null != $configProfileIds = \Dealer\Dealer::getConfigValue(\Dealer\Dealer::CONFIG_ALLOW_PROFILE_ID)) {
            $profileIds = explode(',', $configProfileIds);

            if (in_array($admin->getProfileId(), $profileIds)) {
                return $query;
            }
        }

        return $query->useDealerAdminQuery()->filterByAdminId($admin->getId())->endUse();
    }
}
