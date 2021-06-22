<?php

namespace Dealer\Loop;

use Dealer\Model\DealerMetaSeo;
use Dealer\Model\DealerMetaSeoQuery;
use Dealer\Model\DealerQuery;
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
class DealerMetaSeoLoop extends BaseI18nLoop implements PropelSearchLoopInterface
{
    public function parseResults(LoopResult $loopResult)
    {
        /** @var DealerMetaSeo $dealerSeo */
        foreach ($loopResult->getResultDataCollection() as $dealerSeo) {
            $loopResultRow = new LoopResultRow($dealerSeo);

            $loopResultRow
                ->set('ID', $dealerSeo->getId())
                ->set("SLUG", $dealerSeo->getSlug())
                ->set("META_TITLE", $dealerSeo->getMetaTitle())
                ->set("META_DESC", $dealerSeo->getMetaDescription())
                ->set("META_KEYWORDS", $dealerSeo->getMetaKeywords())
                ->set("META_JSON", $dealerSeo->getJson());

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
            Argument::createIntListTypeArgument('id', null)
        );
    }

    /**
     * @inheritdoc
     */
    public function buildModelCriteria()
    {
        $query = DealerMetaSeoQuery::create();

        if ($id = $this->getId()) {
            $query->filterByDealerId($id);
        }

        return $query;
    }
}
