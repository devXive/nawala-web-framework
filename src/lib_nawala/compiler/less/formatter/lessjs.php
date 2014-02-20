<?php
/**
 * @package          Nawala Rapid Development Kit
 * @subPackage       Nawala - Library
 * @author           devXive - research and development <support@devxive.com> (http://www.devxive.com)
 * @copyright        Copyright (C) 1997 - 2013 devXive - research and development. All rights reserved.
 * @license          GNU General Public License version 2 or later; see LICENSE.txt
 * @assetsLicense    devXive Proprietary Use License (http://www.devxive.com/license)
 */

// Check to ensure this file is included in Nawala!RDK environment
defined('_NRDKRA') or die;

/**
 * Nawala Framework NCompilerLess Class
 *
 * @package       Framework
 * @subpackage    FormatterLessjs
 * @since         1.0
 * 
 * This class allows to compile less files and add ability to store file informations for simple and
 * secure caching and is taken verbatim from:
 *
 * lessphp v0.4.0
 * http://leafo.net/lessphp
 *
 * LESS CSS compiler, adapted from http://lesscss.org
 *
 * Copyright 2013, Leaf Corcoran <leafot@gmail.com>
 * Licensed under MIT or GPLv3, see LICENSE
 * 
 */
class NCompilerLessFormatterLessjs extends NCompilerLessFormatterClassic {
	public $disableSingle = true;
	public $breakSelectors = true;
	public $assignSeparator = ": ";
	public $selectorSeparator = ",";
}