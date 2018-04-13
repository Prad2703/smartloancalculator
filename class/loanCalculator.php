<?php

/**
 * CLASS:: LOAN CALCULATOR SETTINGS & Calculation
 */
if (!class_exists('loanCalculator')) {

    class loanCalculator {

        private $shortcode_key;
        private $shortcode_id;
        public $calculator_settings;
        public $static_attr = array('title' => '', 'box_color' => '#ffff', 'text_color' => '#ffff', 'min' => '', 'max' => '', 'show' => 1, 'top_show' => 1, 'max_error_msg' => '', 'min_error_msg' => '', 'top_msg' => '', 'bottom_msg' => '', 'error' => '', 'dropmenu' => array(), 'file' => '', 'image_apply' => '0');
        public $default_fields;
        protected $db;

        public function __construct($shortcode_key = NULL) {
            global $wpdb;
            $this->db = &$wpdb;
            $defaults_attr = new stdClass();
            foreach ($this->static_attr as $key => $val) {
                $defaults_attr->$key = $val;
            }

            $this->default_fields = $defaults_attr;

            $this->calculator_settings = new stdClass();
            $this->calculator_settings->currency = $this->default_fields;
            $this->calculator_settings->amount = $this->default_fields;
            $this->calculator_settings->options = $this->default_fields;
            $this->calculator_settings->percentage = $this->default_fields;
            $this->calculator_settings->monthly_payment = $this->default_fields;
            $this->calculator_settings->amount_message = $this->default_fields;
            $this->calculator_settings->message_restriction = $this->default_fields;
            $this->calculator_settings->bg_color = $this->default_fields;
            $this->calculator_settings->months = $this->default_fields;
            $this->calculator_settings->cal_title = $this->default_fields;
            $this->calculator_settings->cal_button = $this->default_fields;

            if (!empty($shortcode_key))
                $this->shortcode_key = $shortcode_key;
        }

        public function check_shortcode_exists($shortcode_key) {
            $query = "SELECT * FROM " . SMART_CALC_TBL . " WHERE shortcode_key='{$shortcode_key}'";
            return $this->db->get_row($query);
        }

        public function save_calc_shortcode($data) {
            if ($this->shortcode_key) {
                $this->db->update(SMART_CALC_TBL, $data, array('shortcode_key' => $this->shortcode_key));
            } else {
                $this->db->insert(SMART_CALC_TBL, $data);
                return $this->db->insert_id;
            }
        }

        public function get_calculator_shortcodes() {
            $query = "SELECT * FROM " . SMART_CALC_TBL . "";
            return $this->db->get_results($query);
        }

        public function get_current_shortcode_settings() {


            $query = "SELECT * FROM " . SMART_CALC_TBL . " WHERE shortcode_key='{$this->shortcode_key}'";
            return $this->db->get_row($query);
        }

        public function get_shortcode_key($frm_id) {
            $query = "SELECT * FROM " . SMART_CALC_TBL . " WHERE ID='{$frm_id}'";
            return $this->db->get_row($query);
        }

        /**
         * 
         * @return type
         */
        public function store_calculator_settings() {

            $calculator_settings = $this->get_current_shortcode_settings();

            $this->calculator_settings->frm_id = $calculator_settings->ID;
            $this->calculator_settings->shortcode_key = $calculator_settings->shortcode_key;

            if (isset($calculator_settings->cal_title) && $calculator_settings->cal_title != '') {
                $this->calculator_settings->cal_title = json_decode($calculator_settings->cal_title);
            }

            if (isset($calculator_settings->currency) && $calculator_settings->currency != '') {
                $this->calculator_settings->currency = json_decode($calculator_settings->currency);
            }

            if (isset($calculator_settings->amount) && $calculator_settings->amount != '') {
                $this->calculator_settings->amount = json_decode($calculator_settings->amount);
            }

            if (isset($calculator_settings->options_dropmenu) && $calculator_settings->options_dropmenu != '') {
                $this->calculator_settings->options = json_decode($calculator_settings->options_dropmenu);
            }

            if (isset($calculator_settings->percentage) && $calculator_settings->percentage != '') {
                $this->calculator_settings->percentage = json_decode($calculator_settings->percentage);
            }

            if (isset($calculator_settings->monthly_payment) && $calculator_settings->monthly_payment != '') {
                $this->calculator_settings->monthly_payment = json_decode($calculator_settings->monthly_payment);
            }

            if (isset($calculator_settings->months) && $calculator_settings->months != '') {
                $this->calculator_settings->months = json_decode($calculator_settings->months);
            }



            if (isset($calculator_settings->amount_message) && $calculator_settings->amount_message != '') {
                $this->calculator_settings->amount_message = json_decode($calculator_settings->amount_message);
            }

            if (isset($calculator_settings->message_restriction) && $calculator_settings->message_restriction != '') {
                $this->calculator_settings->message_restriction = json_decode($calculator_settings->message_restriction);
            }

            if (isset($calculator_settings->bg_color) && $calculator_settings->bg_color != '') {
                $this->calculator_settings->bg_color = json_decode($calculator_settings->bg_color);
            }

            if (isset($calculator_settings->cal_button) && $calculator_settings->cal_button != '') {
                $this->calculator_settings->cal_button = json_decode($calculator_settings->cal_button);
            }

            return $this->calculator_settings;
        }

        /**
         * 
         * @param type $message
         * @return type
         */
        public function extract_values($values, $index = 0) {
            $explode = explode(',', trim($values));
            return trim($explode[$index]);
        }

    }

}