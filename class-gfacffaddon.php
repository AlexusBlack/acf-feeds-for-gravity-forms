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
                'name'    => 'target_type',
                'type'    => 'select',
                'label'   => __('Target Type', 'gfacff'),
                'choices' => array(
                  array(
                    'label' => __('ACF Target Selector', 'gfacff'),
                    'value' => 'acf_target_selector'
                  ),
                )
              ),
              array(
                'name' => 'target_post_id',
                'label' => __('ACF Target ID', 'gfacff'),
                'type' => 'text',
                'class' => 'medium merge-tag-support mt-position-right',
                'tooltip' => __('Can be page, post, custom post, user, term, taxonomy, widget, comment, options page, etc... Empty field means current post. Supports GF merge tags. For more information please refer to ACF documentation.', 'gfacff')
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

      public function get_menu_icon() {
        return 'dashicons-media-spreadsheet';
      }

      public function process_feed($feed, $entry, $form) {
        $this->log_debug(__METHOD__ . '(): Start feed processing');
        // Get ACF selector of a target into which we want to write out data
        $raw_target_id = rgars($feed, 'meta/target_post_id');
        $target_id = false; // current post
        // if selector isn't empty, extracting it's value
        if(trim($raw_target_id) !== "") {
          $target_id = GFCommon::replace_variables($raw_target_id, $form, $entry, false, false, false);
          $this->log_debug(__METHOD__ . '(): Provided target: ' . $target_id);
        } else {
          $this->log_debug(__METHOD__ . '(): The target is current post');
        }

        // Load dynamic map between GF and ACF fields
        $acfMap = $this->get_dynamic_field_map_fields($feed, 'acf_field_map');

        // Extract data from the form entry and write it into appropriate fields
        foreach($acfMap as $target_field_name => $source_field_id) {
          $field = null;
          if(strpos($source_field_id, '.') === false) {
            $field = $this->get_form_field_by_id($form, intval($source_field_id));
            if($field === null) {
              $this->log_debug(__METHOD__ . sprintf('(): GF field with id "%s" wasn\'t found', $source_field_id));
              continue;
            }
          }

          if($field !== null && $field->type == 'checkbox') {
            $checked_values = array();
            foreach($field->choices as $idx => $choice) {
              $value = rgar($entry, $source_field_id . '.' . ($idx + 1));
              if($value !== '') array_push($checked_values, $value);
            }
            $this->log_debug(__METHOD__ . sprintf('(): Writing from GF field "%s" to ACF field "%s" value "%s"', $source_field_id, $target_field_name, implode(', ', $checked_values)));
            update_field($target_field_name, $checked_values, $target_id);

          } else {
            $source_field_value = rgar($entry, $source_field_id);
            $this->log_debug(__METHOD__ . sprintf('(): Writing from GF field "%s" to ACF field "%s" value "%s"', $source_field_id, $target_field_name, $source_field_value));
            update_field($target_field_name, $source_field_value, $target_id);
          }
        }
      }

      function get_form_field_by_id($form, $field_id) {
        foreach($form['fields'] as &$field) {
          if($field->id == $field_id) return $field;
        }
        return null;
      }
}
?>
