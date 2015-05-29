<?php namespace Dealer\Event\Base;

use Thelia\Core\Event\ActionEvent;
use Dealer\Model\DealerTab;

class DealerTabEvent extends ActionEvent
{
    protected $id;
    protected $company;
    protected $address1;
    protected $address2;
    protected $zipcode;
    protected $city;
    protected $country;
    protected $description;
    protected $schedule;
    protected $phoneNumber;
    protected $webSite;
    protected $latitude;
    protected $longitude;
    protected $dealerTab;
    public function getId()
    {
        return $this->id;
    }
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }
    public function getCompany()
    {
        return $this->company;
    }
    public function setCompany($company)
    {
        $this->company = $company;
        return $this;
    }
    public function getAddress1()
    {
        return $this->address1;
    }
    public function setAddress1($address1)
    {
        $this->address1 = $address1;
        return $this;
    }
    public function getAddress2()
    {
        return $this->address2;
    }
    public function setAddress2($address2)
    {
        $this->address2 = $address2;
        return $this;
    }
    public function getZipcode()
    {
        return $this->zipcode;
    }
    public function setZipcode($zipcode)
    {
        $this->zipcode = $zipcode;
        return $this;
    }
    public function getCity()
    {
        return $this->city;
    }
    public function setCity($city)
    {
        $this->city = $city;
        return $this;
    }
    public function getCountry()
    {
        return $this->country;
    }
    public function setCountry($country)
    {
        $this->country = $country;
        return $this;
    }
    public function getDescription()
    {
        return $this->description;
    }
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }
    public function getSchedule()
    {
        return $this->schedule;
    }
    public function setSchedule($schedule)
    {
        $this->schedule = $schedule;
        return $this;
    }
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }
    public function getWebSite()
    {
        return $this->webSite;
    }
    public function setWebSite($webSite)
    {
        $this->webSite = $webSite;
        return $this;
    }
    public function getLatitude()
    {
        return $this->latitude;
    }
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
        return $this;
    }
    public function getLongitude()
    {
        return $this->longitude;
    }
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
        return $this;
    }
    public function getDealerTab()
    {
        return $this->dealerTab;
    }
    public function setDealerTab(DealerTab $dealerTab)
    {
        $this->dealerTab = $dealerTab;
        return $this;
    }
}
