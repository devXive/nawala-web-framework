<?php
/**
 * @package          Nawala Rapid Development Kit
 * @subPackage       Nawala - Theme
 * @author           devXive - research and development <support@devxive.com> (http://www.devxive.com)
 * @copyright        Copyright (C) 1997 - 2014 devXive - research and development. All rights reserved.
 * @license          GNU General Public License version 2 or later; see LICENSE.txt
 * @assetsLicense    devXive Proprietary Use License (http://www.devxive.com/license)
 * 
 * @origin based on  Gantry4, Copyright (C) 2007 - 2013 RocketTheme, LLC (http://www.rockettheme.com, http://gantry-framework.com)
 *
 * Gantry uses the Joomla Framework (http://www.joomla.org), a GNU/GPLv2 content management system
 */

// Check to ensure this file is included in Nawala!RDK environment
defined('_NRDKRA') or die();

/**
 * Nawala!Theme Feature GantryFeatureBranding Class
 *
 * Class that build the nawala theme branding
 *
 * @package       Theme
 * @subpackage    Feature
 * @since         1.0
 */
class GantryFeatureBranding extends GantryFeature {
    var $_feature_name = 'branding';

	function render($position)
	{
		ob_start();
		?>
		<div class="rt-block">
			<a href="http://www.devxive.com/" title="devXive - research and development" class="powered-by"></a>
		</div>
		<?php

		return ob_get_clean();
    }
}