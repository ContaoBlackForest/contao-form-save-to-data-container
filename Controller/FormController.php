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
use Contao\Input;
use Contao\RequestToken;
use ContaoBlackForest\FormSave\Event\GetFormDataControllerEvent;

/**
 * The form controller
 */
class FormController
{
    /**
     * Prepare form data for save form to data container.
     *
     * @param array $submitted The submit data.
     *
     * @param array $label     The labels.
     *
     * @param Form  $form      The form.
     *
     * @param array $fields    The form fields.
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

    /**
     * Handle the redirect after create with data container.
     *
     * @return void
     */
    public function initializeSystem()
    {
        if (TL_MODE !== 'FE') {
            return;
        }

        $sessionController = new SessionController();
        if ($sessionController->getEditId()
            || $sessionController->getState() === 'is_edit'
        ) {
            Input::setPost('FORM_SUBMIT', $sessionController->getPostFormSubmit());
        }

        if (!Input::get('act')
            || !Input::get('id')
            || !Input::get('s2e')
        ) {
            if ($sessionController->getState() === 'edit'
                && !$sessionController->getEditId()
            ) {
                $sessionController->removeSession();
            }

            if ($sessionController->getState() === 'edit') {
                Input::setPost('REQUEST_TOKEN', RequestToken::get());
            }

            return;
        }

        if ($sessionController->getState() !== 'edit') {
            return;
        }

        Controller::redirect($sessionController->getFormPage());
    }
}
