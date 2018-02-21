<?php
/**
* Fired when the plugin is uninstalled.
*
* @package wp-expert-agent-xml-feed
* @author FSE Online <info@fse-online.co.uk>
* @license GPL-2.0+
* @link http://www.fse-online.co.uk
* @copyright 2018 FSE Online Ltd
*/

// If uninstall not called from WordPress, then exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Remove Plugin options on uninstall
global $wpdb;
$plugin_options = $wpdb->get_results( "SELECT option_name FROM $wpdb->options WHERE option_name LIKE 'fse_wp_expert_agent_xml_feed_%'" );

foreach( $plugin_options as $option ) {
    delete_option( $option->option_name );
}
