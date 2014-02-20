<?php
/**
 * @package          Nawala Rapid Development Kit
 * @subPackage       Nawala - Library
 * @author           devXive - research and development <support@devxive.com> (http://www.devxive.com)
 * @copyright        Copyright (C) 1997 - 2013 devXive - research and development. All rights reserved.
 * @license          GNU General Public License version 2 or later; see LICENSE.txt
 * @assetsLicense    devXive Proprietary Use License (http://www.devxive.com/license)
 */

// Check to ensure this file is included in Nawala!RDK environment
defined('_NRDKRA') or die();

/**
 * Nawala Framework CoreGlobal Class
 * Class to provide the global $nawala var
 * 
 * Note that static vars can not be override!
 *
 * @package       Framework
 * @subpackage    Core
 * @since         1.0
 */
class NCoreGlobal
{
	/**
	 *
	 */
	const DEFAULT_STYLE_PRIORITY = 10;

	/**
	 *
	 */
	const DEFAULT_SCRIPT_PRIORITY = 20;

	/**
	 * The max wait time for a less compile in microseconds
	 */
	const LESS_MAX_COMPILE_WAIT_TIME = 2;
	
	const LESS_SITE_CACHE_GROUP = 'NawalaLess';
	
	const LESS_ADMIN_CACHE_GROUP = 'NawalaAdminLess';


	/**
	 * @var array
	 */
	static $instances = array();

	/**
	 * @static
	 *
	 * @param $template_name
	 *
	 * @return mixed
	 */
	public static function getInstance($template_name)
	{
		if (!array_key_exists($template_name, self::$instances)) {
			self::$instances[$template_name] = new NCoreGlobal($template_name);
		}

		return self::$instances[$template_name];
	}

	public $basePath;
	public $baseUrl;
	public $cachePath;
	public $cacheUrl;

	/**
	 * @var NCorePlatform
	 */
	public $platform;

	/**
	 *
	 */
	public $statePrefix = null;					 // Prefix build for and from the currently active application for usage in user state scope. As set in $option var (eg. com_app)
	public $dontsetinoverride = array();

	public $defaultMenuItem;
	public $currentMenuItem;
	public $currentMenuTree;

	public $template = null;
	protected $ajaxModelPaths;
	
	/**
	 * @var JDocumentHTML
	 */
	public $document;

	/**
	 * @var NCoreBrowser
	 */
	public $browser;
	public $language;
//	public $session;	// TODO: also deactivated in init()
	public $currentUrl;

	// Private Vars
	/**#@+
	 * @access private
	 */
	public $_ajaxmodels = array();
	public $_adminajaxmodels = array();

	public $_template;
	public $_param_names = array();
	public $_working_params;
	public $_params_hash;

	public $_setbyurl = array();
	public $_setbycookie = array();
	public $_setbysession = array();
	public $_setinsession = array();
	public $_setincookie = array();
	public $_setinoverride = array();
	public $_setbyoverride = array();

//	public $_menu_item_params = array();

	public $_scripts = array();
	public $_styles = array();
	public $_styles_available = array();
//	public $_domready_script = '';
//	public $_loadevent_script = '';
//	public $_tmp_vars = array();
	
	public $_browser_params = array();
	public $_browser_hash;

	public $adminElements = array();
	/**#@-*/


	/**
	 * @var JObject
	 */
	public $form = null;

	/**
	 * @var NApplicationInput Class
	 */
	public $input = null;

	/**
	 * @var    object    $formatter    Nawala formatter tools object class
	 */
	private $formatter = null;



	/**
	 * Constructor
	 */
	public function __construct($template_name = null)
	{
		// Set the base class vars
		/** Set a reference to the global JDocumentHTML object (aka JFactory::getDocument()) */
		$doc            = JFactory::getDocument();
		$this->document =& $doc;

		// Initialize the NCorePlatform Object
		$this->platform = new NCorePlatform();

		// Initialize the NCoreBrowser Object
		$this->browser = new NCoreBrowser();

		// Load the template vars
		$this->template = new stdClass();
		if ($template_name == null) {
			$this->template->name = $this->getCurrentTemplate();
		} else {
			$this->template->name = $template_name;
		}
		$this->initTemplate();

		// Load the menu vars
		$this->defaultMenuItem = $this->getDefaultMenuItem();
		$this->currentMenuItem = $this->defaultMenuItem;

		// Load form
		$this->loadFormTools();

		// Load the AjaxModels. (NOTE: This just only load the current models. See methods for details)
		$this->loadAjaxModels();
		$this->loadAdminAjaxModels();

		//$this->_checkAjaxTool();
		//$this->_checkLanguageFiles();

		
		//	$this->setScope('nawalaConfig.mediaPaths.' . $type, $mediaPathArray);
		// Set the mediaPaths var
		//	$this->mediaPaths = $this->getScope('nawalaConfig.mediaPaths');
		
		
		
		/**
		 * Init ajax model paths paths from session scope
		 * @deprecated in 2.0 ??
		 */
		$this->initAjaxModelPaths();

		// Init the formatter
		$this->formatter = new NFormatter();
	}


	/**
	 *
	 */
	public function adminInit()
	{
		// Run the init
		if ( !$this->isAdmin() ) {
			$this->init();
			return;
		}

		// Initialize the NApplicationInput Object
		$this->input = new NApplicationInput();

		// Set the statePrefix as described after var in top
		$this->statePrefix = $this->input->option;

		if ( defined('NAWALA_ADMININIT') ) {
			return;
		}

		$this->platform      = new NCorePlatform();
		$this->browser       = new NCoreBrowser();
		$this->_browser_hash = md5(serialize($this->browser));
		$doc                 = JFactory::getDocument();
		$this->document      =& $doc;

		$this->basePath      = $this->platform->baseAdminPath;
		$this->baseUrl       = $this->platform->baseAdminUrl;
		$this->cachePath     = $this->platform->cacheAdminPath;
		$this->cacheUrl      = $this->platform->cacheAdminUrl;

		define('NAWALA_ADMININIT', "NAWALA_ADMININIT");
	}


	/**
	 * Initializer.
	 * This should run when nawala is run from the front end in order and before the component/template file to
	 * populate all user session level data. The defined('NAWALA_INIT') prevent the method to load things twice! 
	 * @return void
	 */
	public function init()
	{
		// Run the admin init
		if ( $this->isAdmin() ) {
			$this->adminInit();
			return;
		}

		$this->basePath      = $this->platform->baseAdminPath;
		$this->baseUrl       = $this->platform->baseAdminUrl;
		$this->cachePath     = $this->platform->cachePath;
		$this->cacheUrl      = $this->platform->cacheUrl;

		// Initialize the NApplicationInput Object
		$this->input = new NApplicationInput();

		// Set the statePrefix as described after var in top
		$this->statePrefix = $this->input->option;

		$app = JFactory::getApplication();
		// Use any menu item level overrides
		$menus                 = $app->getMenu();
		$menu                  = $menus->getActive();
		$this->currentMenuItem = ($menu != null) ? $menu->id : null;
		$this->currentMenuTree = ($menu != null) ? $menu->tree : array();

		if ( defined('NAWALA_INIT') ) {
			return;
		}

		define('NAWALA_INIT', "NAWALA_INIT");

		$cache = NCache::getInstance();

		// Set the main class vars to match the call
		//JHTML::_('behavior.framework');
		$doc = JFactory::getDocument();
		//$doc->setMetaData('templateframework','Nawala!RDK - Rapid Development Kit!');
		$this->document      =& $doc;
		$this->language      = $doc->language;
		$this->session       = JFactory::getSession();
		$uri                 = JURI::getInstance();
		$this->currentUrl    = $uri->toString();

		// Initialise the Browser Object
		$this->browser       = new NCoreBrowser();
		$this->_browser_hash = md5(serialize($this->browser));

		$this->loadBrowserConfig();
	}


	/**
	 * Method to initialize base paths, related on current template (admin/site) and to load nawala system based styles
	 * Method support both, admin and frontend templates
	 */
	public function initTemplate()
	{
		if ( !$this->isAdmin() ) {
			$this->template->path        = $this->cleanPath(JPATH_ROOT . '/' . 'templates' . '/' . $this->template->name);
			$this->template->url         = $this->platform->baseUrl . '/templates' . "/" . $this->template->name;
			$this->template->customPath  = $this->template->path . '/' . 'custom';
		} else {
			$this->template->path        = $this->cleanPath(JPATH_ADMINISTRATOR . '/' . 'templates' . '/' . $this->template->name);
			$this->template->url         = $this->platform->baseAdminUrl . '/templates' . "/" . $this->template->name;
			$this->template->customPath  = $this->template->path . '/' . 'custom';
		}

		// Load up the template details
		$this->_template = new NCoreTemplate();
		$this->_template->init($this);

		if ( $this->_template->getNawalaSupport() && !$this->_template->getGantrySupport() ) {
			// Nawala only
			// Put a base copy of the saved params in the working params
			$this->_working_params  = $this->_template->getParams();
			$this->_params_hash     = md5(serialize($this->_working_params));
			$this->_param_names     = array_keys($this->_template->getParams());
		} else if ( $this->_template->getMultiSupport() || $this->_template->getGantrySupport() ) {
			// Put a base copy of the saved params in the working params
			$this->_working_params  = $this->_template->getParams();
			$this->_params_hash     = md5(serialize($this->_working_params));
			$this->_param_names     = array_keys($this->_template->getParams());
		}

		$this->template->prefix = isset($this->_working_params['template_prefix']['value']) ? $this->_working_params['template_prefix']['value'] : $this->template->name . '-';
	}


