<?php
/**
* @version   $Id: index.php 15529 2013-11-13 22:04:39Z kevin $
 * @author RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 * Gantry uses the Joomla Framework (http://www.joomla.org), a GNU/GPLv2 content management system
 *
 */
// no direct access
defined( '_JEXEC' ) or die( 'Restricted index access' );

// load and inititialize gantry class
require_once(dirname(__FILE__) . '/lib/gantry/gantry.php');
global $gantry;
$gantry->init();

/** Instantiate global $nawala */
global $nawala;
$nawala->init();

// get the current preset
$gpreset = str_replace(' ','',strtolower($gantry->get('name')));

?>
<!doctype html>
<html xml:lang="<?php echo $gantry->language; ?>" lang="<?php echo $gantry->language;?>" >
<head>
	<?php if ($gantry->get('layout-mode') == '960fixed') : ?>
	<meta name="viewport" content="width=960px, initial-scale=1, minimum-scale=1, maximum-scale=1">
	<?php elseif ($gantry->get('layout-mode') == '1200fixed') : ?>
	<meta name="viewport" content="width=1200px, initial-scale=1, minimum-scale=1, maximum-scale=1">
	<?php else : ?>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?php endif; ?>
<?php if ($gantry->browser->name == 'ie') : ?>
	<meta content="IE=edge" http-equiv="X-UA-Compatible" />
<?php endif; ?>	
    <?php
        $gantry->displayHead();
		$gantry->addStyle('grid-responsive.css', 5);
		$gantry->addLess('bootstrap.less', 'bootstrap.css', 6);
        if ($gantry->browser->name == 'ie'){
        	if ($gantry->browser->shortversion == 9){
        		$gantry->addInlineScript("if (typeof RokMediaQueries !== 'undefined') window.addEvent('domready', function(){ RokMediaQueries._fireEvent(RokMediaQueries.getQuery()); });");
        	}
			if ($gantry->browser->shortversion == 8){
				$gantry->addScript('html5shim.js');
			}
		}
		if ($gantry->get('layout-mode', 'responsive') == 'responsive') $gantry->addScript('rokmediaqueries.js');
		if ($gantry->get('loadtransition')) {
		$gantry->addScript('load-transition.js');
		$hidden = ' class="rt-hidden"';}
    ?>
