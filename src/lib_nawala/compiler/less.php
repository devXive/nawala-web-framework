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
 * Nawala Framework CompilerLessphp Class
 *
 * This class allows to compile less files and add ability to store file informations
 * for simple and secure caching.
 *
 * @package       Framework
 * @subpackage    Compiler
 * @since         1.0
 */

require_once(dirname(__FILE__) . '/phpless/Less.php');

class NCompilerLessphp
{
	/**
	 * Template name of the active template
	 * @var
	 */
	protected static $template;

	/**
	 * Template path of the active template
	 * @var
	 */
	protected static $templatePath;

	/**
	 * @var
	 */
	protected static $urlPath;

	/**
	 * @var
	 */
	protected static $pathArray;

	/**
	 * Array of compiled files to load in header
	 * @var
	 */
	protected static $fileArray;


	/**
	 *
	 */
	public function __construct()
	{
		$this->template = NCore::get('template')->name;
		$this->templatePath = NAWALA_BASEPATH_FILESYSTEM . '/templates/' . $this->template;
		$this->urlPath = NAWALA_BASEPATH_URL;

		$this->pathArray = array(
			'templates/' . $this->template . '/less',
			'libraries/nawala/assets/less'
		);

		// Check for existing index.html file in cache/nawala/compiler/phpless folder. If not exist, create the file, folder will be created automatically
		$indexFile = JPATH_ROOT . '/cache/nawala/compiler/phpless/index.html';
		if ( !JFile::exists($indexFile) ) {
			$buffer = '<!DOCTYPE html><title></title>';
			JFile::write($indexFile, $buffer);
		}
	}


	/**
	 * Compile Less File
	 *
	 * @param     string    $file        Filename of the file to compile. Looks first in templates less folder, then in nawala library assets/less folder
	 * @param     string    $compiled    Filename of the compiled file in templates css-compiled folder
	 * @param     string    $priority    Priority of the file. Should determine on which position file will be rendered/loaded
	 * @param     string    $check       Determine wether or not if the method shuld check if there already exist a compiled file or not. If the compiled file is older than it original or the file does not exist it will create a new one.
	 *
	 * @return    void
	 */
	public function compile( $file, $compiled = false, $priority = 0, $lessVariables = false, $check = true )
	{
		$doc = JFactory::getDocument();

		// Find the file in the appropriate pathArray
		if ( !$fileIn = JPath::find($this->pathArray, $file) ) {
			return false;
		}

		if ( !$compiled ) {
			$compiled = str_replace('.less', '.css', $file);
		}

		$fileOut = $this->templatePath . '/css-compiled/' . $compiled;

		if ( $lessVariables ) {
			parent::setVariables($lessVariables);
		}

		if ( $check ) {
			$style = parent::checkedCompile( $fileIn, $fileOut );
		} else {
			$style = parent::compileFile( $fileIn, $fileOut );
		}

		// build url to the compiled file
		$fileUrl = $this->urlPath . '/templates/' . $this->template . '/css-compiled/' . $compiled;

		return $fileOut;
	}
}