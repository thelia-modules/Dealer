<?php

namespace Dealer\Plugin;

use Dealer\Model\CustomerFavoriteDealerQuery;
use Thelia\Core\Security\SecurityContext;
use TheliaSmarty\Template\AbstractSmartyPlugin;
use TheliaSmarty\Template\SmartyPluginDescriptor;

class FavoriteDealerSmartyPlugin extends AbstractSmartyPlugin
{
    /** @var SecurityContext */
    protected $securityContext;

    public function __construct(SecurityContext $securityContext)
    {
        $this->securityContext = $securityContext;
    }

    public function getFavoriteDealer(array $params, \Smarty_Internal_Template &$smarty)
    {
        $customer = $this->securityContext->getCustomerUser();

        $favoriteDealerId = null;
        if ($customer !== null) {
            $favorite = CustomerFavoriteDealerQuery::create()->filterByCustomerId($customer->getId())->findOne();
            if ($favorite !== null) {
                $favoriteDealerId = $favorite->getDealerId();
            }
        }
        return $favoriteDealerId;
    }

    /**
     * Define the various smarty plugins handled by this class
     *
     * @return array an array of smarty plugin descriptors
     */
    public function getPluginDescriptors()
    {
        return array(
            new SmartyPluginDescriptor('function', 'favoriteDealer', $this, 'getFavoriteDealer')
        );
    }
}