</head>
<body <?php echo $gantry->displayBodyTag(); ?>>
    <?php /** Begin Top Surround **/ if ($gantry->countModules('top') or $gantry->countModules('header')) : ?>
    <header id="rt-top-surround">
		<?php /** Begin Top **/ if ($gantry->countModules('top')) : ?>
		<div id="rt-top" <?php echo $gantry->displayClassesByTag('rt-top'); ?>>
			<div class="rt-container">
				<?php echo $gantry->displayModules('top','standard','standard'); ?>
				<div class="clear"></div>
			</div>
		</div>
		<?php /** End Top **/ endif; ?>
		<?php /** Begin Header **/ if ($gantry->countModules('header')) : ?>
		<div id="rt-header">
			<div class="rt-container">
				<?php echo $gantry->displayModules('header','standard','standard'); ?>
				<div class="clear"></div>
			</div>
		</div>
		<?php /** End Header **/ endif; ?>
	</header>
	<?php /** End Top Surround **/ endif; ?>
	<?php /** Begin Drawer **/ if ($gantry->countModules('drawer')) : ?>
    <div id="rt-drawer">
        <div class="rt-container">
            <?php echo $gantry->displayModules('drawer','standard','standard'); ?>
            <div class="clear"></div>
        </div>
    </div>
    <?php /** End Drawer **/ endif; ?>
	<?php /** Begin Showcase **/ if ($gantry->countModules('showcase')) : ?>
	<div id="rt-showcase">
		<div class="rt-showcase-pattern">
			<div class="rt-container">
				<?php echo $gantry->displayModules('showcase','standard','standard'); ?>
				<div class="clear"></div>
			</div>
		</div>
	</div>
	<?php /** End Showcase **/ endif; ?>
	<div id="rt-transition"<?php if ($gantry->get('loadtransition')) echo $hidden; ?>>
		<div id="rt-mainbody-surround">
			<?php /** Begin Feature **/ if ($gantry->countModules('feature')) : ?>
			<div id="rt-feature">
				<div class="rt-container">
					<?php echo $gantry->displayModules('feature','standard','standard'); ?>
					<div class="clear"></div>
				</div>
			</div>
			<?php /** End Feature **/ endif; ?>
			<?php /** Begin Utility **/ if ($gantry->countModules('utility')) : ?>
			<div id="rt-utility">
				<div class="rt-container">
					<?php echo $gantry->displayModules('utility','standard','standard'); ?>
					<div class="clear"></div>
				</div>
			</div>
			<?php /** End Utility **/ endif; ?>
			<?php /** Begin Breadcrumbs **/ if ($gantry->countModules('breadcrumb')) : ?>
			<div id="rt-breadcrumbs">
				<div class="rt-container">
					<?php echo $gantry->displayModules('breadcrumb','standard','standard'); ?>
					<div class="clear"></div>
				</div>
			</div>
			<?php /** End Breadcrumbs **/ endif; ?>
			<?php /** Begin Main Top **/ if ($gantry->countModules('maintop')) : ?>
			<div id="rt-maintop">
				<div class="rt-container">
					<?php echo $gantry->displayModules('maintop','standard','standard'); ?>
					<div class="clear"></div>
				</div>
			</div>
			<?php /** End Main Top **/ endif; ?>
			<?php /** Begin Full Width**/ if ($gantry->countModules('fullwidth')) : ?>
			<div id="rt-fullwidth">
				<?php echo $gantry->displayModules('fullwidth','basic','basic'); ?>
					<div class="clear"></div>
				</div>
			<?php /** End Full Width **/ endif; ?>
			<?php /** Begin Main Body **/ ?>
			<div class="rt-container">
		    		<?php echo $gantry->displayMainbody('mainbody','sidebar','standard','standard','standard','standard','standard'); ?>
		    	</div>
			<?php /** End Main Body **/ ?>
			<?php /** Begin Main Bottom **/ if ($gantry->countModules('mainbottom')) : ?>
			<div id="rt-mainbottom">
				<div class="rt-container">
					<?php echo $gantry->displayModules('mainbottom','standard','standard'); ?>
					<div class="clear"></div>
				</div>
			</div>
			<?php /** End Main Bottom **/ endif; ?>
			<?php /** Begin Extension **/ if ($gantry->countModules('extension')) : ?>
			<div id="rt-extension">
				<div class="rt-container">
					<?php echo $gantry->displayModules('extension','standard','standard'); ?>
					<div class="clear"></div>
				</div>
			</div>
			<?php /** End Extension **/ endif; ?>
		</div>
	</div>
	<?php /** Begin Bottom **/ if ($gantry->countModules('bottom')) : ?>
	<div id="rt-bottom">
		<div class="rt-container">
			<?php echo $gantry->displayModules('bottom','standard','standard'); ?>
			<div class="clear"></div>
		</div>
	</div>
	<?php /** End Bottom **/ endif; ?>
	<?php /** Begin Footer Section **/ if ($gantry->countModules('footer') or $gantry->countModules('copyright')) : ?>
	<footer id="rt-footer-surround">
		<?php /** Begin Footer **/ if ($gantry->countModules('footer')) : ?>
		<div id="rt-footer">
			<div class="rt-container">
				<?php echo $gantry->displayModules('footer','standard','standard'); ?>
				<div class="clear"></div>
			</div>
		</div>
		<?php /** End Footer **/ endif; ?>
		<?php /** Begin Copyright **/ if ($gantry->countModules('copyright')) : ?>
		<div id="rt-copyright">
			<div class="rt-container">
				<?php echo $gantry->displayModules('copyright','standard','standard'); ?>
				<div class="clear"></div>
			</div>
		</div>
		<?php /** End Copyright **/ endif; ?>
	</footer>
	<?php /** End Footer Surround **/ endif; ?>
	<?php /** Begin Debug **/ if ($gantry->countModules('debug')) : ?>
	<div id="rt-debug">
		<div class="rt-container">
			<?php echo $gantry->displayModules('debug','standard','standard'); ?>
			<div class="clear"></div>
		</div>
	</div>
	<?php /** End Debug **/ endif; ?>
	<?php /** Begin Analytics **/ if ($gantry->countModules('analytics')) : ?>
	<?php echo $gantry->displayModules('analytics','basic','basic'); ?>
	<?php /** End Analytics **/ endif; ?>
	</body>
</html>
<?php
$gantry->finalize();
?>

<?php 
// ----------------------------------------------------------------------------------------------------------------------------
$test = '';
$test2 = '';
$test3 = '';

$request = new JRequest();
$input = new JApplication();
$test = $request->get();
$test2 = $_REQUEST;
// $test3 = $input->input->get('option');

$file = 'master-ie9.css';
$file0 = 'template.css';
$file1 = 'var/www/Live-NawalaRDK/templates/rdkapptheme/css/template.css';
$file2 = '/var/www/Live-NawalaRDK/templates/rdkapptheme/css/template.css';
$file3 = '/Live-NawalaRDK/templates/rdkapptheme/css/template.css';
$file4 = 'Live-NawalaRDK/templates/rdkapptheme/css/template.css';
$file5 = '/templates/rdkapptheme/css/template.css';
$file6 = 'templates/rdkapptheme/css/template.css';
$file7 = 'http://192.168.100.230/Live-NawalaRDK/templates/rdkapptheme/css/master-ie9.css?hans=wert&peter=pan';

$file8 = 'http://docs.joomla.org/extensions/HeaderTabs/skins-jquery/ext.headertabs.jquery-large.css?hans=wert&peter=pan';
$file9 = '//docs.joomla.org/extensions/HeaderTabs/skins-jquery/ext.headertabs.jquery-large.css';

