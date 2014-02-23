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

/**
 * Nawala Framework NFilesystemFile Class
 * 
 * Object of file based informations (only local filesystem informations)
 *
 * @package       Framework
 * @subpackage    Filesystem
 * @since         1.2
 * 
 * @TODO add a working method to find files based on search paths
 */
class NFilesystemFile extends JFile
{
	/**
	 * The local filesystem path
	 * @var string
	 */
	protected $path;

	/**
	 * The local (instance) url
	 * @var string
	 */
	protected $url;

	/**
	 * The file basename
	 * @var string
	 */
	protected $basename;

	/**
	 * The filename without extension
	 * @var string
	 */
	protected $filename;

	/**
	 * The files extension
	 * @var string
	 */
	protected $extension;

	/**
	 * The file type (eg: doc, jpeg, etc...)
	 * @var string
	 */
	protected $type;

	/**
	 * The files query
	 * @var string
	 */
	protected $query;

	/**
	 * The root path ( /var/www )
	 * @var string
	 */
	protected $rootPath;

	/**
	 * The instance path ( myWebsite/mySubwebsite )
	 * @var string
	 */
	protected $instancePath;

	/**
	 * The instance path ( templates/myTemplate/css )
	 * @var string
	 */
	protected $instanceSubPath;

	/**
	 * The full filePath to work with
	 * @var string
	 */
	protected $filePath;

	/**
	 * The full fileUrl to work with incl. query
	 * @var string
	 */
	protected $fileUrl;

	/**
	 * File stats
	 * @var stdClass
	 */
	protected $stats;


	/**
	 * Build a file object for nawala with specific options
	 *
	 * @param    string    $file        Full local filesystem path to the file
	 * @param    string    $query       Additional query
	 * @param    bool      $loadStats   Populate the opbejct with file stats
	 */
	function __construct($file, $query = null, $loadStats = false)
	{
		// Populate file informations
		$this->populateFileInfo( $file, $query, $loadStats );
	}

	/**
	 * Populate file paths and names for nawala
	 *
	 * @param    string    $file        Full local filesystem path to the file
	 * @param    string    $query       Additional query passed through __constructor
	 * @param    bool      $loadStats   Populate the opbejct with file stats
	 */
	protected function populateFileInfo($file, $query = null, $loadStats = false)
	{
		// Get and build possible queries
		$this->query = $this->buildQuery($file, $query);

		// Strip query from $file to check if the file exists
		$file = $this->stripQuery($file);

		// Check first if the file exists and if it is not type 'dir'. NOTE: If the file have trailing query, this will also return false! Use the $query instead
		if ( !$this->exists($file) ) {
			throw new Exception($file . ' not found', 404);
			return;
		}

		// Set filename infos
		$this->basename  = pathinfo($file, PATHINFO_BASENAME);		// eg: filename.js
		$this->filename  = pathinfo($file, PATHINFO_FILENAME);		// eg: filename
		$this->type      = pathinfo($file, PATHINFO_EXTENSION);		// eg: .js
		$this->extension = '.' . $this->type;						// eg: .js

		$this->path            = JPath::clean( pathinfo($file, PATHINFO_DIRNAME) );		// eg: /var/www/instancePathToWebsite/templates/myTemplate/js
		$this->rootPath        = $_SERVER['DOCUMENT_ROOT'];								// eg: /var/www

		$this->instancePath    = JPath::clean( ltrim( str_replace($this->rootPath, '', JPATH_ROOT), '/') );										// eg: instancePathToWebsite
		$this->instanceSubPath = JPath::clean( ltrim(str_replace( array($this->rootPath . '/', $this->instancePath), '', $this->path), '/') );	// eg: templates/myTemplate/js

		$this->url             = JPath::clean( JUri::root(true) . '/' . $this->instanceSubPath );	// eg: /instancePathToWebsite/templates/myTemplate/js

		$this->filePath        = $this->path . '/' . $this->basename;								// eg: /var/www/instancePathToWebsite/templates/myTemplate/js/filename.js
		$this->fileUrl         = $this->url . '/' . $this->basename . $this->query;					// eg: /var/www/instancePathToWebsite/templates/myTemplate/js/filename.js?peter=pan

		if ( $loadStats ) {
			$this->populateFileStats( $file );
		}
	}


