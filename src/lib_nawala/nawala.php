<?php
/**
 * @package          Nawala Rapid Development Kit
 * @subPackage       Nawala - Library
 * @author           devXive - research and development <support@devxive.com> (http://www.devxive.com)
 * @copyright        Copyright (C) 1997 - 2013 devXive - research and development. All rights reserved.
 * @license          GNU General Public License version 2 or later; see LICENSE.txt
 * @assetsLicense    devXive Proprietary Use License (http://www.devxive.com/license)
 */

// Restrict access
defined('_JEXEC') or die;

// Define Nawala Rapid Development Kit Restricted Access (NRDKRA) as entrypoint and further check to ensure this file is included in Nawala!RDK environment
define('_NRDKRA', 1);

/** Instantiate global $nawala */
global $nawala;

if (!defined('NAWALA_VERSION')) {
	// Register a debug log
	if (defined('JDEBUG') && JDEBUG)
	{
//		JLog::addLogger(array('text_file' => 'nawala.log.php'), $this->params->get('debugloglevel', 63), array('nawala'));
		JLog::addLogger(array('text_file' => 'nawala.log.php'), JLog::ALL, array('nawala'));
	}

	if (!defined('DS')) {
		define('DS', DIRECTORY_SEPARATOR);
	}

	define( 'NAWALA_LIBRARY', JPATH_LIBRARIES . '/nawala' );

	// Register the library.
//	JLoader::registerPrefix('N', JPATH_LIBRARIES . '/nawala');
	require_once NAWALA_LIBRARY . '/autoloader/nrdk.php';
	NAutoloaderRdk::init();

	/**
	 * @name NAWALA_VERSION
	 */
	$platform = new NCorePlatform();
	define('NAWALA_VERSION', $platform->version);


	/**
	 * HELPER FUNCTION
	 * @return array|mixed
	 */
	function nawala_getAllTemplates()
	{
		$cache = JFactory::getCache('com_templates', '');
		$tag   = JFactory::getLanguage()->getTag();

		$templates = $cache->get('templates0' . $tag);
		if ($templates === false) {
			// Load styles
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('id, home, template, params');
			$query->from('#__template_styles');
			$query->where('client_id = 0');

			$db->setQuery($query);
			$templates = $db->loadObjectList('id');
			foreach ($templates as &$template) {
				$registry = new JRegistry;
				$registry->loadString($template->params);
				$template->params = $registry;

				// Create home element
				if ($template->home == '1' && !isset($templates[0]) && $template->home == $tag) {
					$templates[0] = clone $template;
				}
			}
			$cache->store($templates, 'templates0' . $tag);
		}

		return $templates;
	}


	/**
	 * HELPER FUNCTION
	 * @return array|mixed
	 */
	function nawala_admin_getAllTemplates()
	{
		$cache = JFactory::getCache('com_templates', '');
		$tag   = JFactory::getLanguage()->getTag();

		$templates = $cache->get('templates1' . $tag);
		if ($templates === false) {
			// Load styles
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('id, home, template, params');
			$query->from('#__template_styles');
			$query->where('client_id = 1');

			$db->setQuery($query);
			$templates = $db->loadObjectList('id');
			foreach ($templates as &$template) {
				$registry = new JRegistry;
				$registry->loadString($template->params);
				$template->params = $registry;

				// Create home element
				if ($template->home == '1' && !isset($templates[0]) && $template->home == $tag) {
					$templates[0] = clone $template;
				}
			}
			$cache->store($templates, 'templates1' . $tag);
		}

		return $templates;
	}


	/**
	 * HELPER FUNCTION
	 * Function to get template params. Checks first if there is such a templatename and if it is a master template
	 * @param $template_name
	 * @return bool
	 */
	function nawala_getTemplateStyleByName($template_name)
	{
		$app = JFactory::getApplication();
		if ( $app->isAdmin() ) {
			$templates = nawala_admin_getAllTemplates();
			foreach ($templates as $template) {
				if ( $template->template == $template_name && $template->params->get('master') == 'true' ) {
// TODO				return;
				} else if ( $template->template == $template_name && $template->params->get('master') != 'true' ) {
					return $template;
				}
			}
		} else {
			$templates = nawala_getAllTemplates();
			foreach ($templates as $template) {
				if ( $template->template == $template_name && $template->params->get('master') == 'true' ) {
// TODO				return;
				} else if ( $template->template == $template_name && $template->params->get('master') != 'true' ) {
					return $template;
				}
			}
		}

		return false;
	}




/** ##### CHECKED ##### CHECKED ##### CHECKED ##### CHECKED ##### CHECKED ##### CHECKED ##### CHECKED ##### CHECKED ##### CHECKED ##### CHECKED ##### **/


	/**
	 * Set the current templates in the session
	 */
	function nawala_setTemplateSessionNames() {
		$session   = JFactory::getSession();
		$templates = new stdClass();

		// Set the admin template
		if ( !$session->get('nawala-current-admin-template', null) ) {
			$admin_templates = nawala_admin_getAllTemplates();
			foreach ( $admin_templates as $at ) {
				if ( $at->home == 1 ) {
					$templates->admin = $at->template;
					$session->set('nawala-current-admin-template', $at->template);
					break;
				}
			}
		}

		// Set the site template
		if ( !$session->get('nawala-current-admin-template', null) ) {
			$site_templates = nawala_getAllTemplates();
			foreach ( $site_templates as $st ) {
				if ( $st->home == 1 ) {
					$templates->site = $st->template;
					$session->set('nawala-current-site-template', $st->template);
					break;
				}
			}
		}

		return $templates;
	}


	/**
	 * Function to initialize nawala system based styles
	 */
	function nawala_template_initialize()
	{
		if (defined('NAWALA_INITTEMPLATE')) {
			return;
		}
		define('NAWALA_INITTEMPLATE', "NAWALA_INITTEMPLATE");

		/** @var $nawala NCoreGlobal */
		global $nawala;

		$nawala->initTemplate();
	}


	/**
	 * SETUP NAWALA FRONTEND
	 */
	function nawala_setup()
	{
		jimport('joomla.html.parameter');

		/** @var $nawala NCoreGlobal */
		global $nawala;

		$conf = JFactory::getConfig();
		$app  = JFactory::getApplication();

		// Get the current site template
		$template      = $app->getTemplate(true);
		$template_name = $template->template;

		// TODO: Check NCache
		if ($template->params->get("cache.enabled", 1) == 1) {
			$cache = NCache::getCache(NCache::GROUP_NAME);
			$cache->addWatchFile(JPATH_SITE . '/templates/' . $template_name . '/templateDetails.xml');
			$cache->addWatchFile(JPATH_SITE . '/templates/' . $template_name . '/template-options.xml');
			$nawala = $cache->call('Nawala-' . $template_name, array('NCoreGlobal', 'getInstance'), array($template_name));
		} else {
			$nawala = NCoreGlobal::getInstance($template_name);
		}

		$nawala->initTemplate();
		$nawala->init();
	}


	/**
	 * SETUP NAWALA ADMIN
	 */
	function nawala_admin_setup()
	{
		jimport('joomla.html.parameter');
	
		/** @var $nawala NCoreGlobal */
		global $nawala;

		$app  = JFactory::getApplication();

		// Get the current site template
		$template      = $app->getTemplate(true);
		$template_name = $template->template;
	
		// TODO: Check NCache
		if ($template->params->get("cache.enabled", 1) == 1) {
			$cache = NCache::getCache(NCache::ADMIN_GROUP_NAME, null, true);
			$cache->addWatchFile(JPATH_SITE . '/templates/' . $template_name . '/templateDetails.xml');
			$cache->addWatchFile(JPATH_SITE . '/templates/' . $template_name . '/template-options.xml');
			$nawala = $cache->call('Nawala-' . $template_name, array('NCoreGlobal', 'getInstance'), array($template_name));
		} else {
			$nawala = NCoreGlobal::getInstance($template_name);
		}

		$nawala->initTemplate();
		$nawala->adminInit();
	}


	/**
	 * Run the appropriate init
	 */
	
	$app = JFactory::getApplication();

	nawala_setTemplateSessionNames();		// Set current templates names for admin and site in the session

	if ($app->isAdmin()) {
		nawala_admin_setup();
	} else {
		nawala_setup();
		nawala_template_initialize();

		if (!isset($nawala_calling_file))
		{
			$backtrace = debug_backtrace();
			$nawala_calling_file = $backtrace[0]['file'];
		}
		// $app->triggerEvent('onNawalaTemplateInit', array($nawala_calling_file));
	}
}