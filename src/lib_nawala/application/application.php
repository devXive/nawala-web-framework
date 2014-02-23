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
 * Nawala Framework Application Core Class
 *
 * Class that buil and push all informations we need in all nawala environments
 *
 * @package       Framework
 * @subpackage    Application
 * @since         1.0
 */
class NApplication
{
	/**
	 * Application instances container.
	 *
	 * @var    array
	 * @since  1.0
	 */
	protected static $instances = array();

	/**
	 * Static prefix build for and from the currently active application for usage in user state scope
	 *
	 * @var    string    $statePrefix    As set in $option var (eg. com_app)
	 * @since  1.1
	 */
	public $statePrefix = null;

	/**
	 * @var boolean
	 */
	public $isAdmin;
	public $isSite;

	/**
	 * @var    object    $input    NApplicationInput
	 */
	public $input = null;

	/**
	 * @var    object    $formatter    Nawala formatter tools object
	 */
	public $formatter = null;

	/**
	 * @var    object    $mainframe    See JApplication
	 */
	public $mainframe = null;


	/**
	 * Constructor
	 */
	public function __construct()
	{
		// Initialise the global mainframe aka JFactory::getApplication()
		$this->getMainframe();

		// Initialize
		$this->isAdmin = $this->mainframe->isAdmin();
		$this->isSite = $this->mainframe->isSite();

		// Get and set the Input object
		$this->input = new NApplicationInput();

		$this->statePrefix = $this->input->option;
		
		// Init the formatter
		$this->formatter = new NFormatter();
	}


	/**
	 * Returns a reference to the global NApplication object, only creating it if it doesn't already exist.
	 *
	 * This method must be invoked as: $web = NApplication::getInstance();
	 *
	 * @param   string  $name  The name (optional) of the NApplication class to instantiate.
	 *
	 * @return  NApplication
	 *
	 * @since   1.0
	 * @throws  RuntimeException
	 * 
	 * @deprecated 2.0 due to use of autoloader
	 */
	public function getInstance($name = null)
	{
		if (empty(static::$instances[$name]))
		{
			// Create a JApplication object.
			$classname = 'NApplication' . ucfirst($name);

			if (!class_exists($classname))
			{
				throw new RuntimeException(JText::sprintf('JLIB_APPLICATION_ERROR_APPLICATION_LOAD', $name), 500);
			}

			static::$instances[$name] = new $classname;
		}

		return static::$instances[$name];
	}


	/**
	 * Returns a reference to the global JApplicationCms object (aka JFactory::getApplication())
	 *
	 * @return  JFactory::getApplication object
	 *
	 * @since   1.0
	 */
	private function getMainframe()
	{
		if (!$this->mainframe)
		{
			$app = JFactory::getApplication();
			$this->mainframe =& $app;
		}

		return $this->mainframe;
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
}