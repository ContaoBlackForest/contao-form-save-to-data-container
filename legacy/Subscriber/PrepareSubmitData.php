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

namespace ContaoBlackForest\FormSave\Subscriber;

use Contao\Database;
use Contao\FilesModel;
use ContaoBlackForest\FormSave\Event\PostPrepareSubmitDataEvent;
use ContaoBlackForest\FormSave\Event\PrePrepareSubmitDataEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * The Subscriber for prepare submit data.
 */
class PrepareSubmitData implements EventSubscriberInterface
{
    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2')))
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            PrePrepareSubmitDataEvent::NAME => array(
                array('validateAllowedProperties', 999),
                array('prepareUploadProperties', 999)
            ),

            PostPrepareSubmitDataEvent::NAME => array(
                array('setSubmitDefaultsNews', 999)
            )
        );
    }

    /**
     * Validate allowed properties.
     *
     * @param PrePrepareSubmitDataEvent $event The event.
     *
     * @return void
     */
    public function validateAllowedProperties(PrePrepareSubmitDataEvent $event)
    {
        $controller = $event->getController();
        $submitData = $controller->getSubmitData();

        $database      = Database::getInstance();
        $propertyNames = $database->getFieldNames($event->getName());

        foreach (array_keys($submitData) as $submitProperty) {
            if (in_array($submitProperty, $propertyNames, null)) {
                continue;
            }

            unset($submitData[$submitProperty]);
        }

        $controller->setSubmitData($submitData);
    }

    /**
     * Set submit default values news.
     *
     * @param PostPrepareSubmitDataEvent $event The event.
     *
     * @return void
     */
    public function setSubmitDefaultsNews(PostPrepareSubmitDataEvent $event)
    {
        $controller = $event->getController();
        $submitData = $controller->getSubmitData();
        $form       = $controller->getForm();
        $model      = $form->getModel();

        $submitData['author'] = $model->author;
        $submitData['pid']    = $model->news_archive;

        $controller->setSubmitData($submitData);
    }

    /**
     * Prepare upload properties.
     *
     * @param PrePrepareSubmitDataEvent $event The event.
     *
     * @return void
     */
    public function prepareUploadProperties(PrePrepareSubmitDataEvent $event)
    {
        $controller = $event->getController();
        $submitData = $controller->getSubmitData();
        $fields     = $controller->getFields();

        foreach ($fields as $field) {
            if ($field->type !== 'upload'
                || !$field->storeFile
                || !array_key_exists('FILES', $_SESSION)
                || !array_key_exists($field->name, $_SESSION['FILES'])
            ) {
                continue;
            }

            $fileName = $_SESSION['FILES'][$field->name]['name'];

            $uploadFolderUuid = null;
            if ($field->uploadFolder) {
                $uploadFolderUuid = $field->uploadFolder;
            }

            if (!$uploadFolderUuid) {
                return;
            }

            $folder = FilesModel::findByUuid($uploadFolderUuid);
            $file = FilesModel::findByPath($folder->path . DIRECTORY_SEPARATOR . $fileName);

            $submitData[$field->name] = $file->uuid;
        }

        $controller->setSubmitData($submitData);
    }
}
