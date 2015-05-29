<?php namespace Dealer\Action\Base;

use Dealer\Model\Map\DealerTabTableMap;
use Dealer\Event\DealerTabEvent;
use Dealer\Event\DealerTabEvents;
use Dealer\Model\DealerTabQuery;
use Dealer\Model\DealerTab;
use Thelia\Action\BaseAction;
use Thelia\Core\Event\ToggleVisibilityEvent;
use Thelia\Core\Event\UpdatePositionEvent;
use Propel\Runtime\Propel;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Thelia\Core\Event\TheliaEvents;
use \Thelia\Core\Event\TheliaFormEvent;

class DealerTabAction extends BaseAction implements EventSubscriberInterface
{
    public function create(DealerTabEvent $event)
    {
        $this->createOrUpdate($event, new DealerTab());
    }
    public function update(DealerTabEvent $event)
    {
        $model = $this->getDealerTab($event);
        $this->createOrUpdate($event, $model);
    }
    public function delete(DealerTabEvent $event)
    {
        $this->getDealerTab($event)->delete();
    }
    protected function createOrUpdate(DealerTabEvent $event, DealerTab $model)
    {
        $con = Propel::getConnection(DealerTabTableMap::DATABASE_NAME);
        $con->beginTransaction();
        try {
            if (null !== $id = $event->getId()) {
                $model->setId($id);
            }
            if (null !== $company = $event->getCompany()) {
                $model->setCompany($company);
            }
            if (null !== $address1 = $event->getAddress1()) {
                $model->setAddress1($address1);
            }
            if (null !== $address2 = $event->getAddress2()) {
                $model->setAddress2($address2);
            }
            if (null !== $zipcode = $event->getZipcode()) {
                $model->setZipcode($zipcode);
            }
            if (null !== $city = $event->getCity()) {
                $model->setCity($city);
            }
            if (null !== $country = $event->getCountry()) {
                $model->setCountry($country);
            }
            if (null !== $description = $event->getDescription()) {
                $model->setDescription($description);
            }
            if (null !== $schedule = $event->getSchedule()) {
                $model->setSchedule($schedule);
            }
            if (null !== $phoneNumber = $event->getPhoneNumber()) {
                $model->setPhoneNumber($phoneNumber);
            }
            if (null !== $webSite = $event->getWebSite()) {
                $model->setWebSite($webSite);
            }
            if (null !== $latitude = $event->getLatitude()) {
                $model->setLatitude($latitude);
            }
            if (null !== $longitude = $event->getLongitude()) {
                $model->setLongitude($longitude);
            }
            $model->save($con);
            $con->commit();
        } catch (\Exception $e) {
            $con->rollback();
            throw $e;
        }
        $event->setDealerTab($model);
    }
    protected function getDealerTab(DealerTabEvent $event)
    {
        $model = DealerTabQuery::create()->findPk($event->getId());
        if (null === $model) {
            throw new \RuntimeException(sprintf("The 'dealer_tab' id '%d' doesn't exist", $event->getId()));
        }
        return $model;
    }
    public function beforeCreateFormBuild(TheliaFormEvent $event)
    {
    }
    public function beforeUpdateFormBuild(TheliaFormEvent $event)
    {
    }
    public function afterCreateFormBuild(TheliaFormEvent $event)
    {
    }
    public function afterUpdateFormBuild(TheliaFormEvent $event)
    {
    }
    public static function getSubscribedEvents()
    {
        return array( DealerTabEvents::CREATE => array("create", 128), DealerTabEvents::UPDATE => array("update", 128), DealerTabEvents::DELETE => array("delete", 128), TheliaEvents::FORM_BEFORE_BUILD . ".dealer_tab_create" => array("beforeCreateFormBuild", 128), TheliaEvents::FORM_BEFORE_BUILD . ".dealer_tab_update" => array("beforeUpdateFormBuild", 128), TheliaEvents::FORM_AFTER_BUILD . ".dealer_tab_create" => array("afterCreateFormBuild", 128), TheliaEvents::FORM_AFTER_BUILD . ".dealer_tab_update" => array("afterUpdateFormBuild", 128), );
    }
}
