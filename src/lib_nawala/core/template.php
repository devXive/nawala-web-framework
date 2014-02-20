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
 * Nawala Framework NCoreTemplate Class
 *
 * Class that build and push all informations we need in all nawala environments
 *
 * @package       Framework
 * @subpackage    Template
 * @since         1.2
 */
class NCoreTemplate
{
	/**
	 * Template Author
	 * @access private
	 * @var string
	 */
	protected $author;

	/**
	 * Template Version
	 * @access private
	 * @var string
	 */
	protected $version;

	/**
	 * Template Short Name
	 * @access private
	 * @var string
	 */
	protected $name;

	/**
	 * Template license
	 * @access private
	 * @var string
	 */
	protected $license;

	/**
	 * Template Full Name
	 * @access private
	 * @var string
	 */
	protected $fullname;

	/**
	 * Creation Date
	 * @access private
	 * @var string
	 */
	protected $creationDate;

	/**
	 * Template Author Email
	 * @access private
	 * @var string
	 */
	protected $authorEmail;


	/**
	 * Template Author Url
	 * @access private
	 * @var string
	 */
	protected $authorUrl;

	/**
	 * Template Description
	 * @access private
	 * @var string
	 */
	protected $description;

	/**
	 * Template Copyright
	 * @access private
	 * @var string
	 */
	protected $copyright;

	/**
	 * @var boolean    True if template has nawala rdk support
	 */
	protected $multiSupport = false;

	/**
	 * @var boolean    True if template has nawala rdk support
	 */
	protected $nawalaSupport = false;

	/**
	 * @var boolean    True if template has gantry framework support
	 */
	protected $gantrySupport = false;

	/**
	 * @var array    Layouts, PushPull schemas
	 */
	protected $config;
	protected $config_vars;

	/**
	 * @var NCoreSimplexml
	 */
	protected $xml;

	/**
	 * @var array
	 */
	protected $positions = array();

	/**
	 * @var array
	 */
	protected $positionInfo = array();

	/**
	 * @var array
	 */
	protected $params = array();

	/**
	 * @var bool
	 */
	protected $legacycss = true;

	/**
	 * @var bool
	 */
	protected $gridcss = true;

	/**
	 * @var array
	 */
	protected $cached_possitions = array();

	/**
	 * @return array
	 */
	public function __sleep()
	{
		return array(
			'author',
			'version',
			'name',
			'license',
			'fullname',
			'creationDate',
			'authorEmail',
			'authorUrl',
			'description',
			'copyright',
			'positions',
			'legacycss',
			'gridcss',
			'cached_possitions'
		);
	}

	/**
	 * @param $nawala
	 */
	public function init(NCoreGlobal &$nawala)
	{
		$this->xml = new NCoreSimplexml($nawala->template->path . '/templateDetails.xml', null, true);
		$this->_getTemplateSupport($nawala);
		$this->loadPositions();
		$this->_getTemplateInfo();
		$this->getConfig($nawala);
	}


	/**
	 * @return array
	 */
	protected function & loadPositions()
	{
		$positions     = array();
		$xml_positions = $this->xml->xpath('//positions/position');
		foreach ($xml_positions as $position) {
			//$position_name = $position->data();
			array_push($this->positions, $position->data());
			$shortposition = preg_replace("/(\-[a-f])$/i", "", $position->data());
			if (!array_key_exists($shortposition, $this->positionInfo)) {
				$positionObject                     = new stdClass();
				$attrs                              = $position->attributes();
				$positionObject->name               = $shortposition;
				$positionObject->id                 = $shortposition;
				$positionObject->max_positions      = 1;
				$positionObject->mobile             = ((string)$attrs['mobile'] == 'true') ? true : false;
				$this->positionInfo[$shortposition] = $positionObject;
			} else {
				$this->positionInfo[$shortposition]->max_positions++;
			}
		}
		return $positions;
	}


	/**
	 * @return array
	 */
	public function getUniquePositions()
	{
		return array_keys($this->positionInfo);
	}


	/**
	 * @param $position_name
	 *
	 * @return mixed
	 */
	public function getPositionInfo($position_name)
	{
		$shortposition = preg_replace("/(\-[a-f])$/i", "", $position_name);
		if (array_key_exists($shortposition, $this->positionInfo)) {
			return $this->positionInfo[$shortposition];
		}
		return false;
	}