	/**
	 * TODO: DOIT
	 */
	protected function ___adminFinalize()
	{
		ksort($this->_styles);
		foreach ($this->_styles as $priorities) {
			foreach ($priorities as $css_file) {
				/** @var $css_file NCoreUtilityLink */
				$this->document->addStyleSheet($css_file->getFileUrl());
			}
		}
		foreach ($this->_scripts as $js_file) {
			$this->document->addScript($js_file);
		}

		$this->renderCombinesInlines();

	}

	/**
	 * TODO: DOIT
	 */
	protected function ___renderCombinesInlines()
	{
		$lnEnd   = "\12";
		$tab     = "\11";
		$tagEnd  = ' />';
		$strHtml = '';

		// Generate domready script
		if (isset($this->_domready_script) && strlen($this->_domready_script) > 0) {
			$strHtml .= 'window.addEvent(\'domready\', function() {' . $this->_domready_script . $lnEnd . '});' . $lnEnd;
		}

		// Generate load script
		if (isset($this->_loadevent_script) && strlen($this->_loadevent_script) > 0) {
			$strHtml .= 'window.addEvent(\'load\', function() {' . $this->_loadevent_script . $lnEnd . '});' . $lnEnd;
		}

		$this->document->addScriptDeclaration($strHtml);
	}

	/**
	 * TODO: DOIT
	 */
	public function finalize()
	{
		if ( !defined('NAWALA_FINALIZED') ) {
			// Run the admin init
			if ($this->isAdmin()) {
//				$this->adminFinalize();
				return;
			}

			$this->addStyle($this->template->name . '-custom.css', 1000);
//			gantry_import('core.params.overrides.gantrycookieparamoverride');
//			gantry_import('core.params.overrides.gantrysessionparamoverride');

//			$cache = GantryCache::getInstance();
//			if (!$this->_parts_cached) {
//				$parts_cache = array();
//				foreach ($this->_parts_to_cache as $part) {
//					$parts_cache[$part] = $this->$part;
//				}
//				if ($parts_cache) {
//					$cache->set($this->cacheKey('parts'), $parts_cache);
//				}
//			}

//			if ($this->get("gzipper-enabled", false)) {
//				gantry_import('core.gantrygzipper');
//				GantryGZipper::processCSSFiles();
//				GantryGZipper::processJsFiles();
//			} else {
				ksort($this->_styles);
				foreach ($this->_styles as $priorities) {
					foreach ($priorities as $css_file) {
						/** @var $css_file NCoreUtilityLink */
						$this->document->addStyleSheet($css_file->getFileUrl());
					}
				}
				foreach ($this->_scripts as $js_file) {
					$this->document->addScript($js_file);
				}
//			}
			define('NAWALA_FINALIZED', true);
		}

//		if ($this->altindex !== false) {
//			$contents = ob_get_contents();
//			ob_end_clean();
//			ob_start();
//			echo $this->altindex;
//		}
	}


	/**
	 * @return bool
	 */
	public function isAdmin()
	{
		$app = JFactory::getApplication();

		return $app->isAdmin();
	}


	/**
	 * Get a value from the template working params
	 * 
	 * @param bool   $param
	 * @param string $default
	 *
	 * @return string
	 */
	public function get($param = false, $default = "")
	{
		if (array_key_exists($param, $this->_working_params)) $value = $this->_working_params[$param]['value']; else $value = $default;
		return $value;
	}


	/**
	 * Get a values default from the template working params
	 * @param bool $param
	 *
	 * @return string
	 */
	public function getDefault($param = false)
	{
		$value = "";
		if (array_key_exists($param, $this->_working_params)) $value = $this->_working_params[$param]['default'];
		return $value;
	}


	/**
	 * Set/Override a value to the template working params
	 * 
	 * @param      $param
	 * @param bool $value
	 *
	 * @return bool
	 */
	public function set($param, $value = false)
	{
		$return = false;
		if (array_key_exists($param, $this->_working_params)) {
			$this->_working_params[$param]['value'] = $value;
			$return                                 = true;
		}
		return $return;
	}


	/**
	 * Method to get the ajax url of the given library. If no lib is set, the nawala library ajax url returns
	 * 
	 * @param     string    $lib    Name of the lib (nawala|gantry|core)
	 * 
	 * @return string
	 */
	public function getAjaxUrl( $lib = false )
	{
		switch ($lib) {
			case 'nawala':
				$component_url = $this->platform->nawalaAjaxUrl;
				break;
			case 'gantry':
				$component_url = $this->platform->gantryAjaxUrl;
				break;
			case 'core':
				$component_url = $this->platform->coreAjaxUrl;
				break;
			default:
				$component_url = $this->platform->nawalaAjaxUrl;
			break;
		}

		$url = $this->platform->baseUrl;
		if ( $this->isAdmin() ) {
			$url .= '/administrator/' . $component_url;
		} else {
			$url .= '/' . $component_url;
		}

		return $url;
	}


	/**
	 * @param null $prefix
	 * @param bool $remove_prefix
	 *
	 * @return array
	 */
	public function getParams($prefix = null, $remove_prefix = false)
	{
		if (null == $prefix) {
			return $this->_working_params;
		}

		$params = array();

		foreach ($this->_working_params as $param_name => $param_value) {
			$matches = array();
			if (preg_match("/^" . $prefix . "-(.*)$/", $param_name, $matches)) {
				if ($remove_prefix) {
					$param_name = $matches[1];
				}
				$params[$param_name] = $param_value;
			}
		}

		return $params;
	}


	/**
	 * Gets the current URL and query string and can ready it for more query string vars
	 *
	 * @param array $ignore
	 *
	 * @return mixed|string
	 */
	public function getCurrentUrl($ignore = array())
	{
		$url = NCoreUtilityUrl::explode($this->currentUrl);

		if (!empty($ignore) && array_key_exists('query_params', $url)) {
			foreach ($ignore as $k) {
				if (array_key_exists($k, $url['query_params'])) unset($url['query_params'][$k]);
			}
		}

		return NCoreUtilityUrl::implode($url);
	}

	/**
	 * Get the current URL as splitted parts
	 *
	 * @return mixed|string
	 */
	public function getCurrentUrlSplitted()
	{
		$url = NCoreUtilityUrl::explode($this->currentUrl);

		return $url;
	}


	/**
	 * Sets the value of a user state (aka appScope) variable.
	 * @see JFactory::getApplication()->setUserState
	 *
	 * @param   string  $key    The path of the state.
	 * @param   string  $value  The value of the variable.
	 *
	 * @return  mixed  The previous state, if one existed.
	 *
	 * @since   1.1
	 */
	public function setScope($key, $value)
	{
		$session = JFactory::getSession();
		$registry = $session->get('registry');
	
		if (!is_null($registry))
		{
			return $registry->set($key, $value);
		}
	
		return null;
	}


	/**
	 * Gets a value of a user state (aka appScope) variable.
	 * @see JFactory::getApplication()->getUserState
	 *
	 * @param   string  $key      The path of the state.
	 * @param   mixed   $default  Optional default value, returned if the internal value is null.
	 *
	 * @return  mixed  The user state or null.
	 *
	 * @since   1.1
	 */
	public function getScope($key, $default = null)
	{
		$session = JFactory::getSession();
		$registry = $session->get('registry');

		if (!is_null($registry))
		{
			return $registry->get($key, $default);
		}

		return $default;
	}


	/**
	 * Gets the value of a user state variable (aka appScope).
	 * @see JFactory::getApplication()->getUserStateFromRequest
	 *
	 * @param   string  $key      The key of the user state variable.
	 * @param   string  $request  The name of the variable passed in a request.
	 * @param   string  $default  The default value for the variable if not found. Optional.
	 * @param   string  $type     Filter for the variable, for valid values see {@link JFilterInput::clean()}. Optional.
	 *
	 * @return  object  The request user state (aka appScope).
	 *
	 * @since   1.1
	 */
	public function getScopeRequest($key, $request, $default = null, $type = 'none')
	{
		$cur_state = $this->getScope($key, $default);
		$new_state = $this->mainframe->input->get($request, null, $type);

		// Save the new value only if it was set in this request.
		if ($new_state !== null)
		{
			$this->setScope($key, $new_state);
		}
		else
		{
			$new_state = $cur_state;
		}

		return $new_state;
	}


