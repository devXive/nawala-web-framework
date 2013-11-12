<?php
/**
 * @version   2.0.5-SNAPSHOT October 31, 2013
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

class RokInstallerEvents extends JPlugin
{

	const STATUS_ERROR     = 'error';
	const STATUS_INSTALLED = 'installed';
	const STATUS_UPDATED   = 'updated';

	protected static $messages = array();

	/**
	 * @var JInstaller
	 */
	protected $toplevel_installer;

	public function setTopInstaller(&$installer)
	{
		$this->toplevel_installer = $installer;
	}

	public function __construct(&$subject, $config = array())
	{

		parent::__construct($subject, $config);

		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');

		$install_html_file = dirname(__FILE__) . '/../install.html';
		$install_css_file  = dirname(__FILE__) . '/../install.css';
		$tmp_path          = JPATH_ROOT . '/tmp';
		if (JFolder::exists($tmp_path)) {
			// Copy install.css to tmp dir for inclusion
			JFile::copy($install_css_file, $tmp_path . '/install.css');
			JFile::copy($install_html_file, $tmp_path . '/install.html');
		}

	}

	public static function addMessage($package, $status, $message = '')
	{
		self::$messages[] = call_user_func_array(array('RokInstallerEvents', $status), array($package, $message));
	}



	/**
	 * @return string
	 */
	protected static function loadCss()
	{
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		$buffer            = '';
		// Drop out Style
		if (file_exists( JPATH_ROOT . '/tmp/install.html')) {
			$buffer .= JFile::read(JPATH_ROOT . '/tmp/install.html');
		}
		return $buffer;
	}


	/**
	 * @param $package
	 * @param $msg
	 *
	 * @return string
	 */
	public static function error($package, $msg)
	{
		ob_start();
		?>
    <li class="xinstall-failure">
		<span class="xinstall-icon"><span></span></span>
        <span class="xinstall-row"><?php echo $package['name'];?> installation failed</span>
        <span class="xinstall-errormsg">
            <?php echo $msg; ?>
        </span>
    </li>
	<?php
		$out = ob_get_clean();
		return $out;
	}

	/**
	 * @param $package
	 *
	 * @return string
	 */
	public static function installed($package)
	{
		ob_start();
		?>
    <li class="xinstall-success">
    	<span class="xinstall-icon"><span></span></span>
        <span class="xinstall-row"><?php echo $package['name'];?> installation was successful</span></li>
	<?php
		$out = ob_get_clean();
		return $out;
	}

	/**
	 * @param $package
	 *
	 * @return string
	 */
	public static function updated($package)
	{
		ob_start();
		?>
    <li class="xinstall-update">
    	<span class="xinstall-icon"><span></span></span>
    	<span class="xinstall-row"><?php echo $package['name'];?> update was successful</span>
    </li>
	<?php
		$out = ob_get_clean();
		return $out;
	}

	public function onExtensionAfterInstall($installer, $eid)
	{
		$lang = JFactory::getLanguage();
		$lang->load('install_override', dirname(__FILE__), $lang->getTag(), true);
		$this->toplevel_installer->set('extension_message', $this->getMessages());
	}

	public function onExtensionAfterUpdate($installer, $eid)
	{
		$lang = JFactory::getLanguage();
		$lang->load('install_override', dirname(__FILE__), $lang->getTag(), true);
		$this->toplevel_installer->set('extension_message', $this->getMessages());
	}


	protected function getMessages()
	{
		$buffer = '';
		$buffer .= self::loadCss();
		$buffer .= '<div id="xinstall"><ul id="xinstall-status">';
		$buffer .= implode('', self::$messages);
		$buffer .= '</ul>';
		$buffer .= '<a href="http://devxive.com" target="_blank" alt="powered by devXive" title="powered by devXive"><i class="xinstall-logo"></i></a>';
		$buffer .= '</div>';
		return $buffer;
	}


}
