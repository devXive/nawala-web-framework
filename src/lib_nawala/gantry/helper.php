<?php
/**
 * @version   $Id: gantryupdates.class.php 4060 2012-10-02 18:03:24Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 * Gantry uses the Joomla Framework (http://www.joomla.org), a GNU/GPLv2 content management system
 *
 */
defined('_NRDKRA') or die();

/**
 *
 */
class NGantryHelper
{
	/**
	 *
	 */
	const DEFAULT_STYLE_PRIORITY = 10;

	/**
	 * Template name of the active template
	 * @var
	 */
	static $template;

	/**
	 * Template path of the active template
	 * @var
	 */
	static $templatePath;

	/**
	 * @var
	 */
	static $urlPath;

	/**
	 * array    $pathArray    Array of paths methods and functions will look into to find the appropriate files
	 *
	 * @var
	 */
	static $pathArray;

	/**
	 * @var
	 */
	static $lessCompression;

	/**
	 * @var
	 */
	static $cssCompression;


	/**
	 *
	 */
	public function __construct()
	{
		// Initialize global gantry
		global $gantry;

		$this->template = NCore::get('template')->name;
		$this->templatePath = NAWALA_BASEPATH_FILESYSTEM . '/templates/' . $this->template;
		$this->urlPath = NAWALA_BASEPATH_URL;

		$this->pathArray = array(
			'templates/' . $this->template . '/less',
			'libraries/nawala/assets/less',
			'libraries/nawala/assets/css'
		);

		$this->lessCompression = (string) $gantry->get('less-compression', true);
		$this->cssCompression = (string) $gantry->get('css-compression', false);
	}


	/**
	 * Import and compile less files
	 *
	 * @param     mixed      $file                       Filename or array of filenames to import. Looks first in templates less folder, then in nawala library assets/less folder
	 * @param     string     $compiledName               Filename of the compiled file in templates css-compiled folder, if set to false it will generate a md5 dynamic name based on the imported files
	 * @param     int        $priority                   Priority of the file. Should determine on which position file will be rendered/loaded
	 * @param     array      $template_files_override    Add conditional style variables to less which will be also compiled: see http://leafo.net/lessphp/docs/#setting_variables_from_php for more informations
	 * @param     boolean    $compress                   Enable the compressor to read file content and build a compressed one in the templates css-compiled folder.
	 *                                                   Note: If in template settings CSSmin.YUI is set to "ON", this option will not have any effect!. This can only be used to override if template option is set to "OFF"
	 *
	 * @return    void
	 */
	public function addStyle( $file = '', $compiledName = false, $priority = self::DEFAULT_STYLE_PRIORITY, $template_files_override = false, $compress = false )
	{
		// Initialize global gantry
		global $gantry;

		if ( $this->cssCompression || $compress ) {
			require_once(NAWALA_BASEPATH_FILESYSTEM . '/libraries/nawala/compiler/cssmin.yui.php');
			$compressor = new CSSmin();
		}

		// Find the file in the appropriate pathArray
		if ( !$fileIn = JPath::find($this->pathArray, $file) ) {
			return false;
		}

		// Check for existing index.html file in templates css-compiled folder. If not exist, create the file, folder will be created automatically
		$indexFile = $this->templatePath . '/css-compiled/index.html';
		if ( !JFile::exists($indexFile) ) {
			$buffer = '<!DOCTYPE html><title></title>';
			JFile::write($indexFile, $buffer);
		}

		if ( !$compiledName ) {
			$fileName = JFile::stripExt($file);
			$fileExtension = JFile::getExt($file);
			$md5 = md5($file);
			
			if ( $this->cssCompression || $compress ) {
				$compiledName = $fileName . '-compressed-' . $md5 . '.' . $fileExtension;
			} else {
				$compiledName = $fileName . '-' . $md5 . '.' . $fileExtension;
			}
		}

		$fileOut = $this->templatePath . '/css-compiled/' . $compiledName;

		if ( !is_file($fileOut) || filemtime($fileIn) > filemtime($fileOut) ) {
			if ( is_file($fileOut) ) {
				JFile::delete( $fileOut );
			}
			if ( $this->cssCompression || $compress ) {
				// Read files content
				$cssBuffer = $compressor->run( JFile::read($fileIn) );

				// Write file
				JFile::write($fileOut, $cssBuffer);
			} else {
				JFile::copy( $fileIn, $fileOut );
			}
		}

		$fileUrl = $this->urlPath . '/templates/' . $this->template . '/css-compiled/' . $compiledName;

		$gantry->addStyle($fileUrl, $priority, $template_files_override);
	}


