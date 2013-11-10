<?php
/**
 * @package          Nawala Rapid Development Kit
 * @subPackage	Nawala - System Plugin
 * @author           devXive - research and development <support@devxive.com> (http://www.devxive.com)
 * @copyright        Copyright (C) 1997 - 2013 devXive - research and development. All rights reserved.
 * @license          GNU General Public License version 2 or later; see LICENSE.txt
 * @assetsLicense    devXive Proprietary Use License (http://www.devxive.com/license)
 */

// Check to ensure this file is included in OS
defined('_JEXEC') or die;

class plgSystemNawala extends JPlugin
{
	/**
	 * @var array
	 */
	protected $bootstrapTriggers = array(
		'data-toggle="tab"',
		'data-toggle="pill"',
		'data-dismiss="alert"'
	);

	public function __construct(&$subject, $config = array())
	{
		parent::__construct($subject, $config);
		$app  = JFactory::getApplication();
		$lang = JFactory::getLanguage();
		$lang->load('plg_system_nawala', JPATH_ADMINISTRATOR);
		JLog::addLogger(array('text_file' => 'nawala.php'), $this->params->get('debugloglevel', 63), array('nawala'));
	}


	/**
	 *
	 */
	public function onAfterInitialise()
	{
		$app = JFactory::getApplication();
		if ( $app->isSite() ) {
		}
	}


	/**
	 * Catch the routed functions for
	 */
	public function onAfterRoute()
	{
		$app = JFactory::getApplication();
		if ( $app->isSite() ) {
		}
	}


	/**
	 * @param     $context
	 * @param     $article
	 * @param     $params
	 * @param int $page
	 */
	function onContentBeforeDisplay($context, &$article, &$params, $page = 0)
	{
	}


	/**
	 *
	 */
	public function onBeforeCompileHead()
	{
		$doc = JFactory::getDocument();
		$app = JFactory::getApplication();
		if (!$app->isAdmin()) {
			$template_info = $app->getTemplate(true);
			// If its a gantry template dont load up
//			if ($this->isGantryTemplate($template_info->id) && isset($doc->_styleSheets[JURI::root(true) . '/templates/' . $app->getTemplate() . '/css-compiled/bootstrap.css'])) {
//				unset($doc->_styleSheets[JUri::base(true) . '/media/jui/css/tabs-state.js']); // No Function for js in this case
//				unset($doc->_styleSheets[JUri::base(true) . '/media/jui/css/bootstrap.css']);
//				unset($doc->_styleSheets[JUri::base(true) . '/media/jui/css/bootstrap.min.css']);
//				unset($doc->_styleSheets[JUri::base(true) . '/media/jui/css/bootstrap-responsive.css']);
//				unset($doc->_styleSheets[JUri::base(true) . '/media/jui/css/bootstrap-responsive.min.css']);
//				unset($doc->_styleSheets[JUri::base(true) . '/media/jui/css/bootstrap-extended.css']);
//				unset($doc->_styleSheets[JUri::base(true) . '/media/jui/css/bootstrap-rtl.css']);
//			}
		}
	}


	/**
	 *
	 */
	public function onAfterRender()
	{
		$app = JFactory::getApplication();
	}


	/**
	 *
	 */
	public function onAfterDispatch()
	{
		$app = JFactory::getApplication();

		if ($app->isAdmin()) return;

		$document = JFactory::getDocument();
		$doctype  = $document->getType();
		$messages = JFactory::getSession()->get('application.queue');

		if ($doctype == 'html') {
			$buffer      = "";
			$tmp_buffers = $document->getBuffer();
			if (is_array($tmp_buffers)) {
				foreach ($document->getBuffer() as $key => $value) {
					$buffer .= $document->getBuffer($key);
				}
			}

			if (empty($buffer) && !count($messages)) return;

			// wether to load bootstrap jui or not
			if (($this->_contains($buffer, $this->bootstrapTriggers) || count($messages)) && version_compare(JVERSION, '3.0.0') >= 0) {
				JHtml::_('bootstrap.framework');
			}
		}
	}


	/**
	 * @param       $string
	 * @param array $search
	 * @param bool  $caseInsensitive
	 *
	 * @return bool
	 */
	private function _contains($string, array $search, $caseInsensitive = false)
	{
		$exp = '/' . implode('|', array_map('preg_quote', $search)) . ($caseInsensitive ? '/i' : '/');
		return preg_match($exp, $string) ? true : false;
	}


	/**
	 *
	 */
	public function onSearch()
	{
	}
}