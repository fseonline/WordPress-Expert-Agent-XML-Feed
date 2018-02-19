<?php
/**
 * Setup the Plugin Settings page's form fields.
 *
 * This includes the form submitting an AJAX call.
 * That'll run 'Fetch XML File', which although similar is
 * separate from the cron job.
 */

 if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

  if(!function_exists('ftp_connect')) {
      // Hide form FTP is not enabled on the server
      echo '<div class="wrap"><h1>WordPress Expert Agent XML Feed</h1><div class="notice notice-error"><p><strong>FTP is required</strong> to use this plugin. Please inform your server administrator to <a target="_blank" href="https://stackoverflow.com/questions/39841936/enabling-ftp-functions-in-existing-php-install">enable FTP</a> on your server.</p></div>
      <p>Sorry, we require FTP enabled on your server. Please see the error above.</p></div>';
      die();
  }

   ?>

   <?php
     $plugin_path = __FILE__;
     $plugin_basename = plugin_basename( $plugin_path );
    ?>
   <div class="wrap">
    <h1>WordPress Expert Agent XML Feed</h1>

     <form id="fse_wpeaxf" method="post" action="options.php">
       <?php settings_fields( 'fse_wpeaxf_settings_group' ); ?>
       <?php do_settings_sections( 'fse_wpeaxf_settings_group' ); ?>
       <table class="form-table">
         <tr valign="top">
         <th scope="row">Remote File</th>
         <td><input type="text" name="fse_wpeaxf_remote_file" value="<?php echo esc_attr( get_option('fse_wpeaxf_remote_file') ); ?>" placeholder="e.g. properties.xml" size="33" /></td>
         </tr>

         <tr valign="top">
         <th scope="row">Remote User</th>
         <td><input type="text" name="fse_wpeaxf_remote_user" value="<?php echo esc_attr( get_option('fse_wpeaxf_remote_user') ); ?>" placeholder="e.g. Excellent Agency" size="33" /></td>
         </tr>

         <tr valign="top">
         <th scope="row">Remote Password</th>
         <td><input type="password" name="fse_wpeaxf_remote_pass" value="<?php echo esc_attr( get_option('fse_wpeaxf_remote_pass') ); ?>" size="33" /></td>
         </tr>

       </table>

       <p>
         <?php submit_button('Fetch XML File', 'primary', 'Fetch XML File', false); ?>

       </p>

       <div id="data"></div>

         <script>

           jQuery(function($) {
             $('#fse_wpeaxf').submit(function(e) {
               var b;
               e.preventDefault();
               b = $(this).serialize();
               $.post( 'options.php', b, function() { // after posting form to WP
                 $.ajax({
                   type: 'POST',
                   url: '<?php echo plugins_url( 'ajax.php', $plugin_path ); ?>',
                   dataType: 'html',
                   data: {
                     a: 'ftp_download',
                     file_id: 1
                   }
                 });
                 checkStatus(1);

               });

               return false;
             });
           });
           function checkStatus(idFile) {
             jQuery.ajax({
               type: 'POST',
               url: '<?php echo plugins_url( 'check_status.php', $plugin_path ); ?>',
               dataType: 'JSON',
               data: {
                 file_id: idFile
               },
               success: function(response) {
                 jQuery('#data').html(response.message);
                 if (response.done != true) {
                   setTimeout("checkStatus(" + idFile + ")", 1000);
                 }
               }
             });
           }

         </script>

     </form>

   </div>
