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

gantry_import('core.gantryfeature');

/**
 * Nawala!Theme Feature GantryFeatureLogo Class
 *
 * Class that build the nawala theme logo
 *
 * @package       Theme
 * @subpackage    Feature
 * @since         1.0
 */
class GantryFeaturelogo extends GantryFeature {
    var $_feature_name = 'logo';

	function isEnabled()
    {
		global $gantry;

		if (!isset($gantry->browser)) return $this->get('enabled');
		if ($gantry->browser->platform != 'iphone' && $gantry->browser->platform != 'android') return $this->get('enabled');

		$prefix = $gantry->get('template_prefix');
		$cookiename = $prefix.$gantry->browser->platform.'-switcher';
		$cookie = $gantry->retrieveTemp('platform', $cookiename);

		if ($cookie == 1 && $gantry->get($gantry->browser->platform.'-enabled')) return true;

        return $this->get('enabled');
	}

	function isInPosition($position)
	{
		global $gantry;

		if (!isset($gantry->browser)) return $this->get('enabled');
		if ($gantry->browser->platform != 'iphone' && $gantry->browser->platform != 'android') return ($this->getPosition() == $position);

		$prefix = $gantry->get('template_prefix');
		$cookiename = $prefix.$gantry->browser->platform.'-switcher';
		$cookie = $gantry->retrieveTemp('platform', $cookiename);

		if ($cookie == 1 && $gantry->get($gantry->browser->platform.'-enabled') && ($position == 'mobile-top')) return true;

		return ($this->getPosition() == $position);
	}

	function render($position)
	{
		global $gantry;

		ob_start();
		?>
		<div class="rt-block logo-block">
			<a href="<?php echo $gantry->baseUrl; ?>" id="rt-logo"></a>
		</div>
		<?php

		return ob_get_clean();
	}
}