<?php
/**
 * Created by PhpStorm.
 * User: apenalver
 * Date: 14/03/2016
 * Time: 10:22
 */

namespace Dealer\Controller;

use Dealer\Model\Dealer;
use Dealer\Model\DealerContact;
use Dealer\Model\DealerQuery;
use Propel\Runtime\Map\TableMap;
use Symfony\Component\Config\Definition\Exception\Exception;
use Thelia\Core\HttpFoundation\JsonResponse;
use Thelia\Controller\Admin\ApiController as BaseApiController;


/**
 * Class ApiController
 * @package Dealer\Controller
 */
class ApiController extends BaseApiController
{

    const DEFAULT_LIMIT = 10;

    public function defaultAction()
    {
        $return = [];
        $code = 200;

        try {
            $query = DealerQuery::create()
                ->joinWithI18n($this->getLocale())
                ->filterByVisible(1);

            $query->limit($this->getLimit());
            $query->offset($this->getPageOffset());

            if (null != $id = $this->getRequest()->get("dealer_id")) {
                $query->filterById($id);
            }

            $dealers = $query->find();

            $return["data"] = [];
            $return["limit"] = $this->getLimit();
            $return["offset"] = $this->getOffset();

            /** @var Dealer $dealer */
            foreach ($dealers as $dealer) {
                $dataI18n = $dealer->getDealerI18ns()->getData()[0]->toArray(TableMap::TYPE_FIELDNAME);
                $dataRow = array_merge($dealer->toArray(TableMap::TYPE_FIELDNAME), $dataI18n);
                $dataRow["contacts"] = $this->getContacts($dealer);
                $dataRow["default_schedules"] = $this->getDefaultSchedules($dealer);
                $dataRow["extra_schedules"] = $this->getExtraSchedules($dealer);

                $return["data"][] = $dataRow;
            }

        } catch (Exception $e) {
            $code = 500;
            $return["error"] = $e->getMessage();
        }


        return new JsonResponse($return, $code);
    }

    protected function getContacts(Dealer $dealer)
    {

        $return = [];
        foreach ($dealer->getDealerContacts() as $dealerContact) {
            $dataRow = $dealerContact->toArray(TableMap::TYPE_FIELDNAME);
            $dataRow = array_merge($dataRow, $dealerContact->getDealerContactI18ns()->getData()[0]->toArray(TableMap::TYPE_FIELDNAME));
            $dataRow["data"] = $this->getContactInfo($dealerContact);
            $return[] = $dataRow;
        }
        return $return;
    }

    protected function getContactInfo(DealerContact $contact)
    {
        $return = [];
        foreach ($contact->getDealerContactInfos() as $dealerContact) {
            $dataRow = $dealerContact->toArray(TableMap::TYPE_FIELDNAME);
            $dataRow = array_merge($dataRow, $dealerContact->getDealerContactInfoI18ns()->getData()[0]->toArray(TableMap::TYPE_FIELDNAME));
            $return[] = $dataRow;
        }
        return $return;
    }

    protected function getDefaultSchedules(Dealer $dealer)
    {
        $return = [];
        foreach ($dealer->getDefaultSchedules() as $schedules) {
            $return[] = $schedules->toArray(TableMap::TYPE_FIELDNAME);
        }
        return $return;
    }

    protected function getExtraSchedules(Dealer $dealer)
    {
        $return = [];
        foreach ($dealer->getExtraSchedules() as $schedules) {
            $return[] = $schedules->toArray(TableMap::TYPE_FIELDNAME);
        }
        return $return;
    }

    protected function getLimit()
    {
        $limit = $this->getRequest()->get("limit");
        return ($limit) ? $limit : static::DEFAULT_LIMIT;
    }

    protected function getOffset()
    {
        $offset = $this->getRequest()->get("offset");
        return ($offset) ? $offset : 0;
    }

    protected function getPageOffset()
    {
        $page = $this->getRequest()->get("page");
        return ($page) ? ($page - 1) * $this->getLimit() : 0;
    }

    protected function getLocale()
    {
        $locale = $this->getRequest()->get("locale");
        return ($locale) ? $locale : 'fr_FR';
    }
}