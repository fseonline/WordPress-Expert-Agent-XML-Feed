<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

namespace fse_wpeaxf\Inc\Core;

/**
 * Fired during plugin activation
 *
 * This class defines all code necessary to run during the plugin's activation.

 * @link       http://www.fse-online.co.uk
 * @since      1.0.0
 *
 * @author     FSE Online Ltd
 */

class Activator {

	/**
	 * Short Description.
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

			$min_php = '5.6.0';

		// Check PHP Version and deactivate & die if it doesn't meet minimum requirements.
		if ( version_compare( PHP_VERSION, $min_php, '<' ) ) {
					deactivate_plugins( plugin_basename( __FILE__ ) );
			wp_die( 'This plugin requires a minimum PHP Version of ' . $min_php );
		}

	}

}
