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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Thelia\Controller\Front\BaseFrontController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/3/dealer", name="dealer_api")
 * Class ApiController
 * @package Dealer\Controller
 */
class ApiController extends BaseFrontController
{

    const DEFAULT_LIMIT = 10;

    /**
     * @return JsonResponse
     * @Route("", name="_default", methods="GET")
     */
    public function defaultAction(RequestStack $requestStack)
    {
        $return = [];
        $code = 200;
        $request = $requestStack->getCurrentRequest();

        try {
            if ($request === null){
                throw new \Exception("Request null");
            }
            $query = DealerQuery::create()
                ->joinWithI18n($this->getLocale($request))
                ->filterByVisible(1);

            if (null != $id = $request->get("dealer_id")) {
                $query->filterById($id);
            }

            $return["total"] = $query->count();

            $query->limit($this->getLimit($request));
            $return["limit"] = $this->getLimit($request);


            if ($this->getPageOffset($request) != 0) {
                $query->offset($this->getPageOffset($request));
                $return["offset"] = $this->getPageOffset($request);
            }
            if ($this->getOffset($request) != 0) {
                $query->offset($this->getOffset($request));
                $return["offset"] = $this->getOffset($request);
            }

            $query = $this->addOrder($query, $request);

            $dealers = $query->find();

            $return["data"] = [];

            /** @var Dealer $dealer */
            foreach ($dealers as $dealer) {
                $dataI18n = $dealer->getDealerI18ns()->getData()[0]->toArray(TableMap::TYPE_FIELDNAME);
                $dataRow = array_merge($dealer->toArray(TableMap::TYPE_FIELDNAME), $dataI18n);
                $dataRow["contacts"] = $this->getContacts($dealer);
                $dataRow["default_schedules"] = $this->getDefaultSchedules($dealer);
                $dataRow["extra_schedules"] = $this->getExtraSchedules($dealer);

                $dataRow = $this->afterProcessDealer($dataRow);

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
    protected function getLimit(Request $request)
    {
        $limit = $request->get("limit");
        return ($limit) ? $limit : static::DEFAULT_LIMIT;
    }

    /**
     * @return int|mixed
     */
    protected function getOffset(Request $request)
    {
        $offset = $request->get("offset");
        return ($offset) ? $offset : 0;
    }

    /**
     * @return int|mixed
     */
    protected function getPageOffset(Request $request)
    {
        $page = $request->get("page");
        return ($page) ? ($page - 1) * $this->getLimit($request) : 0;
    }

    /**
     * @return mixed|string
     */
    protected function getLocale(Request $request)
    {
        $locale = $request->get("locale");
        return ($locale) ? $locale : 'fr_FR';
    }

    /**
     * @param DealerQuery $query
     * @return DealerQuery
     */
    protected function addOrder(DealerQuery $query, Request $request)
    {
        $order = $request->get("order");
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
     * @return mixed
     */
    protected function afterProcessDealer($dataRow)
    {
        return $dataRow;
    }
}
