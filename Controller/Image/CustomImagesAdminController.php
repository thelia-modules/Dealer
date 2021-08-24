<?php

namespace Dealer\Controller\Image;

use Dealer\Dealer;
use Dealer\Form\DealerImageBoxForm;
use Dealer\Form\DealerImageHeaderForm;
use Dealer\Model\DealerImage;
use Dealer\Model\DealerImageQuery;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Thelia\Controller\Admin\FileController;
use Thelia\Core\Event\File\FileCreateOrUpdateEvent;
use Thelia\Core\Event\File\FileDeleteEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Core\Template\ParserContext;
use Thelia\Core\Translation\Translator;
use Thelia\Files\FileManager;
use Thelia\Log\Tlog;
use Thelia\Model\Lang;
use Thelia\Model\LangQuery;
use Thelia\Tools\URL;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/module/dealer/image", name="dealer_image")
 */
class CustomImagesAdminController extends FileController
{
    const MODULE_RIGHT = Dealer::DOMAIN_NAME;
    const PRODUCT_IMAGE_PARENT_TYPE = 'dealer';

    /**
     * @Route("/header/update", name="_header", methods="POST")
     */
    public function updateProductImageHeader(ParserContext $parserContext, EventDispatcherInterface $eventDispatcher)
    {
        return $this->uploadProductFile(DealerImageHeaderForm::DEALER_IMAGE_HEADER_FORM_ID, $parserContext, $eventDispatcher);
    }

    /**
     * @Route("/box/update", name="_box", methods="POST")
     */
    public function updateProductImageBox(ParserContext $parserContext, EventDispatcherInterface $eventDispatcher)
    {
        return $this->uploadProductFile(DealerImageBoxForm::DEALER_IMAGE_BOX_FORM_ID, $parserContext, $eventDispatcher);
    }

    /**
     * @Route("/{type}/update/{parentId}/{id}", name="_update", methods="POST")
     */
    public function updateCustomImageAction($id, $parentId, $type, EventDispatcherInterface $eventDispatcher, FileManager $fileManager)
    {
        try {
            $this->registerDealerCustomProductImageType($type, $fileManager);
            if (null !== $response = $this->checkAccessForParentType(AccessManager::UPDATE)) {
                return $response;
            }
            return $this->updateFileAction($eventDispatcher, $id,self::PRODUCT_IMAGE_PARENT_TYPE, $type, TheliaEvents::IMAGE_UPDATE);
        } catch (\Exception $exception) {
            Tlog::getInstance()->error($exception->getMessage());
        }
        return $this
            ->generateRedirect(URL::getInstance()
                ->absoluteUrl(sprintf('/admin/module/dealer/image/%1$s/edit/%2$s/%3$s', $type, $parentId, $id))
            );
    }

    /**
     * @param $type
     * @param $parentId
     * @param $id
     * @return mixed|\Thelia\Core\HttpFoundation\Response
     * @throws \Exception
     * @Route("/{type}/edit/{parentId}/{id}", name="_edit", methods="GET")
     */
    public function editCustomImageAction($type, $parentId, $id, FileManager $fileManager)
    {
        $this->registerDealerCustomProductImageType($type, $fileManager);
        if (null !== $response = $this->checkAccessForParentType(AccessManager::UPDATE)) {
            return $response;
        }
        $imageModel = $fileManager->getModelInstance($type, self::PRODUCT_IMAGE_PARENT_TYPE);

        /** @var DealerImage $image */
        $image = DealerImageQuery::create()
            ->filterByType(DealerImage::getTypeIdFromLabel($type))
            ->findPk($id);
        if ($image === null) {
            return $this->pageNotFound();
        }

        $redirectionUrl = '/admin/module/Dealer/dealer/edit?dealer_id=' . $parentId;
        $redirectUrl = URL::getInstance()->absoluteUrl($redirectionUrl, ['current_tab' => 'images']);

        return $this->render('custom-dealer-image-edit', array(
            'imageId' => $id,
            'imageType' => $type,
            'redirectUrl' => $redirectUrl,
            'parentId' => $parentId,
            'formId' => $imageModel->getUpdateFormId(),
        ));
    }

