<?php

namespace Dealer\Plugin;

use Dealer\Model\CustomerFavoriteDealerQuery;
use Symfony\Component\HttpFoundation\RequestStack;
use Thelia\Core\Security\SecurityContext;
use TheliaSmarty\Template\AbstractSmartyPlugin;
use TheliaSmarty\Template\SmartyPluginDescriptor;

class FavoriteDealerSmartyPlugin extends AbstractSmartyPlugin
{
    /** @var SecurityContext */
    protected $securityContext;

    /** @var RequestStack */
    protected $requestStack;

    public function __construct(SecurityContext $securityContext, RequestStack $requestStack)
    {
        $this->securityContext = $securityContext;
        $this->requestStack = $requestStack;
    }

    public function getFavoriteDealer(array $params, \Smarty_Internal_Template &$smarty)
    {
        $customer = $this->securityContext->getCustomerUser();

        $favoriteDealerId = null;
        if ($customer !== null) {
            $favoriteDealerId = (int) $this->requestStack->getCurrentRequest()->getSession()->get("favoriteDealer");
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
