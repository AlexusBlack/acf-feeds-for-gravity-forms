<?php
/*
Plugin Name: ACF Feeds for Gravity Forms 
Description: Write Gravity Forms submission fields into ACF fields. Accumulate values over time.
Version: 1.0.1
Author: Alex Chernov
Author URI: https://alexchernov.com
*/
define('GFACFF_ADDON_VERSION', '1.0.1');

add_action('gform_loaded', array('ACFFeeds_AddOn_Bootstrap', 'load'), 5);
 
class ACFFeeds_AddOn_Bootstrap {
  public static function load() {
    // Check if Gravity Forms installed
    if(!method_exists('GFForms', 'include_addon_framework')) return;
    // Include primary class
    require_once('class-gfacffaddon.php');
    GFAddOn::register('GFACFFAddOn');
  }
}
 
function gf_acf_feeds_addon() {
      return GFACFFAddOn::get_instance();
}
?>
