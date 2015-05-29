<?php namespace Dealer\Controller\Base;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Thelia\Controller\Admin\AbstractCrudController;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Tools\URL;
use Dealer\Event\DealerTabEvent;
use Dealer\Event\DealerTabEvents;
use Dealer\Model\DealerTabQuery;

class DealerTabController extends AbstractCrudController
{
    public function __construct()
    {
        parent::__construct("dealer_tab", "id", "order", AdminResources::MODULE, DealerTabEvents::CREATE,
            DealerTabEvents::UPDATE, DealerTabEvents::DELETE, null, null, "Dealer");
    }

    protected function getCreationForm()
    {
        return $this->createForm("dealer_tab.create");
    }

    protected function getUpdateForm($data = array())
    {
        if (!is_array($data)) {
            $data = array();
        }

        return $this->createForm("dealer_tab.update", "form", $data);
    }

    protected function hydrateObjectForm($object)
    {
        $data = array(
            "id" => $object->getId(),
            "company" => $object->getCompany(),
            "address1" => $object->getAddress1(),
            "address2" => $object->getAddress2(),
            "zipcode" => $object->getZipcode(),
            "city" => $object->getCity(),
            "country" => $object->getCountry(),
            "description" => $object->getDescription(),
            "schedule" => $object->getSchedule(),
            "phone_number" => $object->getPhoneNumber(),
            "web_site" => $object->getWebSite(),
            "latitude" => $object->getLatitude(),
            "longitude" => $object->getLongitude(),
        );

        return $this->getUpdateForm($data);
    }

    protected function getCreationEvent($formData)
    {
        $event = new DealerTabEvent();
        $event->setCompany($formData["company"]);
        $event->setAddress1($formData["address1"]);
        $event->setAddress2($formData["address2"]);
        $event->setZipcode($formData["zipcode"]);
        $event->setCity($formData["city"]);
        $event->setCountry($formData["country"]);
        $event->setDescription($formData["description"]);
        $event->setSchedule($formData["schedule"]);
        $event->setPhoneNumber($formData["phone_number"]);
        $event->setWebSite($formData["web_site"]);
        $event->setLatitude($formData["latitude"]);
        $event->setLongitude($formData["longitude"]);

        return $event;
    }

    protected function getUpdateEvent($formData)
    {
        $event = new DealerTabEvent();
        $event->setId($formData["id"]);
        $event->setCompany($formData["company"]);
        $event->setAddress1($formData["address1"]);
        $event->setAddress2($formData["address2"]);
        $event->setZipcode($formData["zipcode"]);
        $event->setCity($formData["city"]);
        $event->setCountry($formData["country"]);
        $event->setDescription($formData["description"]);
        $event->setSchedule($formData["schedule"]);
        $event->setPhoneNumber($formData["phone_number"]);
        $event->setWebSite($formData["web_site"]);
        $event->setLatitude($formData["latitude"]);
        $event->setLongitude($formData["longitude"]);

        return $event;
    }

    protected function getDeleteEvent()
    {
        $event = new DealerTabEvent();
        $event->setId($this->getRequest()->request->get("dealer_tab_id"));

        return $event;
    }

    protected function eventContainsObject($event)
    {
        return null !== $this->getObjectFromEvent($event);
    }

    protected function getObjectFromEvent($event)
    {
        return $event->getDealerTab();
    }

    protected function getExistingObject()
    {
        return DealerTabQuery::create()->findPk($this->getRequest()->query->get("dealer_tab_id"));
    }

    protected function getObjectLabel($object)
    {
        return '';
    }

    protected function getObjectId($object)
    {
        return $object->getId();
    }

    protected function renderListTemplate($currentOrder)
    {
        $this->getParser()->assign("order", $currentOrder);

        return $this->render("dealer-tabs");
    }

    protected function renderEditionTemplate()
    {
        $this->getParserContext()->set("dealer_tab_id", $this->getRequest()->query->get("dealer_tab_id"));

        return $this->render("dealer-tab-edit");
    }

    protected function redirectToEditionTemplate()
    {
        $id = $this->getRequest()->query->get("dealer_tab_id");

        return new RedirectResponse(URL::getInstance()->absoluteUrl("/admin/module/Dealer/dealer_tab/edit",
            ["dealer_tab_id" => $id, ]));
    }

    protected function redirectToListTemplate()
    {
        return new RedirectResponse(URL::getInstance()->absoluteUrl("/admin/module/Dealer/dealer_tab"));
    }
}
