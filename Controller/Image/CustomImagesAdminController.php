<?php

namespace Dealer\Controller\Image;

use Dealer\Dealer;
use Dealer\Form\DealerImageBoxForm;
use Dealer\Form\DealerImageHeaderForm;
use Dealer\Model\DealerImage;
use Dealer\Model\DealerImageQuery;
use Thelia\Controller\Admin\FileController;
use Thelia\Core\Event\File\FileCreateOrUpdateEvent;
use Thelia\Core\Event\File\FileDeleteEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Log\Tlog;
use Thelia\Model\Lang;
use Thelia\Model\LangQuery;
use Thelia\Tools\URL;

class CustomImagesAdminController extends FileController
{
    const MODULE_RIGHT = Dealer::DOMAIN_NAME;
    const PRODUCT_IMAGE_PARENT_TYPE = 'dealer';

    public function updateProductImageHeader()
    {
        return $this->uploadProductFile(DealerImageHeaderForm::DEALER_IMAGE_HEADER_FORM_ID);
    }

    public function updateProductImageBox()
    {
        return $this->uploadProductFile(DealerImageBoxForm::DEALER_IMAGE_BOX_FORM_ID);
    }

    public function updateCustomImageAction($id, $parentId, $type)
    {
        try {
            $this->registerDealerCustomProductImageType($type);
            if (null !== $response = $this->checkAccessForParentType(AccessManager::UPDATE)) {
                return $response;
            }
            return $this->updateFileAction($id, self::PRODUCT_IMAGE_PARENT_TYPE, $type, TheliaEvents::IMAGE_UPDATE);
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
     */
    public function editCustomImageAction($type, $parentId, $id)
    {
        $this->registerDealerCustomProductImageType($type);
        if (null !== $response = $this->checkAccessForParentType(AccessManager::UPDATE)) {
            return $response;
        }
        $fileManager = $this->getFileManager();
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

    public function deleteCustomImageAction($type, $parentId, $id)
    {
        $message = null;
        $this->registerDealerCustomProductImageType($type);
        $this->checkAccessForParentType(AccessManager::UPDATE);

        $fileManager = $this->getFileManager();
        $modelInstance = $fileManager->getModelInstance($type, self::PRODUCT_IMAGE_PARENT_TYPE);
        $model = $modelInstance->getQueryInstance()->findPk($id);
        if ($model == null) {
            return $this->pageNotFound();
        }
        // Feed event
        $fileDeleteEvent = new FileDeleteEvent($model);
        // Dispatch Event to the Action
        try {
            $this->dispatch(TheliaEvents::IMAGE_DELETE, $fileDeleteEvent);
            $this->adminLogAppend(
                $this->getAdminResources()->getResource(self::PRODUCT_IMAGE_PARENT_TYPE, ucfirst(Dealer::DOMAIN_NAME)),
                $this->getTranslator()->trans(
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
            $message = $this->getTranslator()->trans(
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
            $message = $this->getTranslator()->trans(
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

    private function uploadProductFile($formName)
    {
        if (null !== $response = $this->checkAuth([AdminResources::PRODUCT], ['LaPelerine'], AccessManager::UPDATE)) {
            return $response;
        }
        return self::uploadFile($formName, true);
    }

    /**
     * @param $formName
     * @param $forProduct
     * @return null|\Symfony\Component\HttpFoundation\Response
     */
    private function uploadFile($formName, $forProduct)
    {
        $imageForm = $this->createForm($formName);
        try {
            $form = $this->validateForm($imageForm);
            $imageFile = $form->get('file')->getData();
            if (is_null($imageFile)) {
                /** @noinspection PhpTranslationKeyInspection */
                throw new \Exception($this->getTranslator()->trans('No files uploaded', [], LaPelerine::DOMAIN_NAME));
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

            $this->dispatch(
                TheliaEvents::IMAGE_SAVE,
                $fileCreateOrUpdateEvent
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
            $this->getParserContext()
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

    private function registerDealerCustomProductImageType($type)
    {
        /** @noinspection PhpParamsInspection */
        /** @noinspection PhpUnhandledExceptionInspection */
        $this->getAdminResources()->addModuleResources([strtoupper(self::PRODUCT_IMAGE_PARENT_TYPE) => "admin.Lapelerine"], ucfirst(static::MODULE_RIGHT));
        $this->getFileManager()->addFileModel(
            $type,
            self::PRODUCT_IMAGE_PARENT_TYPE,
            DealerImage::class
        );
    }
}