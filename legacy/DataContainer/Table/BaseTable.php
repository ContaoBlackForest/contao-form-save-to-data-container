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

namespace ContaoBlackForest\FormSave\DataContainer\Table;

use Contao\Controller;
use Contao\Database;
use Contao\DataContainer;
use Contao\Input;
use ContaoBlackForest\FormSave\Controller\SessionController;

/**
 * The data container base table.
 */
class BaseTable
{
    /**
     * Initialize data container callback for data container table.
     *
     * @param string $dataProvider The data provider name.
     *
     * @return void
     */
    public function initializeDataContainerCallback($dataProvider)
    {
        $this->handleParseSessionFormData($dataProvider);

        $this->handleParseEmptyTimestamp($dataProvider);
    }

    /**
     * Handle parse session form data for data container table.
     *
     * @param string $dataProvider The data provider name.
     *
     * @return void
     */
    protected function handleParseSessionFormData($dataProvider)
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

    /**
     * Handle parse empty timestamp for data container table.
     *
     * @param string $dataProvider The data provider name.
     *
     * @return void
     */
    protected function handleParseEmptyTimestamp($dataProvider)
    {
        if ($GLOBALS['TL_DCA'][$dataProvider]['config']['dataContainer'] !== 'Table') {
            return;
        }

        $sessionController = new SessionController();

        if ($sessionController->getState() !== 'edit'
            || !$sessionController->getSubmitData()
        ) {
            return;
        }

        $firstField = array_keys($sessionController->getSubmitData())[0];
        if (!array_key_exists($firstField, $GLOBALS['TL_DCA'][$dataProvider]['fields'])) {
            return;
        }

        $GLOBALS['TL_DCA'][$dataProvider]['fields'][$firstField]['save_callback'][] =
            array(__CLASS__, 'parseEmptyTimestamp');
    }

    /**
     * Parse empty timestamp value after save the form.
     *
     * @param  mixed        $value
     *
     * @param DataContainer $dc The data container.
     *
     * @return mixed
     */
    public function parseEmptyTimestamp($value, DataContainer $dataContainer)
    {
        $activeRecord = $dataContainer->activeRecord;
        if (method_exists($activeRecord, 'tstamp')
            || $activeRecord->tstamp !== '0'
        ) {
            return $value;
        }

        $database = Database::getInstance();
        $database->prepare('UPDATE ' . $dataContainer->table . ' %s WHERE id=?')
            ->set(array('tstamp' => time()))
            ->execute($dataContainer->id);

        return $value;
    }
}
