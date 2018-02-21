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

  add_action( 'admin_post_nds_form_response', 'the_form_response');
  function the_form_response() {

  		if( isset( $_POST['fse_wpeaxf_add_form_nonce'] ) && wp_verify_nonce( $_POST['fse_wpeaxf_add_form_nonce'], 'fse_wpeaxf_add_meta_form_nonce') ) {

  			// sanitize the input
  			$fse_wpeaxf_remote_file = sanitize_text_field( $_POST['fse_wpeaxf']['remote_file'] );
  			$fse_wpeaxf_remote_user = sanitize_text_field( $_POST['fse_wpeaxf']['remote_user'] );
  			$fse_wpeaxf_remote_pass = sanitize_text_field( $_POST['fse_wpeaxf']['remote_pass'] );
  			$fse_wpeaxf_user =  get_user_by( 'login',  $_POST['fse_wpeaxf']['user_select'] );
  			$fse_wpeaxf_user_id = absint( $fse_wpeaxf_user->ID ) ;

  			// do the processing

  			// add the admin notice
  			$admin_notice = "success";

  			// redirect the user to the appropriate page
        wp_redirect(admin_url('options-general.php?page=wp-expert-agent-xml-feed%2Fwp-expert-agent-xml-feed.php'));
  			// exit;
  		}
  		else {
  			wp_die( __( 'Invalid nonce specified', $this->plugin_name ), __( 'Error', $this->plugin_name ), array(
  						'response' 	=> 403,
  						'back_link' => 'admin.php?page=' . $this->plugin_name,
  				) );
  		}
  	}

  add_action( 'admin_post_fse_wpeaxf_send', 'fse_wpeaxf_send_func' );

  function fse_wpeaxf_send_func() {

    // Continue if FTP extension exists on the PHP server
    if(!function_exists('ftp_connect')) {
      exit ('Please enable FTP on your server.');
    }

    //#STEP 1 - Connect to FTP


    $ftp_host = 'ftp.expertagent.co.uk'; // this is constant
    $ftp_port = 21; // this is constant
    $ftp_timeout = 10; // 10 seconds timeout
    $ftp_username = esc_attr( get_option('fse_wpeaxf_remote_user') );
    $ftp_password = esc_attr( get_option('fse_wpeaxf_remote_pass') );

    $ftp_connection = ftp_connect($ftp_host, $ftp_port, $ftp_timeout);
    if ($ftp_connection === false) {

    }
    $login_result = ftp_login($ftp_connection, $ftp_username, $ftp_password);
    if ($ftp_connection === false) {

    }

    //#STEP 2 - Download File
    $plugin_basename = plugin_basename( __FILE__ );
    $plugin_name = trim( dirname( $plugin_basename ), '/' );

    $file = esc_attr( get_option('fse_wpeaxf_remote_file') );
    $extension = pathinfo($file, PATHINFO_EXTENSION);
    $filename = basename($file, '.xml');

    // make sure it's XML since that's what we do!
    if( $extension !== 'xml' ) {

      die();
    }

    $upload_dir = wp_upload_dir(); // Array of key => value pairs
    $plugin_upload_dir = $upload_dir['basedir'] . '/' . $plugin_name . '/';
    $plugin_upload_dir_xml = $upload_dir['basedir'] . '/' . $plugin_name . '/xml/';

    // Create Directory /wp-uploads/plugin_name/
    if(!file_exists( $plugin_upload_dir )) {
      mkdir( $plugin_upload_dir, 0755, true );
    }

    // Create Directory /wp-uploads/plugin_name/xml/
    if(!file_exists( $plugin_upload_dir_xml )) {
      mkdir( $plugin_upload_dir_xml, 0755, true );
    }

    $local_file_path = $plugin_upload_dir_xml . $file;
    $server_file_path = $file;


    ftp_pasv($ftp_connection, true); // So we don't get a BINARY Warning...

    if (ftp_get($ftp_connection, $local_file_path, $server_file_path, FTP_BINARY)) {

    } else {

    }
    ftp_close($ftp_connection);

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
