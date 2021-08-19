<?php

namespace Dealer\Controller;

use Dealer\Model\CustomerFavoriteDealerQuery;
use Dealer\Model\DealerMetaSeoQuery;
use Dealer\Model\DealerQuery;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Thelia\Controller\Front\BaseFrontController;
use Thelia\Core\HttpFoundation\JsonResponse;


class DealerFrontController extends BaseFrontController
{
    public function show($slug)
    {
        $dealerMetaSeo = DealerMetaSeoQuery::create()
            ->filterBySlug($slug)
            ->findOne();

        if(null === $dealerMetaSeo){
            throw new NotFoundHttpException();
        }

        return $this->render('dealer-front', ['dealer_id'=>$dealerMetaSeo->getDealerId()]);
    }

    public function addToFavorite($dealerId)
    {
        $this->checkAuth();
        $customer = $this->getSecurityContext()->getCustomerUser();

        $dealer = DealerQuery::create()->filterById($dealerId)->findOne();

        if ($dealer == null) {
            return new JsonResponse([
                "error" => "Dealer not found"
            ], 400);
        }

        $customerFavoriteDealer = CustomerFavoriteDealerQuery::create()->filterByCustomerId($customer->getId())->findOneOrCreate();
        $customerFavoriteDealer->setCustomerId($customer->getId());
        $customerFavoriteDealer->setDealerId($dealerId);
        $customerFavoriteDealer->save();

        return new JsonResponse([], 200);
    }

    public function removeFavorite()
    {
        $this->checkAuth();
        $customer = $this->getSecurityContext()->getCustomerUser();

        $customerFavoriteDealer = CustomerFavoriteDealerQuery::create()->filterByCustomerId($customer->getId())->findOne();
        if ($customerFavoriteDealer !== null) {
            $customerFavoriteDealer->delete();
        }

        return new JsonResponse([], 200);
    }
}
