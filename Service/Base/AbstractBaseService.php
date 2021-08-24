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
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Thelia\Core\Event\ActionEvent;
use Thelia\Core\Event\DefaultActionEvent;

/**
 * Class BaseService
 * @package Dealer\Service\Base
 */
abstract class AbstractBaseService
{
    const EVENT_CREATE = null;
    const EVENT_CREATE_BEFORE = null;
    const EVENT_CREATE_AFTER = null;
    const EVENT_UPDATE = null;
    const EVENT_UPDATE_BEFORE = null;
    const EVENT_UPDATE_AFTER = null;
    const EVENT_DELETE = null;
    const EVENT_DELETE_BEFORE = null;
    const EVENT_DELETE_AFTER = null;

    protected EventDispatcherInterface $dispatcher;

    /**
     * AbstractBaseService constructor.
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    //CREATION

    /**
     * Allow to proccess Creation Even Sender
     * @param ActionEvent $event
     */
    protected function create(ActionEvent $event)
    {
        $this->dispatch(static::EVENT_CREATE_BEFORE, $event);
        if (!$event->isPropagationStopped()) {
            $this->createProcess($event);
            if (!$event->isPropagationStopped()) {
                $this->dispatch(static::EVENT_CREATE_AFTER, $event);
            }
        }
    }

    /**
     * Allow to create an object from an Event
     * @param ActionEvent $event
     */
    protected function createProcess(ActionEvent $event)
    {
        $this->dispatch(static::EVENT_CREATE, $event);
    }

    // UPDATE

    /**
     * Allow to send Update Events
     * @param ActionEvent $event
     */
    protected function update(ActionEvent $event)
    {
        $this->dispatch(static::EVENT_UPDATE_BEFORE, $event);
        if (!$event->isPropagationStopped()) {
            $this->updateProcess($event);
            if (!$event->isPropagationStopped()) {
                $this->dispatch(static::EVENT_UPDATE_AFTER, $event);
            }
        }
    }


    /**
     * Allow to update an object from an event
     * @param ActionEvent $event
     */
    protected function updateProcess(ActionEvent $event)
    {
        $this->dispatch(static::EVENT_UPDATE, $event);
    }

    // DELETE

    protected function delete(ActionEvent $event)
    {
        $this->dispatch(static::EVENT_DELETE_BEFORE, $event);
        if (!$event->isPropagationStopped()) {
            $this->deleteProcess($event);
            if (!$event->isPropagationStopped()) {
                $this->dispatch(static::EVENT_DELETE_AFTER, $event);
            }
        }
    }

    protected function deleteProcess(ActionEvent $event)
    {
        $this->dispatch(static::EVENT_DELETE, $event);
    }


    // DEPENDENCIES

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

        $this->getDispatcher()->dispatch($event, $eventName);
    }

    /**
     * Return the event dispatcher,
     *
     * @return EventDispatcherInterface
     */
    public function getDispatcher()
    {
        return $this->dispatcher;
    }

    public function setDispatcher(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }
}