$file44 = '///Live-NawalaRDK/templates/rdkapptheme/css/master-ie9.css/';

$file10 = 'load-transition.js';
$file11 = 'var/www/Live-NawalaRDK/templates/rdkapptheme/js/load-transition.js';
$file12 = '/var/www/Live-NawalaRDK/templates/rdkapptheme/js/load-transition.js?peter=pan';
$file13 = '/Live-NawalaRDK/templates/rdkapptheme/js/load-transition.js';
$file14 = 'Live-NawalaRDK/templates/rdkapptheme/js/load-transition.js';
$file15 = '/templates/rdkapptheme/js/load-transition.js';
$file16 = 'templates/rdkapptheme/js/load-transition.js?hans=wert&peter=pan';
$file17 = 'http://192.168.100.230/Live-NawalaRDK/templates/rdkapptheme/js/load-transition.js';
$file18 = 'http://docs.joomla.org/extensions/HeaderTabs/skins-jquery/ext.headertabs.jquery-large.css?hans=wert&peter=pan';
$file19 = '//docs.joomla.org/extensions/HeaderTabs/skins-jquery/ext.headertabs.jquery-large.css';

$file20 = 'templates/rdkapptheme/less/1200fixed.less?peter=lustig';
$file21 = '/var/www/Live-NawalaRDK/templates/rdkapptheme/less/1200fixed.less';
$file22 = '/var/www/Live-NawalaRDK/templates/rdkapptheme/less/bootstrap.less';

$test2 = new NCorePlatform();

// $test2 = $nawala->addLess('bootstrap.less', 'bootstrap', 10, array('test1' => 'hannababera'));

$profiler = new JProfiler();
$mark1 = $profiler->mark('Before compile:');

function autoCompileLess($inputFile) {
	$basefile = pathinfo($inputFile, PATHINFO_BASENAME);
	$filename = pathinfo($inputFile, PATHINFO_FILENAME);
	$cacheDir = JPATH_ROOT . '/cache/nrdk';
	$outFile = $cacheDir . '/compiled/' . $filename . '.css';

	// load the cache
	$cacheFile = $cacheDir . '/cached/' . $basefile . ".cache";

	if (file_exists($cacheFile)) {
		$cache = unserialize(file_get_contents($cacheFile));
	} else {
		$cache = $inputFile;
	}

	$less = new NCompilerLess();
	$less_search_paths = array();
	$less_search_paths[] = '/var/www/Live-NawalaRDK/templates/rdkapptheme/less/joomla/3.0';
	$less_search_paths[] = '/var/www/Live-NawalaRDK/templates/rdkapptheme/less';

	$less->setImportDir($less_search_paths);
	$less->addImportDir(NAWALA_LIBRARY . '/assets');

	$newCache = $less->cachedCompile($cache);

	if ( !file_exists($outFile) || (!is_array($cache) || $newCache["updated"] > $cache["updated"]) ) {
		file_put_contents($cacheFile, serialize($newCache));
		file_put_contents($outFile, $newCache['compiled']);
	}
}

$file22 = NAWALA_LIBRARY . '/assets/less/bootstrap.less';

$test = $nawala->addLess('bootstrap.less', 'hanswerter', 10, array('font-size-base'=>'16px'));


// $lessc = new NCompilerLessAdapter();
// $new_cache = $lessc->cachedCompile($file22);

// $test = $new_cache;
// $directories = array( NAWALA_LIBRARY . '/assets' => '/mysite/bootstrap/' );
//$options = array('compressed' => true);
//$parser = new Less_Parser($options);
//$parser->parseFile( $file22, JUri::root(true) );
//$css = $parser->getCss();
//$imported_files = $parser->allParsedFiles();

//$test = $imported_files;

$mark2 = $profiler->mark('After compile:');
$test2 = $mark1 . "\n" . $mark2;



// $stats = $test->getStats();
// $test = $stats->size['KB'];
// $test = new NCoreSimplexml($nawala->template->path . '/templateDetails.xml', null, true);



// __LINE__, __FILE__, __FUNCTION__, __CLASS__, and __METHOD__

// get_called_class() get's the current class. This might also be interesting: debug_print_backtrace().

// get_class($this)

// $test = $nawala->addStyle($file);


// $test3 = array_merge($test, $test2);

// $menu = JFactory::getApplication()->getMenu();
// $test = $menu->getActive();

// array_unshift($test, 'template7system');

// $test = NFactory::getAjax();
// $test = $test->getGantryAjaxSupport();
// $session = JFactory::getSession();
// $test = $session->get('mediaPaths', 'NixDa', 'nawalaConfig');





echo '<pre>';
print_r( $test );
echo '<hr>';
print_r( $test2 );
echo '<hr>';
print_r( $test3 );
echo '<hr>';
print_r( $testFunction );
echo '</pre>';
$nawala->finalize();
?>

