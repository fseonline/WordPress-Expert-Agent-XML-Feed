<?php
/*
   Plugin Name: WordPress Expert Agent XML Feed
   Plugin URI: http://www.fse-online.co.uk/?utm_source=wordpress&utm_medium=plugin&utm_campaign=WordPress%20Expert%20Agent%20XML%20Feed
   description: Fetch daily for your specified Expert Agent XML feed using wp-cron.
   Version: 1.0.0
   Author: FSE Online Ltd
   Author URI: http://www.fse-online.co.uk/?utm_source=wordpress&utm_medium=plugin&utm_campaign=WordPress%20Expert%20Agent%20XML%20Feed
   License: GPL2


   ███████╗███████╗███████╗     ██████╗ ███╗   ██╗██╗     ██╗███╗   ██╗███████╗
   ╚══════╝██╔════╝██╔════╝    ██╔═══██╗████╗  ██║██║     ██║████╗  ██║██╔════╝
   █████╗  ███████╗█████╗      ██║   ██║██╔██╗ ██║██║     ██║██╔██╗ ██║█████╗
   ██╔══╝  ╚════██║██╔══╝      ██║   ██║██║╚██╗██║██║     ██║██║╚██╗██║██╔══╝
   ██║     ███████║███████╗    ╚██████╔╝██║ ╚████║███████╗██║██║ ╚████║███████╗
   ╚═╝     ╚══════╝╚══════╝     ╚═════╝ ╚═╝  ╚═══╝╚══════╝╚═╝╚═╝  ╚═══╝╚══════╝


   */


  /**
  * Read XML file from Expert Agent
  * Creates an XML file within the /xml/ folder found in the WP Uploads directory
  * @link http://expertagent.co.uk/
  */

  register_activation_hook(__FILE__, 'do_activation');

  /**
  * Fetch the XML file daily through wp-cron.php from within Plugin's cron.php
  * @link http://expertagent.co.uk/
  */
  add_action( 'check_daily', 'fse_download_xml' );

  function do_activation() {
    if ( !wp_next_scheduled( 'check_daily' ) ) {
	     wp_schedule_event( time(), 'daily', 'check_daily' );
    }
  }
  function fse_download_xml() {
    include('cron.php');
    cron();
  }


  // Create custom plugin settings menu
  add_action('admin_menu', 'fse_wp_expert_agent_xml_feed_create_menu');

  function fse_wp_expert_agent_xml_feed_create_menu() {

  	// Add plugin's settings page under 'Settings'
    add_submenu_page( 'options-general.php', 'WordPress Expert Agent XML Feed', 'WordPress Expert Agent XML Feed', 'administrator', __FILE__, 'wp_expert_agent_xml_feed_settings_page' );

  	// Call register settings function
  	add_action( 'admin_init', 'register_wp_expert_agent_xml_feed_settings' );
  }

  function register_wp_expert_agent_xml_feed_settings() {
  	// Register our settings
  	register_setting( 'fse-settings-group', 'fse_wp_expert_agent_xml_feed_remote_file' );
  	register_setting( 'fse-settings-group', 'fse_wp_expert_agent_xml_feed_remote_user' );
  	register_setting( 'fse-settings-group', 'fse_wp_expert_agent_xml_feed_remote_pass' );
  }

  function wp_expert_agent_xml_feed_settings_page() {
    include('settings.php');

  }

  function fse_plugin_action_links( $links ) {
  	$links = array_merge( array(
  		'<a href="' . esc_url( admin_url( 'options-general.php?page=' . plugin_basename( __FILE__ ) ) ) . '">' . 'Settings' . '</a>'
  	), $links );
  	return $links;
  }
  add_action( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'fse_plugin_action_links' );


  register_deactivation_hook( __FILE__, 'do_deactivation' );

  function do_deactivation() {
  	wp_clear_scheduled_hook( 'fse_download_xml' );
  }
