<?php
/**
 * @package          Nawala Rapid Development Kit
 * @subPackage	Nawala - Library
 * @author           devXive - research and development <support@devxive.com> (http://www.devxive.com)
 * @copyright        Copyright (C) 1997 - 2013 devXive - research and development. All rights reserved.
 * @license          GNU General Public License version 2 or later; see LICENSE.txt
 * @assetsLicense    devXive Proprietary Use License (http://www.devxive.com/license)
 */

// Check to ensure this file is included in Nawala!RDK environment
defined('_NRDKRA') or die;

/**
 * Nawala Framework DebugOut Class
 *
 * This class allows for simple but smart looking debugging outputs,
 * for use during development, etc...
 *
 * @package       Framework
 * @subpackage    Debug
 * @since         1.0
 */
class NDebugOut
{
	/**
	 *
	 */
	public function __construct()
	{
	}


	/**
	 * Code Section
	 */
	public function setCodeBox( $data )
	{
		echo '<style>.nw-test-container {display: table; margin: 0 50px; min-height: 1px;} rt-container .nw-test-container {display: table; margin: 0;}</style>';

		echo '<div class="nw-test-container">';
			echo '<hr>';
			echo '<h1>Code Section</h1>';
			echo '<pre class="prettyprint">';
				print_r( $data );
			echo '</pre>';
		echo '</div>';
	}


	/**
	 * Code Section
	 */
	public function setHtmlBox( $data )
	{
		echo '<style>.nw-test-container {display: table; margin: 0 50px; min-height: 1px;} rt-container .nw-test-container {display: table; margin: 0;}</style>';

		echo '<div class="nw-test-container">';
			echo '<hr>';
			echo '<h1>HTML Section</h1>';
			echo '<pre class="prettyprint">';
				print_r( htmlentities( $data ) );
			echo '</pre>';
		echo '</div>';
	}
}
