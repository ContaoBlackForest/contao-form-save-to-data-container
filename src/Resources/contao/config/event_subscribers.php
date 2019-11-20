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

use ContaoBlackForest\FormSave\Subscriber\GetController;
use ContaoBlackForest\FormSave\Subscriber\PrepareSubmitData;

return array(
    new GetController(),
    new PrepareSubmitData()
);
