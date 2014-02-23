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
defined('_NRDKRA') or die;

/**
 * Nawala Framework ApplicationInput Class
 *
 * @package       Framework
 * @subpackage    Application
 * @since         1.1
 */
class NApplicationInput
{
	/**
	 * @var
	 */
	private static $instance;

	/**
	 * @var    string    $option    Current component
	 */
	public $option = null;

	/**
	 * @var    int    $id    Database id of the current item
	 */
	public $id = null;

	/**
	 * @var    string    $task    Current task (formerly known as view.layout/action)
	 */
	public $task = null;

	/**
	 * @var    string    $view    Current view
	 */
	public $view = null;

	/**
	 * @var    string    $layout    Current layout/action
	 */
	public $layout = null;

	/**
	 * @var    string    $task    Current action
	 */
	public $action = null;

	/**
	 * @var    string    $format    Current format
	 */
	public $format = null;

	/**
	 * @var    string    $itemId    Current itemId
	 */
	public $itemId = null;

	/**
	 * @var    string    $menuId    Current menuId
	 */
	public $menuId = null;

	/**
	 * @var    string    $tmpl    Current template (eg. tmpl=component)
	 */
	public $tmpl = null;

	/**
	 * @var    string    $returnPath    Current return path
	 */
	public $returnPath = null;


	/**
	 * Check and create session object if not exist
	 */
	public function __construct()
	{
		// Initialize
		$this->init();
	}

	/**
	 * Initialize session (used in construct)
	 * 
	 * Filter examples:
	 *     The filter defaults to 'CMD'
	 *     
	 *     Example 1 var (.';//55com44_fattern33.3,5%)
	 *     Example 2 var (/var/www/Live-NawalaRDK/templates/ntheme)
	 *     
	 *     Only use the first integer value                                            = INT|INTEGER    ( Based on example 1 it will return "55" )
	 *     Only use the first integer value                                            = UINT           ( Based on example 1 it will return "55" )
	 *     Only use the first floating point value                                     = FLOAT|DOUBLE   ( Based on example 1 it will return "55" )
	 *     Boolean                                                                     = BOOL|BOOLEAN   ( Based on example 1 it will return "1" )
	 *     Only allow characters a-z, and underscores                                  = WORD           ( Based on example 1 it will return "com_fattern" )
	 *     Allow a-z and 0-9 only                                                      = ALNUM          ( Based on example 1 it will return "55com44fattern3335" )
	 *     Allow a-z, 0-9, underscore, dot, dash. Also remove leading dots from result = CMD            ( Based on example 1 it will return "55com44_fattern33.35" )
	 *     Allow a-z, 0-9, slash, plus, equals                                         = BASE64         ( Based on example 1 it will return "//55com44fattern3335" )
	 *     
	 *          More:
	 *               STRING        ( Based on example 1 it will return "';//55com44_fattern33.3,5%" )
	 *               HTML          ( Based on example 1 it will return "';//55com44_fattern33.3,5%" )
	 *               SAFE_HTML     ( Based on example 1 it will return "';//55com44_fattern33.3,5%" )
	 *               ARRAY         ( Based on example 1 it will return "Array( [0] => ';//55com44_fattern33.3,5% )" )
	 *               PATH          ( Based on example 1 it will return "" )
	 *               PATH          ( Based on example 2 it will return "" )
	 *               RAW           ( Based on example 1 it will return "';//55com44_fattern33.3,5%" )
	 *               USERNAME      ( Based on example 1 it will return ";//55com44_fattern33.3,5" )
	 * 
	 * @return void
	 */
	private function init()
	{
		$jinput = JFactory::getApplication()->input;
		
		$this->option     = $jinput->get('option', null, 'CMD');
		$this->id         = $jinput->get('id', null, 'INT');

		// Check and build task/view/action
		$this->task       = $jinput->get('task', null, 'CMD');
		$this->view       = $jinput->get('view', null, 'CMD');
		$this->layout     = $jinput->get('layout', null, 'CMD');
		$this->action     = $jinput->get('action', null, 'CMD');
		
		$taskSplit = explode('.', $this->task);
		if ( count($taskSplit) == 2 && (!$this->view || (!$this->layout && !$this->action)) ) {
			$this->view   = $taskSplit[0];
			$this->action = $taskSplit[1];
			$this->layout = $taskSplit[1];
		}

		$this->format     = $jinput->get('format', null, 'CMD');
		$this->itemId     = $jinput->get('itemId', null, 'INT');
		$this->menuId     = $jinput->get('menuId', null, 'INT');
		$this->tmpl       = $jinput->get('tmpl', null, 'CMD');
		$this->returnPath = base64_decode($jinput->get('return', null, 'BASE64'));
	}
}