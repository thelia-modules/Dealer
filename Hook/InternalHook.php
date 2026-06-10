<?php
/**
 * Created by PhpStorm.
 * User: apenalver
 * Date: 22/02/2016
 * Time: 16:59
 */

namespace Dealer\Hook;

use Dealer\Form\BrandLinkForm;
use Dealer\Form\ContentLinkForm;
use Dealer\Form\FolderLinkForm;
use Dealer\Model\DealerBrandQuery;
use Dealer\Model\DealerContentQuery;
use Dealer\Model\DealerFolderQuery;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Thelia\Core\Event\Hook\HookRenderEvent;
use Thelia\Core\Form\TheliaFormFactory;
use Thelia\Core\Hook\BaseHook;
use Thelia\Core\Template\Parser\ParserResolver;
use Thelia\Model\BrandQuery;
use Thelia\Model\ContentQuery;
use Thelia\Model\FolderQuery;

/**
 * Class InternalHook
 * @package Dealer\Hook
 */
class InternalHook extends BaseHook
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
            'dealer.associated.tabcontent' => [
                ['type' => 'back', 'method' => 'insertContent'],
                ['type' => 'back', 'method' => 'insertFolder'],
                ['type' => 'back', 'method' => 'insertBrand'],
            ],
        ];
    }

    public function insertContent(HookRenderEvent $event): void
    {
        $dealerId = $event->getArgument('dealer_id');

        $event->add($this->render('Dealer/associated/content-linked.html.twig', [
            'dealer_id' => $dealerId,
            'dealer_current_url' => $this->getCurrentUrl(),
            'dealer_linked_content' => $this->getLinkedContent($dealerId),
            'dealer_all_content' => $this->getAllContent(),
            'dealer_content_link_form' => $this->dealerFormFactory->createForm(ContentLinkForm::getName())->createView()->getView(),
        ]));
    }

    public function insertFolder(HookRenderEvent $event): void
    {
        $dealerId = $event->getArgument('dealer_id');

        $event->add($this->render('Dealer/associated/folder-linked.html.twig', [
            'dealer_id' => $dealerId,
            'dealer_current_url' => $this->getCurrentUrl(),
            'dealer_linked_folder' => $this->getLinkedFolder($dealerId),
            'dealer_all_folder' => $this->getAllFolder(),
            'dealer_folder_link_form' => $this->dealerFormFactory->createForm(FolderLinkForm::getName())->createView()->getView(),
        ]));
    }

    public function insertBrand(HookRenderEvent $event): void
    {
        $dealerId = $event->getArgument('dealer_id');

        $event->add($this->render('Dealer/associated/brand-linked.html.twig', [
            'dealer_id' => $dealerId,
            'dealer_current_url' => $this->getCurrentUrl(),
            'dealer_linked_brand' => $this->getLinkedBrand($dealerId),
            'dealer_all_brand' => $this->getAllBrand(),
            'dealer_brand_link_form' => $this->dealerFormFactory->createForm(BrandLinkForm::getName())->createView()->getView(),
        ]));
    }

    private function getCurrentUrl(): string
    {
        return $this->getRequest()?->getRequestUri() ?? '';
    }

    private function getCurrentLocale(): string
    {
        return $this->getRequest()?->getSession()?->getLang()?->getLocale() ?? 'en_US';
    }

    /**
     * @return array<int, array{id: int, title: string}>
     */
    private function getLinkedContent(mixed $dealerId): array
    {
        if ($dealerId === null) {
            return [];
        }

        $ids = [];
        foreach (DealerContentQuery::create()->filterByDealerId((int) $dealerId)->find() as $link) {
            $ids[] = $link->getContentId();
        }

        if ($ids === []) {
            return [];
        }

        $locale = $this->getCurrentLocale();
        $rows = [];
        foreach (ContentQuery::create()->filterById($ids)->find() as $content) {
            $rows[] = ['id' => $content->getId(), 'title' => $content->setLocale($locale)->getTitle()];
        }

        return $rows;
    }

    /**
     * @return array<int, array{id: int, title: string}>
     */
    private function getLinkedFolder(mixed $dealerId): array
    {
        if ($dealerId === null) {
            return [];
        }

        $ids = [];
        foreach (DealerFolderQuery::create()->filterByDealerId((int) $dealerId)->find() as $link) {
            $ids[] = $link->getFolderId();
        }

        if ($ids === []) {
            return [];
        }

        $locale = $this->getCurrentLocale();
        $rows = [];
        foreach (FolderQuery::create()->filterById($ids)->find() as $folder) {
            $rows[] = ['id' => $folder->getId(), 'title' => $folder->setLocale($locale)->getTitle()];
        }

        return $rows;
    }

    /**
     * @return array<int, array{id: int, title: string}>
     */
    private function getLinkedBrand(mixed $dealerId): array
    {
        if ($dealerId === null) {
            return [];
        }

        $ids = [];
        foreach (DealerBrandQuery::create()->filterByDealerId((int) $dealerId)->find() as $link) {
            $ids[] = $link->getBrandId();
        }

        if ($ids === []) {
            return [];
        }

        $locale = $this->getCurrentLocale();
        $rows = [];
        foreach (BrandQuery::create()->filterById($ids)->find() as $brand) {
            $rows[] = ['id' => $brand->getId(), 'title' => $brand->setLocale($locale)->getTitle()];
        }

        return $rows;
    }

    /**
     * @return array<int, array{id: int, title: string}>
     */
    private function getAllContent(): array
    {
        $locale = $this->getCurrentLocale();
        $rows = [];
        foreach (ContentQuery::create()->find() as $content) {
            $rows[] = ['id' => $content->getId(), 'title' => $content->setLocale($locale)->getTitle()];
        }

        return $rows;
    }

    /**
     * @return array<int, array{id: int, title: string}>
     */
    private function getAllFolder(): array
    {
        $locale = $this->getCurrentLocale();
        $rows = [];
        foreach (FolderQuery::create()->find() as $folder) {
            $rows[] = ['id' => $folder->getId(), 'title' => $folder->setLocale($locale)->getTitle()];
        }

        return $rows;
    }

    /**
     * @return array<int, array{id: int, title: string}>
     */
    private function getAllBrand(): array
    {
        $locale = $this->getCurrentLocale();
        $rows = [];
        foreach (BrandQuery::create()->find() as $brand) {
            $rows[] = ['id' => $brand->getId(), 'title' => $brand->setLocale($locale)->getTitle()];
        }

        return $rows;
    }
}
