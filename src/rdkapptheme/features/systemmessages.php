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

gantry_import('core.gantryfeature');

/**
 * @package     gantry
 * @subpackage  features
 */
class GantryFeatureSystemMessages extends GantryFeature
{
	var $_feature_name = 'systemmessages';

	function render($position)
	{
		$app = NFactory::getApplication();

	    /* dummy msgs for testing */
//	    $app->setMessageQueue('This is a "message" but also a "notice" in <b>blue</b> and do not need a second var. Standard: "message"');
//	    $app->setMessageQueue('This is a "error" message in <b>red</b>', 'error');
//	    $app->setMessageQueue('This is a "success" message in <b>green</b>', 'success');
//	    $app->setMessageQueue('This is a "warning" message in <b>yellow</b>', 'warning');
		
		$msgs = $app->getMessageQueue();
		
		ob_start();
		if (sizeof($msgs) > 0) :
		?>
		<div class="clear"></div>
		<jdoc:include type="message" />
		<?php
		endif;
		return ob_get_clean();
	}
}