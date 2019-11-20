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


/**
 * Prepare form data for save form to data container.
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
