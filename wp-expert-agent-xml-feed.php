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

  function do_activation() {

    if ( !wp_next_scheduled( 'check_daily' ) ) {
	     wp_schedule_event( time(), 'daily', 'fse_read_properties_xml' );
    }
  }

  add_action( 'check_daily', 'fse_read_properties_xml' );

  function fse_read_properties_xml() {
    $plugin_path = __FILE__;
    $plugin_basename = plugin_basename( $plugin_path );
    $plugin_name = trim( dirname( $plugin_basename ), '/' );

    $filepath = esc_attr( get_option('fse_wp_expert_agent_xml_feed_remote_url') );
    $extension = pathinfo($filepath, PATHINFO_EXTENSION);
    $filename = basename($filepath, '.xml');
    $propertiesXML = $filename . '.' . $extension;

    if( $extension === 'xml' ) { // make sure it's XML since that's what we do!
      $propertiesXML = $filename . '.' . $extension;

    }
    if(isset($propertiesXML)) { // Making sure it's a valid XML file first...

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

      if( in_array('curl', get_loaded_extensions() ) ) { // Check if Curl exists in server
        $remote_url = get_option('fse_wp_expert_agent_xml_feed_remote_url');
        $remote_user = get_option('fse_wp_expert_agent_xml_feed_remote_user');
        $remote_pass = get_option('fse_wp_expert_agent_xml_feed_remote_pass');

        // Download latest XML file
        // and place into the plugin's wp-uploads directory
        $curl = curl_init();
        $file = fopen( $plugin_upload_dir_xml . $propertiesXML, 'w');
        curl_setopt($curl, CURLOPT_URL, $remote_url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_FILE, $file); // save into plugin's wp-uploads
        curl_setopt($curl, CURLOPT_USERPWD, $remote_user.":".$remote_pass);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10); // timeout in 10 seconds
        curl_exec($curl);
        curl_close($curl);
        fclose($file);

      }

    }



  }

  // create custom plugin settings menu
  add_action('admin_menu', 'fse_wp_expert_agent_xml_feed_create_menu');

  function fse_wp_expert_agent_xml_feed_create_menu() {

  	//create new top-level menu
    add_submenu_page( 'options-general.php', 'WordPress Expert Agent XML Feed', 'WordPress Expert Agent XML Feed', 'administrator', __FILE__, 'wp_expert_agent_xml_feed_settings_page' );

  	//call register settings function
  	add_action( 'admin_init', 'register_wp_expert_agent_xml_feed_settings' );
  }


  function register_wp_expert_agent_xml_feed_settings() {
  	//register our settings
  	register_setting( 'fse-settings-group', 'fse_wp_expert_agent_xml_feed_remote_url' );
  	register_setting( 'fse-settings-group', 'fse_wp_expert_agent_xml_feed_remote_user' );
  	register_setting( 'fse-settings-group', 'fse_wp_expert_agent_xml_feed_remote_pass' );
  }

  function wp_expert_agent_xml_feed_settings_page() {
    fse_read_properties_xml(); // Execute the plugin
  ?>
  <div class="wrap">
    <h1>WordPress Expert Agent XML Feed</h1>
    <form method="post" action="options.php">
      <?php settings_fields( 'fse-settings-group' ); ?>
      <?php do_settings_sections( 'fse-settings-group' ); ?>
      <table class="form-table">
        <tr valign="top">
        <th scope="row">Remote URL</th>
        <td><input type="url" name="fse_wp_expert_agent_xml_feed_remote_url" value="<?php echo esc_attr( get_option('fse_wp_expert_agent_xml_feed_remote_url') ); ?>" size="42" /></td>
        </tr>

        <tr valign="top">
        <th scope="row">Remote User</th>
        <td><input type="text" name="fse_wp_expert_agent_xml_feed_remote_user" value="<?php echo esc_attr( get_option('fse_wp_expert_agent_xml_feed_remote_user') ); ?>" size="42" /></td>
        </tr>

        <tr valign="top">
        <th scope="row">Remote Password</th>
        <td><input type="password" name="fse_wp_expert_agent_xml_feed_remote_pass" value="<?php echo esc_attr( get_option('fse_wp_expert_agent_xml_feed_remote_pass') ); ?>" size="42" /></td>
        </tr>
      </table>

      <?php submit_button(); ?>

    </form>
  </div>

  <?php }

  function fse_plugin_action_links( $links ) {
  	$links = array_merge( array(
  		'<a href="' . esc_url( admin_url( 'options-general.php?page=' . plugin_basename( __FILE__ ) ) ) . '">' . 'Settings' . '</a>'
  	), $links );
  	return $links;
  }
  add_action( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'fse_plugin_action_links' );


  register_deactivation_hook( __FILE__, 'do_deactivation' );

  function do_deactivation() {
  	wp_clear_scheduled_hook( 'fse_read_properties_xml' );
  }
