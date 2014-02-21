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

// Create the Templates XML Object
$nxml = new NCoreSimplexml(JPATH_ROOT . '/templates/' . $gantry->templateName . '/templateDetails.xml', null, true);
?>
<div class="template-preview">
	<img src="<?php echo $gantry->templateUrl;?>/template-thumb-big.png" style="max-width:425px;" />
	<h2>Key Features</h2>
	<ul>
		<li>Responsive Design supporting Phone, Tablets and Desktops</li>
		<li>LESS CSS auto-compilation</li>
		<li>Flexible widgets for template customization</li>
		<li>Nawala Rapid Development Kit with full extensible framework architecture</li>
		<li>XML driven and with overrides for unprecedented levels of customization</li>
		<li>Per menu-item level of control over any configuration option with inheritance</li>
	</ul>
</div>
<div class="template-description">
	<h1><?php echo JText::_($nxml->get('name')); ?> <span class="g4-version">v<?php echo $nxml->get('version'); ?></span></h1>
	<h2>Application Optimized Theme</h2>

	<p><?php echo JText::_($nxml->get('name')); ?> is a clean modern responsive design that is especially build and optimized for web applications.</p>

	<h2>What is the Nawala!RDK?</h2>

	<p>
		The Nawala!RDK (Rapid Development Kit) is a sophisticated set of powerful frameworks and applications with the sole intention of
		being the best platform to build solid web apps with.<br>
		<br>
		Nawala takes all the lessons learned during the development of many XiveApp's and XiveTheme's and distills that knowledge into a
		single super-flexible environment that is easy to configure, simple to extend, and powerful enough to handle anything we want to
		throw at it.
	</p>
	
	<p>Get help and find out more at <a href="http://devxive.com" target="_blank">http://devxive.com</a></p>

	<p class="text-right text-stroke"><small>Last updated: <?php echo JHTML::_( 'date', $nxml->get('creationDate'), JText::_('DATE_FORMAT_LC') ); ?></small></p>
</div>