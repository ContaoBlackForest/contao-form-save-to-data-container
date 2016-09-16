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
