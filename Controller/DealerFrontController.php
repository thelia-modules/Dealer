<?php

namespace Dealer\Controller;

use Dealer\Model\DealerMetaSeoQuery;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Thelia\Controller\Front\BaseFrontController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/magasin/{slug}", name="dealer_front")
 */
class DealerFrontController extends BaseFrontController
{
    /**
     * @Route("", name="_slug", methods="GET")
     */
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
