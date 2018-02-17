<?php
/**
 * Setup the Plugin Settings page's form fields.
 *
 * This includes the form submitting an AJAX call.
 * That'll run 'Fetch XML File', which although similar is
 * separate from the cron job.
 */
 ?>

<div class="wrap">
  <h1>WordPress Expert Agent XML Feed</h1>
  <?php
    $plugin_path = __FILE__;
    $plugin_basename = plugin_basename( $plugin_path );

   ?>

  <form id="fse_wp_expert_agent_xml_feed" method="post" action="options.php">
    <?php settings_fields( 'fse-settings-group' ); ?>
    <?php do_settings_sections( 'fse-settings-group' ); ?>
    <table class="form-table">
      <tr valign="top">
      <th scope="row">Remote File</th>
      <td><input type="text" name="fse_wp_expert_agent_xml_feed_remote_file" value="<?php echo esc_attr( get_option('fse_wp_expert_agent_xml_feed_remote_file') ); ?>" placeholder="e.g. properties.xml" size="33" /></td>
      </tr>

      <tr valign="top">
      <th scope="row">Remote User</th>
      <td><input type="text" name="fse_wp_expert_agent_xml_feed_remote_user" value="<?php echo esc_attr( get_option('fse_wp_expert_agent_xml_feed_remote_user') ); ?>" placeholder="e.g. Excellent Agency" size="33" /></td>
      </tr>

      <tr valign="top">
      <th scope="row">Remote Password</th>
      <td><input type="password" name="fse_wp_expert_agent_xml_feed_remote_pass" value="<?php echo esc_attr( get_option('fse_wp_expert_agent_xml_feed_remote_pass') ); ?>" size="33" /></td>
      </tr>

    </table>

    <p>
      <?php submit_button('Fetch XML File', 'primary', 'Fetch XML File', false); ?>

    </p>

    <div id="data"></div>

      <script>

        jQuery(function($) {
          $('#fse_wp_expert_agent_xml_feed').submit(function(e) {
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
                },
                success: function(txt) {
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