	/**
	 * Enqueue a system message.
	 * @see JFactory::getApplication()->enqueueMessage()
	 * 
	 * Usage:
	 *     $app->setMessageQueue(JText::_('MESSAGE_TO_SHOW_ON_NEW_PAGELOAD'), 'notice');
	 * Types:
	 *     'success' => green system message in the drawer
	 *     'notice'  => blue ssystem message in the drawer
	 *     'warning' => yellow system message in the drawer
	 *     'error'   => red system message in the drawer
	 *
	 * @param   string  $msg   The message to enqueue.
	 * @param   string  $type  The message type. Default is message.
	 *
	 * @return  void
	 *
	 * @since   1.1
	 */
	public function setMessageQueue($msg, $type = 'message')
	{
		// Enqueue the message.
		$this->mainframe->enqueueMessage($msg, $type);
	}

	/**
	 * Get the system message queue.
	 *
	 * @return  array  The system message queue.
	 *
	 * @since   1.1
	 */
	public function getMessageQueue()
	{
		// For empty queue, if messages exists in the session, enqueue them.
		return $this->mainframe->getMessageQueue();
	}



	/**
	 * ############################ Load internal and external script and style files ###############################
	 */


	/**
	 * Add a script file to the document based on set media paths and without platform based checks.
	 * This method uses the gantry framework to add scripts
	 *
	 * @param     $file     string    Filename of the file you wish to add
	 * @param     $debug    bool      Debug add fileversions to the string
	 *
	 * @return    void
	 */
	public function addScript($file = '', $query_string = null, $component = false, $priority = self::DEFAULT_SCRIPT_PRIORITY, $debug = false) {
		// Array check
		if (is_array($file)) {
			$this->addScripts($file);
			return;
		}

		if ( !$component ) {
			$component = $this->statePrefix;
		}

		// If no debugging value is set, use the configuration settings
		$debug    = $debug ? $debug : $this->getDebugStatus();

		// If we're in admin or debug is enabled, set a query string
		if ( $this->isAdmin() || $debug ) {
			if (strpos(NAWALA_VERSION, 'project.version') === false) {
				if ( $query_string ) {
					$query_string .= '&nawala_version=' . NAWALA_VERSION;
				} else {
					$query_string = '?nawala_version=' . NAWALA_VERSION;
				}
			}
		}

		/** @var $outFiles NCoreUtilityLink[] */
		$out_files = $this->qualifyMediaFile($file, $query_string, $component, $priority, false, $debug);

		foreach ($out_files as $link) {
			$addit = true;

			foreach ($this->_scripts as $script_priority => $priority_links) {
				$index = array_search($link, $priority_links);
				if ($index !== false) {
					if ($priority < $script_priority) {
						unset($this->_scripts[$style_priority][$index]);
					} else {
						$addit = false;
					}
				}
			}

			if ($addit) {
				if (!defined('NAWALA_FINALIZED')) {
					$this->_scripts[$priority][] = $link;
				} else {
					$this->document->addScript($link->getFileUrl());
				}
			}
		}

		// Clean up scripts
		foreach ($this->_scripts as $script_priority => $priority_links) {
			if (count($priority_links) == 0) {
				unset($this->_scripts[$script_priority]);
			}
		}
	}
	
	
	/**
	 * @param array $scripts
	 */
	public function addScripts($scripts = array())
	{
		foreach ($scripts as $script) $this->addScript($script);
	}
	
	
	/**
	 * Add inline script to the document
	 *
	 * @param  $js    string
	 *
	 * @return void
	 */
	function addInlineScript($js = '')
	{
		if ( defined('NAWALA_FINALIZED') ) {
			$this->document->addScriptDeclaration($js);

			return;
		} else {
			// Init global gantry
			global $gantry;

			$gantry->addInlineScript($js);
			// TODO: Add own inline scripts method
		}
	}


	/**
	 * Add a style file to the document based on set media paths and without platform based checks.
	 * This method uses the gantry framework to add scripts
	 * 
	 * @param string $file
	 * @param int    $priority
	 * @param bool   $template_files_override
	 * 
	 * Example: How to addStyle
	 *                         - Adding single files, that use Nawala!RDK based checks
	 *                             addStyle('template.css')
	 *                                 will add possible files:
	 *                                     [] = template.css
	 *                                     [] = template-override.css
	 *                                     [] = template.bootstrap.css
	 *                                     [] = template-override.bootstrap.css
	 *                                     [] = template.webkit.css
	 *                                     [] = template.mac.css
	 *                                     [] = template.chrome.css
	 *                                     [] = template.chrome-32.css
	 *                                 located in those folders
	 *                                     [] = /template/myTheme/css/template.css
	 *                                     [] = template.css
	 *                                     [] = template.css
	 *                                     [] = template.css
	 *                                     [] = template.css
	 *                         - Use this, even if the webOS is installed in a subfolder/sepecific instance
	 *                             addStyle('/templates/myTemplate/css/template.css')
	 *                             addStyle('/media/com_myComponent/css/template.css')
	 *                         - Use this to load external files
	 *                             addStyle('http://externaldomain.com/path/to/css/jquery-large.css');
	 *                             addStyle('//externaldomain.com/path/to/css/jquery-large.css');      // Google styled links to support wether http or https
	 */
	public function addStyle($file = '', $query_string = null, $component = false, $priority = self::DEFAULT_STYLE_PRIORITY, $debug = false, $template_files_override = false)
	{
		if (is_array($file)) {
			$this->addStyles($file, $priority);
			return;
		}

		if ( !$component ) {
			$component = $this->statePrefix;
		}

		// If no debugging value is set, use the configuration settings
		$debug    = $debug ? $debug : $this->getDebugStatus();

		// If we're in admin or debug is enabled, set a query string
		if ( $this->isAdmin() || $debug ) {
			if (strpos(NAWALA_VERSION, 'project.version') === false) {
				if ( $query_string ) {
					$query_string .= '&nawala_version=' . NAWALA_VERSION;
				} else {
					$query_string = '?nawala_version=' . NAWALA_VERSION;
				}
			}
		}

		/** @var $outFiles NCoreUtilityLink[] */
		$out_files = $this->qualifyMediaFile($file, $query_string, $component, $priority, $template_files_override, $debug);

		foreach ($out_files as $link) {
			$addit = true;

			foreach ($this->_styles as $style_priority => $priority_links) {
				$index = array_search($link, $priority_links);
				if ($index !== false) {
					if ($priority < $style_priority) {
						unset($this->_styles[$style_priority][$index]);
					} else {
						$addit = false;
					}
				}
			}

			if ($addit) {
				if (!defined('NAWALA_FINALIZED')) {
					$this->_styles[$priority][] = $link;
				} else {
					$this->document->addStyleSheet($link->getFileUrl());
				}
			}
		}

		// Clean up styles
		foreach ($this->_styles as $style_priority => $priority_links) {
			if (count($priority_links) == 0) {
				unset($this->_styles[$style_priority]);
			}
		}
	}


	/**
	 * @param $path
	 *
	 * @return bool
	 */
	protected function isStyleAvailable($path)
	{
		if (isset($this->_styles_available[$path])) {
			return true;
		} else if (file_exists($path) && is_file($path)) {
			$this->_styles_available[$path] = $path;
			return true;
		}

		return false;
	}


	/**
	 * @param array $styles
	 * @param int   $priority
	 */
	public function addStyles($styles = array(), $priority = self::DEFAULT_STYLE_PRIORITY)
	{
		if (defined('NAWALA_FINALIZED')) return;
		foreach ($styles as $style) $this->addStyle($style, $priority);
	}


	/**
	 * Add inline style to the document
	 * 
	 * @param string $css
	 *
	 * @return null
	 */
	public function addInlineStyle($css = '')
	{
		if ( defined('NAWALA_FINALIZED') ) {
			$this->document->addStyleDeclaration($css);

			return;
		} else {
			// Init global gantry
			global $gantry;

			$gantry->addInlineStyle($css);
		}
	}