	/**
	 * @return array
	 */
	public function getPositions()
	{
		return $this->positions;
	}


	/**
	 * @param $position
	 * @param $pattern
	 *
	 * @return array
	 */
	public function parsePosition($position, $pattern)
	{
		$filtered_positions = array();
		if (count($this->positions) > 0) {
			if (!array_key_exists($position, $this->cached_possitions)) {
				if (null == $pattern) {
					$pattern = "(-)?";
				}
				$regpat = "/^" . $position . $pattern . "/";
				foreach ($this->positions as $key => $value) {
					if (preg_match($regpat, $value) == 1) {
						$filtered_positions[] = $value;
					}
				}
				$this->cached_possitions[$position] = $filtered_positions;
			}
		} else {
			return $filtered_positions;
		}
		return $this->cached_possitions[$position];
	}


	/**
	 * Method to check supported templates. This is a simple check if typical files in template folder exists.
	 */
	protected function _getTemplateSupport(NCoreGlobal &$nawala)
	{
		$nawalaExist = false;
		$gantryExist = false;

		// Nawala
		$nawalaLibraryFile = $nawala->template->path . '/lib/nawala/nawala.php';
		if ( $nawala->checkFile($nawalaLibraryFile) ) {
			$this->nawalaSupport = true;
		}

		$gantryConfigFile = $nawala->template->path . '/gantry.config.php';
		$gantryLibraryFile = $nawala->template->path . '/lib/gantry/gantry.php';
		if ( $nawala->checkFile($gantryConfigFile) && $nawala->checkFile($gantryLibraryFile) ) {
			$gantryLibraryCoreFile = JPATH_SITE . '/libraries/gantry/gantry.php';

			/** Instantiate global $gantry */
			global $gantry;

			require_once( $gantryLibraryCoreFile );

			if ( !empty($gantry) ) {
				$this->params = $gantry->_template->getParams();
				$this->gantrySupport = true;
			} else {
				$this->gantrySupport = false;
			}
		}

		if ( $this->nawalaSupport && $this->gantrySupport ) {
			$this->multiSupport = true;
		}
	}


	/**
	 * load the basic template info
	 * @return void
	 */
	protected function _getTemplateInfo()
	{
		if ($this->xml->name) $this->setName((string)$this->xml->name);
		if ( isset($this->xml->fullName) ) {
			$this->setFullname((string)$this->xml->fullName);
		} else if ( isset($this->params['template_full_name']['default']) ) {
			$this->setFullname((string)$this->params['template_full_name']['default']);
		} else {
			$this->setFullname((string)$this->xml->name);
		}
		if ($this->xml->version) $this->setVersion((string)$this->xml->version);
		if ($this->xml->creationDate) $this->setCreationDate((string)$this->xml->creationDate);
		if ($this->xml->author) $this->setAuthor((string)$this->xml->author);
		if ($this->xml->authorUrl) $this->setAuthorUrl((string)$this->xml->authorUrl);
		if ($this->xml->authorEmail) $this->setAuthorEmail((string)$this->xml->authorEmail);
		if ($this->xml->copyright) $this->setCopyright((string)$this->xml->copyright);
		if ($this->xml->license) $this->setLicense((string)$this->xml->license);
		if ($this->xml->description) $this->setDescription((string)$this->xml->description);
		if ($this->xml->legacycss) $this->setLegacycss((string)$this->xml->legacycss);
		if ($this->xml->gridcss) $this->setGridcss((string)$this->xml->gridcss);
	}


	/**
	 * Gets the version for nawala
	 * @access public
	 * @return string
	 */
	public function getVersion()
	{
		return $this->version;
	}

