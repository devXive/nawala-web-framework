<?php
/**
 * @package          Nawala Rapid Development Kit
 * @subPackage       Installer
 * @author           devXive - research and development <support@devxive.com> (http://www.devxive.com)
 * @copyright        Copyright (C) 1997 - 2014 devXive - research and development. All rights reserved.
 * @license          GNU General Public License version 2 or later; see LICENSE.txt
 * @assetsLicense    devXive Proprietary Use License (http://www.devxive.com/license)
 */
if (!class_exists('PlgSystemnawala_installerInstallerScript')) {

	/**
	 *
	 */
	class PlgSystemnawala_installerInstallerScript
	{
		/**
		 * @var array
		 */
		protected $packages = array();
		/**
		 * @var
		 */
		protected $sourcedir;
		/**
		 * @var
		 */
		protected $installerdir;
		/**
		 * @var
		 */
		protected $manifest;

		/**
		 * RokInstaller
		 */
		protected $parent;

		/**
		 * @param $parent
		 */
		protected function setup($parent)
		{
			$this->parent       = $parent;
			$this->sourcedir    = $parent->getParent()->getPath('source');
			$this->manifest     = $parent->getParent()->getManifest();
			$this->installerdir = $this->sourcedir . '/' . 'installer';
		}

		/**
		 * @param $parent
		 *
		 * @return bool
		 */
		public function install( $parent )
		{
			// Set the Postinstall Messages
			$this->setPostInstallMessages();

			$this->cleanBogusError();

			jimport('joomla.filesystem.file');
			jimport('joomla.filesystem.folder');


			$retval = true;
			$buffer = '';


			$buffer .= ob_get_clean();

			$run_installer = true;


			// Cycle through cogs and install each

			if ($run_installer) {
				if (count($this->manifest->cogs->children())) {
					if (!class_exists('RokInstaller')) {
						require_once($this->installerdir . '/' . 'RokInstaller.php');
					}

					foreach ($this->manifest->cogs->children() as $cog) {
						$folder_found = false;
						$folder = $this->sourcedir . '/' . trim($cog);

						jimport('joomla.installer.helper');
						if (is_dir($folder)) {
							// if its actually a directory then fill it up
							$package                = Array();
							$package['dir']         = $folder;
							$package['type']        = JInstallerHelper::detectType($folder);
							$package['installer']   = new RokInstaller();
							$package['name']        = (string)$cog->name;
							$package['state']       = 'Success';
							$package['description'] = (string)$cog->description;
							$package['msg']         = '';
							$package['type']        = ucfirst((string)$cog['type']);

							$package['installer']->setCogInfo($cog);
							// add installer to static for possible rollback
							$this->packages[] = $package;
							if (!@$package['installer']->install($package['dir'])) {
								while ($error = JError::getError(true)) {
									$package['msg'] .= $error;
								}
								RokInstallerEvents::addMessage($package, RokInstallerEvents::STATUS_ERROR, $package['msg']);
								break;
							}
							if ($package['installer']->getInstallType() == 'install') {
								RokInstallerEvents::addMessage($package, RokInstallerEvents::STATUS_INSTALLED);
							} else {
								RokInstallerEvents::addMessage($package, RokInstallerEvents::STATUS_UPDATED);
							}
						} else {
							$package                = Array();
							$package['dir']         = $folder;
							$package['name']        = (string)$cog->name;
							$package['state']       = 'Failed';
							$package['description'] = (string)$cog->description;
							$package['msg']         = '';
							$package['type']        = ucfirst((string)$cog['type']);
							RokInstallerEvents::addMessage($package, RokInstallerEvents::STATUS_ERROR, JText::_('JLIB_INSTALLER_ABORT_NOINSTALLPATH'));
							break;
						}
					}
				} else {
					$parent->getParent()->abort(JText::sprintf('JLIB_INSTALLER_ABORT_PACK_INSTALL_NO_FILES', JText::_('JLIB_INSTALLER_' . strtoupper($this->route))));
				}
			}
			return $retval;
		}

		/**
		 * @param $parent
		 */
		public function uninstall( $parent )
		{
			// Remove the Postinstall Messages
			$this->removePostInstallMessages();
		}

		/**
		 * @param $parent
		 *
		 * @return bool
		 */
		public function update($parent)
		{
			return $this->install($parent);
		}

		/**
		 * @param $type
		 * @param $parent
		 *
		 * @return bool
		 */
		public function preflight($type, $parent)
		{
			$this->setup($parent);

			//Load Event Handler
			if (!class_exists('RokInstallerEvents')) {
				$event_handler_file = $this->installerdir . '/RokInstallerEvents.php';
				require_once($event_handler_file);
				$dispatcher = JDispatcher::getInstance();
				$plugin = new RokInstallerEvents($dispatcher);
				$plugin->setTopInstaller($this->parent->getParent());
			}

			if (is_file(dirname(__FILE__) . '/requirements.php')) {
				// check to see if requierments are met
				if (($loaderrors = require_once(dirname(__FILE__) . '/requirements.php')) !== true) {
					$manifest = $parent->get('manifest');
					$package['name'] = (string)$manifest->description;
					RokInstallerEvents::addMessage($package, RokInstallerEvents::STATUS_ERROR, implode('<br />', $loaderrors));
					return false;
				}
			}
		}

		/**
		 * @param $type
		 * @param $parent
		 */
		public function postflight($type, $parent)
		{
			$conf = JFactory::getConfig();
			$conf->set('debug', false);
			$parent->getParent()->abort();
		}

		/**
		 * @param null $msg
		 * @param null $type
		 */
		public function abort($msg = null, $type = null)
		{
			if ($msg) {
				JError::raiseWarning(100, $msg);
			}
			foreach ($this->packages as $package) {
				$package['installer']->abort(null, $type);
			}
		}

		/**
		 *
		 */
		protected function cleanBogusError()
		{
			$errors = array();
			while (($error = JError::getError(true)) !== false) {
				if (!($error->get('code') == 1 && $error->get('level') == 2 && $error->get('message') == JText::_('JLIB_INSTALLER_ERROR_NOTFINDXMLSETUPFILE'))) {
					$errors[] = $error;
				}
			}
			foreach ($errors as $error) {
				JError::addToStack($error);
			}

			$app               = new RokInstallerJAdministratorWrapper(JFactory::getApplication());
			$enqueued_messages = $app->getMessageQueue();
			$other_messages    = array();
			if (!empty($enqueued_messages) && is_array($enqueued_messages)) {
				foreach ($enqueued_messages as $enqueued_message) {
					if (!($enqueued_message['message'] == JText::_('JLIB_INSTALLER_ERROR_NOTFINDXMLSETUPFILE') && $enqueued_message['type']) == 'error') {
						$other_messages[] = $enqueued_message;
					}
				}
			}
			$app->setMessageQueue($other_messages);
		}

		/**
		 * Prepare and build the Postinstallation messages
		 */
		public function setPostInstallMessages()
		{
			$db = JFactory::getDbo();
			$query = 'INSERT INTO ' . $db->quoteName('#__postinstall_messages') .
			' ( `extension_id`,
                  `title_key`,
                  `description_key`,
                  `action_key`,
                  `language_extension`,
                  `language_client_id`,
                  `type`,
                  `action_file`,
                  `action`,
                  `condition_file`,
                  `condition_method`,
                  `version_introduced`,
                  `enabled`) VALUES '
				.'( 700,
               "PLG_SYSTEM_NAWALA_POSTINSTALL_TITLE",
               "PLG_SYSTEM_NAWALA_POSTINSTALL_BODY",
               "PLG_SYSTEM_NAWALA_POSTINSTALL_ACTION",
               "lib_nawala",
                1,
               "action",
               "site://libraries/nawala/postinstall/actions.php",
               "nawala_postinstall_action",
               "site://libraries/nawala/postinstall/conditions.php",
               "nawala_postinstall_condition",
               "3.2.2",
               1)';
			 
			$db->setQuery($query);
			$db->execute();
		}

		public function removePostInstallMessages( $parent )
		{
			$db = JFactory::getDbo();
			$query = 'DELETE FROM '.$db->quoteName('#__postinstall_messages').
			' WHERE '. $db->quoteName('language_extension').' = '.$db->quote('lib_nawala');
			$db->setQuery($query);
			$db->execute();
		}
	}

	if (!class_exists('RokInstallerJAdministratorWrapper')) {
		$jversion = new JVersion();
		if ($jversion->isCompatible('3.2')) {
			class RokInstallerJAdministratorWrapper extends JApplicationCms
			{
				protected $app;

				public function __construct(JApplicationCms $app)
				{
					$this->app =& $app;
				}

				public function getMessageQueue()
				{
					return $this->app->getMessageQueue();
				}

				public function setMessageQueue($messages)
				{
					$this->app->_messageQueue = $messages;
				}
			}
		} else {
			class RokInstallerJAdministratorWrapper extends JAdministrator
			{
				protected $app;

				public function __construct(JAdministrator $app)
				{
					$this->app =& $app;
				}

				public function getMessageQueue()
				{
					return $this->app->getMessageQueue();
				}

				public function setMessageQueue($messages)
				{
					$this->app->_messageQueue = $messages;
				}
			}
		}
	}
}