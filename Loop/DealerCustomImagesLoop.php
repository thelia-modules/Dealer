<?php

namespace Dealer\Loop;


use Dealer\Model\DealerImage;
use Dealer\Model\DealerImageQuery;
use Thelia\Core\Event\Image\ImageEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Element\LoopResultRow;
use Thelia\Core\Template\Element\PropelSearchLoopInterface;
use Thelia\Core\Template\Loop\Argument\Argument;
use Thelia\Core\Template\Loop\Image;
use Thelia\Log\Tlog;

class DealerCustomImagesLoop extends Image
{
    const ARG_IMAGE_TYPE = "image_type";

    /**
     * @var array Possible standard image sources
     */
    protected $possible_sources = array('product', 'category');

    /**
     * @return DealerImageQuery|\Propel\Runtime\ActiveQuery\ModelCriteria|\Thelia\Model\ProductDocumentQuery
     */
    public function buildModelCriteria()
    {
        $this->objectType = null;
        if (!empty($this->getProduct())) {
            $this->objectType = $this->possible_sources[0];
        }

        if (is_null($this->objectType)) {
            throw new \InvalidArgumentException("Argument type must be specified");
        }

        $type = $this->getArgValue(self::ARG_IMAGE_TYPE);

        $search = DealerImageQuery::create();
        $typeIndex = array_search($type, DealerImage::POSSIBLE_TYPE);
        $search->filterByType($typeIndex);
        $search->filterByDealerId($this->getProduct());


        $this->configureI18nProcessing($search);
        return $search;
    }

    protected function getArgDefinitions()
    {
        $args = parent::getArgDefinitions();
        $args->addArgument(Argument::createAlphaNumStringTypeArgument(self::ARG_IMAGE_TYPE));
        return $args;
    }

    /**
     * @param LoopResult $loopResult
     * @return LoopResult
     */
    public function parseResults(LoopResult $loopResult)
    {
        // Create image processing event
        $event = new ImageEvent();

        // Prepare tranformations
        $width = $this->getWidth();
        $height = $this->getHeight();
        $rotation = $this->getRotation();
        $background_color = $this->getBackgroundColor();
        $quality = $this->getQuality();
        $effects = $this->getEffects();

        $event->setAllowZoom($this->getAllowZoom());

        if (! is_null($effects)) {
            $effects = explode(',', $effects);
        }

        switch ($this->getResizeMode()) {
            case 'crop':
                $resizeMode = \Thelia\Action\Image::EXACT_RATIO_WITH_CROP;
                break;

            case 'borders':
                $resizeMode = \Thelia\Action\Image::EXACT_RATIO_WITH_BORDERS;
                break;

            case 'none':
            default:
                $resizeMode = \Thelia\Action\Image::KEEP_IMAGE_RATIO;

        }

        /** @var DealerImage $result */
        foreach ($loopResult->getResultDataCollection() as $result) {
            // Setup required transformations
            if (! is_null($width)) {
                $event->setWidth($width);
            }
            if (! is_null($height)) {
                $event->setHeight($height);
            }
            $event->setResizeMode($resizeMode);
            if (! is_null($rotation)) {
                $event->setRotation($rotation);
            }
            if (! is_null($background_color)) {
                $event->setBackgroundColor($background_color);
            }
            if (! is_null($quality)) {
                $event->setQuality($quality);
            }
            if (! is_null($effects)) {
                $event->setEffects($effects);
            }

            // Put source image file path
            $sourceFilePath = sprintf(
                '%s/%s',
                $result->getUploadDir(),
                $result->getFile()
            );

            $event->setSourceFilepath($sourceFilePath);
            $event->setCacheSubdirectory($this->objectType);

            $loopResultRow = new LoopResultRow($result);

            $loopResultRow
                ->set("ID", $result->getId())
                ->set("LOCALE", $this->locale)
                ->set("ORIGINAL_IMAGE_PATH", $sourceFilePath)
                ->set("TITLE", $result->getVirtualColumn('i18n_TITLE'))
                ->set("CHAPO", $result->getVirtualColumn('i18n_CHAPO'))
                ->set("DESCRIPTION", $result->getVirtualColumn('i18n_DESCRIPTION'))
                ->set("POSTSCRIPTUM", $result->getVirtualColumn('i18n_POSTSCRIPTUM'))
                ->set("OBJECT_TYPE", $this->objectType)
                ->set("OBJECT_ID", $this->objectId)
            ;

            $addRow = true;

            $returnErroredImages = $this->getBackendContext() || ! $this->getIgnoreProcessingErrors();

            try {
                // Dispatch image processing event
                $this->dispatcher->dispatch($event, TheliaEvents::IMAGE_PROCESS);

                $loopResultRow
                    ->set("IMAGE_URL", $event->getFileUrl())
                    ->set("ORIGINAL_IMAGE_URL", $event->getOriginalFileUrl())
                    ->set("IMAGE_PATH", $event->getCacheFilepath())
                    ->set("PROCESSING_ERROR", false)
                ;
            } catch (\Exception $ex) {
                // Ignore the result and log an error
                Tlog::getInstance()->addError(sprintf("Failed to process image in lapelerine_product_image loop: %s", $ex->getMessage()));

                if ($returnErroredImages) {
                    $loopResultRow
                        ->set("IMAGE_URL", '')
                        ->set("ORIGINAL_IMAGE_URL", '')
                        ->set("IMAGE_PATH", '')
                        ->set("PROCESSING_ERROR", true)
                    ;
                } else {
                    $addRow = false;
                }
            }

            if ($addRow) {
                $this->addOutputFields($loopResultRow, $result);

                $loopResult->addRow($loopResultRow);
            }
        }

        return $loopResult;
    }
}