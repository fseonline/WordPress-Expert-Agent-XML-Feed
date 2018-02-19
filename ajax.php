<?php
// if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if (!isset( $_POST['file_id'] ) ) {
  die();
}

$fileHash = md5( $_POST['file_id'] );

// Continue if FTP extension exists on the PHP server
if(!function_exists('ftp_connect')) {
  die();
}

//#STEP 1 - Connect to FTP
file_put_contents($fileHash, json_encode(array('message' => 'Connecting', 'done' => false)));

$ftp_host = 'ftp.expertagent.co.uk'; // this is constant
$ftp_port = 21; // this is constant
$ftp_timeout = 10; // 10 seconds timeout
$ftp_username = esc_attr( get_option('fse_wpeaxf_remote_user') );
$ftp_password = esc_attr( get_option('fse_wpeaxf_remote_pass') );

$ftp_connection = ftp_connect($ftp_host, $ftp_port, $ftp_timeout);
if ($ftp_connection === false) {
    file_put_contents($fileHash, json_encode(array('message' => 'Connection error', 'done' => true)));
}
$login_result = ftp_login($ftp_connection, $ftp_username, $ftp_password);
if ($ftp_connection === false) {
    file_put_contents($fileHash, json_encode(array('message' => 'Login error', 'done' => true)));
}

//#STEP 2 - Download File
$plugin_basename = plugin_basename( __FILE__ );
$plugin_name = trim( dirname( $plugin_basename ), '/' );

$file = esc_attr( get_option('fse_wpeaxf_remote_file') );
$extension = pathinfo($file, PATHINFO_EXTENSION);
$filename = basename($file, '.xml');

// make sure it's XML since that's what we do!
if( $extension !== 'xml' ) {
  file_put_contents($fileHash, json_encode(array('message' => 'Download error. File is not XML. Try something like <code>properties.xml</code>', 'done' => true)));
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

file_put_contents($fileHash, json_encode(array('message' => 'Downloading', 'done' => false)));
ftp_pasv($ftp_connection, true); // So we don't get a BINARY Warning...

if (ftp_get($ftp_connection, $local_file_path, $server_file_path, FTP_BINARY)) {
    file_put_contents($fileHash, json_encode(array('message' => 'Download complete! <p>File located at ' . '<code>' . $local_file_path . '</code></p>', 'done' => true)));
} else {
    file_put_contents($fileHash, json_encode(array('message' => 'Download error', 'done' => true)));
}
ftp_close($ftp_connection);
