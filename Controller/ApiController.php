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
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\Map\TableMap;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Thelia\Controller\Api\BaseApiController;

/**
 * Class ApiController
 * @package Dealer\Controller
 */
class ApiController extends BaseApiController
{

    const DEFAULT_LIMIT = 10;

    /**
     * @return JsonResponse
     */
    public function defaultAction()
    {
        $return = [];
        $code = 200;

        try {
            $query = DealerQuery::create()
                ->joinWithI18n($this->getLocale())
                ->filterByVisible(1);

            if (null != $id = $this->getRequest()->get("dealer_id")) {
                $query->filterById($id);
            }

            $return["total"] = $query->count();

            $query->limit($this->getLimit());
            $return["limit"] = $this->getLimit();


            if ($this->getPageOffset() != 0) {
                $query->offset($this->getPageOffset());
                $return["offset"] = $this->getPageOffset();
            }
            if ($this->getOffset() != 0) {
                $query->offset($this->getOffset());
                $return["offset"] = $this->getOffset();
            }

            $query = $this->addOrder($query);

            $dealers = $query->find();

            $return["data"] = [];

            /** @var Dealer $dealer */
            foreach ($dealers as $dealer) {
                $dataI18n = $dealer->getDealerI18ns()->getData()[0]->toArray(TableMap::TYPE_FIELDNAME);
                $dataRow = array_merge($dealer->toArray(TableMap::TYPE_FIELDNAME), $dataI18n);
                $dataRow["contacts"] = $this->getContacts($dealer);
                $dataRow["default_schedules"] = $this->getDefaultSchedules($dealer);
                $dataRow["extra_schedules"] = $this->getExtraSchedules($dealer);

                $dataRow = $this->afterProcessDealer($dataRow, $dealer);

                $return["data"][] = $dataRow;
            }
        } catch (Exception $e) {
            $code = 500;
            $return["error"] = $e->getMessage();
        }


        return new JsonResponse($return, $code);
    }

    /**
     * @param Dealer $dealer
     * @return array
     */
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

    /**
     * @param DealerContact $contact
     * @return array
     */
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

    /**
     * @param Dealer $dealer
     * @return array
     */
    protected function getDefaultSchedules(Dealer $dealer)
    {
        $return = [];
        foreach ($dealer->getDefaultSchedules() as $schedules) {
            $return[] = $schedules->toArray(TableMap::TYPE_FIELDNAME);
        }
        return $return;
    }

    /**
     * @param Dealer $dealer
     * @return array
     */
    protected function getExtraSchedules(Dealer $dealer)
    {
        $return = [];
        foreach ($dealer->getExtraSchedules() as $schedules) {
            $return[] = $schedules->toArray(TableMap::TYPE_FIELDNAME);
        }
        return $return;
    }

    /**
     * @return int|mixed
     */
    protected function getLimit()
    {
        $limit = $this->getRequest()->get("limit");
        return ($limit) ? $limit : static::DEFAULT_LIMIT;
    }

    /**
     * @return int|mixed
     */
    protected function getOffset()
    {
        $offset = $this->getRequest()->get("offset");
        return ($offset) ? $offset : 0;
    }

    /**
     * @return int|mixed
     */
    protected function getPageOffset()
    {
        $page = $this->getRequest()->get("page");
        return ($page) ? ($page - 1) * $this->getLimit() : 0;
    }

    /**
     * @return mixed|string
     */
    protected function getLocale()
    {
        $locale = $this->getRequest()->get("locale");
        return ($locale) ? $locale : 'fr_FR';
    }

    /**
     * @param DealerQuery $query
     * @return DealerQuery
     */
    protected function addOrder(DealerQuery $query)
    {
        $order = $this->getRequest()->get("order");
        switch ($order) {
            case "id" :
                $query->orderById();
                break;
            case "id-reverse" :
                $query->orderById(Criteria::DESC);
                break;
            case "date" :
                $query->orderByCreatedAt();
                break;
            case "date-reverse" :
                $query->orderByCreatedAt(Criteria::DESC);
                break;
            default:
                $query->orderById();
                break;
        }
        return $query;
    }

    /**
     * @param $dataRow
     * @param Dealer $dealer
     * @return mixed
     */
    protected function afterProcessDealer($dataRow, Dealer $dealer)
    {
        return $dataRow;
    }
}
