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
 * Nawala Framework Object Class
 *
 * This class allows for ???? TODO: Set Description
 *
 * @package       Framework
 * @subpackage    Core
 * @since         1.0
 */
class NCoreUpdates
{
	/**
	 * @var
	 */
	protected static $instance;

	/**
	 * @static
	 * @return NawalaUpdates
	 */
	public static function &getInstance()
	{
		if (self::$instance == null) {
			self::$instance = new NCoreUpdates();
		}
		return self::$instance;
	}

	/**
	 * @var null
	 */
	private $extensionInfo = null;

	/**
	 * @var null
	 */
	private $updateInfo = null;

	/**
	 *
	 */
	public function __construct()
	{
		$this->populateExtensionInfo();
	}

	/**
	 * @return string
	 */
	public function getCurrentVersion()
	{
		if ($this->extensionInfo && array_key_exists('version', $this->extensionInfo->manifest_cache)) {
			return $this->extensionInfo->manifest_cache['version'];
		} else {
			//TODO: move to translation
			return 'unknown';
		}
	}

	/**
	 * @return string
	 */
	public function getLatestVersion()
	{
		$this->populateUpdateInfo();
		if ($this->updateInfo) {
			return $this->updateInfo->version;
		} else {
			return $this->getCurrentVersion();
		}
	}

	/**
	 * @return int|bool
	 */
	public function getLastUpdated()
	{
		$this->populateUpdateInfo();
		if ($this->extensionInfo && array_key_exists('last_update', $this->extensionInfo->custom_data)) {
			return $this->extensionInfo->custom_data['last_update'];
		} else {
			return 0;
		}
	}

	/**
	 * @return void
	 */
	protected function populateExtensionInfo()
	{
		$table = JTable::getInstance('extension');
		$id    = $table->find(array('type' => 'library', 'element' => 'lib_nawala'));
		if (empty($id)) {
			return;
		}
		$table->load($id);

		// convert manifest_cache to array
		$registry = new JRegistry();
		$registry->loadString($table->manifest_cache);
		$table->manifest_cache = $registry->toArray();

		// convert custom_data to array
		$registry = new JRegistry();
		$registry->loadString($table->custom_data);
		$table->custom_data = $registry->toArray();

		$this->extensionInfo = $table;
	}

	/**
	 *
	 */
	protected function populateUpdateInfo()
	{
		if (empty($this->updateInfo)) {
			$table    = JTable::getInstance('update');
			$updateid = @$table->find(array('extension_id' => $this->extensionInfo->extension_id));
			if (empty($updateid)) {
				return;
			}
			$table->load($updateid);
			$this->updateInfo = $table;
		}

	}

	/**
	 * @param  $last_checked
	 *
	 * @return void
	 */
	public function setLastChecked($last_checked)
	{
		if (!empty($this->extensionInfo)) {
			$this->extensionInfo->custom_data['last_update'] = $last_checked;

			$registry = new JRegistry();
			$registry->loadArray($this->extensionInfo->custom_data);
			$this->extensionInfo->custom_data = $registry->toString();

			$registry = new JRegistry();
			$registry->loadArray($this->extensionInfo->manifest_cache);
			$this->extensionInfo->manifest_cache = $registry->toString();

			$this->extensionInfo->store();
			$this->populateExtensionInfo();
		}
	}

	/**
	 * @return int
	 */
	public function getNawalaExtensionId()
	{
		return $this->extensionInfo->extension_id;
	}


}