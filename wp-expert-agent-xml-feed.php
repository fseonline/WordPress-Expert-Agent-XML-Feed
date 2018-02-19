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

   if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


  /**
  * Read XML file from Expert Agent
  * Creates an XML file within the /xml/ folder found in the WP Uploads directory
  * @link http://expertagent.co.uk/
  */

  register_activation_hook(__FILE__, 'fse_wpeaxf_do_activation');

  /**
  * Fetch the XML file daily through wp-cron.php from within Plugin's cron.php
  * @link http://expertagent.co.uk/
  */
  add_action( 'check_daily', 'fse_wpeaxf_download_xml' );

  function fse_wpeaxf_do_activation() {
    if ( !wp_next_scheduled( 'check_daily' ) ) {
	     wp_schedule_event( time(), 'daily', 'check_daily' );
    }
  }
  function fse_wpeaxf_download_xml() {
    include('cron.php');
    fse_wpeaxf_cron();
  }


  // Create custom plugin settings menu
  add_action('admin_menu', 'fse_wpeaxf_create_menu');

  function fse_wpeaxf_create_menu() {

  	// Add plugin's settings page under 'Settings'
    add_submenu_page( 'options-general.php', 'WordPress Expert Agent XML Feed', 'WordPress Expert Agent XML Feed', 'administrator', __FILE__, 'fse_wpeaxf_settings_page' );

  	// Call register settings function
  	add_action( 'admin_init', 'fse_wpeaxf_register_settings' );
  }

  function fse_wpeaxf_register_settings() {
  	// Register our settings
  	register_setting( 'fse_wpeaxf_settings_group', 'fse_wpeaxf_remote_file' );
  	register_setting( 'fse_wpeaxf_settings_group', 'fse_wpeaxf_remote_user' );
  	register_setting( 'fse_wpeaxf_settings_group', 'fse_wpeaxf_remote_pass' );
  }

  function fse_wpeaxf_settings_page() {
    include('settings.php');
    include('ajax.php');

  }

  function fse_wpeaxf_plugin_action_links( $links ) {
  	$links = array_merge( array(
  		'<a href="' . esc_url( admin_url( 'options-general.php?page=' . plugin_basename( __FILE__ ) ) ) . '">' . 'Settings' . '</a>'
  	), $links );
  	return $links;
  }
  add_action( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'fse_wpeaxf_plugin_action_links' );


  register_deactivation_hook( __FILE__, 'fse_wpeaxf_do_deactivation' );

  function fse_wpeaxf_do_deactivation() {
  	wp_clear_scheduled_hook( 'fse_wpeaxf_download_xml' );
  }
