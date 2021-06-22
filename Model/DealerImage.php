<?php

namespace Dealer\Model;

use Dealer\Dealer;
use Dealer\Form\DealerImageBoxForm;
use Dealer\Form\DealerImageHeaderForm;
use Dealer\Model\Base\DealerImage as BaseDealerImage;
use Propel\Runtime\Connection\ConnectionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Routing\Router;
use Thelia\Files\FileModelInterface;
use Thelia\Files\FileModelParentInterface;
use Thelia\Model\Breadcrumb\CatalogBreadcrumbTrait;
use Thelia\Model\ConfigQuery;

class DealerImage extends BaseDealerImage implements FileModelInterface
{
    use CatalogBreadcrumbTrait;

    const IMAGE_TYPE_HEADER = "imageHeader";
    const IMAGE_TYPE_BOX = "imageBox";

    /**
     * @var array Possible image types
     */
    const POSSIBLE_TYPE = array(self::IMAGE_TYPE_HEADER, self::IMAGE_TYPE_BOX);

    /**
     * Set file parent id
     *
     * @param int $parentId parent id
     *
     * @return $this
     */
    public function setParentId($parentId)
    {
        $this->setDealerId($parentId);
        return $this;
    }

    /**
     * Get file parent id
     *
     * @return int parent id
     */
    public function getParentId()
    {
        return $this->getDealerId();
    }

    /**
     * @return FileModelParentInterface the parent file model
     */
    public function getParentFileModel()
    {
        return new Dealer();
    }

    public static function getTypeIdFromLabel($type) {
        return array_search($type, self::POSSIBLE_TYPE);
    }

    public function getBreadcrumb(Router $router, $container, $tab, $locale)
    {
        return $this->getProductBreadcrumb($router, $container, $tab, $locale);
    }

    /**
     * @return string the URL to redirect to after update from the back-office
     */
    public function getRedirectionUrl()
    {
        return '/admin/module/Dealer/dealer/edit?dealer_id='.$this->getDealerId();
    }

    public function getUploadDir()
    {
        $uploadDir = ConfigQuery::read('images_library_path');
        if ($uploadDir === null) {
            $uploadDir = THELIA_LOCAL_DIR . 'media' . DS . 'images';
        } else {
            $uploadDir = THELIA_ROOT . $uploadDir;
        }
        return $uploadDir . DS . Dealer::DOMAIN_NAME ;
    }

    /**
     * Get the Query instance for this object
     *
     * @return DealerImageQuery
     */
    public function getQueryInstance()
    {
        return DealerImageQuery::create();
    }

    /**
     * Set the current locale
     *
     * @param  bool $visible true if the file is visible, false otherwise
     * @return FileModelInterface
     */
    public function setVisible($visible)
    {
        return $this;
    }

    public function preDelete(ConnectionInterface $con = null)
    {
        $fs = new Filesystem();
        try {
            $fs->remove($this->getUploadDir() . DS . $this->getFile());
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get the ID of the form used to change this object information
     * @return int type
     */
    public function getUpdateFormId()
    {
        return 'dealer.image.modification';
    }

    /**
     * get the image type according form id
     *
     * @param $formId
     * @return int type
     */
    public static function getTypeFromFormId($formId)
    {
        switch ($formId) {
            case DealerImageHeaderForm::DEALER_IMAGE_HEADER_FORM_ID:
                $imageType = array_search(self::IMAGE_TYPE_HEADER, self::POSSIBLE_TYPE);
                break;
            case DealerImageBoxForm::DEALER_IMAGE_BOX_FORM_ID:
                $imageType = array_search(self::IMAGE_TYPE_BOX, self::POSSIBLE_TYPE);
                break;
            default :
                throw new \InvalidArgumentException("Unknown form id " . $formId);
        }
        return $imageType;
    }

    /**
     * @param $parentId
     * @param $formName
     * @return DealerImage
     */
    public static function fromProductIdAndFormName($parentId, $formName)
    {
        $imageType = DealerImage::getTypeFromFormId($formName);
        $imageSearch = new DealerImageQuery();
        $imageSearch->filterByType($imageType);
        $imageSearch->filterByDealerId($parentId);
        $fileModel = $imageSearch->findOne();
        if ($fileModel === null) {
            $fileModel = new DealerImage();
            $fileModel->setType($imageType);
            $fileModel->setDealerId($parentId);
        }
        return $fileModel;
    }
}