	/**
	 * Method to add and compile LESS files!
	 * NOTE: file -overrides arent supportet at this thime !!!!!!!!
	 * 
	 * @param string $lessfile
	 * @param bool   $cssfile
	 * @param int    $priority
	 *
	 * @param array  $options
	 *
	 * @throws RuntimeException
	 */
	public function addLess( $lessfile, $cssfile = null, $priority = self::DEFAULT_STYLE_PRIORITY, array $options = array() )
	{
		$profiler = new JProfiler();
		$mark1 = $profiler->mark(': MARK1 (addLess - START)');

		if (is_array($lessfile)) {
			return false;
		}

		$less_search_paths = array();
		// Template folders
//		$less_search_paths = $this->platform->getAvailablePlatformVersions($this->template->path . '/less', null, true);
//		$less_search_paths[] = $this->template->path . '/less';
		// Library folders
		$less_platform_paths = $this->platform->getAvailablePlatformVersions($this->platform->nawalaPath . '/assets/less', null, true);
		foreach ( $less_platform_paths as $less_platform_path ) {
			$less_search_paths[] = $less_platform_path;
		}
		$less_search_paths[] = $this->platform->nawalaPath . '/assets/less';

		// Try to find the correct path if only a filename is given and setup the less filename
		$fileCheck = false;
		if (dirname($lessfile) == '.') {
			foreach ($less_search_paths as $less_path) {
				if (is_dir($less_path)) {
					$search_file = preg_replace('#[/\\\\]+#', '/', $less_path . '/' . $lessfile);
					if (is_file($search_file)) {
						$lessfile = $search_file;
						$fileCheck = true;
						break;
					}
				}
			}
		}

		// Abort if the less file isnt there
		if ( !$fileCheck ) {
			throw new Exception('No .less file found for: ' . $lessfile, 404);
		}

		$less_file_md5  = md5($lessfile);
		$less_file_path = $this->convertToPath($lessfile);
		$less_file_url  = $this->convertToUrl($less_file_path);


		// Get an md5 sum of any passed in options
		$tmp_options = $options;
		array_walk($tmp_options, create_function('&$v,$k', '$v = " * @".$k." = " .$v;'));
		$options_string = implode($tmp_options, "\n");
		$options_md5    = md5($options_string . (string)$this->get('less-compression', true));

		$css_append = '';
		if (!empty($options)) {
			$css_append = '-' . $options_md5;
		}

		$fileSystem = new NToolsFileSystem();
		$default_compiled_css_dir = $fileSystem->createFolder('css-compiled', $this->cachePath);

		// Setup the output css file name
		if ( is_null($cssfile) || $cssfile == '' || !$cssfile ) {
			$css_file_path   = $default_compiled_css_dir . '/' . pathinfo($lessfile->getName(), PATHINFO_FILENAME) . $css_append . '.css';
			$css_passed_path = pathinfo($css_file_path, PATHINFO_BASENAME);
		} else {
			if (dirname($cssfile) == '.') {
				$css_file_path   = $default_compiled_css_dir . '/' . pathinfo($cssfile, PATHINFO_FILENAME) . $css_append . '.css';
				$css_passed_path = pathinfo($css_file_path, PATHINFO_BASENAME);
			} else {
				$css_file_path           = dirname($this->convertToPath($this->platform->basePath . '/' . $cssfile)) . '/' . pathinfo($cssfile, PATHINFO_FILENAME) . $css_append . '.css';
				$css_passed_path         = $css_file_path;
				$custom_compiled_css_dir = $fileSystem->createFolder('css-compiled', dirname($css_file_path));
			}
		}
		$cssfile_md5 = md5($css_file_path);

		// set base compile modes
		$force_compile  = false;
		$single_compile = false;

		if ( !$this->isAdmin() ) {
			$cachegroup = self::LESS_SITE_CACHE_GROUP;
		} else {
			$cachegroup = self::LESS_ADMIN_CACHE_GROUP;
		}

		$runcompile    = false;
		$cache_handler = NCache::getCache($cachegroup, null, true);

		$cached_less_compile = $cache_handler->get($cssfile_md5, false);
		if ($cached_less_compile === false || !file_exists($css_file_path)) {
			$cached_less_compile = $less_file_path;
			$runcompile          = true;
		} elseif (is_array($cached_less_compile) && isset($cached_less_compile['root'])) {
			if (isset($cached_less_compile['files']) and is_array($cached_less_compile['files'])) {
				foreach ($cached_less_compile['files'] as $fname => $ftime) {
					if (!file_exists($fname) or filemtime($fname) > $ftime) {
						// One of the files we knew about previously has changed
						// so we should look at our incoming root again.
						$runcompile = true;
						break;
					}
				}
			}
		}

		if ($runcompile) {
			$quick_expire_cache = NCache::getCache($cachegroup, $this->get('less-compilewait', self::LESS_MAX_COMPILE_WAIT_TIME));

			$timewaiting = 0;
			while ($quick_expire_cache->get($cssfile_md5 . '-compiling') !== false) {
				$wait = 100000; // 1/10 of a second;
				usleep($wait);
				$timewaiting += $wait;
				if ($timewaiting >= $this->get('less-compilewait', self::LESS_MAX_COMPILE_WAIT_TIME) * 1000000) {
					break;
				}
			}

			// IF BOOTSTRAP 2.3.2 USE NCompilerLess
//			$less = new NCompilerLess();
			// IF BOOTSTRAP 3.x use NCompilerLessAdapter @@ NOTE THAT THIS IS DEPRECATED IN 2.0
			// Adapter for oyejorge's Less.php Library
			$less = new NCompilerLessAdapter();
			
			if (!$this->isAdmin()){
				$less->setImportDir($less_search_paths);
			}

			$less->addImportDir($this->platform->nawalaPath . '/assets/less');

			if (!empty($options)) {
				$less->setVariables($options);
			}

			if ($this->get('less-compression', true)) {
				$less->setFormatter("compressed");
			}

			// remove duplicate paths from the importDirs
			$less->setImportDir( array_unique($less->importDir) );

			$quick_expire_cache->set($cssfile_md5 . '-compiling', true);
			try {
				$new_cache = $less->cachedCompile($cached_less_compile, $force_compile);
			} catch (Exception $ex) {
				$quick_expire_cache->clear($cssfile_md5 . '-compiling');
				throw new RuntimeException('Less Parse Error: ' . $ex->getMessage());
			}

			if (!is_array($cached_less_compile) || $new_cache['updated'] > $cached_less_compile['updated']) {
				$cache_handler->set($cssfile_md5, $new_cache);
				$tmp_ouput_file = tempnam(dirname($css_file_path), 'nawala_less');

				$header = '';
				if ($this->get('less-debugheader', false)) {
					$header .= sprintf("/*\n * Main File : %s", str_replace(JURI::root(true), '', $less_file_url));
					if (!empty($options)) {
						$header .= sprintf("\n * Variables :\n %s", $options_string);
					}
					if (count($new_cache['files']) > 1) {
						$included_files = array_keys($new_cache['files']);
						unset($included_files[0]);
						array_walk($included_files, create_function('&$v,$k', 'global $nawala;$v=" * ".$nawala->convertToUrl($v);'));
						$header .= sprintf("\n * Included Files : \n%s", implode("\n", str_replace(JURI::root(true), '', $included_files)));
					}
					$header .= "\n */\n";
				}
				file_put_contents($tmp_ouput_file, $header . $new_cache['compiled']);

				// Do the messed up file renaming for windows
				if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
					$move_old_file_name = tempnam(dirname($css_file_path), 'nawala_less');
					if (is_file($css_file_path)) @rename($css_file_path, $move_old_file_name);
					@rename($tmp_ouput_file, $css_file_path);
					@unlink($move_old_file_name);
				} else {
					@rename($tmp_ouput_file, $css_file_path);
				}
				JPath::setPermissions($css_file_path);
			}

			$quick_expire_cache->clear($cssfile_md5 . '-compiling');
		}

		$mark2 = $profiler->mark(': MARK2 (addLess - END)');

		$this->addStyle($css_passed_path, null, false, $priority);
		if (!empty($css_append) && !is_null($cssfile) && dirname($cssfile) == '.') {
			$this->addStyle($cssfile, null, false, $priority);
		}

