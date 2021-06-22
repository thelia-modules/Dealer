<?php

namespace Dealer\Controller;

use Dealer\Model\DealerMetaSeoQuery;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Thelia\Controller\Front\BaseFrontController;


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
}
