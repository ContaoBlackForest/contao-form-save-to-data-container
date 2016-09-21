<?php

/**
 * Copyright © ContaoBlackForest
 *
 * @package   contao-form-save-to-data-container
 * @author    Sven Baumann <baumann.sv@gmail.com>
 * @author    Dominik Tomasi <dominik.tomasi@gmail.com>
 * @license   GNU/LGPL
 * @copyright Copyright 2014-2016 ContaoBlackForest
 */

namespace ContaoBlackForest\FormSave\Subscriber;

use ContaoBlackForest\FormSave\Controller\FormDataNewsController;
use ContaoBlackForest\FormSave\Event\GetFormDataControllerEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * The Subscriber for get controller.
 */
class GetController implements EventSubscriberInterface
{
    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2')))
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            GetFormDataControllerEvent::NAME => array(
                array('getFormDataNewsController')
            )
        );
    }

    /**
     * Get the form data news controller.
     *
     * @param GetFormDataControllerEvent $event The event.
     *
     * @return void
     */
    public function getFormDataNewsController(GetFormDataControllerEvent $event)
    {
        if ($event->getName() !== 'tl_news') {
            return;
        }

        $controller = new FormDataNewsController($event->getName());

        $event->setController($controller);
    }
}
