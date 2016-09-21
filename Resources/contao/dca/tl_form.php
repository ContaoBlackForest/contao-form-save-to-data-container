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
