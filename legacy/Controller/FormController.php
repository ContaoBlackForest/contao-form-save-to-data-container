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

            if (in_array($sessionController->getState(), array('edit', 'saved'))) {
                Input::setPost('REQUEST_TOKEN', RequestToken::get());
            }

            return;
        }

        if (!in_array($sessionController->getState(), array('edit', 'saved'))) {
            return;
        }

        Controller::redirect($sessionController->getFormPage());
    }
}
