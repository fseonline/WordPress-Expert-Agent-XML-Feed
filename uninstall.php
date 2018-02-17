<?php

if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

// Remove Plugin options on uninstall
global $wpdb;
$plugin_options = $wpdb->get_results( "SELECT option_name FROM $wpdb->options WHERE option_name LIKE 'fse_wp_expert_agent_xml_feed_%'" );

foreach( $plugin_options as $option ) {
    delete_option( $option->option_name );
}
