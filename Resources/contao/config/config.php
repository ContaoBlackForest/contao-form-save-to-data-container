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

/**
 * Prepare form data.
 */
$GLOBALS['TL_HOOKS']['prepareFormData'][] =
    array('ContaoBlackForest\FormSave\Controller\FormController', 'prepareFormData');


/**
 * Handle the redirect after create with data container.
 */
$GLOBALS['TL_HOOKS']['initializeSystem'][] =
    array('ContaoBlackForest\FormSave\Controller\FormController', 'initializeSystem');

/**
 * Initialize on load callback for data container table.
 */
$GLOBALS['TL_HOOKS']['loadDataContainer'][] =
    array('ContaoBlackForest\FormSave\DataContainer\Table\BaseTable', 'initializeDataContainerCallback');
