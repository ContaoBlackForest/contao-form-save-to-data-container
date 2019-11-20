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

namespace ContaoBlackForest\FormSave\Event;

use ContaoBlackForest\FormSave\Controller\AbstractFormDataProviderController;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * The event for pre prepare submit data event.
 */
class PrePrepareSubmitDataEvent extends Event
{
    /**
     * The event name.
     */
    const NAME = 'ContaoBlackForest\FormSave\Event\PrePrepareSubmitDataEvent';

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
     * PrePrepareSubmitDataEvent constructor.
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
