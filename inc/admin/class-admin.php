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
 	 * Register the JavaScript for the admin area.
 	 *
 	 * @since    1.0.0
 	 */
 	public function enqueue_scripts() {

 		$params = array ( 'ajaxurl' => admin_url( 'admin-ajax.php' ) );
 		wp_enqueue_script( 'fse_wpeaxf_ajax_handle', plugin_dir_url( __FILE__ ) . 'js/wp-expert-agent-xml-feed-admin-ajax-handler.js', array( 'jQuery' ), $this->version, false );
 		wp_localize_script( 'fse_wpeaxf_ajax_handle', 'params', $params );

 	}

 	/**
 	 * Callback for the admin menu
 	 *
 	 * @since    1.0.0
 	 */
 	public function add_plugin_admin_menu() {

 		add_menu_page(	__( 'Admin Form Demo', $this->plugin_text_domain ), //page title
 						__( 'Admin Form Demo', $this->plugin_text_domain ), //menu title
 						'manage_options', //capability
 						$this->plugin_name //menu_slug
 					);

 		 // Add a submenu page and save the returned hook suffix.
 		$html_form_page_hook = add_submenu_page(
 									$this->plugin_name, //parent slug
 									__( 'Admin Form Demo', $this->plugin_text_domain ), //page title
 									__( 'HTML Form Submit', $this->plugin_text_domain ), //menu title
 									'manage_options', //capability
 									$this->plugin_name, //menu_slug
 									array( $this, 'html_form_page_content' ) //callback for page content
 									);

 		// Add a submenu page and save the returned hook suffix.
 		$ajax_form_page_hook = add_submenu_page(
 									$this->plugin_name, //parent slug
 									__( 'Admin Form Demo', $this->plugin_text_domain ), //page title
 									__( 'Ajax Form Sumit', $this->plugin_text_domain ), //menu title
 									'manage_options', //capability
 									$this->plugin_name . '-ajax', //menu_slug
 									array( $this, 'ajax_form_page_content' ) //callback for page content
 									);

 		/*
 		 * The $page_hook_suffix can be combined with the load-($page_hook) action hook
 		 * https://codex.wordpress.org/Plugin_API/Action_Reference/load-(page)
 		 *
 		 * The callback below will be called when the respective page is loaded
 		 */
 		add_action( 'load-'.$html_form_page_hook, array( $this, 'loaded_html_form_submenu_page' ) );
 		add_action( 'load-'.$ajax_form_page_hook, array( $this, 'loaded_ajax_form_submenu_page' ) );
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
 	 * Callback for the add_submenu_page action hook
 	 *
 	 * The plugin's HTML Ajax is loaded from here
 	 *
 	 * @since	1.0.0
 	 */
 	public function ajax_form_page_content() {
 		include_once( 'views/partials-ajax-form-view.php' );
 	}

 	/*
 	 * Callback for the load-($html_form_page_hook)
 	 * Called when the plugin's submenu HTML form page is loaded
 	 *
 	 * @since	1.0.0
 	 */
 	public function loaded_html_form_submenu_page() {
 		// called when the particular page is loaded.
 	}

 	/*
 	 * Callback for the load-($ajax_form_page_hook)
 	 * Called when the plugin's submenu Ajax form page is loaded
 	 *
 	 * @since	1.0.0
 	 */
 	public function loaded_ajax_form_submenu_page() {
 		// called when the particular page is loaded.
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



 			// server processing logic

      $this->download_xml( $fse_wpeaxf_remote_file, $fse_wpeaxf_remote_user, $fse_wpeaxf_remote_pass );

 			if( isset( $_POST['ajaxrequest'] ) && $_POST['ajaxrequest'] === 'true' ) {
 				// server response
 				echo '<pre>';
 					print_r( $_POST );
 				echo '</pre>';
 				wp_die();
             }

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
        $file = sanitize_file_name( $_REQUEST['fse_wpeaxf']['remote_file'] );
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

      $ftp_connection = ftp_connect($ftp_host, $ftp_port, $ftp_timeout);
      if ($ftp_connection === false) {
      }
      $login_result = ftp_login($ftp_connection, $ftp_username, $ftp_password);
      if ($ftp_connection === false) {
      }

      //#STEP 2 - Download File
      $plugin_basename = explode("/", plugin_basename( __FILE__ ), 2)[0]; // get the plugin's directory name

      $file = $remote_file;
      $extension = pathinfo($file, PATHINFO_EXTENSION); // gotta be 'xml'
      $filename = basename($file, '.xml');

      // make sure it's XML since that's what we do!
      if( $extension !== 'xml' ) {
        error( 'Please specify an XML file.' );
      }

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
      error( 'Please enable FTP on your server before proceeding.' );
    }
  }


 }
