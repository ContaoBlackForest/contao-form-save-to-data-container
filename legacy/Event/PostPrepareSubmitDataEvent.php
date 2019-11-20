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

namespace ContaoBlackForest\FormSave\Event;

use ContaoBlackForest\FormSave\Controller\AbstractFormDataProviderController;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * The event for post prepare submit data event.
 */
class PostPrepareSubmitDataEvent extends Event
{
    /**
     * The event name.
     */
    const NAME = 'ContaoBlackForest\FormSave\Event\PostPrepareSubmitDataEvent';

    /**
     * The data provider name.
     *
     * @var string $dataProvider
     */
    protected $dataProvider;

    /**
     * The form data provider controller.
     *
     * @var AbstractFormDataProviderController $controller
     */
    protected $controller;

    /**
     * The event dispatcher.
     *
     * @var EventDispatcherInterface $eventDispatcher
     */
    protected $eventDispatcher;

    /**
     * PostPrepareSubmitDataEvent constructor.
     *
     * @param EventDispatcherInterface           $eventDispatcher The event dispatcher.
     *
     * @param string                             $dataProvider    The data provider name.
     *
     * @param AbstractFormDataProviderController $controller      The controller.
     */
    public function __construct(EventDispatcherInterface $eventDispatcher, $dataProvider, $controller)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->dataProvider    = $dataProvider;
        $this->controller      = $controller;
    }

    /**
     * Return the data provider name.
     *
     * @return string The data provider.
     */
    public function getName()
    {
        return $this->dataProvider;
    }

    /**
     * Return the form data provider controller.
     *
     * @return AbstractFormDataProviderController The controller.
     */
    public function getController()
    {
        return $this->controller;
    }
}
