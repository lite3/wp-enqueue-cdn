<?php

if (!class_exists('LITE3_WP_Plugin_Base')) {

    class LITE3_WP_Plugin_Base {
        var $version = '';
        var $option_name = '';
        function __construct($version = '', $option_name = '') {
            $this->version = $version;
            $this->option_name = $option_name;
        }

        public function get_default_options() {
            return array();
        }

        public function get_options() {
            $options = get_option($this->option_name);
            $option_change = false;
            if(!is_array($options)) {
                $options = array();
            }
            $default = $this->get_default_options();
            foreach ( $default as $key => $value) {
                if(!isset($options[$key])) {
                    $options[$key] = $value;
                    $option_change = true;
                }
            }
            if ($option_change) {
                update_option($this->option_name, $options);
            }
            return $options;
        }

        public function reset_options() {
            $options = $this->get_default_options();
            update_option($this->option_name, $options);
            return $options;
        }

        public function update_options($options) {
            update_option($this->option_name, $options);
        }
    }
}
?>
