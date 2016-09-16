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

use Contao\Database;
use Contao\DC_Table;
use Contao\Form;
use Contao\Input;
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
     * AbstractFormDataProviderController constructor.
     *
     * @param $dataProvider string The data provider name.
     */
    public function __construct($dataProvider)
    {
        $this->dataProvider = $dataProvider;
        $this->setDataContainer();
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
        global $container;

        $eventDispatcher = $container['event-dispatcher'];

        $prePrepareSubmitDataEvent = new PrePrepareSubmitDataEvent($eventDispatcher, $this->getName(), $this);
        $eventDispatcher->dispatch(PrePrepareSubmitDataEvent::NAME, $prePrepareSubmitDataEvent);

        $postPrepareSubmitDataEvent = new PostPrepareSubmitDataEvent($eventDispatcher, $this->getName(), $this);
        $eventDispatcher->dispatch(PostPrepareSubmitDataEvent::NAME, $postPrepareSubmitDataEvent);

        $this->save();
    }

    /**
     * Save the form data.
     *
     * @return void
     */
    protected function save()
    {
        $database           = Database::getInstance();
        $excludedProperties = $database->getFieldNames($this->getName());

        foreach ($excludedProperties as $excludedProperty) {
            $GLOBALS['TL_DCA'][$this->getName()]['fields'][$excludedProperty]['exclude'] = false;
        }

        Input::setPost('FORM_SUBMIT', $this->getName());
        Input::setPost('FORM_FIELDS', array($GLOBALS['TL_DCA']['tl_member']['palettes']['default']));

        $this->getDataContainer()->create($this->getSubmitData());
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
}
