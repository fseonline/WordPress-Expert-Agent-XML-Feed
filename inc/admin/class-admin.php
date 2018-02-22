<?php

namespace fse_wpeaxf\Inc\Admin;

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @link       http://www.fse-online.co.uk
 * @since      1.0.0
 *
 * @author    FSE Online Ltd
 */
 class Admin {

 	/**
 	 * The ID of this plugin.
 	 *
 	 * @since    1.0.0
 	 * @access   private
 	 * @var      string    $plugin_name    The ID of this plugin.
 	 */
 	private $plugin_name;

 	/**
 	 * The version of this plugin.
 	 *
 	 * @since    1.0.0
 	 * @access   private
 	 * @var      string    $version    The current version of this plugin.
 	 */
 	private $version;

 	/**
 	 * The text domain of this plugin.
 	 *
 	 * @since    1.0.0
 	 * @access   private
 	 * @var      string    $plugin_text_domain    The text domain of this plugin.
 	 */
 	private $plugin_text_domain;

 	/**
 	 * Initialize the class and set its properties.
 	 *
 	 * @since    1.0.0
 	 * @param    string $plugin_name	The name of this plugin.
 	 * @param    string $version	The version of this plugin.
 	 * @param	 string $plugin_text_domain	The text domain of this plugin
 	 */
 	public function __construct( $plugin_name, $version, $plugin_text_domain ) {

 		$this->plugin_name = $plugin_name;
 		$this->version = $version;
 		$this->plugin_text_domain = $plugin_text_domain;

 	}

 	/**
 	 * Callback for the admin menu
 	 *
 	 * @since    1.0.0
 	 */
 	public function add_plugin_admin_menu() {

 		$form_page_hook = add_submenu_page(
          'options-general.php', //parent slug
          __( 'WordPress Expert Agent XML Feed', $this->plugin_text_domain ), //page title
          __( 'WordPress Expert Agent XML Feed', $this->plugin_text_domain ), //menu title
          'manage_options', //capability
          $this->plugin_name, //menu_slug
          array( $this, 'html_form_page_content' ) //callback for page content
 					);

 		/*
 		 * The $page_hook_suffix can be combined with the load-($page_hook) action hook
 		 * https://codex.wordpress.org/Plugin_API/Action_Reference/load-(page)
 		 *
 		 * The callback below will be called when the respective page is loaded
 		 */
 		add_action( 'load-'.$form_page_hook, array( $this, 'loaded_form_submenu_page' ) );
 	}

 	/*
 	 * Callback for the add_submenu_page action hook
 	 *
 	 * The plugin's HTML form is loaded from here
 	 *
 	 * @since	1.0.0
 	 */
 	public function html_form_page_content() {
 		//show the form
 		include_once( 'views/partials-html-form-view.php' );
 	}

 	/*
 	 * Callback for the load-($form_page_hook)
 	 * Called when the plugin's submenu HTML form page is loaded
 	 *
 	 * @since	1.0.0
 	 */
 	public function loaded_form_submenu_page() {
 		// called when the particular page is loaded.
 	}

  /**
  * Fetch the XML file daily through wp-cron.php from within Plugin's cron.php
  * @link     http://expertagent.co.uk/
  *
  * @since    1.0.0
  */
  // add_action( 'check_daily', 'fse_wpeaxf_download_xml' );

  public function fse_wpeaxf_do_activation() {
    if ( !wp_next_scheduled( 'check_daily' ) ) {
	     wp_schedule_event( time(), 'daily', 'check_daily' );
    }
  }
  public function fse_wpeaxf_download_xml() {
    $this->download_xml( $fse_wpeaxf_remote_file, $fse_wpeaxf_remote_user, $fse_wpeaxf_remote_pass );
  }

 	/**
 	 *
 	 * @since    1.0.0
 	 */
 	public function the_form_response() {

 		if( isset( $_POST['fse_wpeaxf_ftp_nonce'] ) && wp_verify_nonce( $_POST['fse_wpeaxf_ftp_nonce'], 'fse_wpeaxf_add_ftp_form_nonce') ) {
 			$fse_wpeaxf_remote_file = sanitize_file_name( $_POST['fse_wpeaxf']['remote_file'] ); // sanitize the XML filename
 			$fse_wpeaxf_remote_user = sanitize_user( $_POST['fse_wpeaxf']['remote_user'] ); // sanitize the FTP username
      $fse_wpeaxf_remote_pass = $_POST['fse_wpeaxf']['remote_pass'];

      update_option( 'fse_wpeaxf_remote_file', $fse_wpeaxf_remote_file );
      update_option( 'fse_wpeaxf_remote_user', $fse_wpeaxf_remote_user );
      update_option( 'fse_wpeaxf_remote_pass', $fse_wpeaxf_remote_pass );

 			// server processing logic
      $this->download_xml( $fse_wpeaxf_remote_file, $fse_wpeaxf_remote_user, $fse_wpeaxf_remote_pass );

 			// server response
 			$admin_notice = "success";
 			$this->custom_redirect( $admin_notice, $_POST );
 			exit;
 		}
 		else {
 			wp_die( __( 'Invalid nonce specified', $this->plugin_name ), __( 'Error', $this->plugin_name ), array(
 						'response' 	=> 403,
 						'back_link' => 'admin.php?page=' . $this->plugin_name,

 				) );
 		}
 	}

 	/**
 	 * Redirect
 	 *
 	 * @since    1.0.0
 	 */
 	public function custom_redirect( $admin_notice ) {
 		wp_redirect( esc_url_raw( add_query_arg( array(
 									'fse_wpeaxf_admin_add_notice' => $admin_notice,
 									),
 							admin_url('admin.php?page='. $this->plugin_name )
 					) ) );

 	}


 	/**
 	 * Print Admin Notices
 	 *
 	 * @since    1.0.0
 	 */
 	public function print_plugin_admin_notices() {
 		  if ( isset( $_REQUEST['fse_wpeaxf_admin_add_notice'] ) ) {
 			if( $_REQUEST['fse_wpeaxf_admin_add_notice'] === "success") {
        $plugin_basename = explode("/", plugin_basename( __FILE__ ), 2)[0]; // get the plugin's directory name
        $file = esc_attr( get_option('fse_wpeaxf_remote_file') );
        $upload_dir = wp_upload_dir(); // Array of key => value pairs
        $plugin_upload_dir_xml = $upload_dir['basedir'] . '/' . $plugin_basename . '/xml/';
        $local_file_path = $plugin_upload_dir_xml . $file;

 				$html =	'<div class="notice notice-success is-dismissible">
 							<p><strong>The XML file fetch is successful. </strong></p>';
 				$html .= '<p>Your file should be found in <code>' . $local_file_path . '</code></p></div>';
 				echo $html;
 			}

 			// handle other types of form notices

 		  }
 		  else {
 			  return;
 		  }

 	}

  /**
 	 * Download the XML from the user-specified FTP Server
 	 *
 	 * @since    1.0.0
 	 */
  public function download_xml( $remote_file, $remote_user, $remote_pass )  {
    // Continue if FTP extension exists on the PHP server
    if(function_exists('ftp_connect')) {
      //#STEP 1 - Connect to FTP

      $ftp_host = 'ftp.expertagent.co.uk'; // this is constant
      $ftp_port = 21; // this is constant
      $ftp_timeout = 10; // 10 seconds timeout
      $ftp_username = $remote_user;
      $ftp_password = $remote_pass;

      $file = $remote_file;
      $extension = pathinfo($file, PATHINFO_EXTENSION); // gotta be 'xml'
      $filename = basename($file, '.xml');

      // make sure it's XML since that's what we do!
      if( $extension !== 'xml' ) {
        update_option( 'fse_wpeaxf_remote_file', '' );
        exit( 'File does not exist. Please specify an XML file. <a href="' . admin_url('admin.php?page='. $this->plugin_name ) . '">Go back</a>.' );
      }

      //#STEP 2 - Download File
      $ftp_connection = ftp_connect( $ftp_host, $ftp_port, $ftp_timeout );
      if( !$ftp_connection ) {
        exit( 'Could not connect to FTP at the moment. Please check your FTP server status and try again. <br />If unsure, please contact Expert Agent at support@expertagent.co.uk <a href="' . admin_url('admin.php?page='. $this->plugin_name ) . '">Go back</a>' );
      }

      $login_result = ftp_login( $ftp_connection, $ftp_username, $ftp_password );
      if( !$login_result ) {
        exit( 'Login details did not match. Please check your FTP login details and try again. <a href="' . admin_url('admin.php?page='. $this->plugin_name ) . '">Go back</a>' );
      }

      $plugin_basename = explode( "/", plugin_basename( __FILE__ ), 2 )[0]; // get the plugin's directory name


      $upload_dir = wp_upload_dir(); // Array of key => value pairs
      $plugin_upload_dir = $upload_dir['basedir'] . '/' . $plugin_basename . '/';
      $plugin_upload_dir_xml = $upload_dir['basedir'] . '/' . $plugin_basename . '/xml/';

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

      ftp_pasv( $ftp_connection, true ); // So we don't get a BINARY Warning...
      ftp_get( $ftp_connection, $local_file_path, $server_file_path, FTP_BINARY );
      ftp_close( $ftp_connection );
    } else {
      exit( 'Please enable FTP on your server before proceeding. <a href="' . admin_url('admin.php?page='. $this->plugin_name ) . '">Go back</a>' );
    }
  }


 }
