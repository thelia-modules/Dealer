<?php

namespace Dealer\Test\PhpUnit\Base;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Thelia\Core\HttpFoundation\Request;
use Thelia\Core\HttpFoundation\Session\Session;
use Thelia\Core\Security\SecurityContext;
use Thelia\Core\Translation\Translator;
use Thelia\Log\Tlog;

/**
 * Class ContainerAwareTestCase
 * @package Dealer\Test
 */
abstract class ContainerAwareTestCase extends \PHPUnit_Framework_TestCase
{
    protected $import;

    /** @var ContainerBuilder */
    protected $container;

    /** @var  Session */
    protected $session;

    public function __construct()
    {
        parent::__construct();

        Tlog::getNewInstance();

        $this->session = $this->getSession();
        $this->getContainer();

        $this->initializeRequest();
    }

    public function getContainer()
    {
        if (null == $this->container) {
            $this->container = new \Symfony\Component\DependencyInjection\ContainerBuilder();
        }

        $this->container->set("thelia.translator", new Translator(new Container()));

        $this->initializeRequest();


        $dispatcher = $this->getMock("Symfony\Component\EventDispatcher\EventDispatcherInterface");
        $this->container->set("event_dispatcher", $dispatcher);

        $this->buildContainer($this->container);
    }

    public function initializeRequest($attributes = [], $query = [], $request = [])
    {
        $request = new Request(
            $query,
            $request,
            $attributes
        );
        $request->setSession($this->getSession());
        $this->container->set("request", $request);
        $this->container->set("thelia.securitycontext", new SecurityContext($request));
    }

    public function getSession()
    {
        return new Session(new MockArraySessionStorage());
    }

    /**
     * Use this method to build the container with the services that you need.
     */
    abstract protected function buildContainer(ContainerBuilder $container);
}