    /**
     * @Route("/{type}/delete/{parentId}/{id}", name="_delete", methods="GET")
     */
    public function deleteCustomImageAction($type, $parentId, $id, EventDispatcherInterface $eventDispatcher, FileManager $fileManager)
    {
        $message = null;
        $this->registerDealerCustomProductImageType($type, $fileManager);
        $this->checkAccessForParentType(AccessManager::UPDATE);

        $modelInstance = $fileManager->getModelInstance($type, self::PRODUCT_IMAGE_PARENT_TYPE);
        $model = $modelInstance->getQueryInstance()->findPk($id);
        if ($model == null) {
            return $this->pageNotFound();
        }
        // Feed event
        $fileDeleteEvent = new FileDeleteEvent($model);
        // Dispatch Event to the Action
        try {
            $eventDispatcher->dispatch($fileDeleteEvent, TheliaEvents::IMAGE_DELETE);
            $this->adminLogAppend(
                $this->getAdminResources()->getResource(self::PRODUCT_IMAGE_PARENT_TYPE, ucfirst(Dealer::DOMAIN_NAME)),
                Translator::getInstance()->trans(
                    'Deleting %obj% for %id% with parent id %parentId%',
                    array(
                        '%obj%' => $type,
                        '%id%' => $fileDeleteEvent->getFileToDelete()->getId(),
                        '%parentId%' => $fileDeleteEvent->getFileToDelete()->getParentId(),
                    )
                ),
                AccessManager::UPDATE,
                $fileDeleteEvent->getFileToDelete()->getId()
            );
        } catch (\Exception $e) {
            $message = Translator::getInstance()->trans(
                'Fail to delete  %obj% for %id% with parent id %parentId% (Exception : %e%)',
                array(
                    '%obj%' => $type,
                    '%id%' => $fileDeleteEvent->getFileToDelete()->getId(),
                    '%parentId%' => $fileDeleteEvent->getFileToDelete()->getParentId(),
                    '%e%' => $e->getMessage()
                )
            );
        }
        if (null === $message) {
            $message = Translator::getInstance()->trans(
                '%obj%s deleted successfully',
                ['%obj%' => ucfirst($type)],
                Dealer::DOMAIN_NAME
            );
        }
        $this->adminLogAppend(
            self::PRODUCT_IMAGE_PARENT_TYPE,
            AccessManager::UPDATE,
            $message,
            $fileDeleteEvent->getFileToDelete()->getId()
        );
        $redirectionUrl = '/admin/module/Dealer/dealer/edit?dealer_id=' . $parentId;
        return $this->generateRedirect(URL::getInstance()->absoluteUrl($redirectionUrl, ['current_tab' => 'images']));
    }

    private function uploadProductFile($formName, ParserContext $parserContext, EventDispatcherInterface $eventDispatcher)
    {
        if (null !== $response = $this->checkAuth(AdminResources::PRODUCT, [], AccessManager::UPDATE)) {
            return $response;
        }
        return $this->uploadFile($formName, true, $parserContext, $eventDispatcher);
    }

    /**
     * @param $formName
     * @param $forProduct
     * @return null|\Symfony\Component\HttpFoundation\Response
     */
    private function uploadFile($formName, $forProduct, ParserContext $parserContext, EventDispatcherInterface $eventDispatcher)
    {
        $imageForm = $this->createForm($formName);
        try {
            $form = $this->validateForm($imageForm);
            $imageFile = $form->get('file')->getData();
            if (is_null($imageFile)) {
                /** @noinspection PhpTranslationKeyInspection */
                throw new \Exception(Translator::getInstance()->trans('No files uploaded', [], Dealer::DOMAIN_NAME));
            }
            $parentId = $form->get('parent_id')->getData();

            if ($forProduct) {
                $fileModel = DealerImage::fromProductIdAndFormName($parentId, $formName);
            }

            $uploadDir = $fileModel->getUploadDir();
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $fileCreateOrUpdateEvent = new FileCreateOrUpdateEvent($parentId);
            $fileCreateOrUpdateEvent->setModel($fileModel);
            $fileCreateOrUpdateEvent->setUploadedFile($imageFile);

            $eventDispatcher->dispatch(
                $fileCreateOrUpdateEvent,
                TheliaEvents::IMAGE_SAVE
            );

            // Compensate issue #1005
            $langs = LangQuery::create()->find();

            /** @var Lang $lang */
            foreach ($langs as $lang) {
                $pageProductImage = $fileCreateOrUpdateEvent->getModel();
                $pageProductImage->setLocale($lang->getLocale());
                $pageProductImage->setTitle('');
                $pageProductImage->save();
            }

            return $this->generateSuccessRedirect($imageForm);

        } catch (\Exception $e) {
            Tlog::getInstance()->addError(sprintf("Failed to upload file with form %s error :%s", $formName, $e->getMessage()));
            $error_message = $e->getMessage();
            $imageForm->setErrorMessage($error_message);
            $parserContext
                ->addForm($imageForm)
                ->setGeneralError($error_message);
            return $this->generateErrorRedirect($imageForm);
        }
    }


    /**
     * @param string $access
     * @return mixed null if authorization is granted, or a Response object which contains the error page otherwise
     */
    protected function checkAccessForParentType($access)
    {
        return $this->checkAuth(AdminResources::MODULE, [Dealer::DOMAIN_NAME], $access);
    }

    private function registerDealerCustomProductImageType($type, FileManager $fileManager)
    {
        /** @noinspection PhpParamsInspection */
        /** @noinspection PhpUnhandledExceptionInspection */
        $this->getAdminResources()->addModuleResources([strtoupper(self::PRODUCT_IMAGE_PARENT_TYPE) => "admin.Dealer"], static::MODULE_RIGHT);
        $fileManager->addFileModel(
            $type,
            self::PRODUCT_IMAGE_PARENT_TYPE,
            DealerImage::class
        );
    }
}