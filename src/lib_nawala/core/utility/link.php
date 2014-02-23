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
 * Nawala Framework NCoreUtilityLink Class
 *
 * @package       Framework
 * @subpackage    CoreUtility
 * @since         1.2
 */
class NCoreUtilityLink
{
	/**
	 * Type
	 * @access private
	 * @var string (url or local)
	 */
	protected $type;

	/**
	 * The local filesystem path for the link
	 * @access private
	 * @var string
	 */
	protected  $path;

	/**
	 * The url for the link, local or full (http, https, //)
	 * @access private
	 * @var string
	 */
	protected $url;

	/**
	 * The name for the link, local or full
	 * @access private
	 * @var string
	 */
	protected $name;


	/**
	 * The query for the link, local or full
	 * @access private
	 * @var string
	 */
	protected $query;


	/**
	 * The fileUrl for the link, local or full
	 * @access private
	 * @var string
	 */
	protected $fileUrl;


	/**
	 * The filePath for the link, local or full
	 * @access private
	 * @var string
	 */
	protected $filePath;


	/**
	 * The info indikator. If set to true, will catch file informations if available
	 * @access private
	 * @var bool
	 */
	private $info;


	/**
	 * Build a link object for nawala with link specific options
	 * 
	 * @param $type
	 * @param $path
	 * @param $url
	 * @param $name
	 * @param $query
	 */
	function __construct($type, $path, $url, $name = null, $query = null, $info = null)
	{
		$this->type     = $type;
		$this->path     = $path;
		$this->url      = $url;
		$this->name     = $name;
		$this->query    = $query;

		$this->fileUrl  = $this->getFileUrl();
		$this->filePath = $this->getFilePath();

		$this->info     = $info;
	}


	/**
	 * Gets a ready to work with file path for nawala
	 * @access public
	 * @return string
	 */
	public function getFilePath()
	{
		return $this->path . '/' . $this->name;
	}


	/**
	 * Gets a ready to load file url for nawala
	 * @param  bool    $getQuery    If set to true, a possible query will be added to the url.
	 * @access public
	 * @return string
	 */
	public function getFileUrl( $getQuery = true )
	{
		if ( $getQuery ) {
			return $this->url . '/' . $this->name . $this->query;
		} else {
			return $this->url . '/' . $this->name;
		}
	}


	/**
	 * Gets the type for nawala
	 * @access public
	 * @return string
	 */
	public function getType()
	{
		return $this->type;
	}


	/**
	 * Sets the type for nawala
	 * @access public
	 *
	 * @param string $type
	 */
	public function setType($type)
	{
		$this->type = $type;
	}


	/**
	 * Gets the path for nawala
	 * @access public
	 * @return string
	 */
	public function getPath()
	{
		return $this->path;
	}


	/**
	 * Sets the path for nawala
	 * @access public
	 *
	 * @param string $path
	 */
	public function setPath($path)
	{
		$this->path = $path;
	}


	/**
	 * Gets the url for nawala
	 * @access public
	 * @return string
	 */
	public function getUrl()
	{
		return $this->url;
	}


	/**
	 * Sets the url for nawala
	 * @access public
	 *
	 * @param string $url
	 */
	public function setUrl($url)
	{
		$this->url = $url;
	}


	/**
	 * Gets the name for nawala
	 * @access public
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}
	
	
	/**
	 * Sets the name for nawala
	 * @access public
	 *
	 * @param string $name
	 */
	public function setName($name)
	{
		$this->name = $name;
	}


	/**
	 * Gets the query for nawala
	 * @access public
	 * @return string
	 */
	public function getQuery()
	{
		return $this->query;
	}
	
	
	/**
	 * Sets the query for nawala
	 * @access public
	 *
	 * @param string $query
	 */
	public function setQuery($query)
	{
		$this->query = $query;
	}
}