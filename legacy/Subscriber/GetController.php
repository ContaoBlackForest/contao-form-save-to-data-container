<?php

/**
 * This file is part of contaoblackforest/contao-form-save-to-data-container.
 *
 * (c) 2016-2019 The Contao Blackforest team.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * This project is provided in good faith and hope to be usable by anyone.
 *
 * @package    contaoblackforest/contao-form-save-to-data-container
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @copyright  20116-2019 The Contao Blackforest team.
 * @license    https://github.com/contaoblackforest/contao-form-save-to-data-container/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
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