	/**
	 * Import and compile less files
	 *
	 * @param     mixed     $files            Filename or array of filenames to import. Looks first in templates less folder, then in nawala library assets/less folder
	 * @param     string    $compiledName     Filename of the compiled file in templates css-compiled folder, if set to false it will generate a md5 dynamic name based on the imported files
	 * @param     int       $priority         Priority of the file. Should determine on which position file will be rendered/loaded
	 * @param     array     $lessVariables    Add conditional style variables to less which will be also compiled: see http://leafo.net/lessphp/docs/#setting_variables_from_php for more informations
	 *
	 * @return    void
	 */
	public function importLess( $files, $compiledName = false, $priority = self::DEFAULT_STYLE_PRIORITY, array $lessVariables = array() )
	{
		// Initialize global gantry
		global $gantry;
		$template_files_override = false; // Not relevant in importLess method

		// Require Lessc
		require_once(NAWALA_BASEPATH_FILESYSTEM . '/libraries/nawala/compiler/lessc.inc.php');
		$lessc = new lessc;
		$lessc->setImportDir( $this->pathArray );

		if ( !empty($lessVariables) ) {
			$lessc->setVariables($lessVariables);
		}

		if ( $this->lessCompression ) {
			require_once(NAWALA_BASEPATH_FILESYSTEM . '/libraries/nawala/compiler/cssmin.yui.php');
			$compressor = new CSSmin();
		}

		$content = '';
		$cache = array();

		if ( is_array($files) ) {
			foreach ( $files as $file ) {
				// Find the file in the appropriate pathArray
				if ( $fileIn = JPath::find($this->pathArray, $file) ) {
					$fileName = JFile::stripExt($file);
					$fileExtension = JFile::getExt($file);
					$md5 = md5($file);

					$content .= '@import "' . $fileName . '"; ';

					// Set the cache file
					$cache[$fileName]['timestamp'] = filemtime($fileIn);
				}
			}
		} else {
			// Find the file in the appropriate pathArray
			if ( $fileIn = JPath::find($this->pathArray, $files) ) {
				$fileName = JFile::stripExt($files);
				$fileExtension = JFile::getExt($files);
				$md5 = md5($files);

				$content = '@import "' . $fileName . '";';

				// Set the cache file
				$cache[$fileName]['timestamp'] = filemtime($fileIn);
			}
		}

		if ( !$compiledName ) {
			if ( $this->lessCompression ) {
				$compiledName = 'less-compressed-' . md5($content) . '.css';
			} else {
				$compiledName = 'less-compiled-' . md5($content) . '.css';
			}
		}

		$fileOut = $this->templatePath . '/css-compiled/' . $compiledName;

		// Check for existing index.html file in templates css-compiled folder. If not exist, create the file, folder will be created automatically
		$indexFile = $this->templatePath . '/css-compiled/index.html';
		if ( !JFile::exists($indexFile) ) {
			$buffer = '<!DOCTYPE html><title></title>';
			JFile::write($indexFile, $buffer);
		}

		$create = false;
		// Check for existing compiled file in templates css-compiled folder and compare if we have to recompile it.
		if ( JFile::exists( $fileOut ) ) {
			// File exist, check if we have to recompile it
			foreach ( $cache as $file ) {
				if ( $file['timestamp'] > filemtime($fileOut) ) {
					$create = true;
					JFile::delete( $fileOut );
				}
			}
		} else {
			// File did not exist, therefore create it
			$create = true;
		}

		if ( $create ) {
			if ( $this->lessCompression ) {
				// Use cssMin to minify css output
				$cssBuffer = $compressor->run( $lessc->compile( $content ) );
			} else {
				$cssBuffer = $lessc->compile( $content );
			}

			// Write file
			JFile::write($fileOut, $cssBuffer);
		}

		$fileUrl = $this->urlPath . '/templates/' . $this->template . '/css-compiled/' . $compiledName;

		// Import compiled less file
		$gantry->addStyle($fileUrl, $priority, $template_files_override);
	}


	/**
	 * Method to add include paths where the compiler should look in to find the appropriate file
	 *
	 * @return mixed
	 */
	public function addPath( $addPath )
	{
		$newPaths = array();

		// Get existing paths
		if ( isset($this->pathArray) ) {
			foreach ( $this->pathArray as $key => $val ) {
				array_push($newPaths, NAWALA_BASEPATH_FILESYSTEM . '/' . $val);
			}
		}

		// Check and get new path/s
		if ( is_array($addPath) ) {
			foreach ( $addPath as $paths ) {
				array_push($newPaths, NAWALA_BASEPATH_FILESYSTEM . '/' . $paths);
			}
		} else {
			array_push($newPaths, NAWALA_BASEPATH_FILESYSTEM . '/' . $addPath);
		}

		return $this->pathArray = array_unique($newPaths);
	}
}