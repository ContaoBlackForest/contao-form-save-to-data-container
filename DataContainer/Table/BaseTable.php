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

namespace ContaoBlackForest\FormSave\DataContainer\Table;

use Contao\Controller;
use Contao\Input;

/**
 * The data container base table.
 */
class BaseTable
{
    /**
     * Initialize on load callback for data container table.
     *
     * @param $dataProvider string The data provider name.
     *
     * @return void
     */
    public function initializeOnLoadCallback($dataProvider)
    {
        if ($GLOBALS['TL_DCA'][$dataProvider]['config']['dataContainer'] !== 'Table'
            || !array_key_exists('FORM_DATA', $_SESSION)
            || !array_key_exists('id', $_SESSION['FORM_DATA'])
            || Input::get('act') !== 'edit'
        ) {
            return;
        }

        $GLOBALS['TL_DCA'][$dataProvider]['config']['onload_callback'][] = array(__CLASS__, 'parseSessionFormData');
    }

    /**
     * Parse the session form data.
     * If this not the right information, remove the form data from session.
     *
     * @return void
     */
    public function parseSessionFormData()
    {
        if (Input::get('id') === $_SESSION['FORM_DATA']['id']) {
            return;
        }

        unset($_SESSION['FORM_DATA']);

        Controller::reload();
    }
}
