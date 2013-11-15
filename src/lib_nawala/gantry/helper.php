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
	 * Hold conditional array of style variables which will be used in compiler
	 *
	 * @var
	 */
	static $lessVariables;
	static $lessVariables_md5;
	static $lessVariables_string;

	/**
	 * Hold conditional array of @import files which will be used in compiler
	 *
	 * @var
	 */
	static $lessImportFiles;

	/**
	 * Hold the output path, where compiled/compressed files should be stored
	 *
	 * @var
	 */
	static $outputPath;

	/**
	 *
	 */
	public function __construct()
	{
		// Initialize global gantry
		global $gantry;

		$this->template = NCore::get('template')->name;
		$this->templatePath = NAWALA_BASEPATH_FILESYSTEM . '/templates/' . $this->template;
		$this->outputPath = $this->templatePath . '/css-compiled';
		$this->urlPath = NAWALA_BASEPATH_URL;

		$this->pathArray = array(
			'templates/' . $this->template . '/less',
			'libraries/nawala/assets/less',
			'libraries/nawala/assets/css'
		);

		$this->lessCompression = (string) $gantry->get('less-compression', true);
		$this->cssCompression = (string) $gantry->get('css-compression', false);

		// Set empty md5 to use if we have no variables
		$this->lessVariables_md5 = md5('');
		$this->lessVariables_string = '';

		// Set the lessImportFiles var
		$this->lessImportFiles = array();
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
	 * Method to autocompile and compress less files with caching and load by gantry
	 *
	 * @param     mixed     $inputFile     Filename or array of filenames to import. Looks first in templates less folder, then in nawala library assets/less folder
	 * @param     string    $outputFile    Filename of the compiled file in templates css-compiled folder, if set to false it will generate a md5 dynamic name based on the imported files
	 * @param     int       $priority      Priority of the file. Should determine on which position file will be rendered/loaded
	 *
	 * @return    void
	 */
	public function addLess( $inputFile, $outputFile = "compiled.css", $priority = self::DEFAULT_STYLE_PRIORITY )
	{
		// Initialize global gantry
		global $gantry;
		$template_files_override = false; // Not relevant in importLess method

		// Find the inputFile in the appropriate pathArray
		if ( !$fileIn = JPath::find($this->pathArray, $inputFile) ) {
			return false;
		}
		$inputFile = $fileIn;

		// Make sure we have a filename
		if ( !$outputFile || $outputFile == '' || $outputFile == null ) {
			$outputFile = "compiled.css";
		}

		// Require Lessc
		require_once(NAWALA_BASEPATH_FILESYSTEM . '/libraries/nawala/compiler/lessc.inc.php');
		$lessc = new lessc;
		$lessc->setImportDir( $this->pathArray );
		$groupMethod = 'lessCompiler';

		if ( $this->lessCompression ) {
			$lessc->setFormatter( 'compressed' );
			$groupMethod = 'lessCompilerCompressor';
		}

		// Load lessVariables if array is not empty
		if ( !empty($this->lessVariables) ) {
			$lessc->setVariables($this->lessVariables);
		}

		// Check for existing index.html file in cache folder. If not exist, create the file, folder will be created automatically
		$indexFile = NAWALA_BASEPATH_FILESYSTEM . '/cache/NawalaLess/index.html';
		if ( !JFile::exists($indexFile) ) {
			$buffer = '<!DOCTYPE html><title></title>';
			JFile::write( $indexFile, $buffer );
		}

		// Prepare a die string
		$die = '<?php die("Access Denied"); ?>' . "\n";

		$cleanInputFileName = JFile::getName( JFile::stripExt( $inputFile ) );
		$cleanOutputFileName = JFile::getName( JFile::stripExt( $outputFile ) );

		// Load the cache
		$cacheFile = NAWALA_BASEPATH_FILESYSTEM . '/cache/NawalaLess/' . md5( $groupMethod ) . '-' . $cleanOutputFileName . '-cache-nawala-' . md5( $cleanInputFileName . $cleanOutputFileName . $this->lessVariables_string ) . '.php';

		// Check if we have a cache File
		if ( JFile::exists($cacheFile) ) {
			$cacheBuffer = JFile::read($cacheFile);
			// Remove the initial die() statement and unserialize cacheBuffer
			$cache = unserialize( preg_replace('/^.*\n/', '', $cacheBuffer) );
		} else {
			$cache = $inputFile;
		}

		$newCache = $lessc->cachedCompile($cache);

		$fileOutName = $cleanOutputFileName . '-' . $this->lessVariables_md5 . '.css';
		$fileOut = $this->outputPath . '/' . $fileOutName;
		if ( !is_array($cache) || $newCache['updated'] > $cache['updated'] ) {
			// Prepend the die string
			$cacheBuffer = $die . serialize($newCache);

			// Write files
			JFile::write( $cacheFile, $cacheBuffer );
			JFile::write( $fileOut, $newCache['compiled'] );
		}

		$fileUrl = $this->urlPath . '/templates/' . $this->template . '/css-compiled/' . $fileOutName;

		// Import compiled less file
		$gantry->addStyle($fileUrl, $priority, $template_files_override);

		return $newCache;
//		return $newCache['files'];
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


	/**
	 * Method to set less variables used in less compiler
	 * See http://leafo.net/lessphp/docs/#setting_variables_from_php for more informations
	 *
	 * @return void
	 */
	public function setVariables( $lessVariables )
	{
		$newVars = array();

		// push the old vars to the $newVars variable if it is not empty
		if ( !empty($this->lessVariables) ) {
			foreach ( $this->lessVariables as $key => $val ) {
				$newVars[$key] = $val;
			}
		}

		// push the new lessVariables to the $newVars variable
		foreach ( $lessVariables as $key => $val ) {
			$newVars[$key] = $val;
		}

		// get an md5 sum of $this->lessVariables
		$tmp_options = $newVars;
		array_walk($tmp_options, create_function('&$v,$k', '$v = " * @".$k." = " .$v;'));
		$options_string = implode($tmp_options, "\n");
		$options_md5    = md5($options_string . (string)$this->lessCompression);

		// Store back to lessVariables
		$this->lessVariables        = $newVars;
		$this->lessVariables_md5    = $options_md5;
		$this->lessVariables_string = $options_string . (string)$this->lessCompression;
	}


	/**
	 * Method to set a list of files that should be imported through the compiler. See: compileImport() Method for more Informations
	 * Note that only less files with no @import files could use by this method!
	 *
	 * @params    array|string    $files    File or array of files to set to the lessImportFiles list
	 *
	 * @return void
	 */
	public function addImport( $files )
	{
		$newVars = $this->lessImportFiles;

		if ( is_array($files) ) {
			foreach ( $files as $file ) {
				// Find the inputFile in the appropriate pathArray and add to lessImportFiles array
				if ( $fileIn = JPath::find($this->pathArray, $file) ) {
					$newFile = str_replace(NAWALA_BASEPATH_FILESYSTEM, JURI::base(true), $fileIn);
					array_push($newVars, $newFile);
				}
			}
		} else {
			if ( $fileIn = JPath::find($this->pathArray, $files) ) {
				$file = str_replace(NAWALA_BASEPATH_FILESYSTEM, JURI::base(true), $fileIn);
				array_push($newVars, $file);
			}
		}

		$uniqueVars = array_unique( $newVars );
		$this->lessImportFiles = $uniqueVars;
	}



	/**
	 * Method to compile the lessImportFile list
	 *
	 * @param     string    $compiledName     Filename of the compiled file in templates css-compiled folder, if set to false it will generate a md5 dynamic name based on the imported files
	 * @param     int       $priority         Priority of the file. Should determine on which position file will be rendered/loaded
	 *
	 * @return    void
	 */
	public function compileImport( $compiledName = false, $priority = self::DEFAULT_STYLE_PRIORITY )
	{
		// Initialize global gantry
		global $gantry;
		$template_files_override = false; // Not relevant in importLess method

		// Require Lessc
		require_once(NAWALA_BASEPATH_FILESYSTEM . '/libraries/nawala/compiler/lessc.inc.php');
		$lessc = new lessc;
		$lessc->setImportDir( $this->pathArray );

		$groupMethod = 'lessCompiler';
		if ( $this->lessCompression ) {
			$lessc->setFormatter( 'compressed' );
			$groupMethod = 'lessCompilerCompressor';
		}

		if ( !empty($this->lessVariables) ) {
			$lessc->setVariables($this->lessVariables);
		}

		$cache = array();
		$cache['root'] = 'entrypoint, wird die gecachte less file';
		$cache['compiled'] = 'alles zusammen';
		$cache['files'] = array('filename' => 'timestamp import files');
		$cache['updated'] = 'timestamp cache file';

		if ( is_array($files) ) {
			foreach ( $files as $file ) {
				// Find the file in the appropriate pathArray
				if ( $fileIn = JPath::find($this->pathArray, $file) ) {
					$fileName = JFile::stripExt($file);
					$fileExtension = JFile::getExt($file);

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

				$content = '@import "' . $fileName . '";';

				// Set the cache file
				$cache[$fileName]['timestamp'] = filemtime($fileIn);
			}
		}

		if ( !$compiledName ) {
			if ( $this->lessCompression ) {
				$compiledName = 'less-compressed-' . md5( $content . $options_set ) . '.css';
			} else {
				$compiledName = 'less-compiled-' . md5( $content . $options_set ) . '.css';
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
}