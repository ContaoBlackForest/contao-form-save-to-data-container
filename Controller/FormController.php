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

namespace ContaoBlackForest\FormSave\Controller;

use Contao\Controller;
use Contao\Form;
use ContaoBlackForest\FormSave\Event\GetFormDataControllerEvent;

/**
 * The form controller
 */
class FormController
{
    /**
     * Prepare the form data.
     *
     * @param array $submitted
     *
     * @param array $label
     *
     * @param Form  $form
     *
     * @param array $fields
     *
     * @return void
     */
    public function prepareFormData($submitted, $label, Form &$form, $fields)
    {
        if (!$form->storeValues
            && !$form->targetTable
        ) {
            return;
        }

        Controller::loadDataContainer($form->targetTable);

        if (!array_key_exists($form->targetTable, $GLOBALS['TL_DCA'])
            || !array_key_exists('config', $GLOBALS['TL_DCA'][$form->targetTable])
            || !array_key_exists('dataContainer', $GLOBALS['TL_DCA'][$form->targetTable]['config'])
        ) {
            return;
        }

        global $container;

        $eventDispatcher = $container['event-dispatcher'];

        $event = new GetFormDataControllerEvent($eventDispatcher, $form->targetTable);
        $eventDispatcher->dispatch(GetFormDataControllerEvent::NAME, $event);
        $controller = $event->getController();

        if (!$controller) {
            return;
        }

        $controller->setFormData($submitted, $label, $form, $fields);
        $controller->process();

        $form->storeValues = null;
        $form->targetTable = null;
    }
}
