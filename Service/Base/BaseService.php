<?php
/*************************************************************************************/
/*      This file is part of the Thelia package.                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : dev@thelia.net                                                       */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/
/*************************************************************************************/

namespace Dealer\Service\Base;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Core\Event\ActionEvent;

/**
 * Class BaseService
 * @package Dealer\Service\Base
 */
abstract class BaseService extends BaseAdminController
{
    const EVENT_CREATE = null;
    const EVENT_CREATE_BEFORE = null;
    const EVENT_CREATE_AFTER = null;
    const EVENT_UPDATE = null;
    const EVENT_UPDATE_BEFORE = null;
    const EVENT_UPDATE_AFTER = null;
    const EVENT_DELETE = null;

    protected $dispatcher;


    protected function create(Event $event)
    {
        $this->dispatch(static::EVENT_CREATE_BEFORE, $event);
        if (!$event->isPropagationStopped()) {
            $this->createProcess($event);
            if (!$event->isPropagationStopped()) {
                $this->dispatch(static::EVENT_CREATE_AFTER, $event);
            }
        }
    }

    protected function createProcess(Event $event)
    {
        $this->dispatch(static::EVENT_CREATE, $event);
    }

    protected function update(Event $event)
    {
        $this->dispatch(static::EVENT_CREATE_BEFORE, $event);
        $this->updateProcess($event);
        $this->dispatch(static::EVENT_CREATE_AFTER, $event);
    }

    protected function updateProcess(Event $event)
    {
        $this->dispatch(static::EVENT_UPDATE, $event);
    }

    /**
     * Dispatch a Thelia event
     *
     * @param string $eventName a TheliaEvent name, as defined in TheliaEvents class
     * @param ActionEvent $event the action event, or null (a DefaultActionEvent will be dispatched)
     */
    protected function dispatch($eventName, ActionEvent $event = null)
    {
        if ($event == null) {
            $event = new DefaultActionEvent();
        }

        $this->getDispatcher()->dispatch($eventName, $event);
    }

    /**
     * Return the event dispatcher,
     *
     * @return \Symfony\Component\EventDispatcher\EventDispatcher
     */
    public function getDispatcher()
    {
        return $this->dispatcher;
    }

    public function setDispatcher(EventDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

}