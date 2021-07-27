<?php
/*
Plugin Name: Gravity Forms - Advanced Custom Field Feeds
Description: Allows to write Gravity Forms submission fields into ACF fields
Version: 1.0.0
Author: Alex Chernov @ Red Realities PTY LTD
Author URI: https://redrealities.com
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
