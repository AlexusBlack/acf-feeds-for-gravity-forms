<?php
/*
Plugin Name: ACF Feeds for Gravity Forms 
Description: Write Gravity Forms submission fields into ACF fields. Accumulate values over time.
Version: 1.0.0
Author: Alex Chernov
Author URI: https://alexchernov.com
*/
define('GFACFF_ADDON_VERSION', '1.0.0');

add_action('gform_loaded', array('GF_GFACFF_AddOn_Bootstrap', 'load'), 5);
 
class GF_GFACFF_AddOn_Bootstrap {
   
  public static function load() {
    if(!method_exists('GFForms', 'include_addon_framework')) {
      return;
    }
    require_once('class-gfacffaddon.php');
    GFAddOn::register('GFACFFAddOn');
  }
}
 
function gf_acff_addon() {
      return GFACFFAddOn::get_instance();
}
?>
