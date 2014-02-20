<?php
/**
 * @package          Nawala Rapid Development Kit
 * @subPackage       Nawala - Component
 * @author           devXive - research and development <support@devxive.com> (http://www.devxive.com)
 * @copyright        Copyright (C) 1997 - 2013 devXive - research and development. All rights reserved.
 * @license          GNU General Public License version 2 or later; see LICENSE.txt
 * @assetsLicense    devXive Proprietary Use License (http://www.devxive.com/license)
 */

// Check to ensure this file is included in Nawala!RDK environment
defined('_NRDKRA') or die();

jimport('joomla.application.component.controller');

/**
 * @package        Joomla
 * @subpackage    RokGantry
 */
class NawalaAjaxController extends NawalaLegacyJController
{
    public function ajax()
    {
        /** @var $gantry Gantry */
		global $gantry;

        // load and inititialize gantry class
        $gantry_path = JPATH_SITE . '/libraries/gantry/gantry.php';
        if (file_exists($gantry_path))
        {
            require_once($gantry_path);
        }
        else
        {
            echo "error " . JText::_('Unable to find Gantry library.  Please make sure you have it installed.');
            die;
        }

        $model = $gantry->getAjaxModel(JFactory::getApplication()->input->getString('model'),false);
        if ($model === false) die();
        include_once($model);

        /*
            - USAGE EXAMPLE -

            new Request({
				url: 'http://url/template/administrator/index.php?option=com_admin&tmpl=gantry-ajax-admin',
                onSuccess: function(response) {console.log(response);}
            }).request({
                'model': 'example', // <- mandatory, see "ajax-models" folder
                'template': 'template_folder', // <- mandatory, the name of the gantry template folder (rt_dominion_j15)
                'example': 'example1', // <-- from here are all custom query posts you can use
                'name': 'w00fz',
                'message': 'Hello World!'
            });
        */

        // Clear the cache gantry cache after each call
//        $cache = GantryCache::getInstance();
//        $cache->clearGroupCache();
    }
}
