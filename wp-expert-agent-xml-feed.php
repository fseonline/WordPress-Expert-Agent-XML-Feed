<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://www.fse-online.co.uk
 * @since             1.0.2
 * @package           fse_wpeaxf
 *
 * @wordpress-plugin
 * Plugin Name:       WordPress Expert Agent XML Feed
 * Description:       Fetch daily for your specified Expert Agent XML feed using the WP-Cron system.
 * Version:           1.0.2
 * Author:            FSE Online Ltd
 * Author URI:        http://www.fse-online.co.uk/?utm_source=wordpress&utm_medium=plugin&utm_campaign=WordPress%20Expert%20Agent%20XML%20Feed
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       wp-expert-agent-xml-feed
 * Domain Path:       /languages
 *
 *//*

 ███████╗███████╗███████╗     ██████╗ ███╗   ██╗██╗     ██╗███╗   ██╗███████╗
 ╚══════╝██╔════╝██╔════╝    ██╔═══██╗████╗  ██║██║     ██║████╗  ██║██╔════╝
 █████╗  ███████╗█████╗      ██║   ██║██╔██╗ ██║██║     ██║██╔██╗ ██║█████╗
 ██╔══╝  ╚════██║██╔══╝      ██║   ██║██║╚██╗██║██║     ██║██║╚██╗██║██╔══╝
 ██║     ███████║███████╗    ╚██████╔╝██║ ╚████║███████╗██║██║ ╚████║███████╗
 ╚═╝     ╚══════╝╚══════╝     ╚═════╝ ╚═╝  ╚═══╝╚══════╝╚═╝╚═╝  ╚═══╝╚══════╝

 */

namespace fse_wpeaxf;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define Constants
 */

define( __NAMESPACE__ . '\NS', __NAMESPACE__ . '\\' );

define( NS . 'PLUGIN_NAME', 'wp-expert-agent-xml-feed' );

define( NS . 'PLUGIN_VERSION', '1.0.2' );

define( NS . 'PLUGIN_NAME_DIR', plugin_dir_path( __FILE__ ) );

define( NS . 'PLUGIN_NAME_URL', plugin_dir_url( __FILE__ ) );

define( NS . 'PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

define( NS . 'PLUGIN_TEXT_DOMAIN', 'wp-expert-agent-xml-feed' );


/**
 * Autoload Classes
 */

require_once( PLUGIN_NAME_DIR . 'inc/libraries/autoloader.php' );

/**
 * Register Activation and Deactivation Hooks
 * This action is documented in inc/core/class-activator.php
 */

register_activation_hook( __FILE__, array( NS . 'Inc\Core\Activator', 'activate' ) );

/**
 * The code that runs during plugin deactivation.
 * This action is documented inc/core/class-deactivator.php
 */

register_deactivation_hook( __FILE__, array( NS . 'Inc\Core\Deactivator', 'deactivate' ) );


/**
 * Plugin Singleton Container
 *
 * Maintains a single copy of the plugin app object
 *
 * @since    1.0.2
 */
class fse_wpeaxf {

	static $init;
	/**
	 * Loads the plugin
	 *
	 * @access    public
	 */
	public static function init() {

		if ( null == self::$init ) {
			self::$init = new Inc\Core\Init();
			self::$init->run();
		}

		return self::$init;
	}

}

/*
 *
 * Begins execution of the plugin
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * Also returns copy of the app object so 3rd party developers
 * can interact with the plugin's hooks contained within.
 *
 */
function fse_wpeaxf_init() {
		return fse_wpeaxf::init();
}

$min_php = '5.6.0';

// Check the minimum required PHP version and run the plugin.
if ( version_compare( PHP_VERSION, $min_php, '>=' ) ) {
		fse_wpeaxf_init();
}
