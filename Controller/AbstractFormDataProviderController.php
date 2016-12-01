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

use Contao\BackendUser;
use Contao\Controller;
use Contao\Database;
use Contao\DC_Table;
use Contao\FilesModel;
use Contao\Form;
use Contao\Input;
use Contao\RequestToken;
use Contao\StringUtil;
use ContaoBlackForest\FormSave\Event\PostPrepareSubmitDataEvent;
use ContaoBlackForest\FormSave\Event\PrePrepareSubmitDataEvent;

/**
 * The trait for form data provider controller.
 */
abstract class AbstractFormDataProviderController
{
    /**
     * The data provider name.
     *
     * @var string $dataProvider
     */
    protected $dataProvider;

    /**
     * The data container.
     *
     * @var DC_Table $dataContainer
     */
    protected $dataContainer;

    /**
     * The submitted data.
     *
     * @var array $submitData
     */
    protected $submitData;

    /**
     * The labels from the form.
     *
     * @var array $label
     */
    protected $label;

    /**
     * The form.
     *
     * @var Form $form
     */
    protected $form;

    /**
     * The form fields.
     *
     * @var array $fields
     */
    protected $fields;

    /**
     * The session controller.
     *
     * @var SessionController $sessionController
     */
    protected $sessionController;

    /**
     * AbstractFormDataProviderController constructor.
     *
     * @param $dataProvider string The data provider name.
     */
    public function __construct($dataProvider)
    {
        $this->dataProvider = $dataProvider;
        $this->setDataContainer();
        $this->sessionController = new SessionController();
    }

    /**
     * Return the name of the data provider
     *
     * @return string The data provider name.
     */
    public function getName()
    {
        return $this->dataProvider;
    }

    /**
     * Return the data container.
     *
     * @return DC_Table The data container.
     */
    public function getDataContainer()
    {
        return $this->dataContainer;
    }

    /**
     * Set the data container.
     *
     * @return void
     */
    protected function setDataContainer()
    {
        $dataContainer = $GLOBALS['TL_DCA'][$this->getName()]['config']['dataContainer'];

        switch ($dataContainer) {
            case 'Table':
                $this->dataContainer = new DC_Table($this->getName());
                break;

            default:
        }
    }

    /**
     * Set the form data.
     *
     * @param array $submitData The submitted data.
     *
     * @param array $label      The form labels.
     *
     * @param Form  $form       The form.
     *
     * @param array $fields     The form fields.
     *
     * @return void
     */
    public function setFormData(array $submitData, array $label, Form $form, array $fields)
    {
        $this->submitData = $submitData;
        $this->label      = $label;
        $this->form       = $form;
        $this->fields     = $fields;
    }

    /**
     * Process the controller.
     *
     * @return void
     */
    public function process()
    {
        $sessionController = $this->getSessionController();

        if ($sessionController->getState() !== 'create') {
            $this->save();
        }

        global $container;

        $eventDispatcher = $container['event-dispatcher'];

        $prePrepareSubmitDataEvent = new PrePrepareSubmitDataEvent($eventDispatcher, $this->getName(), $this);
        $eventDispatcher->dispatch(PrePrepareSubmitDataEvent::NAME, $prePrepareSubmitDataEvent);

        $postPrepareSubmitDataEvent = new PostPrepareSubmitDataEvent($eventDispatcher, $this->getName(), $this);
        $eventDispatcher->dispatch(PostPrepareSubmitDataEvent::NAME, $postPrepareSubmitDataEvent);

        $sessionController->setSubmitData($this->getSubmitData());

        $this->save();
    }

    /**
     * Save the form data.
     *
     * @return void
     */
    protected function save()
    {
        $sessionController = $this->getSessionController();

        $database           = Database::getInstance();
        $excludedProperties = $database->getFieldNames($this->getName());

        Controller::loadDataContainer($this->getName());

        foreach ($excludedProperties as $excludedProperty) {
            $GLOBALS['TL_DCA'][$this->getName()]['fields'][$excludedProperty]['exclude'] = false;
        }

        $result = $database->prepare('SELECT * FROM ' . $this->getName() . ' WHERE id=?')
            ->limit(1)
            ->execute($sessionController->getEditId());

        $submitData = $sessionController->getSubmitData();

        if ($result->count() > 0) {
            Input::setGet('id', $result->id);

            $backendUser        = BackendUser::getInstance();
            $backendUser->admin = true;

            $this->setDataContainer();
        }

        Input::setPost('FORM_SUBMIT', $this->getName());
        Input::setPost('REQUEST_TOKEN', RequestToken::get());
        Input::setPost('FORM_FIELDS', $this->prepareFormFields($this->getDataContainer()));

        if ($sessionController->getState() === 'create') {
            $sessionController->setState('edit');

            $this->getDataContainer()->create($this->getSubmitData());
        }

        if ($sessionController->getState() === 'edit') {
            $sessionController->setState('saved');

            if ($sessionController->getEditId()) {
                foreach ($result->row() as $property => $value) {
                    $fileModel = FilesModel::findByUuid($value);
                    if ($fileModel) {
                        $value = StringUtil::binToUuid($value);
                    }

                    Input::setPost($property, $value);
                }

                $this->getDataContainer()->edit($sessionController->getEditId());
            }
        }

        $sessionController->removeSession();
    }

    /**
     * Return the submitted data.
     *
     * @return array The submitted data.
     */
    public function getSubmitData()
    {
        return $this->submitData;
    }

    /**
     * Set the submitted data.
     *
     * @param array $submitData The submitted data.
     *
     * @return void
     */
    public function setSubmitData($submitData)
    {
        $this->submitData = $submitData;
    }

    /**
     * Return the label.
     *
     * @return array The label.
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Return the form.
     *
     * @return Form The form.
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * Return the fields.
     *
     * @return array The fields.
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Return the session.
     *
     * @return SessionController The session.
     */
    public function getSessionController()
    {
        return $this->sessionController;
    }

    /**
     * Prepare form fields form dc table.
     *
     * @param DC_Table $dataContainer The data container.
     *
     * @return array The form fields.
     */
    protected function prepareFormFields(DC_Table $dataContainer)
    {
        $formFields = $dataContainer->getPalette();

        return (array) $formFields;
    }
}
