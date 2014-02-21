<?php
/**
* @version   $Id: branding.php 2355 2012-08-14 01:04:50Z btowles $
* @author    RocketTheme http://www.rockettheme.com
* @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*
*/
defined('JPATH_BASE') or die();

gantry_import('core.gantryfeature');

class GantryFeatureBranding extends GantryFeature {
    var $_feature_name = 'branding';

	function render($position) {
	    ob_start();
	    ?>
	    <div class="rt-block">
			<a href="http://www.devxive.com/" title="devXive - research and development" class="powered-by"></a>
		</div>
		<?php
	    return ob_get_clean();
	}
}