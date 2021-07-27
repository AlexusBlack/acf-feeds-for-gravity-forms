<?php
GFForms::include_feed_addon_framework();
 
class GFACFFAddOn extends GFFeedAddOn {
      protected $_version = GFACFF_ADDON_VERSION;
      // Earlier versions maybe supported but not tested
      protected $_min_gravityforms_version = '2.5';
      protected $_slug = 'gfacff';
      protected $_path = 'gravity-forms-acf-feeds/gravity-forms-acf-feeds.php';
      protected $_full_path = __FILE__;
      protected $_title = 'Advanced Custom Field Feeds Add-On';
      protected $_short_title = 'ACF Feeds';
                   
      private static $_instance = null;
                   
      public static function get_instance() {
        if(self::$_instance == null) {
          self::$_instance = new GFACFFAddOn();
        }
        return self::$_instance;
      }
                   
      public function init() {
        parent::init();
      }

      public function feed_settings_fields() {
        return array(
          array(
            'title'  => __('Feed Settings', 'gfacff'),
            'fields' => array(
              array(
                'name' => 'feedName',
                'label' => __('Name', 'gfacff'),
                'type' => 'text',
                'required' => true,
                'class' => 'medium',
              ),
              array(
                'name' => 'target_post_id',
                'label' => __('Target Post\Page ID', 'gfacff'),
                'type' => 'text',
                'class' => 'medium merge-tag-support mt-position-right',
                'tooltip' => __('Can be page, post or any custom post type with post id', 'gfacff')
              ),
              array(
                'name' => 'acf_field_map',
                'label' => __('ACF Fields Map', 'gfacff'),
                'type' => 'dynamic_field_map',
                'limit' => 10,
                'tooltip' => __('Enter ACF field name and then pick a GF field with data for that ACF field')
              ),
              array(
                'name'  => 'feed_condition',
                'label' => __('Feed Condition', 'gfacff'),
                'type'  => 'feed_condition',
              )
            )
          )
        );
      }

      public function feed_list_columns() {
        return array(
          'feedName' => __('Name', 'gfacff')
        );
      }

      public function process_feed($feed, $entry, $form) {
        // Get ID of a target post into which we want to write out data
        $target_id = intval(rgars($feed, 'meta/target_post_id'));

        // Load dynamic map between GF and ACF fields
        $acfMap = $this->get_dynamic_field_map_fields($feed, 'acf_field_map');

        // Extract data from the form entry and write it into appropriate fields
        foreach($acfMap as $target_field_name => $source_field_id) {
          $source_field_value = rgar($entry, $source_field_id);
          update_field($target_field_name, $source_field_value, $target_id);
        }
      }
}
?>
