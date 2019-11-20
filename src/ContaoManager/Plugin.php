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

declare(strict_types=1);

namespace ContaoBlackForest\FormSaveBundle\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Contao\NewsBundle\ContaoNewsBundle;
use ContaoBlackForest\FormSaveBundle\BlackForestFormToSaveBundle;

/**
 * The contao manager plugin
 */
class Plugin implements BundlePluginInterface
{
    /**
     * {@inheritDoc}
     */
    public function getBundles(ParserInterface $parser): array
    {
        return [
            BundleConfig::create(BlackForestFormToSaveBundle::class)
                ->setLoadAfter(
                    [
                        ContaoCoreBundle::class,
                        ContaoNewsBundle::class
                    ]
                )
            ->setReplace(['form-save-to-data-container'])
        ];
    }
}
