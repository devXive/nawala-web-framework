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
	 * @var
	 */
	static $pathArray;


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
			'libraries/nawala/assets/less',
			'libraries/nawala/assets/css'
		);
	}


	/**
	 * Method to ...
	 *
	 * @param     string    $var    Description
	 *
	 * @return    void
	 */
	public function addStyle( $file = '', $compiledName = false, $priority = self::DEFAULT_STYLE_PRIORITY, $template_files_override = false )
	{
		// Initialize global gantry
		global $gantry;

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
			
			$compiledName = $fileName . '-' . $md5 . '.' . $fileExtension;
		}

		$fileOut = $this->templatePath . '/css-compiled/' . $compiledName;

		if ( !is_file($fileOut) || filemtime($fileIn) > filemtime($fileOut) ) {
			if ( is_file($fileOut) ) {
//				JFile::delete( $fileOut );
			}
			JFile::copy( $fileIn, $fileOut );
		}

		$gantry->addStyle($fileOut, $priority, $template_files_override);
	}


	/**
	 * Import and compile less files
	 *
	 * @param     mixed      $files                      Filename or array of filenames to import. Looks first in templates less folder, then in nawala library assets/less folder
	 * @param     string     $compiledName               Filename of the compiled file in templates css-compiled folder, if set to false it will generate a md5 dynamic name based on the imported files
	 * @param     int        $priority                   Priority of the file. Should determine on which position file will be rendered/loaded
	 * @param     boolean    $compress                   Compress the file with cssMinifyer?
	 * @param     boolean    $template_files_override    ...
	 *
	 * @return    void
	 */
	public function importLess( $files, $compiledName = false, $priority = self::DEFAULT_STYLE_PRIORITY, $lessVariables = false, $compress = false, $template_files_override = false )
	{
		// Initialize global gantry
		global $gantry;

		// Require Lessc
		require_once(NAWALA_BASEPATH_FILESYSTEM . '/libraries/nawala/compiler/lessc.inc.php');
		$lessc = new lessc;
		$lessc->setImportDir( $this->pathArray );

		if ( $lessVariables ) {
			$lessc->setVariables($lessVariables);
		}

		if ( $compress ) {
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
			if ( $compress ) {
				$compiledName = 'compressed-' . md5($content) . '.css';
			} else {
				$compiledName = 'compiled-' . md5($content) . '.css';
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
//					JFile::delete( $fileOut );
				}
			}
		} else {
			// File did not exist, therefore create it
			$create = true;
		}

		if ( $create ) {
			if ( $compress ) {
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