	/**
	 * Sets the version for nawala
	 * @access public
	 *
	 * @param string $version
	 */
	protected function setVersion($version)
	{
		$this->version = $version;
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
	protected function setName($name)
	{
		$this->name = $name;
	}


	/**
	 * Gets the fullname for nawala
	 * @access public
	 * @return string
	 */
	public function getFullname()
	{
		return $this->fullname;
	}

	/**
	 * Sets the fullname for nawala
	 * @access public
	 *
	 * @param string $fullname
	 */
	protected function setFullname($fullname)
	{
		$this->fullname = $fullname;
	}


	/**
	 * Gets the creationDate for nawala
	 * @access public
	 * @return string
	 */
	public function getCreationDate()
	{
		return $this->creationDate;
	}

	/**
	 * Sets the creationDate for nawala
	 * @access public
	 *
	 * @param string $creationDate
	 */
	protected function setCreationDate($creationDate)
	{
		$this->creationDate = $creationDate;
	}


	/**
	 * Gets the authorEmail for nawala
	 * @access public
	 * @return string
	 */
	public function getAuthorEmail()
	{
		return $this->authorEmail;
	}

	/**
	 * Sets the authorEmail for nawala
	 * @access public
	 *
	 * @param string $authorEmail
	 */
	protected function setAuthorEmail($authorEmail)
	{
		$this->authorEmail = $authorEmail;
	}


	/**
	 * Gets the authorUrl for nawala
	 * @access public
	 * @return string
	 */
	public function getAuthorUrl()
	{
		return $this->authorUrl;
	}

	/**
	 * Sets the authorUrl for nawala
	 * @access public
	 *
	 * @param string $authorUrl
	 */
	protected function setAuthorUrl($authorUrl)
	{
		$this->authorUrl = $authorUrl;
	}


	/**
	 * Gets the description for nawala
	 * @access public
	 * @return string
	 */
	public function getDescription()
	{
		return $this->description;
	}

	/**
	 * Sets the description for nawala
	 * @access public
	 *
	 * @param string $description
	 */
	protected function setDescription($description)
	{
		$this->description = $description;
	}


	/**
	 * Gets the copyright for nawala
	 * @access public
	 * @return string
	 */
	public function getCopyright()
	{
		return $this->copyright;
	}

	/**
	 * Sets the copyright for nawala
	 * @access public
	 *
	 * @param string $copyright
	 */
	protected function setCopyright($copyright)
	{
		$this->copyright = $copyright;
	}


	/**
	 * Gets the license for nawala
	 * @access public
	 * @return string
	 */
	public function getLicense()
	{
		return $this->license;
	}

	/**
	 * Sets the license for nawala
	 * @access public
	 *
	 * @param string $license
	 */
	protected function setLicense($license)
	{
		$this->license = $license;
	}


	/**
	 * Gets the author for nawala
	 * @access public
	 * @return string
	 */
	public function getAuthor()
	{
		return $this->author;
	}

	/**
	 * Sets the author for nawala
	 * @access public
	 *
	 * @param string $author
	 */
	protected function setAuthor($author)
	{
		$this->author = $author;
	}


	/**
	 * @param $legacycss
	 */
	public function setLegacycss($legacycss)
	{
		$set = true;
		if ($legacycss == 'false') {
			$this->legacycss = false;
		}
	}

	/**
	 * @return bool
	 */
	public function getLegacycss()
	{
		return $this->legacycss;
	}


	/**
	 * @param boolean $gridcss
	 */
	public function setGridcss($gridcss)
	{
		$set = true;
		if ($gridcss == 'false') {
			$this->gridcss = false;
		}
	}

	/**
	 * @return boolean
	 */
	public function getGridcss()
	{
		return $this->gridcss;
	}


	/**
	 * @return multitype;
	 */
	public function getParams()
	{
		return $this->params;
	}


	/**
	 * @return the $multiSupport
	 */
	public function getMultiSupport()
	{
		return $this->multiSupport;
	}


	/**
	 * @return the $nawalaSupport
	 */
	public function getNawalaSupport()
	{
		return $this->nawalaSupport;
	}


	/**
	 * @return the $gantrySupport
	 */
	public function getGantrySupport()
	{
		return $this->gantrySupport;
	}

	/**
	 * @return void
	 */
	public function getConfig(NCoreGlobal &$nawala)
	{
		$platform    = new NCorePlatform();
		$check_file  = $platform->nawalaPath . '/nawala.config.tpl.php';

		$fileTools   = new NToolsFileSystem($check_file);

		if ( $fileTools->isFile ) {
//			require_once($check_file);

			$this->config_vars = $fileTools->getFileVariables();

//			$this->nawala_default_grid = $nawala_default_grid;
//			$this->nawala_default_layoutschemas = $nawala_default_layoutschemas;
//			$this->nawala_default_mainbodyschemas = $nawala_default_mainbodyschemas;
//			$this->nawala_default_pushpullschemas = $nawala_default_pushpullschemas;
//			$this->nawala_default_mainbodyschemascombos = $nawala_default_mainbodyschemascombos;
		}
	}
}