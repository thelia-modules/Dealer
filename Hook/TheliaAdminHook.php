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

namespace Dealer\Hook;

use Dealer\Form\BrandLinkForm;
use Dealer\Form\ContentLinkForm;
use Dealer\Form\FolderLinkForm;
use Dealer\Form\ProductLinkForm;
use Dealer\Model\DealerQuery;
use Dealer\Model\Map\DealerBrandTableMap;
use Dealer\Model\Map\DealerContentTableMap;
use Dealer\Model\Map\DealerFolderTableMap;
use Dealer\Model\Map\DealerProductTableMap;
use Dealer\Model\Map\DealerTableMap;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\Join;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Thelia\Core\Event\Hook\HookRenderEvent;
use Thelia\Core\Form\TheliaFormFactory;
use Thelia\Core\Hook\BaseHook;
use Thelia\Core\Template\Parser\ParserResolver;

/**
 * Class TheliaAdminHook
 * @package Dealer\Hook
 */
class TheliaAdminHook extends BaseHook
{
    public function __construct(
        private readonly TheliaFormFactory $dealerFormFactory,
        ?EventDispatcherInterface $dispatcher = null,
        ?ParserResolver $parserResolver = null,
    ) {
        parent::__construct($dispatcher, $parserResolver);
    }

    public static function getSubscribedHooks(): array
    {
        return [
            'content.tab-content' => [['type' => 'back', 'method' => 'onContentModuleTab']],
            'folder.tab-content' => [['type' => 'back', 'method' => 'onFolderModuleTab']],
            'brand.tab-content' => [['type' => 'back', 'method' => 'onBrandModuleTab']],
            'product.tab-content' => [['type' => 'back', 'method' => 'onProductModuleTab']],
        ];
    }

    public function onContentModuleTab(HookRenderEvent $event): void
    {
        $contentId = $event->getArgument('content_id');

        $event->add($this->render('Dealer/hook/content.html.twig', [
            'dealer_content_id' => $contentId,
            'dealer_current_url' => $this->getCurrentUrl(),
            'dealer_all_dealers' => $this->getAllDealers(),
            'dealer_linked_dealers' => $this->getLinkedDealers(DealerContentTableMap::COL_DEALER_ID, DealerContentTableMap::COL_CONTENT_ID, $contentId),
            'dealer_content_link_form' => $this->dealerFormFactory->createForm(ContentLinkForm::getName())->createView()->getView(),
        ]));
    }

    public function onFolderModuleTab(HookRenderEvent $event): void
    {
        $folderId = $event->getArgument('folder_id');

        $event->add($this->render('Dealer/hook/folder.html.twig', [
            'dealer_folder_id' => $folderId,
            'dealer_current_url' => $this->getCurrentUrl(),
            'dealer_all_dealers' => $this->getAllDealers(),
            'dealer_linked_dealers' => $this->getLinkedDealers(DealerFolderTableMap::COL_DEALER_ID, DealerFolderTableMap::COL_FOLDER_ID, $folderId),
            'dealer_folder_link_form' => $this->dealerFormFactory->createForm(FolderLinkForm::getName())->createView()->getView(),
        ]));
    }

    public function onBrandModuleTab(HookRenderEvent $event): void
    {
        $brandId = $event->getArgument('brand_id');

        $event->add($this->render('Dealer/hook/brand.html.twig', [
            'dealer_brand_id' => $brandId,
            'dealer_current_url' => $this->getCurrentUrl(),
            'dealer_all_dealers' => $this->getAllDealers(),
            'dealer_linked_dealers' => $this->getLinkedDealers(DealerBrandTableMap::COL_DEALER_ID, DealerBrandTableMap::COL_BRAND_ID, $brandId),
            'dealer_brand_link_form' => $this->dealerFormFactory->createForm(BrandLinkForm::getName())->createView()->getView(),
        ]));
    }

    public function onProductModuleTab(HookRenderEvent $event): void
    {
        $productId = $event->getArgument('product_id');

        $event->add($this->render('Dealer/hook/product.html.twig', [
            'dealer_product_id' => $productId,
            'dealer_current_url' => $this->getCurrentUrl(),
            'dealer_all_dealers' => $this->getAllDealers(),
            'dealer_linked_dealers' => $this->getLinkedDealers(DealerProductTableMap::COL_DEALER_ID, DealerProductTableMap::COL_PRODUCT_ID, $productId),
            'dealer_product_link_form' => $this->dealerFormFactory->createForm(ProductLinkForm::getName())->createView()->getView(),
        ]));
    }

    private function getCurrentUrl(): string
    {
        return $this->getRequest()?->getRequestUri() ?? '';
    }

    /**
     * @return array<int, array{id: int, title: string}>
     */
    private function getAllDealers(): array
    {
        $locale = $this->getCurrentLocale();

        $dealers = [];
        foreach (DealerQuery::create()->orderById()->find() as $dealer) {
            $dealers[] = [
                'id' => $dealer->getId(),
                'title' => $dealer->setLocale($locale)->getTitle(),
            ];
        }

        return $dealers;
    }

    /**
     * Dealers linked to the given entity, mirroring the legacy {loop type="dealer" <entity>_id=...}.
     *
     * @return array<int, array{id: int, title: string}>
     */
    private function getLinkedDealers(string $dealerColumn, string $entityColumn, mixed $entityId): array
    {
        if ($entityId === null || $entityId === '') {
            return [];
        }

        $locale = $this->getCurrentLocale();

        $join = new Join(DealerTableMap::COL_ID, $dealerColumn, Criteria::LEFT_JOIN);

        $query = DealerQuery::create()
            ->addJoinObject($join)
            ->where($entityColumn . ' = ?', (int) $entityId, \PDO::PARAM_INT)
            ->orderById();

        $dealers = [];
        foreach ($query->find() as $dealer) {
            $dealers[] = [
                'id' => $dealer->getId(),
                'title' => $dealer->setLocale($locale)->getTitle(),
            ];
        }

        return $dealers;
    }

    private function getCurrentLocale(): string
    {
        return $this->getRequest()?->getSession()?->getLang()?->getLocale() ?? 'en_US';
    }
}