	/**
	 * Populate file stats for nawala
	 * 
	 * @param    string    $file    Full local filesystem path to the file
	 */
	protected function populateFileStats( $file )
	{
		// Set the stats object
		$returned_stats = new stdClass();

		$stats = stat($file);

		// Gets file modification time
		$time = $stats['mtime'];
		$returned_stats->time = new stdClass();
		$returned_stats->time->timestamp = $time;
		$returned_stats->time->date      = date( 'Y-m-d - H:i:s', $time );

		// Gets file owner ( userid of the owner )
		$returned_stats->owner = $stats['uid'];

		// Gets file group ( groupid of the owner )
		$returned_stats->group = $stats['gid'];
		
		// Gets file permissions
		$perms = fileperms($file);
		$returned_stats->perms = substr(sprintf('%o', $perms), -4);

		// Gets file size
		$size = $stats['size'];
		$returned_stats->size = new stdClass();
		$returned_stats->size->B = $size;
		$returned_stats->size->KB = round( $size / 1000,       2, PHP_ROUND_HALF_UP );
		$returned_stats->size->MB = round( $size / 1000000,    2, PHP_ROUND_HALF_UP );
		$returned_stats->size->GB = round( $size / 1000000000, 2, PHP_ROUND_HALF_UP );

		// Gets file type ( 'file' or 'dir' )
		$returned_stats->filetype = filetype($file);

		$this->stats = $returned_stats;
		
		return $returned_stats;
	}


	/**
	 * Blind return $file without a query
	 * 
	 * @param    string    $file    Filename or path to the file (local or remote)
	 */
	protected function stripQuery($file)
	{
//		JLog::add(__METHOD__ . ' is deprecated. Use native file_get_contents() syntax.', JLog::WARNING, 'deprecated');

		if ( preg_match('@([^?]+)@i', $file, $match) ) {
			return $match[0];
		} else {
			return $file;
		}
	}


	/**
	 * Build a query from $query and a possible query from $file
	 * 
	 * @param    string    $file    Filename or path to the file (local or remote)
	 * @param    string    $query   Additional query passed through __constructor
	 */
	protected function buildQuery($file, $query = null)
	{
//		JLog::add(__METHOD__ . ' is deprecated. Use native file_get_contents() syntax.', JLog::WARNING, 'deprecated');

		$newQuery   = '?';
		$parsedFile = parse_url($file);

		if ( isset($parsedFile['query']) ) {
			$extQuery = rtrim($parsedFile['query'], '&');

			$newQuery .= $extQuery;
		}

		if ( $query ) {
			$query = ltrim($query, '&');
			$query = ltrim($query, '?');

			if ( isset($parsedFile['query']) ) {
				$newQuery .= '&' . $query;
			} else {
				$newQuery .= $query;
			}
		}

		if ( $newQuery == '?' ) {
			return null;
		} else {
			return $newQuery;
		}
	}


	/**
	 * @return the $path
	 */
	public function getPath()
	{
		return $this->path;
	}


	/**
	 * @return the $url
	 */
	public function getUrl()
	{
		return $this->url;
	}


	/**
	 * @return the $basename
	 */
	public function getBasename()
	{
		return $this->basename;
	}


	/**
	 * @return the $extension
	 */
	public function getExtension()
	{
		return $this->extension;
	}


	/**
	 * @return the $query
	 */
	public function getQuery()
	{
		return $this->query;
	}


	/**
	 * @return the $filePath
	 */
	public function getFilePath()
	{
		return $this->filePath;
	}


	/**
	 * @return the $fileUrl
	 */
	public function getFileUrl()
	{
		return $this->fileUrl;
	}


	/**
	 * @return the $stats
	 */
	public function getStats()
	{
		return $this->stats;
	}
}