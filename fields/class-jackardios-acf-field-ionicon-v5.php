<?php

// exit if accessed directly
if (!defined('ABSPATH')) exit;


// check if class already exists
if (!class_exists('jackardios_acf_field_ionicon')) :


    class jackardios_acf_field_ionicon extends acf_field
    {

        private $manifest_url = "https://unpkg.com/ionicons@5.4.0/dist/ionicons.json";

        /*
        *  __construct
        *
        *  This function will setup the field type data
        *
        *  @type	function
        *  @date	5/03/2014
        *  @since	5.0.0
        *
        *  @param	n/a
        *  @return	n/a
        */

        function __construct($settings)
        {

            /*
            *  name (string) Single word, no spaces. Underscores allowed
            */

            $this->name = 'ionicon';


            /*
            *  label (string) Multiple words, can include spaces, visible when selecting a field type
            */

            $this->label = __('Ionicon', 'acf-ionicon');


            /*
            *  category (string) basic | content | choice | relational | jquery | layout | CUSTOM GROUP NAME
            */

            $this->category = 'content';


            /*
            *  defaults (array) Array of default settings which are merged into the field object. These are used later in settings
            */

            $this->defaults = array(
                'default_value' => '',
                'default_label' => '',
                'show_preview' => 1,
                'allow_null' => 0,
            );

            /*
            *  settings (array) Store plugin settings (url, path, version) as a reference for later use with assets
            */

            $this->settings = $settings;


            // do not delete!
            parent::__construct();

            add_action('wp_ajax_acf/fields/ionicon/query', array($this, 'select2_ajax_request'));
            add_filter('acf_field_ionicon_get_icons', array($this, 'get_icons'), 5, 1);
        }


        /*
        *  render_field_settings()
        *
        *  Create extra settings for your field. These are visible when editing a field
        *
        *  @type	action
        *  @since	3.6
        *  @date	23/01/13
        *
        *  @param	$field (array) the $field being edited
        *  @return	n/a
        */

        function render_field_settings($field)
        {

            /*
            *  acf_render_field_setting
            *
            *  This function will create a setting for your field. Simply pass the $field parameter and an array of field settings.
            *  The array of settings does not require a `value` or `prefix`; These settings are found from the $field array.
            *
            *  More than one setting can be added by copy/paste the above code.
            *  Please note that you must also have a matching $defaults value for the field name (font_size)
            */

            acf_render_field_setting($field, array(
                'label' => __('Default Icon', 'acf-ionicon'),
                'instructions' => '',
                'type' => 'select',
                'name' => 'default_value',
                'class' => 'select2-ionicon ionicon-create',
                'choices' => !empty($field['default_value']) ? [$field['default_value'] => $this->render_select2_ionicon_option($field['default_value'])] : [],
                'value' => $field['default_value'],
                'placeholder' => 'Choose a default icon (optional)',
                'ui' => 1,
                'allow_null' => 0,
                'ajax' => 1,
                'ajax_action' => 'acf/fields/ionicon/query'
            ));

            acf_render_field_setting($field, array(
                'label' => __('Show Icon Preview', 'acf-ionicon'),
                'instructions' => __('Set to \'Yes\' to include a larger icon preview on any admin pages using this field.', 'acf-ionicon'),
                'type' => 'true_false',
                'name' => 'show_preview',
                'ui' => 1,
            ));

            acf_render_field_setting($field, array(
                'label' => __('Allow Null?', 'acf-ionicon'),
                'instructions' => '',
                'type' => 'true_false',
                'name' => 'allow_null',
                'ui' => 1,
            ));
        }

        public function get_icons($icons = array())
        {
            $icons = get_option('acf_field_ionicon_icon_data');

            if (empty($icons)) {
                $remote_get    = wp_remote_get($this->manifest_url);
                $response_code = (int)($remote_get['response']['code'] ?? 0);

                if (!is_wp_error($remote_get) && ($response_code >= 200) && ($response_code < 300)) {
                    $body = wp_remote_retrieve_body($remote_get);

                    if (!empty($body)) {
                        $parsed_body = $body ? json_decode($body, true) : null;

                        if (is_array($parsed_body) && !empty($parsed_body)) {
                            $icons = $parsed_body['icons'] ?? null;

                            if (!empty($icons)) {
                                update_option('acf_field_ionicon_icon_data', $icons, false);
                            }
                        }
                    }
                } else {
                    update_option('acf_field_ionicon_cdn_error', true);
                }
            }

            if (!empty($icons)) {
                return $icons;
            } else {
                return array();
            }
        }

        public function select2_ajax_request()
        {
            if (!acf_verify_ajax()) {
                die();
            }

            $response = $this->get_ajax_query($_POST);

            acf_send_ajax_results($response);
        }

        private function render_select2_ionicon_option(string $name)
        {
            return "<ion-icon name=\"{$name}\" style=\"font-size:1.125rem;\"></ion-icon> {$name}";
        }

        private function get_ajax_query($options = array())
        {
            $fieldKey = ($options['field_key'] ?? null) ? sanitize_key($options['field_key']) : null;
            $searchText = ($options['s'] ?? null) ? wp_unslash(sanitize_text_field($options['s'])) : null;
            $searchText = !empty($searchText) ? wp_unslash($searchText) : null;

            $results = array();

            if ('default_value' != $fieldKey) {
                $field = acf_get_field($fieldKey);
                if (!$field) return false;
            }

            $icons = apply_filters('acf_field_ionicon_get_icons', array());

            if ($icons) {
                foreach ($icons as $iconData) {
                    $name = $iconData['name'] ?? null;
                    $tags = $iconData['tags'] ?? array();

                    if ($name) {
                        if (is_array($tags) && !empty($tags) && is_string($searchText)) {
                            foreach ($tags as $tag) {
                                $tag = strval($tag);
                                if (stripos($tag, $searchText) === false) {
                                    continue;
                                }
                                $results[] = array(
                                    'id'   => $name,
                                    'text' => $this->render_select2_ionicon_option($name)
                                );
                                break;
                            }
                        } else {
                            $results[] = array(
                                'id'   => $name,
                                'text' => $this->render_select2_ionicon_option($name)
                            );
                        }
                    }
                }
            }

            $response = array(
                'results' => $results
            );

            return $response;
        }


        /*
        *  render_field()
        *
        *  Create the HTML interface for your field
        *
        *  @param	$field (array) the $field being rendered
        *
        *  @type	action
        *  @since	3.6
        *  @date	23/01/13
        *
        *  @param	$field (array) the $field being edited
        *  @return	n/a
        */

        public function render_field($field)
        {
            if ($field['allow_null']) {
                $select_value = $field['value'];
            } else {
                $select_value = ('null' != $field['value']) ? $field['value'] : $field['default_value'];
            }

            $field['type'] = 'select';
            $field['ui'] = 1;
            $field['ajax'] = 1;
            $field['choices'] = array();
            $field['multiple'] = false;
            $field['class'] = 'select2-ionicon ionicon-edit';

            $icons = $this->get_icons();

            if ($select_value) {
                $field['choices'][$select_value] = $this->render_select2_ionicon_option($select_value);
            } else if (!$select_value && !$field['allow_null'] && isset($icons[0]['name'])) {
                $default_value = $icons[0]['name'];
                $field['choices'][$default_value] = $this->render_select2_ionicon_option($default_value);
            }

            if ($field['show_preview']) :
?>
                <div class="icon_preview"></div>
<?php
            endif;

            acf_render_field($field);
        }


        /*
        *  input_admin_enqueue_scripts()
        *
        *  This action is called in the admin_enqueue_scripts action on the edit screen where your field is created.
        *  Use this action to add CSS + JavaScript to assist your render_field() action.
        *
        *  @type	action (admin_enqueue_scripts)
        *  @since	3.6
        *  @date	23/01/13
        *
        *  @param	n/a
        *  @return	n/a
        */

        function input_admin_enqueue_scripts()
        {
            // vars
            $url = $this->settings['url'];
            $version = $this->settings['version'];

            // register & include JS
            wp_register_script('acf-ionicon-ionicons', "https://unpkg.com/ionicons@5.4.0/dist/ionicons/ionicons.js", array(), $version);
            wp_enqueue_script('acf-ionicon-ionicons');

            wp_register_script('acf-ionicon-ionicons-module', "https://unpkg.com/ionicons@5.4.0/dist/ionicons/ionicons.esm.js", array('acf-ionicon-ionicons'), $version);
            wp_enqueue_script('acf-ionicon-ionicons-module');

            wp_register_script('acf-ionicon-input', "{$url}assets/js/input.js", array('acf-input', 'acf-ionicon-ionicons', 'acf-ionicon-ionicons-module'), $version);
            wp_enqueue_script('acf-ionicon-input');

            // add module/nomodule attributes for ionicons scripts
            add_filter('script_loader_tag', function ($tag, $handle) {
                if ($handle === 'acf-ionicon-ionicons') {
                    return str_replace(' src', ' nomodule defer src', $tag);
                }
                if ($handle === 'acf-ionicon-ionicons-module') {
                    return str_replace(' src', ' type="module" src', $tag);
                }

                return $tag;
            }, 10, 3);

            // register & include CSS
            wp_register_style('acf-ionicon-input', "{$url}assets/css/input.css", array('acf-input'), $version);
            wp_enqueue_style('acf-ionicon-input');
        }
    }


    // initialize
    new jackardios_acf_field_ionicon($this->settings);


// class_exists check
endif;

?>