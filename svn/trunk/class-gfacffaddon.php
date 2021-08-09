<?php
GFForms::include_feed_addon_framework();
 
class GFACFFAddOn extends GFFeedAddOn {
      protected $_version = GFACFF_ADDON_VERSION;
      // Earlier versions maybe supported but not tested
      protected $_min_gravityforms_version = '2.5';
      protected $_slug = 'gfacff';
      protected $_path = 'acf-feeds-for-gravity-forms/acf-feeds-for-gravity-forms.php';
      protected $_full_path = __FILE__;
      protected $_title = 'ACF Feeds Add-On';
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
                // 'limit' => 10,
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
        $target_selector = $this->get_acf_target($feed, $entry, $form); // current post

        // Load dynamic map between GF and ACF fields
        $acfMap = $this->get_dynamic_field_map_fields($feed, 'acf_field_map');

        // Extract data from the form entry and write it into appropriate fields
        foreach($acfMap as $target_field_name => $source_field_id) {
          // If field id contains no dot then it is a proper field, 
          // otherwise it is a sub-field of a field
          $is_subfield = strpos($source_field_id, '.') !== false;
          // get a field object by ID, or parent field for a sub-field
          $field = $this->get_form_field_by_id($form, $source_field_id);

          // if no field or parent field than something wrong with a source id
          if($field === null) {
            $this->log_debug(__METHOD__ . sprintf('(): GF field with id "%s" wasn\'t found', $source_field_id));
            continue;
          }

          // processing merge tags in target_field_name
          $target_field_name = GFCommon::replace_variables($target_field_name, $form, $entry, false, false, false);

          // handling different types of fields
          if(!$is_subfield) {
            if($field->type == 'checkbox') {
              $this->process_checkbox($field, $entry, $source_field_id, $target_field_name, $target_selector);
            } else if($field->type == 'number') {
              $this->process_number($field, $entry, $source_field_id, $target_field_name, $target_selector);
            } else {
              $this->process_default($entry, $source_field_id, $target_field_name, $target_selector);
            }
          } else {
            // default processing for sub-fields for now
            $this->process_default($entry, $source_field_id, $target_field_name, $target_selector);
          }
        }
      }

      function get_acf_target($feed, $entry, $form) {
        // getting a target setting
        $raw_target = rgars($feed, 'meta/target_post_id');
        $raw_target = trim($raw_target);

        if($raw_target == '') {
          $this->log_debug(__METHOD__ . '(): The target is current post');
          return false; // Current post in ACF
        }

        // Processing merge tags
        $target_selector = GFCommon::replace_variables($raw_target, $form, $entry, false, false, false);
        $this->log_debug(__METHOD__ . '(): Provided ACF target: ' . $target_selector);
        
        return $target_selector;
      }

      function process_checkbox($field, $entry, $source_field_id, $target_field_name, $target_selector) {
        // Extracting checked choices to write into ACF
        $checked_values = array();
        foreach($field->choices as $idx => $choice) {
          $value = rgar($entry, $source_field_id . '.' . ($idx + 1));
          if($value !== '') array_push($checked_values, $value);
        }
        $this->log_debug(__METHOD__ . sprintf('(): Writing from GF field "%s" to ACF field "%s" value "%s"', $source_field_id, $target_field_name, implode(', ', $checked_values)));
        update_field($target_field_name, $checked_values, $target_selector);
      }

      function process_number($field, $entry, $source_field_id, $target_field_name, $target_selector) {
        $supported_operations = array('+', '-', '*');
        $operation = null;

        // checking if any operation required
        if(in_array($target_field_name[0], $supported_operations)) {
          $operation = $target_field_name[0];
          $target_field_name = substr($target_field_name, 1);
        }

        // extracing value from entry
        $entry_value = rgar($entry, $source_field_id);

        // performing operation if required
        if($operation !== null) {
          $current_value = get_field($target_field_name, $target_selector);

          switch($operation) {
          case '+':
            $entry_value = $current_value + $entry_value;
            break;
          case '-':
            $entry_value = $current_value - $entry_value;
            break;
          case '*':
            $entry_value = $current_value * $entry_value;
          };
        }

        $this->log_debug(__METHOD__ . sprintf('(): Writing from GF field "%s" to ACF field "%s" value "%s"', $source_field_id, $target_field_name, $entry_value));
        // writing new value into ACF
        update_field($target_field_name, $entry_value, $target_selector);
      }
      
      function process_default($entry, $source_field_id, $target_field_name, $target_selector) {
        $supported_operations = array('+'); // assuming default is 'string' compatible, concatenation allowed
        $operation = null;

        // checking if any operation required
        if(in_array($target_field_name[0], $supported_operations)) {
          $operation = $target_field_name[0];
          $target_field_name = substr($target_field_name, 1);
        }

        // extracing value from entry
        $entry_value = rgar($entry, $source_field_id);

        // performing operation if required
        if($operation !== null) {
          $current_value = get_field($target_field_name, $target_selector);

          switch($operation) {
          case '+':
            $entry_value = $current_value . $entry_value;
            break;
          }
        }

        $this->log_debug(__METHOD__ . sprintf('(): Writing from GF field "%s" to ACF field "%s" value "%s"', $source_field_id, $target_field_name, $entry_value));
        update_field($target_field_name, $entry_value, $target_selector);
      }
      
      // Finds field info by an ID
      // Will get a parent field if sub-field id provided
      function get_form_field_by_id($form, $field_id) {
        $field_id = intval($field_id); 
        foreach($form['fields'] as &$field) {
          if($field->id == $field_id) return $field;
        }
        return null;
      }
}
?>
