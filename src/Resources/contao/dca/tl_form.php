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


/** @see tl_form */

/**
 * Add Palettes
 */
$selector = array('targetTable');

$GLOBALS['TL_DCA']['tl_form']['palettes']['__selector__'] =
    array_merge($GLOBALS['TL_DCA']['tl_form']['palettes']['__selector__'], $selector);


$subPalettes = array(
    'targetTable_tl_news' => 'news_archive,author'
);

$GLOBALS['TL_DCA']['tl_form']['subpalettes'] = array_merge($GLOBALS['TL_DCA']['tl_form']['subpalettes'], $subPalettes);


/**
 * Add fields
 *
 * todo add language
 */
$fields = array(
    'author'       => array
    (
        'label'      => &$GLOBALS['TL_LANG']['tl_form']['author'],
        'default'    => BackendUser::getInstance()->id,
        'exclude'    => true,
        'search'     => true,
        'filter'     => true,
        'sorting'    => true,
        'flag'       => 11,
        'inputType'  => 'select',
        'foreignKey' => 'tl_user.name',
        'eval'       => array(
            'doNotCopy'          => true,
            'chosen'             => true,
            'mandatory'          => true,
            'includeBlankOption' => true,
            'tl_class'           => 'w50'
        ),
        'sql'        => "int(10) unsigned NOT NULL default '0'",
        'relation'   => array('type' => 'hasOne', 'load' => 'eager')
    ),
    'news_archive' => array
    (
        'label'      => &$GLOBALS['TL_LANG']['tl_form']['news_archive'],
        'exclude'    => true,
        'flag'       => 11,
        'inputType'  => 'select',
        'foreignKey' => 'tl_news_archive.title',
        'eval'       => array('doNotCopy'          => true,
                              'chosen'             => true,
                              'mandatory'          => true,
                              'includeBlankOption' => true,
                              'tl_class'           => 'w50'
        ),
        'sql'        => "int(10) unsigned NOT NULL default '0'",
        'relation'   => array('type' => 'hasOne', 'load' => 'eager')
    )
);

$GLOBALS['TL_DCA']['tl_form']['fields'] = array_merge($GLOBALS['TL_DCA']['tl_form']['fields'], $fields);


/**
 * Extend fields
 */
$GLOBALS['TL_DCA']['tl_form']['fields']['targetTable']['eval']['submitOnChange'] = true;
