=== WordPress Expert Agent XML Feed ===
Contributors: fseonline
Tags: expert agent, xml, plugin, admin, cron, wp-cron
Tested up to: 4.9

WordPress Expert Agent XML Feed lets you fetch the latest XML feed from your Expert Agent property feed.

== Description ==

WordPress Expert Agent XML Feed lets you fetch the latest XML feed from your Expert Agent property feed. From the admin screen you can:
 * Add Remote File
 * Add Remote User login details
 * Fetch the latest XML feed manually via the button 'Fetch XML File'

= Usage =

1. Go to the `Settings -> WordPress Expert Agent XML Feed` menu to manage settings for fetching the XML feed.

== Frequently Asked Questions ==

= How do I know that the XML feed is working? =

After you've specified your FTP server and login details which Expert Agent will have provided, you will have to click the button 'Fetch XML File'. Afterwards, a prompt below the button will appear, and will mention if the file download is successful.

= Is this the official plugin from Expert Agent? =

Unfortunately not yet, but we are working with Expert Agent to make sure that this plugin is kept robust.

= Where can I find my Remote URL, User, and Password login details? =

You will need to create a Log Ticket through the Expert Agent Management system, informing them that you would like to ask for the XML feed FTP login details.

= The XML feed is now working, how do I extract its data? =

Unfortunately this plugin does not provide extracting the data. It is up to you or your developer to extract the data, e.g. using PHP through [simpleXML](http://php.net/manual/en/simplexml.examples-basic.php).

= Why did you place the file under the Uploads folder? =

This is the proper place for a plugin to generate its files.

= How can I output/use the XML file? =

It is up to you or your developer to extract the data, e.g. using PHP through [simpleXML](http://php.net/manual/en/simplexml.examples-basic.php).

For example, let us get the 'Property of the Week':

`
<?php
  $upload_dir = wp_upload_dir();
  $propertiesXML = $upload_dir['basedir'] . '/wp-expert-agent-xml-feed/xml/properties.xml';

  if( file_exists( $propertiesXML ) ):

    // Let's get the XML file for the properties...
    $agency = new SimpleXMLElement( file_get_contents( $propertiesXML ) );

    $properties = $agency->branches->branch->properties;
    $property = $properties->property;

    // Let's get the first 'Propertyofweek' we can find...
    // Then break apart once we find it!
    for ($i=0; $i < sizeof($property); $i++) {
      if( $property[$i]->propertyofweek == 'Yes' ) {
        $propertyofweek_price_text = $property[$i]->price_text;
        $propertyofweek_advert_heading = $property[$i]->advert_heading;
        $propertyofweek_main_advert = $property[$i]->main_advert;
        $propertyofweek_web_link = $property[$i]->web_link;
        $propertyofweek_picture_filename = $property[$i]->pictures->picture->filename;
        break;

      }

    } ?>
    <div class="property__title"><?php echo $propertyofweek_advert_heading; ?></div>
  <?php endif; ?>
`

== Screenshots ==

1. Add your FTP login details as provided by Expert Agent. Contact Expert Agent if you have forgotten your XML feed FTP login details.
2. Upon clicking the button 'Fetch XML File', you will find your downloaded file in your Uploads Folder.
3. Here is a visual of drilling down the folders towards the XML file.

== Changelog ==

For WordPress Expert Agent XML Feed's changelog, please see [the Releases page on GitHub](https://github.com/fseonline/WordPress-Expert-Agent-XML-Feed/releases).
