<?php
/**
 * @package          Nawala Rapid Development Kit
 * @subPackage       Nawala - Library
 * @author           devXive - research and development <support@devxive.com> (http://www.devxive.com)
 * @copyright        Copyright (C) 1997 - 2014 devXive - research and development. All rights reserved.
 * @license          GNU General Public License version 2 or later; see LICENSE.txt
 * @assetsLicense    devXive Proprietary Use License (http://www.devxive.com/license)
 */

// Check to ensure this file is included in Nawala!RDK environment
defined('_NRDKRA') or die();

global $nawala;

$nawala_lib_path = JPATH_SITE . '/libraries/nawala/nawala.php';
if (!file_exists($nawala_lib_path)) {
    echo JText::_('NAWALA_BOOTSTRAP_CANT_FIND_LIBRARY');
    die;
}

$backtrace = debug_backtrace();
$nawala_calling_file = $backtrace[0]['file'];
include($nawala_lib_path);