		return $mark1 . "\n" . $mark2;
	}


	/**
	 * ############################ Advanced Settings ###############################
	 */


	/**
	 * Method to check and locate a file, get its direction and return the appropriate file path. his method also look for and return files based on library checks
	 * browser checks and also overrides.
	 * 
	 * @param    string    $file            Filename (eg: load-transition.js) Note: Extension is needed to determine the subfolders!
	 * @param    string    $component       Name of the component, if set, the method will search for files in this folder. Must fit the standard nawala folder structure
	 * @param    string    $query_string    Query string to add to the file url in the returned array.
	 * 
	 * @return    array                     Return an array of files which were found based on all checks this method have done. FALSE if no file was found
	 * 
	 * @TODO   add getAvailablePlatformVersions to get only those dirs that exist // try with benchmark what is faster!!!
	 */
	protected function qualifyMediaFile($file, $query_string = '', $component = null, $priority, $template_files_override = false, $debug = false)
	{
		// Check first if we have only a file, not a directory or any kind of path. (If we find a "/" in $file, return false)
//		if ( preg_match('@/@', $file) ) {
//			return false;
//		}

		// Get caller method
		$dbt = debug_backtrace();
		$caller = $dbt[1]['function'];

		/** @var $out_files NCoreUtilityLink[] */
		$out_files     = array();

		/**
		 * Cut down file based informations
		 */
		// Check and extract possible queries in the ext first and build the type
		$checkExt = pathinfo($file, PATHINFO_EXTENSION);				// eg: .css or .css?what=ever&nothing=else&version=5
		if ( preg_match('@([^?]+)@i', $checkExt, $match) ) {
			$checkExt_query = str_replace($match[0], '', $checkExt);	// eg: ?peter=happy
			$checkExt       = '.' . $match[0];							// eg: .css
			$checkType      = ltrim($checkExt, '.');					// eg: css
		} else {
			$checkType      = pathinfo($file, PATHINFO_EXTENSION);		// eg: css
			$checkExt       = '.' . $checkType;							// eg: .css
			$checkExt_query = '';
		}

		$ext           = $checkExt;										// eg: .css
		$type          = $checkType;									// eg: css
		$filename      = pathinfo($file, PATHINFO_FILENAME);			// eg: template
		$base_file     = $filename . $ext;								// eg: template.css
		$override_file = $filename . "-override" . $ext;				// eg: template-override.css

		$platform    = $this->platform;
		$template    = $this->template;

		// Build the query strings
		if ( $query_string ) {
			$trimmed_query = ltrim($query_string, '?');
			$trimmed_query = ltrim($trimmed_query, '&');
			$tQuery = '?' . $trimmed_query;
		} else {
			$tQuery = false;
		}
		if ( $checkExt_query ) {
			$trimmed_extQuery =ltrim($checkExt_query, '?');
			$trimmed_extQuery =ltrim($trimmed_extQuery, '&');
			if ( $tQuery ) {
				$query_string = $tQuery . '&' . $trimmed_extQuery;
			} else {
				$query_string = '?' . $trimmed_extQuery;
			}
		} else {
			$teQuery = false;
		}
		if ( !$tQuery && !$teQuery ) {
			$query_string = '';
		}

		// Needed for checks in switch statement to determine if we have to search in directories or if we just check for existing and finally adding a full path file
		$fullDirPath = '';

		// Check first to see if $file is a full path file to return possible external files or full path files directly to the appropriate method (addScript, addStyle, addLess)
		$dir = dirname($file);
		if ( $dir != '.' ) {
			// Return full url directly without further checks
			if ( $this->isUriExternal($file) ) {
				$clean_path  = pathinfo($file, PATHINFO_DIRNAME);
				$indexUrlKey = $clean_path . '/' . $base_file;

				$link = new NCoreUtilityLink('url', '', $clean_path, $base_file, $query_string);
				$out_files[$indexUrlKey] = $link;

				// Method caller based checks (addScript, addStyle, etc...) for non full path files ($base_file)
				// Formerly known as extension based checks!
				switch ($caller)
				{
					case 'addScript':
//						$this->_scripts[$priority][] = $link;

						return $out_files;
						break;
					case 'addStyle':
					case 'addLess':
//						$this->_styles[$priority][] = $link;

						return $out_files;
						break;
				}
			}

			$fullDirPath = $this->convertLocalServerUrlToDir($dir);
		}

		/**
		 * PREPARE THE FILE AND DIRECTORY CHECKS
		 */

		/** Get possible filenames, based on library checks. Library checks return an ext specific array of possible filenames. */
		/*
		 * Array
		 *	(
		 *		[0] => template.bootstrap-2.3.2.css
		 *		[1] => template.bs-2.3.2.css
		 *		[2] => template.bootstrap.css
		 *	)
		 */
		$library_checks = $platform->getAdditionalLibraryChecks($base_file);

		/** Get possible override filenames, based on library checks. Library checks return an ext specific array of possible filenames. */
		/*
		 * Same as $library_checks, only the filename is extended by -override!
		 */
		$library_override_checks = $platform->getAdditionalLibraryChecks(basename($override_file));

		/** Get browser based checks */
		/*
		 * Returned on Mac OS X with Chrome Browser
		 * Array
		 * 	(
		 * 		[1] => template.chrome.css
		 * 		[2] => template.mac.css
		 * 		[3] => template.webkit.css
		 * 		[4] => template.chrome-mac.css
		 * 		[5] => template.chrome-32.css
		 * 		[6] => template.chrome32.css
		 * 		[7] => template.chrome32.0.1700.107.css
		 * 		[8] => template.chrome32-mac.css
		 * 	    [9] => template.chrome32.0.1700.107-mac.css
		 * 	)
		 */
		$browser_checks = $this->getBrowserBasedChecks(preg_replace('/-[0-9a-f]{32}\.css$/i', '.css', $base_file));

		/** Get browser based checks for override files */
		/*
		 * Same as $browser_checks, only the filename is extended by -override!
		 */
		$browser_override_checks = $this->getBrowserBasedChecks(basename($override_file));

		// Prepare dir_checks var
		$dir_checks = array();

		// Get directory checks for filename if $fullDirPath is empty, else work with the single $fullDirPath path in $dir_checks array.
		if ( $fullDirPath == '' ) {
			/** Get possible/available folder in /cache dir, based platform checks and based on $type */
			/*
			 * Array
			 * 	(
			 * 	    [0] => /var/www/Live-NawalaRDK/cache/nrdk/css-compiled/com_content
			 * 	    [1] => /var/www/Live-NawalaRDK/cache/nrdk/css-compiled/
			 * 	)
			 */
			if ( $component ) {
				$cache_component_path = $this->cachePath . '/css-compiled/' . $component;
				if ( $this->checkDir($cache_component_path) ) {
					$cache_component_path_dir_checks[] = $cache_component_path;
					$dir_checks = array_merge($dir_checks, $cache_component_path_dir_checks);
				}
			}
			$cache_path = $this->cachePath . '/css-compiled';
			if ( $this->checkDir($cache_path) ) {
				$cache_path_dir_checks[] = $cache_path;
				$dir_checks = array_merge($dir_checks, $cache_path_dir_checks);
			}

			/** Get possible/available folder in /template html dir as override, based platform checks and based on $type */
			/*
			 * Array
			 * 	(
			 * 	    [0] => /var/www/Live-NawalaRDK/templates/rdkapptheme/html/com_content/assets/css/joomla/3.2.0
			 * 	    [1] => /var/www/Live-NawalaRDK/templates/rdkapptheme/html/com_content/assets/css/joomla/3.2
			 * 	    [2] => /var/www/Live-NawalaRDK/templates/rdkapptheme/html/com_content/assets/css
			 * 	)
			 */
			if ( $component ) {
				$component_template_override_path = $template->path . '/html/' . $component . '/assets/' . $type;
				// $component_template_override_dir_checks = $platform->getAdditionalPlatformChecks($component_template_override_path);
				$component_template_override_dir_checks = $platform->getAvailablePlatformVersions($component_template_override_path);
				if ( $this->checkDir($component_template_override_path) ) {
					$component_template_override_dir_checks[] = $component_template_override_path;
				}
				$dir_checks = array_merge($dir_checks, $component_template_override_dir_checks);
			}

			/** Get possible/available folders in /nawala, based on platform checks and current component */
			/*
			 * Array
			 * 	(
			 * 	    [0] => /var/www/Live-NawalaRDK/libraries/nawala/assets/html/com_content/assets/css/joomla/3.2.0
			 * 	    [1] => /var/www/Live-NawalaRDK/libraries/nawala/assets/html/com_content/assets/css/joomla/3.2
			 * 	    [2] => /var/www/Live-NawalaRDK/libraries/nawala/assets/html/com_content/assets/css
			 * 	)
			 */
			if ( $component ) {
				$component_library_path = $platform->nawalaPath . '/assets/html/' . $component . '/assets/' . $type;
				// $component_library_dir_checks = $platform->getAdditionalPlatformChecks($component_library_path);
				$component_library_dir_checks = $platform->getAvailablePlatformVersions($component_library_path);
				if ( $this->checkDir($component_library_path) ) {
					$component_library_dir_checks[] = $component_library_path;
				}
				$dir_checks = array_merge($dir_checks, $component_library_dir_checks);
			}
				
			/** Get possible/available folder in /template, based platform checks and based on $type */
			/*
			 * Array
			 * 	(
			 * 	    [0] => /var/www/Live-NawalaRDK/templates/rdkapptheme/css/joomla/3.2.0
			 * 	    [1] => /var/www/Live-NawalaRDK/templates/rdkapptheme/css/joomla/3.2
			 * 	    [2] => /var/www/Live-NawalaRDK/templates/rdkapptheme/css
			 * 	)
			 */
			$template_path = $template->path . '/' . $type;
			// $template_dir_checks = $platform->getAdditionalPlatformChecks($template_path);
			$template_dir_checks = $platform->getAvailablePlatformVersions($template_path);
			if ( $this->checkDir($template_path) ) {
				$template_dir_checks[] = $template->path . '/' . $type;
			}
			$dir_checks = array_merge($dir_checks, $template_dir_checks);

			/** Get possible/available folders in /media, based on platform checks and current component */
			/*
			 * Array
			 * 	(
			 * 	    [0] => /var/www/Live-NawalaRDK/media/com_content/css/joomla/3.2.0
			 * 	    [1] => /var/www/Live-NawalaRDK/media/com_content/css/joomla/3.2
			 * 	    [2] => /var/www/Live-NawalaRDK/media/com_content/css
			 * 	)
			 */
			if ( $component ) {
				$component_media_path = $platform->basePath . '/media/' . $component . '/' . $type;
				// $media_dir_checks = $platform->getAdditionalPlatformChecks($component_media_path);
				$media_dir_checks = $platform->getAvailablePlatformVersions($component_media_path);
				if ( $this->checkDir($component_media_path) ) {
					$media_dir_checks[] = $component_media_path;
				}
				$dir_checks = array_merge($dir_checks, $media_dir_checks);
			}

			/** Get possible/available folders in /component, based on platform checks and current component */
			/*
			 * @deprecated in 2.0, use /media/com_yourcomponent folder instead
			 * Array
			 * 	(
			 * 	    [0] => /var/www/Live-NawalaRDK/components/com_content/assets/css/joomla/3.2.0
			 * 	    [1] => /var/www/Live-NawalaRDK/components/com_content/assets/css/joomla/3.2
			 * 	    [2] => /var/www/Live-NawalaRDK/components/com_content/assets/css
			 * 	)
			 */
			if ( $component ) {
				$component_path = $platform->basePath . '/components/' . $component . '/assets/' . $type;
				// $component_dir_checks = $platform->getAdditionalPlatformChecks($component_path);
				$component_dir_checks = $platform->getAvailablePlatformVersions($component_path);
				if ( $this->checkDir($component_path) ) {
					$component_dir_checks[] = $component_path;
				}
				$dir_checks = array_merge($dir_checks, $component_dir_checks);
			}

			/** Get possible/available folders in /nawala, based on platform checks */
			/*
			 * Array
			 * 	(
			 * 	    [0] => /var/www/Live-NawalaRDK/libraries/nawala/assets/css/joomla/3.2.0
			 * 	    [1] => /var/www/Live-NawalaRDK/libraries/nawala/assets/css/joomla/3.2
			 * 	    [2] => /var/www/Live-NawalaRDK/libraries/nawala/assets/css
			 * 	)
			 */
			$library_path = $platform->nawalaPath . '/assets/' . $type;
			// $library_dir_checks = $platform->getAdditionalPlatformChecks($library_path);
			$library_dir_checks = $platform->getAvailablePlatformVersions($library_path);
			if ( $this->checkDir($library_path) ) {
				$library_dir_checks[] = $library_path;
			}
			$dir_checks = array_merge($dir_checks, $library_dir_checks);
		} else {
			// Set up the check for fullPathDir with platform and matrix based dirs
			$search_paths = array();
			// $search_paths = $this->platform->getAdditionalPlatformChecks($fullDirPath, $component, true);
			$search_paths = $this->platform->getAvailablePlatformVersions($fullDirPath, $component, true);
			if ( $this->checkDir($fullDirPath) ) {
				$search_paths[] = $fullDirPath;
			}

			foreach ( $search_paths as $search_path ) {
				$dir_checks[] = $search_path;
			}
		}

		if ( $debug ) {
			$this->dir_checks = $dir_checks;
		}

		// Method caller based checks (addScript, addStyle, etc...) for non full path files ($base_file)
		// Formerly known as extension based checks!
		switch ($caller)
		{
			case 'addScript':
				foreach ( $dir_checks as $dir_check ) {
					if ( $this->checkDir($dir_check) ) {
						$base_path    = preg_replace("/\?(.*)/", '', $dir_check);
						$indexFileKey = $base_path . '/' . $base_file;
						
						// Load the base file first if exists
						if ( $this->checkFile($indexFileKey) ) {
							$out_files[$indexFileKey] = new NCoreUtilityLink('local', $base_path, $this->convertToUrl($base_path), $base_file, $query_string);
							break;
						}
					}

					// Check library based files
					foreach ( $library_checks as $check ) {
						$check_path   = preg_replace("/\?(.*)/", '', $dir_check);
						$indexFileKey = $check;

						if ( $this->checkFile($indexFileKey) ) {
							$out_files[$indexFileKey] = new NCoreUtilityLink('local', $check_path, $this->convertToUrl($check_path), $base_file, $query_string);
							break;
						}
					}

					// Check browser based files
					foreach ($browser_checks as $check) {
						$check_path   = preg_replace("/\?(.*)/", '', $dir_check);
						$indexFileKey = $check;

						if ( $this->checkFile($indexFileKey) ) {
							$out_files[$indexFileKey] = new NCoreUtilityLink('local', $check_path, $this->convertToUrl($check_path), $base_file, $query_string);
							break;
						}
					}
				}

				return $out_files;
				break;
			case 'addStyle':
			case 'addLess':
				$base_override = false;

				foreach ( $dir_checks as $dir_check ) {
					$found_file = false;
					$found_file_override = false;

					// Check the base file first
					if ( $this->checkDir($dir_check) ) {
						$base_path            = preg_replace("/\?(.*)/", '', $dir_check);
						$indexFileKey         = $base_path . '/' . $base_file;
						$indexFileKeyOverride = $base_path . '/' . $override_file;

						// Load the base file if exists
						if ( $this->checkFile($indexFileKey) ) {
							if ($this->isStyleAvailable($indexFileKey)) {
								$out_files[$indexFileKey] = new NCoreUtilityLink('local', $base_path, $this->convertToUrl($base_path), $base_file, $query_string);
								$found_file = true;
							}
						}

						// Load the base -override file if exists
						if ( $this->checkFile($indexFileKeyOverride) ) {
							if ($this->isStyleAvailable($indexFileKeyOverride)) {
								if ( $template_files_override ) {
									$out_files[$indexFileKey] = new NCoreUtilityLink('local', $base_path, $this->convertToUrl($base_path), $override_file, $query_string);
									$found_file_override = true;
								} else {
									$out_files[$indexFileKeyOverride] = new NCoreUtilityLink('local', $base_path, $this->convertToUrl($base_path), $override_file, $query_string);
									$found_file_override = true;
								}
							}
						}

						// Found files, break the foreach
						if ( $found_file || $found_file_override ) {
							break;
						}
					}

					// Reset the found_files* var
					$found_file = false;
					$found_file_override = false;

					// Check library based files
					if ( $library_override_checks ) {
						foreach ( $library_override_checks as $check_override ) {
							$check_path            = preg_replace("/\?(.*)/", '', $dir_check);
							$indexFileOverrideName = $check_override;
							$indexFileName         = str_replace('-override', '', $check_override);
							$indexFileOverridePath = $check_path . '/' . $check_override;
							$indexFilePath         = $check_path . '/' . str_replace('-override', '', $check_override);

							// Load the base file if exists
							if ( $this->checkFile($indexFilePath) ) {
								if ($this->isStyleAvailable($indexFilePath)) {
									$out_files[$indexFileName] = new NCoreUtilityLink('local', $check_path, $this->convertToUrl($check_path), $indexFileName, $query_string);
									$found_file = true;
								}
							}

							// Load the library -override file if exists, else try to load the normal file
							if ( $this->checkFile($indexFileOverridePath) ) {
								if ($this->isStyleAvailable($indexFileOverridePath)) {
									if ( $template_files_override ) {
										$out_files[$indexFileName] = new NCoreUtilityLink('local', $check_path, $this->convertToUrl($check_path), $indexFileOverrideName, $query_string);
										$found_file_override = true;
									} else {
										$out_files[$indexFileOverrideName] = new NCoreUtilityLink('local', $check_path, $this->convertToUrl($check_path), $indexFileOverrideName, $query_string);
										$found_file_override = true;
									}
								}
							}

							// Found files, break the foreach
							if ( $found_file || $found_file_override ) {
								break;
							}
						}

						// Reset the found_files* var
						$found_file = false;
						$found_file_override = false;
					}

					// Check browser based files
					foreach ( $browser_override_checks as $check_override ) {
						$check_path            = preg_replace("/\?(.*)/", '', $dir_check);
						$indexFileOverrideName = $check_override;
						$indexFileName         = str_replace('-override', '', $check_override);
						$indexFileOverridePath = $check_path . '/' . $check_override;
						$indexFilePath         = $check_path . '/' . str_replace('-override', '', $check_override);

						// Load the base file if exists
						if ( $this->checkFile($indexFilePath) ) {
							if ($this->isStyleAvailable($indexFilePath)) {
								$out_files[$indexFileName] = new NCoreUtilityLink('local', $check_path, $this->convertToUrl($check_path), $indexFileName, $query_string);
								$found_file = true;
							}
						}

						// Load the browser -override file if exists, else try to load the normal file
						if ( $this->checkFile($indexFileOverridePath) ) {
							if ($this->isStyleAvailable($indexFileOverridePath)) {
								if ( $template_files_override ) {
									$out_files[$indexFileName] = new NCoreUtilityLink('local', $check_path, $this->convertToUrl($check_path), $indexFileOverrideName, $query_string);
									$found_file_override = true;
								} else {
									$out_files[$indexFileOverrideName] = new NCoreUtilityLink('local', $check_path, $this->convertToUrl($check_path), $indexFileOverrideName, $query_string);
									$found_file_override = true;
								}
							}
						}
					
						// Found files, break the foreach
						if ( $found_file || $found_file_override ) {
							break;
						}
					}
				}

				return $out_files;
				break;
		}

		// No files found, return false
		return array();
	}


	/**
	 * Method to get the default menu item
	 * 
	 * @return int|mixed
	 */
	protected function getDefaultMenuItem()
	{
		if ( !$this->isAdmin() ) {
			$app          = JFactory::getApplication();
			$menu         = $app->getMenu();
			$default_item = $menu->getDefault();
			return $default_item->id;
		} else {
			$db      = JFactory::getDBO();
			$default = 0;
			$query   = 'SELECT id' . ' FROM #__menu AS m' . ' WHERE m.home = 1';
	
			$db->setQuery($query);
			$default = $db->loadResult();
			return $default;
		}
	}


	/**
	 * ############################ Helper ###############################
	 */


	/**
	 * Determine if the the passed url is external to the current running platform
	 *
	 * @param     string    $url    the url to check to see if its local;
	 *
	 * @return    mixed
	 */
	protected function isUriExternal($url)
	{
		if (@file_exists($url)) return false;
		$root_url = JURI::root();
		$url_uri  = parse_url($url);
	
		//if the url does not have a scheme must be internal
		if (isset($url_uri['scheme'])) {
			$scheme = strtolower($url_uri['scheme']);
			if ($scheme == 'http' || $scheme == 'https') {
				$site_uri = parse_url($root_url);
				if (isset($url_uri['host']) && strtolower($url_uri['host']) == strtolower($site_uri['host'])) return false;
			} elseif ($scheme == 'file' || $scheme == 'vfs') {
				return false;
			}
		}

		// cover external urls like //foo.com/foo.js
		if (!isset($url_uri['host']) && !isset($url_uri['scheme']) && isset($url_uri['path']) && substr($url_uri['path'], 0, 2) != '//') return false;

		//the url has a host and it isn't internal
		return true;
	}


	/**
	 * Convert url to path
	 * 
	 * @param      string         $url
	 *
	 * @return     bool|string
	 */
	public function convertToPath($url)
	{
		// if its an external link dont even process
		if ($this->isUriExternal($url)) return false;


		$parsed_url = parse_url($url);
		if (preg_match('/^WIN/', PHP_OS) && isset($parsed_url['scheme'])) {
			if (preg_match('/^[A-Za-z]$/', $parsed_url['scheme']) && @file_exists($url)) return $url;
		}
		if (@file_exists($parsed_url['path']) && !isset($parsed_url['scheme'])) return $parsed_url['path'];
		if (isset($parsed_url['scheme'])) {
			$scheme = strtolower($parsed_url['scheme']);
			if ($scheme == 'file') {
				return $parsed_url['path'];
			}

			return $url;
		}

		$instance_url_path           = JURI::root(true);
		$instance_filesystem_path    = $this->cleanPath(JPATH_ROOT);
		$server_filesystem_root_path = $this->cleanPath($_SERVER['DOCUMENT_ROOT']);

		$missing_ds = (substr($parsed_url['path'], 0, 1) != '/') ? '/' : '';
		if (!empty($instance_url_path) && strpos($parsed_url['path'], $instance_url_path) === 0) {
			$stripped_base = $this->cleanPath($parsed_url['path']);
			if (strpos($stripped_base, $instance_url_path) == 0) {
				$stripped_base = substr_replace($stripped_base, '', 0, strlen($instance_url_path));
			}
			$return_path = $instance_filesystem_path . $missing_ds . $this->cleanPath($stripped_base);
		} elseif (empty($instance_url_path) && file_exists($instance_filesystem_path . $missing_ds . $parsed_url['path'])) {
			$return_path = $instance_filesystem_path . $missing_ds . $parsed_url['path'];
		} else {
			$return_path = $server_filesystem_root_path . $missing_ds . $this->cleanPath($parsed_url['path']);
		}

		return $return_path;
	}


	/**
	 * Convert path to url
	 * 
	 * @param      string         $path
	 *
	 * @return mixed|string
	 */
	public function convertToUrl($path)
	{
		// if its external  just return the external url
		if ($this->isUriExternal($path)) return $path;

		$parsed_path     = parse_url($this->cleanPath($path));
		$return_url_path = $parsed_path['path'];
		if (preg_match('/^WIN/', PHP_OS)) {
			$return_url_path = $path;
		}
		if (!@file_exists($return_url_path)) {
			return $return_url_path;
		}
		$instance_url_path           = JURI::root(true);
		$instance_filesystem_path    = $this->cleanPath(JPATH_ROOT);
		$server_filesystem_root_path = $this->cleanPath($_SERVER['DOCUMENT_ROOT']);


		// check if the path seems to be in the instances  or  server path
		// leave it as is if not one of the two
		if (strpos($return_url_path, $instance_filesystem_path) === 0) {
			// its an instance path
			$return_url_path = $instance_url_path . str_replace($instance_filesystem_path, '', $return_url_path);
		} elseif (strpos($return_url_path, $server_filesystem_root_path) === 0) {
			// its a server path
			$return_url_path = str_replace($server_filesystem_root_path, '', $return_url_path);
		}

		// append any passed query string
		if (isset($parsed_path['query'])) {
			$return_url_path = $return_url_path . '?' . $parsed_path['query'];
		}

		return $return_url_path;
	}


	/**
	 * Convert path to dir
	 * 
	 * @param      string         $path
	 *
	 * @return mixed|string
	 */
	protected function convertLocalServerUrlToDir($path)
	{
		// If its external just return the external url
		if ( $this->isUriExternal($path) ) {
			return $path;
		}

		// If its local host url, convert to local path
		if ( preg_match('@^(?:http://)?([^/]+)@i', $path) && preg_match('@' . $_SERVER['HTTP_HOST'] . '@i', $path) ) {
			// preg_match('@^(?:http://)?([^/]+)@i', $_SERVER['HTTP_HOST'], $match);
			// $newPath = '/' . ltrim( str_replace(array('http://', 'https://', $match[0]), '', $path), '/');

			$parsed_path     = parse_url($this->cleanPath($path));
			$return_url_path = $parsed_path['path'];
			$newPath         = str_replace($_SERVER['HTTP_HOST'], '', $return_url_path);

			$path = $this->convertToPath($this->cleanPath($newPath));
		}

		// Ensure that the full path, used for checks below, begins with a slash
		$path =  '/' . ltrim($this->cleanPath($path), '/');

		// Check if the path exists and if it is a directory
		if ( !file_exists($path) && !is_dir($path) ) {
			$instance = ltrim($this->platform->baseUrl, '/');

			if ( $instance != '' ) {
				// webOS is in a subfolder (An own instance under the web root), and the path/dir is not valid. Lets do some checks
				if ( preg_match('@' . $instance . '@', $path) ) {
					// The instance name is within the path string, try to format a new path string
					$cleanPath = str_replace('/' . $instance, '', $path);
					$path = $this->platform->basePath . $cleanPath;
					if ( !file_exists($path) && !is_dir($path) ) {
						$path = $this->platform->basePath . '/' . ltrim($cleanPath, '/');
					}
				} else {
					// The instance name is not within the path string
					$path = $this->platform->basePath . $path;
					if ( !file_exists($path) && !is_dir($path) ) {
						$path = $this->platform->basePath . '/' . ltrim($path, '/');
					}
				}
			} else {
				// webOS is not in a subfolder (An own instance under the web root), and the path/dir is not valid. Lets do some checks
				// TODO: Check this for an installation not located in a subfolder !!!!!!
			}
		}

		return $path;
	}


	/**
	 * Clean path string from trailing slashes, etc...
	 *  
	 * @param    string    $path
	 * @return   string
	 */
	protected function cleanPath($path)
	{
		if (!preg_match('#^/$#', $path)) {
			$path = preg_replace('#[/\\\\]+#', '/', $path);
			$path = preg_replace('#/$#', '', $path);
		}

		return $path;
	}


	/**
	 * @param string $path
	 * @return boolean
	 */
	public function checkFile($path)
	{
		if ( file_exists($path) && is_file($path) && is_readable($path) ) {
			return true;
		} else {
			return false;
		}
	}


	/**
	 * @param string $path
	 * @return boolean
	 */
	public function checkDir($path)
	{
		if ( file_exists($path) && is_dir($path) ) {
			return true;
		} else {
			return false;
		}
	}


	/**
	 * Check Sites Debug Status
	 */
	protected function getDebugStatus()
	{
		$config = JFactory::getConfig();
		$debug  = (boolean) $config->get('debug');

		return $debug;
	}


	/**
	 * ############################ Initialisations ###############################
	 */


	/**
	 * Get Browser based checks
	 * @param      $file
	 *
	 * @param bool $keep_path
	 *
	 * @return array
	 */
	protected function getBrowserBasedChecks($file, $keep_path = false)
	{
		$ext      = substr($file, strrpos($file, '.'));
		$path     = ($keep_path) ? dirname($file) . '/' : '';
		$filename = basename($file, $ext);

		$checks = $this->browser->getChecks($file, $keep_path);

		// check if RTL version needed
		$document = $this->document;
		if ($document->direction == 'rtl' && $this->get('rtl-enabled')) {
			$checks[] = $path . $filename . '-rtl' . $ext;
		}

		return $checks;
	}


	/**
	 * @return mixed|string
	 */
	public function getCurrentTemplate()
	{
		$session = JFactory::getSession();
		if ( !$this->isAdmin() ) {
			$app      = JApplication::getInstance('site', array(), 'J');
			$template = $app->getTemplate();
		} else {
			$admin_template = $this->getAllTemplates('admin', true);
			$template = $admin_template->template;
		}

		return $template;
	}


	/**
	 * Method to get all templates
	 * 
	 * @param    string    admin|site - Get all admin or site templates
	 * @param    string    Get the current template based on $type if set to true
	 * 
	 * @return array|mixed
	 */
	public function getAllTemplates($type, $current = false)
	{
		$cache = JFactory::getCache('com_templates', '');
		$tag   = JFactory::getLanguage()->getTag();

		switch ($type) {
			case 'site':
				$templates = $cache->get('templates0' . $tag);
				if ($templates === false) {
					// Load styles
					$db    = JFactory::getDbo();
					$query = $db->getQuery(true);
					$query->select('id, home, template, params');
					$query->from('#__template_styles');
					$query->where('client_id = 0');
					if ( $current ) {
						$query->where('home = 1');
					}

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
				break;
			case 'admin':
				$templates = $cache->get('templates1' . $tag);
				if ($templates === false) {
					// Load styles
					$db    = JFactory::getDbo();
					$query = $db->getQuery(true);
					$query->select('id, home, template, params');
					$query->from('#__template_styles');
					$query->where('client_id = 1');
					if ( $current ) {
						$query->where('home = 1');
					}

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
				break;
		}

		if ( count($templates) == 1 ) {
			return array_pop($templates);
		}

		return $templates;
	}


	/**
	 * Init the core media paths from and with session scope.
	 * This method have to run on every pageload to prevent searching for files to load which are used in an other templates - if more than one is set (Multi Templating Engine)
	 */
	private function initAjaxModelPaths()
	{
		// Add ajax-models to mediapath array
		$ajaxModelsArray = array(
			$this->template->path . '/ajax-models' . '/' . $this->statePrefix,
			$this->template->path . '/ajax-models',
			$this->platform->basePath . '/' . $this->statePrefix . '/ajax-models',
			$this->platform->nawalaPath . '/ajax-models' . '/' . $this->statePrefix,
			$this->platform->nawalaPath . '/ajax-models'
		);
		$this->setScope('nawalaConfig.ajaxModelPaths', $ajaxModelsArray);
	
		// Set the ajaxModelPaths var
		$this->ajaxModelPaths = $this->getScope('nawalaConfig.ajaxModelPaths');
	}


	/**
	 * @return void
	 */
	protected function loadBrowserConfig()
	{
		$checks = array(
			$this->browser->name,
			$this->browser->platform,
			$this->browser->name . '_' . $this->browser->platform,
			$this->browser->name . $this->browser->shortversion,
			$this->browser->name . $this->browser->version,
			$this->browser->name . $this->browser->shortversion . '_' . $this->browser->platform,
			$this->browser->name . $this->browser->version . '_' . $this->browser->platform
		);

		foreach ($checks as $check) {
			if (array_key_exists($check, $this->_browser_params)) {
				foreach ($this->_browser_params[$check] as $param_name => $param_value) {
					$this->set($param_name, $param_value);
				}
			}
		}
	}


	/**
	 * Method to prepare form based objects
	 */
	protected function loadFormTools()
	{
		// Init the form object
		if ( !$this->form ) {
			$this->form = new JObject();
		}

		// Formerly ==> JFactory::getSession()->get('session.token'));
		$this->form->set( 'session.token', JSession::getFormToken() );
		$this->form->set( 'session.token.alternate', JFactory::getSession()->get('session.token') );
	}


	/**
	 * Method to load the ajax models. This load all models found in supported template and library.
	 * TODO-Check!: Based on the current override terminology, the current component folder will be load right after the template.
	 * 
	 * @param     string    $component    Custom Path, as set in setAjaxModelPath
	 * 
	 * @return    void
	 */
	protected function loadAjaxModels( $component = false )
	{
		$models_paths = array(
			$this->template->path . '/' . 'ajax-models',
			$this->platform->basePath . '/components/' . $this->statePrefix  . '/' . 'ajax-models',
			$this->platform->nawalaPath . '/' . 'ajax-models'
		);

		if ( $component ) {
			array_unshift($models_paths, $component);
		}

		$this->loadModels($models_paths, $this->_ajaxmodels);
		return;
	}

	/**
	 * Method to load the admin ajax models. This load all models found in supported template and library.
	 * TODO-Check!: Based on the current override terminology, the current component folder will be load right after the template.
	 * 
	 * @param     string    $component    Custom Path, as set in setAjaxModelPath
	 * 
	 * @return    void
	 */
	protected function loadAdminAjaxModels( $component = false )
	{
		$models_paths = array(
			$this->template->path . '/' . 'admin' . '/' . 'ajax-models',
			$this->platform->baseAdminPath . '/components/' . $this->statePrefix  . '/' . 'ajax-models',
			$this->platform->nawalaPath . '/' . 'admin' . '/' . 'ajax-models'
		);

		if ( $component ) {
			array_unshift($models_paths, $component);
		}

		$this->loadModels($models_paths, $this->_adminajaxmodels);
		return;
	}

	/**
	 * @param $paths
	 * @param $results
	 */
	protected function loadModels($paths, &$results)
	{
		foreach ($paths as $model_path) {
			if (file_exists($model_path) && is_dir($model_path)) {
				$d = dir($model_path);
				while (false !== ($entry = $d->read())) {
					if ($entry != '.' && $entry != '..') {
						$model_name = basename($entry, ".php");
						$path       = $model_path . '/' . $model_name . '.php';
						if (file_exists($path) && !array_key_exists($model_name, $results)) {
							$results[$model_name] = $path;
						}
					}
				}
				$d->close();
			}
		}
	}


	/**
	 * Method to set a custom ajax model path
	 * 
	 * @param     string     $component       Component where to manually load the ajax-models from. Note: An ajax-model folder must exist in the appropriate component folder.
	 * @param     bool       $isAdmin         Set to true if the component/ajax-model folder is located in administrator.
	 * 
	 * @return    void
	 */
	public function setAjaxModelPath($component, $isAdmin)
	{
		if ( $isAdmin ) {
			$this->loadAdminAjaxModels($component);
		} else {
			$this->loadAjaxModels($component);
		}
	}

	/**
	 * @return void
	 */
	protected function _checkAjaxTool()
	{
		$ajax_tool = "nawala-ajax.php";
		$path      = $this->template-path . '/';
		$origin    = $this->platform->nawalaPath . "/" . $ajax_tool;

		if ((!file_exists($path . $ajax_tool) || (filesize($path . $ajax_tool) != filesize($origin))) && file_exists($path) && is_dir($path) && is_writable($path)) {
			jimport('joomla.filesystem.file');

			if (file_exists($path . $ajax_tool)) JFile::delete($path . $ajax_tool);
			JFile::copy($origin, $path . $ajax_tool);
		}
	}


	/**
	 * @return void
	 */
	function _checkLanguageFiles()
	{
		jimport('joomla.filesystem.file');
		$language_dir       = $this->platform->basePath . '/language/en-GB';
		$admin_language_dir = $this->platform->basePath . '/administrator/language/en-GB';
		$template_lang_file = 'en-GB.tpl_' . $this->template->name . '.ini';

		if ( file_exists($this->template->path . '/' . $template_lang_file) && ((!file_exists($language_dir . '/' . $template_lang_file) && is_writable($language_dir)) || ($this->get('copy_lang_files_if_diff', 0) == 1 && file_exists($language_dir . '/' . $template_lang_file) && filesize($language_dir . '/' . $template_lang_file) != filesize($this->template->path . '/' . $template_lang_file))) )
		{
			JFile::copy($this->template->path . '/' . $template_lang_file, $language_dir . '/' . $template_lang_file);
		}

		if ( file_exists($this->template->path . '/' . 'admin' . '/' . $template_lang_file) && ((!file_exists($admin_language_dir . '/' . $template_lang_file) && is_writable($admin_language_dir) ) || ( $this->get('copy_lang_files_if_diff', 0) == 1 && file_exists($admin_language_dir . '/' . $template_lang_file) && filesize($admin_language_dir . '/' . $template_lang_file) != filesize($this->template->path . '/' . 'admin' . '/' . $template_lang_file))) )
		{
			JFile::copy($this->template->path . '/' . 'admin' . '/' . $template_lang_file, $admin_language_dir . '/' . $template_lang_file);
		}
	}


	/**
	 * @param      $key
	 * @param bool $browser
	 *
	 * @return string
	 */
	protected function getCacheKey($key, $browser = false)
	{
		return $this->template->name . '-' . $this->_params_hash . ($browser ? ('-' . $this->_browser_hash) : '') . "-" . $key;
	}


	/**
	 * @param $className
	 */
	public function addAdminElement($className)
	{
		if (class_exists($className) && !in_array($className, $this->adminElements)) {
			$this->adminElements[] = $className;
		}
	}


	/**
	 * @return string
	 */
	public function getCookiePath()
	{
		$cookieUrl = '';
		if (!empty($this->platform->baseUrl)) {
			if (substr($this->platform->baseUrl, -1, 1) == '/') {
				$cookieUrl = substr($this->platform->baseUrl, 0, -1);
			} else {
				$cookieUrl = $this->platform->baseUrl;
			}
		}
		return $cookieUrl;
	}
}